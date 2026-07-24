<template>
  <div>
    <header class="page-header">
      <h2>{{ title }}</h2>
    </header>

    <!-- Search Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <el-form :inline="true" :model="searchForm" @submit.native.prevent="handleSearch">
          <el-form-item label="Campo">
            <el-select v-model="searchForm.field" placeholder="Seleccione campo para filtrar" style="width: 250px;">
              <el-option label="Factura electrónica de Venta: Numero" value="1"></el-option>
              <el-option label="Factura electrónica de venta - exportación: Numero" value="2"></el-option>
              <el-option label="Instrumento electrónico de transmisión - tipo 03: Numero" value="3"></el-option>
              <el-option label="Nota Credito: Numero" value="4"></el-option>
              <el-option label="Nota Debito: Numero" value="5"></el-option>
              <el-option label="Documento Soporte Electrónico: Numero" value="11"></el-option>
              <el-option label="Fecha" value="6"></el-option>
              <el-option label="Nit Empresa" value="7"></el-option>
              <el-option label="ID Cliente" value="8"></el-option>
              <el-option label="Prefijo" value="9"></el-option>
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

    <!-- Documents Table -->
    <div class="card">
      <div class="card-body p-0">
        <el-table
          :data="documents"
          stripe
          style="width: 100%"
          empty-text="No hay documentos para mostrar."
          v-loading="loading"
        >
          <el-table-column prop="type_document_name" label="Tipo Documento" width="180"></el-table-column>
          <el-table-column prop="date_issue" label="Fecha" width="120"></el-table-column>
          <el-table-column prop="identification_number" label="Nit Empresa" width="140"></el-table-column>
          <el-table-column prop="customer" label="ID Cliente" width="140"></el-table-column>
          <el-table-column prop="prefix" label="Prefijo" width="100"></el-table-column>
          <el-table-column prop="number" label="Numero" width="120"></el-table-column>

          <!-- XML Download -->
          <el-table-column label="XML" width="80" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="primary"
                icon="el-icon-download"
                circle
                @click="downloadFile(scope.row, 'xml')"
              ></el-button>
            </template>
          </el-table-column>

          <!-- PDF Download -->
          <el-table-column label="PDF" width="80" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="danger"
                icon="el-icon-download"
                circle
                @click="downloadFile(scope.row, 'pdf')"
              ></el-button>
            </template>
          </el-table-column>

          <!-- AttachedDocument Download -->
          <el-table-column label="AttachedDocument" width="140" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="success"
                icon="el-icon-download"
                circle
                @click="downloadFile(scope.row, 'attached')"
              ></el-button>
            </template>
          </el-table-column>

          <!-- ZipAtt Download -->
          <el-table-column label="ZipAtt" width="80" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="warning"
                icon="el-icon-download"
                circle
                @click="downloadFile(scope.row, 'zip')"
              ></el-button>
            </template>
          </el-table-column>

          <!-- Send Email -->
          <el-table-column label="Enviar" width="80" align="center">
            <template slot-scope="scope">
              <el-button
                size="mini"
                type="info"
                icon="el-icon-message"
                circle
                @click="sendEmail(scope.row)"
              ></el-button>
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
  </div>
</template>

<script>
export default {
  name: 'OwnerDocuments',
  props: {
    title: {
      type: String,
      default: 'Documentos enviados por todas las empresas.'
    },
    documentsData: {
      type: Array,
      required: true
    },
    allowPublicDownloads: {
      type: Boolean,
      default: true
    },
    searchUrl: {
      type: String,
      default: '/okownersearch'
    },
    paginationData: {
      type: Object,
      default: null
    },
    isSeller: {
      type: Boolean,
      default: false
    },
    companyIdnumber: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      documents: [],
      loading: false,
      searchForm: {
        field: '',
        value: ''
      }
    };
  },
  computed: {
    pagination() {
      return this.paginationData;
    }
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
    handleSearch() {
      if (!this.searchForm.field || !this.searchForm.value) {
        this.$message.warning('Seleccione un campo e ingrese un valor de búsqueda');
        return;
      }

      const params = new URLSearchParams({
        searchfield: this.searchForm.field,
        searchvalue: this.searchForm.value
      });

      window.location.href = `${this.searchUrl}?${params.toString()}`;
    },
    downloadFile(row, type) {
      let filename = '';
      const companyId = this.isSeller ? this.companyIdnumber : row.identification_number;

      switch (type) {
        case 'xml':
          filename = row.xml;
          break;
        case 'pdf':
          filename = row.pdf;
          break;
        case 'attached':
          filename = `Attachment-${row.prefix}${row.number}.xml`;
          break;
        case 'zip':
          filename = `ZipAttachm-${row.prefix}${row.number}.xml`;
          break;
      }

      if (this.allowPublicDownloads) {
        window.open(`/api/download/${companyId}/${filename}`, '_blank');
      } else {
        this.submitDownloadForm(companyId, filename);
      }
    },
    submitDownloadForm(identification, file) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/downloadfile';

      const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);

      const fields = {
        identification: identification,
        file: file,
        type_response: 'false'
      };

      Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
      });

      document.body.appendChild(form);
      form.submit();
      document.body.removeChild(form);
    },
    sendEmail(row) {
      this.$confirm('¿Desea enviar este documento por correo?', 'Enviar Email', {
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        type: 'info'
      })
      .then(() => {
        const companyId = this.isSeller ? this.companyIdnumber : row.identification_number;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/send-email-customer';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        const fields = {
          company_idnumber: companyId,
          prefix: row.prefix,
          number: row.number
        };

        Object.entries(fields).forEach(([name, value]) => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = name;
          input.value = value;
          form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
      })
      .catch(() => {});
    },
    handlePageChange(page) {
      const url = new URL(window.location.href);
      url.searchParams.set('page', page);
      window.location.href = url.toString();
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
</style>
