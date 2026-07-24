<template>
  <div>
    <!-- Search Filters (only for seller portal) -->
    <div v-if="!isCompanyRoute" class="card mb-3">
      <div class="card-body">
        <el-form :inline="true" :model="searchForm" @submit.native.prevent="handleSearch">
          <el-form-item label="Campo">
            <el-select v-model="searchForm.field" placeholder="Seleccione campo para filtrar" style="width: 250px;">
              <el-option label="Factura electrónica de Venta: Numero" value="1"></el-option>
              <el-option label="Nit Emisor" value="2"></el-option>
              <el-option label="Nombre Emisor" value="3"></el-option>
              <el-option label="Acusadas" value="4"></el-option>
              <el-option label="Recibidas" value="5"></el-option>
              <el-option label="Aceptadas" value="6"></el-option>
              <el-option label="Rechazadas" value="7"></el-option>
              <el-option label="Prefijo" value="8"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="Valor">
            <el-input v-model="searchForm.value" placeholder="Buscar..." style="width: 300px;"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" native-type="submit">Buscar</el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="documents.length === 0" class="text-muted text-center py-4">
      No hay documentos para mostrar.
    </div>

    <!-- Documents Table -->
    <div v-else class="card">
      <div class="card-body p-0">
        <el-table :data="documents" stripe style="width: 100%" v-loading="loading">
          <!-- Status Column -->
          <el-table-column label="Estado Actual" width="100" align="center">
            <template slot-scope="scope">
              <i class="fa fa-circle" :style="{ color: getStatusColor(scope.row) }"></i>
            </template>
          </el-table-column>

          <el-table-column prop="type_document_name" label="Tipo Documento" width="150"></el-table-column>
          <el-table-column prop="date_issue" label="Fecha" width="120"></el-table-column>
          <el-table-column prop="identification_number" label="Nit Empresa" width="140"></el-table-column>
          <el-table-column prop="name_seller" label="Nombre" width="180"></el-table-column>
          <el-table-column prop="prefix" label="Prefijo" width="100"></el-table-column>
          <el-table-column prop="number" label="Numero" width="120"></el-table-column>
          <el-table-column label="Impuestos" width="120" align="right">
            <template slot-scope="scope">
              {{ formatNumber(scope.row.total_tax) }}
            </template>
          </el-table-column>
          <el-table-column label="Vr. Documento" width="140" align="right">
            <template slot-scope="scope">
              {{ formatNumber(scope.row.total) }}
            </template>
          </el-table-column>

          <!-- AttachedDocument -->
          <el-table-column label="Attached Document" width="140" align="center">
            <template slot-scope="scope">
              <el-button size="mini" type="primary" icon="el-icon-download" circle @click="downloadFile(scope.row, 'xml')"></el-button>
            </template>
          </el-table-column>

          <!-- PDF -->
          <el-table-column label="PDF" width="80" align="center">
            <template slot-scope="scope">
              <el-button size="mini" type="danger" icon="el-icon-download" circle @click="downloadFile(scope.row, 'pdf')"></el-button>
            </template>
          </el-table-column>

          <!-- Acuse Recibo -->
          <el-table-column label="Acuse Recibo" width="120" align="center">
            <template slot-scope="scope">
              <el-button
                v-if="scope.row.acu_recibo === 0 && canSendEvent(scope.row)"
                size="mini"
                type="primary"
                icon="el-icon-s-promotion"
                circle
                @click="sendEvent(scope.row, 1)"
              ></el-button>
              <i v-else-if="scope.row.acu_recibo === 1" class="fa fa-rss" style="color: blue;"></i>
            </template>
          </el-table-column>

          <!-- Recepcion Bienes -->
          <el-table-column label="Recepcion Bienes" width="140" align="center">
            <template slot-scope="scope">
              <el-button
                v-if="scope.row.rec_bienes === 0 && canSendEvent(scope.row)"
                size="mini"
                type="warning"
                icon="el-icon-s-promotion"
                circle
                @click="sendEvent(scope.row, 3)"
              ></el-button>
              <i v-else-if="scope.row.rec_bienes === 1" class="fa fa-rss" style="color: yellow;"></i>
            </template>
          </el-table-column>

          <!-- Aceptacion Expresa -->
          <el-table-column label="Aceptacion Expresa" width="150" align="center">
            <template slot-scope="scope">
              <el-button
                v-if="scope.row.aceptacion === 0 && canSendEvent(scope.row)"
                size="mini"
                type="success"
                icon="el-icon-s-promotion"
                circle
                @click="sendEvent(scope.row, 4)"
              ></el-button>
              <i v-else-if="scope.row.aceptacion === 1" class="fa fa-rss" style="color: green;"></i>
            </template>
          </el-table-column>

          <!-- Rechazo -->
          <el-table-column label="Rechazo" width="100" align="center">
            <template slot-scope="scope">
              <el-button
                v-if="scope.row.rechazo === 0 && canSendEvent(scope.row)"
                size="mini"
                type="danger"
                icon="el-icon-s-promotion"
                circle
                @click="showRejectModal(scope.row)"
              ></el-button>
              <i v-else-if="scope.row.rechazo === 1" class="fa fa-rss" style="color: red;"></i>
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

    <!-- Modal Rechazo -->
    <el-dialog title="Motivo de Rechazo" :visible.sync="showRejectDialog" width="500px">
      <p>Documento electrónico mediante el cual el Adquiriente manifiesta que no acepta el documento.</p>
      <el-form label-position="top">
        <el-form-item label="Motivo de Rechazo">
          <el-radio-group v-model="rejectForm.rejection_id">
            <el-radio :label="1">Documento con inconsistencias.</el-radio>
            <el-radio :label="2">Mercancía no entregada totalmente.</el-radio>
            <el-radio :label="3">Mercancía no entregada parcialmente.</el-radio>
            <el-radio :label="4">Servicio no prestado.</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showRejectDialog = false">Cerrar</el-button>
        <el-button type="danger" @click="submitReject" :loading="sending">Enviar Rechazo</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'EventsTable',
  props: {
    documentsData: { type: Array, required: true },
    companyIdnumber: { type: String, required: true },
    isCompanyRoute: { type: Boolean, default: false },
    searchUrl: { type: String, default: '' },
    paginationData: { type: Object, default: null }
  },
  data() {
    return {
      documents: [],
      loading: false,
      sending: false,
      showRejectDialog: false,
      searchForm: { field: '', value: '' },
      rejectForm: { rejection_id: 1 },
      currentDocument: null
    };
  },
  computed: {
    pagination() { return this.paginationData; }
  },
  created() {
    this.prepareDocuments();
  },
  methods: {
    prepareDocuments() {
      this.documents = this.documentsData.map(doc => ({
        ...doc,
        type_document_name: doc.type_document ? doc.type_document.name : ''
      }));
    },
    getStatusColor(row) {
      if (row.aceptacion === 1) return 'green';
      if (row.rechazo === 1) return 'red';
      if (row.rec_bienes === 1) return 'yellow';
      if (row.acu_recibo === 1) return 'blue';
      return 'black';
    },
    canSendEvent(row) {
      return [1, 2, 3].includes(row.type_document_id);
    },
    formatNumber(value) {
      return value ? Number(value).toFixed(2) : '0.00';
    },
    handleSearch() {
      if (!this.searchForm.field || !this.searchForm.value) {
        this.$message.warning('Seleccione un campo e ingrese un valor');
        return;
      }
      const params = new URLSearchParams({ searchfield: this.searchForm.field, searchvalue: this.searchForm.value });
      window.location.href = `${this.searchUrl}?${params.toString()}`;
    },
    downloadFile(row, type) {
      const filename = type === 'xml' ? row.xml : row.pdf;
      const url = `/api/receivedfile/${this.companyIdnumber}/${filename}`;
      fetch(url).then(response => {
        if (!response.ok) throw new Error('Archivo no encontrado');
        return response.blob();
      }).then(blob => {
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
      }).catch(() => {
        this.$message.error('Error al descargar el archivo');
      });
    },
    sendEvent(row, eventCode) {
      this.$confirm('¿Desea enviar este evento a la DIAN?', 'Enviar Evento', {
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        type: 'info'
      }).then(() => {
        this.loading = true;
        this.submitEvent(row, eventCode);
      }).catch(() => {});
    },
    showRejectModal(row) {
      this.currentDocument = row;
      this.rejectForm.rejection_id = 1;
      this.showRejectDialog = true;
    },
    submitReject() {
      this.sending = true;
      this.submitEvent(this.currentDocument, 2, this.rejectForm.rejection_id);
    },
    submitEvent(row, eventCode, rejectionId = null) {
      const formData = new FormData();
      formData.append('company_idnumber', row.identification_number);
      formData.append('company_dv', row.dv || '');
      formData.append('company_name', row.name_seller || '');
      formData.append('customer_idnumber', this.companyIdnumber);
      formData.append('prefix', row.prefix);
      formData.append('docnumber', row.number);
      formData.append('issuedate', row.date_issue);
      formData.append('cufe', row.cufe || '');
      formData.append('eventcode', eventCode);
      if (rejectionId) formData.append('rejection_id', rejectionId);

      this.$http.post('/accept-reject-document', formData, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      }).then(response => {
        const data = response.data;
        if (data.success) {
          this.$message.success(data.message);
          setTimeout(() => location.reload(), 2000);
        } else {
          this.$message.error(data.message || 'Error al procesar el evento');
        }
      }).catch(() => {
        this.$message.error('Error de conexión al enviar el evento');
      }).finally(() => {
        this.loading = false;
        this.sending = false;
        this.showRejectDialog = false;
      });
    },
    handlePageChange(page) {
      const url = new URL(window.location.href);
      url.searchParams.set('page', page);
      window.location.href = url.toString();
    }
  }
};
</script>
