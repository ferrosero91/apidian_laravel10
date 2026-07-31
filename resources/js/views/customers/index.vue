<template>
  <div>
    <header class="page-header">
      <h2>Documentos del adquiriente</h2>
      <div class="right-wrapper text-end">
        <span class="text-muted">{{ customerIdnumber }}</span>
      </div>
    </header>

    <div class="card">
      <div class="card-body p-0">
        <el-table
          :data="documents"
          stripe
          style="width: 100%"
          empty-text="No hay documentos para mostrar."
        >
          <el-table-column prop="type_document_name" label="Tipo Documento" width="150"></el-table-column>
          <el-table-column prop="date_issue" label="Fecha" width="120"></el-table-column>
          <el-table-column prop="prefix" label="Prefijo" width="100"></el-table-column>
          <el-table-column prop="number" label="Número" width="120"></el-table-column>

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
    </div>
  </div>
</template>

<script>
export default {
  name: 'CustomersIndex',
  props: {
    documentsData: {
      type: Array,
      required: true
    },
    companyIdnumber: {
      type: String,
      required: true
    },
    customerIdnumber: {
      type: String,
      required: true
    },
    allowPublicDownloads: {
      type: Boolean,
      default: true
    }
  },
  data() {
    return {
      documents: []
    };
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
    downloadFile(row, type) {
      let filename = '';
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
        window.open(`/api/download/${this.companyIdnumber}/${filename}`, '_blank');
      } else {
        // Use form submission for private downloads
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
          identification: this.companyIdnumber,
          file: filename,
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
      }
    },
    sendEmail(row) {
      this.$confirm('¿Desea enviar este documento por correo?', 'Enviar Email', {
        confirmButtonText: 'Enviar',
        cancelButtonText: 'Cancelar',
        type: 'info'
      })
      .then(() => {
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
          company_idnumber: this.companyIdnumber,
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
