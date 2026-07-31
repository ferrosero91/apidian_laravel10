<template>
  <div>
    <!-- Header -->
    <header class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h2>{{ company.user.name }} - {{ company.identification_number }}</h2>
        <br>
        <span class="text-muted">Seleccione el tipo de documento</span>
      </div>
      <div class="mt-auto pb-1">
        <a href="/home" class="btn btn-secondary btn-sm">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
        <el-button
          v-if="type === 'invoice'"
          type="primary"
          size="small"
          class="ml-2"
          @click="showExcelModal = true"
        >
          <i class="fas fa-upload mr-2"></i>Subida Masiva
        </el-button>
      </div>
    </header>

    <!-- Empty State -->
    <div v-if="documents.length === 0" class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
      <div class="text-center">
        <el-alert
          :title="emptyMessage"
          type="info"
          :closable="false"
          show-icon
        ></el-alert>
      </div>
    </div>

    <!-- Documents Table -->
    <div v-else class="card">
      <div class="card-body p-0">
        <el-table
          :data="documents"
          stripe
          style="width: 100%"
          empty-text="No hay documentos"
          v-loading="loading"
        >
          <el-table-column prop="iteration" label="#" width="60" align="center"></el-table-column>

          <!-- DIAN Column -->
          <el-table-column label="DIAN" width="180">
            <template slot-scope="scope">
              <div v-if="scope.row.response_dian">
                <el-button size="mini" type="primary" plain @click="showDianResponse(scope.row)">
                  Respuesta DIAN
                </el-button>
              </div>
              <div v-if="scope.row.cufe" class="mt-1">
                <el-button size="mini" type="primary" plain @click="showCufe(scope.row)">
                  Ver CUFE
                </el-button>
                <el-button size="mini" type="primary" plain @click="consultXml(scope.row)" class="mt-1">
                  Consultar Xml
                </el-button>
              </div>
              <div v-if="!scope.row.state_document_id" class="mt-1">
                <el-button size="mini" type="warning" plain @click="showChangeState(scope.row)">
                  ESTADO
                </el-button>
              </div>
            </template>
          </el-table-column>

          <!-- Downloads Column -->
          <el-table-column label="Descargas" width="120">
            <template slot-scope="scope">
              <el-button-group>
                <el-button size="mini" type="success" @click="downloadFile(scope.row, 'xml')">
                  XML
                </el-button>
                <el-button size="mini" type="success" @click="downloadFile(scope.row, 'pdf')">
                  PDF
                </el-button>
              </el-button-group>
            </template>
          </el-table-column>

          <el-table-column label="Ambiente" width="120">
            <template slot-scope="scope">
              <el-tag :type="scope.row.ambient_id === 2 ? 'warning' : 'success'" size="small">
                {{ scope.row.ambient_id === 2 ? 'Habilitación' : 'Producción' }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column label="Válido" width="80" align="center">
            <template slot-scope="scope">
              <el-tag :type="scope.row.state_document_id ? 'success' : 'danger'" size="small">
                {{ scope.row.state_document_id ? 'Si' : 'No' }}
              </el-tag>
            </template>
          </el-table-column>

          <el-table-column prop="date_issue" label="Fecha" width="120"></el-table-column>

          <el-table-column label="Número" width="120">
            <template slot-scope="scope">
              {{ scope.row.prefix }}{{ scope.row.number }}
            </template>
          </el-table-column>

          <el-table-column label="Cliente" min-width="200">
            <template slot-scope="scope">
              {{ scope.row.client_name }}<br>
              <small class="text-muted">{{ scope.row.document_type }} {{ scope.row.client_identification }}-{{ scope.row.client_dv }}</small>
            </template>
          </el-table-column>

          <el-table-column prop="type_document_name" label="Tipo de Documento" width="150"></el-table-column>

          <el-table-column label="Impuesto" width="120" align="right">
            <template slot-scope="scope">
              {{ formatNumber(scope.row.total_tax) }}
            </template>
          </el-table-column>

          <el-table-column label="Subtotal" width="120" align="right">
            <template slot-scope="scope">
              {{ formatNumber(scope.row.subtotal) }}
            </template>
          </el-table-column>

          <el-table-column label="Total" width="120" align="right">
            <template slot-scope="scope">
              <strong>{{ formatNumber(scope.row.total) }}</strong>
            </template>
          </el-table-column>

          <el-table-column label="Acciones" width="120" align="center" fixed="right">
            <template slot-scope="scope">
              <el-button
                v-if="scope.row.can_create_credit_note"
                size="mini"
                type="info"
                @click="createCreditNote(scope.row)"
              >
                Nota de crédito
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination" class="card-footer d-flex justify-content-center">
        <el-pagination
          background
          layout="prev, pager, next"
          :total="pagination.total"
          :page-size="pagination.per_page"
          :current-page="pagination.current_page"
          @current-change="handlePageChange"
        ></el-pagination>
      </div>
    </div>

    <!-- Modal CUFE -->
    <el-dialog title="CUFE" :visible.sync="showCufeModal" width="500px">
      <el-input v-model="currentCufe" readonly>
        <el-button slot="append" icon="el-icon-document-copy" @click="copyCufe"></el-button>
      </el-input>
      <span v-if="cufeCopied" class="text-success mt-2 d-block">¡Copiado!</span>
    </el-dialog>

    <!-- Modal DIAN Response -->
    <el-dialog title="Respuesta DIAN" :visible.sync="showDianModal" width="700px">
      <pre style="max-height: 400px; overflow-y: auto; background: #f5f5f5; padding: 15px; border-radius: 4px;">{{ currentDianResponse }}</pre>
    </el-dialog>

    <!-- Modal Change State -->
    <el-dialog title="Cambio de Estado" :visible.sync="showStateModal" width="500px">
      <el-alert
        title="Esto cambiará el estado del documento en este listado del API, es importante que se verifique el CUFE en la DIAN donde se muestre como ACEPTADO para continuar con este procedimiento."
        type="warning"
        :closable="false"
        show-icon
      ></el-alert>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showStateModal = false">Cerrar</el-button>
        <el-button type="success" @click="confirmChangeState" :loading="saving">Confirmar</el-button>
      </span>
    </el-dialog>

    <!-- Modal Excel Upload -->
    <el-dialog title="Subida Masiva de Facturas" :visible.sync="showExcelModal" width="700px">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span>Archivo Excel</span>
        <a href="/xlsx/co-documents-batch.xlsx" class="btn btn-sm btn-outline-primary">
          <i class="fas fa-download mr-1"></i>Descargar Plantilla
        </a>
      </div>
      <el-upload
        action="#"
        :auto-upload="false"
        :on-change="handleExcelFile"
        accept=".xls,.xlsx"
        :limit="1"
      >
        <el-button size="small" type="primary">Seleccionar archivo</el-button>
      </el-upload>
      <div v-if="processing" class="mt-3">
        <el-progress :percentage="100" status="success" :indeterminate="true"></el-progress>
        <p class="text-center mt-2">Procesando facturas...</p>
      </div>
      <div v-if="excelResults.length > 0" class="mt-3">
        <el-card shadow="never">
          <div slot="header"><strong>Resultado del Procesamiento</strong></div>
          <div style="max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 12px;">
            <div v-for="(result, index) in excelResults" :key="index" :class="result.success ? 'text-success' : 'text-danger'">
              {{ result.message }}
            </div>
          </div>
        </el-card>
      </div>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showExcelModal = false">Cerrar</el-button>
        <el-button v-if="excelResults.length > 0" type="success" @click="location.reload()">Finalizar</el-button>
        <el-button type="primary" @click="processExcel" :loading="processing" :disabled="!excelFile">
          Procesar Facturas
        </el-button>
      </span>
    </el-dialog>

    <!-- Modal Resolution Selection -->
    <el-dialog title="Seleccionar Resolución para Nota de Crédito" :visible.sync="showResolutionModal" width="600px">
      <el-alert
        title="Seleccione la resolución que desea utilizar para generar la nota de crédito."
        type="info"
        :closable="false"
        show-icon
        class="mb-3"
      ></el-alert>
      <div v-if="processingCreditNote" class="text-center py-4">
        <i class="el-icon-loading" style="font-size: 48px; color: #409eff;"></i>
        <p class="mt-3">Procesando la nota de crédito con la resolución <strong>{{ selectedResolution.prefix }}</strong>...</p>
        <p>Enviando a la DIAN...</p>
      </div>
      <div v-else>
        <div
          v-for="resolution in resolutions"
          :key="resolution.id"
          class="resolution-item"
          @click="selectResolution(resolution)"
        >
          <div class="d-flex justify-content-between">
            <strong>{{ resolution.prefix }}</strong>
            <small>{{ resolution.type_document_name || 'Nota de Crédito' }}</small>
          </div>
          <p class="mb-1">
            <strong>Resolución:</strong>
            {{ resolution.resolution_number || resolution.resolution || 'Sin número' }}
          </p>
          <small>
            <strong>Rango:</strong> {{ resolution.from }} - {{ resolution.to }}
            <span v-if="resolution.date_from && resolution.date_to">
              | <strong>Vigencia:</strong> {{ resolution.date_from }} - {{ resolution.date_to }}
            </span>
          </small>
        </div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'CompanyDocuments',
  props: {
    company: {
      type: Object,
      required: true
    },
    documentsData: {
      type: Array,
      required: true
    },
    resolutionsData: {
      type: Array,
      default: () => []
    },
    type: {
      type: String,
      default: 'invoice'
    },
    token: {
      type: String,
      default: ''
    },
    paginationData: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      documents: [],
      loading: false,
      saving: false,
      showCufeModal: false,
      showDianModal: false,
      showStateModal: false,
      showExcelModal: false,
      showResolutionModal: false,
      currentCufe: '',
      cufeCopied: false,
      currentDianResponse: '',
      currentDocument: null,
      excelFile: null,
      processing: false,
      excelResults: [],
      processingCreditNote: false,
      selectedResolution: {},
      resolutions: []
    };
  },
  computed: {
    emptyMessage() {
      if (this.type === 'invoice') return 'No hay facturas electrónicas generadas para esta empresa.';
      if (this.type === 'support') return 'No hay documentos soporte generados para esta empresa.';
      if (this.type === 'pos') return 'No hay documentos equivalentes generados para esta empresa.';
      return 'No hay documentos generados para este tipo.';
    },
    pagination() {
      return this.paginationData;
    }
  },
  created() {
    this.prepareDocuments();
    this.resolutions = this.resolutionsData;
  },
  methods: {
    prepareDocuments() {
      this.documents = this.documentsData.map((doc, index) => {
        const docType = doc.client && doc.client.type_document_identification
          ? doc.client.type_document_identification.name
          : '';
        return {
          ...doc,
          iteration: index + 1,
          client_name: doc.client ? doc.client.name : 'Sin nombre',
          client_identification: doc.client ? doc.client.identification_number : '',
          client_dv: doc.client ? doc.client.dv : '',
          document_type: docType,
          type_document_name: doc.type_document ? doc.type_document.name : '',
          can_create_credit_note: doc.type_document_id === 1 &&
            doc.response_dian &&
            this.resolutionsData.length > 0 &&
            this.isValidDianResponse(doc.response_dian)
        };
      });
    },
    isValidDianResponse(response) {
      if (!response) return false;
      try {
        const decoded = typeof response === 'string' ? JSON.parse(response) : response;
        return decoded && decoded.Envelope && decoded.Envelope.Body && decoded.Envelope.Body.SendBillSyncResponse && decoded.Envelope.Body.SendBillSyncResponse.SendBillSyncResult && decoded.Envelope.Body.SendBillSyncResponse.SendBillSyncResult.IsValid === 'true';
      } catch (e) {
        return false;
      }
    },
    formatNumber(value) {
      return value ? Number(value).toFixed(2) : '0.00';
    },
    downloadFile(row, type) {
      const file = type === 'xml' ? row.xml : row.pdf;
      window.open(`/api/view/${row.identification_number}/${file}`, '_blank');
    },
    showCufe(row) {
      this.currentCufe = row.cufe;
      this.cufeCopied = false;
      this.showCufeModal = true;
    },
    copyCufe() {
      navigator.clipboard.writeText(this.currentCufe).then(() => {
        this.cufeCopied = true;
        setTimeout(() => { this.cufeCopied = false; }, 2000);
      });
    },
    showDianResponse(row) {
      try {
        const response = typeof row.response_dian === 'string'
          ? JSON.parse(row.response_dian)
          : row.response_dian;
        this.currentDianResponse = JSON.stringify(response, null, 2);
      } catch {
        this.currentDianResponse = row.response_dian;
      }
      this.showDianModal = true;
    },
    showChangeState(row) {
      this.currentDocument = row;
      this.showStateModal = true;
    },
    confirmChangeState() {
      this.saving = true;
      this.$http.post('/document/change-state', {
        document_id: this.currentDocument.id
      }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      })
      .then(() => {
        this.$message.success('Estado cambiado exitosamente');
        this.showStateModal = false;
        location.reload();
      })
      .catch(() => {
        this.$message.error('Error al cambiar estado');
      })
      .finally(() => {
        this.saving = false;
      });
    },
    consultXml(row) {
      this.$http.get(`/company/${this.company.identification_number}/document/${row.cufe}`)
        .then(response => {
          this.currentDianResponse = JSON.stringify(response.data, null, 2);
          this.showDianModal = true;
        })
        .catch(() => {
          this.$message.error('Error al consultar XML');
        });
    },
    handleExcelFile(file) {
      this.excelFile = file.raw;
    },
    processExcel() {
      // Excel processing logic would go here
      this.processing = true;
      setTimeout(() => {
        this.processing = false;
        this.$message.info('Funcionalidad de subida masiva pendiente de implementar');
      }, 1000);
    },
    createCreditNote(row) {
      this.currentDocument = row;
      this.showResolutionModal = true;
    },
    selectResolution(resolution) {
      this.selectedResolution = resolution;
      this.processingCreditNote = true;
      this.showResolutionModal = true;

      // Process credit note
      this.processCreditNoteRequest(resolution);
    },
    processCreditNoteRequest(resolution) {
      const token = this.token;
      const doc = this.currentDocument;

      // Get next consecutive
      this.$http.post('/api/ubl2.1/next-consecutive', {
        type_document_id: 4,
        prefix: resolution.prefix
      }, {
        headers: {
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json'
        }
      })
      .then(response => {
        const nextNumber = response.data.number;
        const now = new Date();
        const colombiaTime = new Date(now.getTime() - (5 * 60 * 60 * 1000));
        const currentDate = colombiaTime.toISOString().split('T')[0];
        const currentTime = colombiaTime.toTimeString().split(' ')[0];

        const originalData = typeof doc.request_api === 'string' ? JSON.parse(doc.request_api) : doc.request_api;

        const creditNoteData = {
          billing_reference: {
            number: doc.prefix + doc.number,
            uuid: doc.cufe,
            issue_date: doc.date_issue
          },
          resolution_number: resolution.resolution_number,
          discrepancyresponsecode: 2,
          discrepancyresponsedescription: "NOTA DE CREDITO GENERADA AUTOMATICAMENTE",
          notes: "NOTA DE CREDITO",
          prefix: resolution.prefix,
          number: nextNumber,
          type_document_id: 4,
          date: currentDate,
          time: currentTime,
          sendmail: originalData.sendmail || false,
          sendmailtome: originalData.sendmailtome || false,
          customer: originalData.customer,
          allowance_charges: originalData.allowance_charges,
          tax_totals: originalData.tax_totals,
          legal_monetary_totals: originalData.legal_monetary_totals,
          credit_note_lines: (originalData.invoice_lines || []).map(line => ({
            unit_measure_id: line.unit_measure_id,
            invoiced_quantity: line.invoiced_quantity,
            line_extension_amount: line.line_extension_amount,
            free_of_charge_indicator: line.free_of_charge_indicator,
            tax_totals: line.tax_totals,
            description: line.description,
            notes: line.notes || '',
            code: line.code,
            type_item_identification_id: line.type_item_identification_id,
            price_amount: line.price_amount,
            base_quantity: line.base_quantity
          }))
        };

        return this.$http.post('/api/ubl2.1/credit-note', creditNoteData, {
          headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json'
          }
        });
      })
      .then(response => {
        const dianResult = response.data && response.data.ResponseDian && response.data.ResponseDian.Envelope && response.data.ResponseDian.Envelope.Body && response.data.ResponseDian.Envelope.Body.SendBillSyncResponse && response.data.ResponseDian.Envelope.Body.SendBillSyncResponse.SendBillSyncResult;
        const statusCode = dianResult && dianResult.StatusCode;

        if (statusCode === '00') {
          this.$message.success('Nota de crédito creada exitosamente');
          this.showResolutionModal = false;
          location.reload();
        } else {
          const errorMessage = (dianResult && dianResult.ErrorMessage && dianResult.ErrorMessage.string) || 'Error desconocido';
          this.$message.error('Error al crear la nota de crédito: ' + errorMessage);
        }
      })
      .catch(error => {
        this.$message.error('Error al procesar la nota de crédito: ' + error.message);
      })
      .finally(() => {
        this.processingCreditNote = false;
      });
    },
    handlePageChange(page) {
      window.location.href = `${window.location.pathname}?page=${page}`;
    }
  }
};
</script>

<style scoped>
.page-header h2 {
  color: #2B323D;
  font-weight: 600;
  margin-bottom: 0;
}

.resolution-item {
  padding: 15px;
  border: 1px solid #ebeef5;
  border-radius: 4px;
  margin-bottom: 10px;
  cursor: pointer;
  transition: all 0.2s;
}

.resolution-item:hover {
  background-color: #f5f7fa;
  border-color: #409eff;
}
</style>
