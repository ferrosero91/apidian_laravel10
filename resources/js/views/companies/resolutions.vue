<template>
  <div>
    <!-- Header -->
    <header class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h2>Listado de Resoluciones</h2>
        <br>
        <span class="text-muted">{{ company.user.name }} - {{ company.user.email }} - {{ company.identification_number }}-{{ company.dv }}</span>
      </div>
      <div class="right-wrapper text-right mt-auto pb-1">
        <a href="/home" class="btn btn-secondary btn-sm">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
        <el-button type="primary" size="small" class="ml-2" @click="showNewModal = true">
          <i class="fas fa-plus"></i> Nueva resolución
        </el-button>
      </div>
    </header>

    <!-- Table -->
    <div class="card">
      <div class="card-body p-0">
        <el-table
          :data="resolutions"
          stripe
          style="width: 100%"
          empty-text="No hay resoluciones"
          v-loading="loading"
        >
          <el-table-column label="Entorno" width="130">
            <template slot-scope="scope">
              <span v-if="scope.row.environment_name">{{ scope.row.environment_name }}</span>
              <el-button v-else size="mini" type="warning" @click="updateEnvironment(scope.row)">
                Actualizar
              </el-button>
            </template>
          </el-table-column>
          <el-table-column prop="prefix" label="Prefijo" width="100"></el-table-column>
          <el-table-column prop="resolution" label="Número" width="150"></el-table-column>
          <el-table-column prop="type_document_name" label="Tipo de Documento" width="180"></el-table-column>
          <el-table-column prop="resolution_date" label="Fecha" width="120"></el-table-column>
          <el-table-column label="Rango" width="150">
            <template slot-scope="scope">
              {{ scope.row.from }} - {{ scope.row.to }}
            </template>
          </el-table-column>
          <el-table-column prop="date_from" label="Inicio" width="120"></el-table-column>
          <el-table-column prop="date_to" label="Fin" width="120"></el-table-column>
          <el-table-column prop="technical_key" label="Clave Técnica" min-width="200" show-overflow-tooltip></el-table-column>
          <el-table-column label="Acciones" width="100" align="center" fixed="right">
            <template slot-scope="scope">
              <el-button size="mini" type="primary" icon="el-icon-edit" circle @click="editResolution(scope.row)"></el-button>
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

    <!-- Modal Nueva Resolución -->
    <el-dialog title="Nueva Resolución" :visible.sync="showNewModal" width="700px" @close="resetNewForm">
      <el-form :model="newForm" :rules="rules" ref="newForm" label-position="top">
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Tipo de Documento" prop="type_document_id">
              <el-select v-model="newForm.type_document_id" placeholder="Seleccionar" style="width: 100%;" @change="handleTypeChange('new')">
                <el-option
                  v-for="type in typeDocuments"
                  :key="type.id"
                  :label="type.name"
                  :value="type.id"
                  :data-code="type.code"
                ></el-option>
              </el-select>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Prefijo" prop="prefix">
              <el-input v-model="newForm.prefix" maxlength="10"></el-input>
            </el-form-item>
          </div>
        </div>

        <el-alert
          v-if="isSimpleType('new')"
          title="Tipo de resolución simplificada: Solo se requieren Tipo de documento, Prefijo y Rangos."
          type="info"
          :closable="false"
          show-icon
          class="mb-3"
        ></el-alert>

        <div v-if="!isSimpleType('new')">
          <div class="row">
            <div class="col-md-6">
              <el-form-item label="Número de Resolución" prop="resolution">
                <el-input v-model="newForm.resolution"></el-input>
              </el-form-item>
            </div>
            <div class="col-md-6">
              <el-form-item label="Fecha de Resolución" prop="resolution_date">
                <el-date-picker v-model="newForm.resolution_date" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
              </el-form-item>
            </div>
          </div>
          <el-form-item label="Clave Técnica" prop="technical_key">
            <el-input v-model="newForm.technical_key"></el-input>
          </el-form-item>
        </div>

        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Rango Inicial" prop="from">
              <el-input-number v-model="newForm.from" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Rango Final" prop="to">
              <el-input-number v-model="newForm.to" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
        </div>

        <div v-if="!isSimpleType('new')">
          <div class="row">
            <div class="col-md-6">
              <el-form-item label="Fecha Inicio Vigencia" prop="date_from">
                <el-date-picker v-model="newForm.date_from" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
              </el-form-item>
            </div>
            <div class="col-md-6">
              <el-form-item label="Fecha Fin Vigencia" prop="date_to">
                <el-date-picker v-model="newForm.date_to" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
              </el-form-item>
            </div>
          </div>
        </div>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showNewModal = false">Cancelar</el-button>
        <el-button type="primary" @click="saveResolution" :loading="saving">
          <i class="fas fa-save"></i> Guardar
        </el-button>
      </span>
    </el-dialog>

    <!-- Modal Editar Resolución -->
    <el-dialog title="Editar Resolución" :visible.sync="showEditModal" width="700px" @close="resetEditForm">
      <el-form :model="editForm" :rules="rules" ref="editForm" label-position="top">
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Tipo de Documento" prop="type_document_id">
              <el-select v-model="editForm.type_document_id" placeholder="Seleccionar" style="width: 100%;" @change="handleTypeChange('edit')">
                <el-option
                  v-for="type in typeDocuments"
                  :key="type.id"
                  :label="type.name"
                  :value="type.id"
                ></el-option>
              </el-select>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Prefijo">
              <el-input v-model="editForm.prefix" disabled style="background-color: #f8f9fa;"></el-input>
              <small class="text-muted">El prefijo no puede ser modificado</small>
            </el-form-item>
          </div>
        </div>

        <el-alert
          v-if="isSimpleType('edit')"
          title="Tipo de resolución simplificada: Solo se requieren Tipo de documento, Prefijo y Rangos."
          type="info"
          :closable="false"
          show-icon
          class="mb-3"
        ></el-alert>

        <div v-if="!isSimpleType('edit')">
          <div class="row">
            <div class="col-md-6">
              <el-form-item label="Número de Resolución" prop="resolution">
                <el-input v-model="editForm.resolution"></el-input>
              </el-form-item>
            </div>
            <div class="col-md-6">
              <el-form-item label="Fecha de Resolución" prop="resolution_date">
                <el-date-picker v-model="editForm.resolution_date" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
              </el-form-item>
            </div>
          </div>
          <el-form-item label="Clave Técnica" prop="technical_key">
            <el-input v-model="editForm.technical_key"></el-input>
          </el-form-item>
        </div>

        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Rango Inicial" prop="from">
              <el-input-number v-model="editForm.from" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Rango Final" prop="to">
              <el-input-number v-model="editForm.to" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
        </div>

        <div v-if="!isSimpleType('edit')">
          <div class="row">
            <div class="col-md-6">
              <el-form-item label="Fecha Inicio Vigencia" prop="date_from">
                <el-date-picker v-model="editForm.date_from" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
              </el-form-item>
            </div>
            <div class="col-md-6">
              <el-form-item label="Fecha Fin Vigencia" prop="date_to">
                <el-date-picker v-model="editForm.date_to" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
              </el-form-item>
            </div>
          </div>
        </div>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showEditModal = false">Cancelar</el-button>
        <el-button type="primary" @click="updateResolution" :loading="saving">
          <i class="fas fa-save"></i> Actualizar
        </el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'CompanyResolutions',
  props: {
    company: {
      type: Object,
      required: true
    },
    resolutionsData: {
      type: Array,
      required: true
    },
    typeDocuments: {
      type: Array,
      required: true
    },
    paginationData: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      resolutions: [],
      loading: false,
      saving: false,
      showNewModal: false,
      showEditModal: false,
      simpleTypeCodes: ['91', '92', '93', '94'],
      newForm: {
        type_document_id: '',
        prefix: '',
        resolution: '',
        resolution_date: '',
        technical_key: '',
        from: '',
        to: '',
        date_from: '',
        date_to: ''
      },
      editForm: {
        id: null,
        type_document_id: '',
        prefix: '',
        resolution: '',
        resolution_date: '',
        technical_key: '',
        from: '',
        to: '',
        date_from: '',
        date_to: ''
      },
      rules: {
        type_document_id: [{ required: true, message: 'Seleccione el tipo de documento', trigger: 'change' }],
        prefix: [{ required: true, message: 'Ingrese el prefijo', trigger: 'blur' }],
        from: [{ required: true, message: 'Ingrese el rango inicial', trigger: 'blur' }],
        to: [{ required: true, message: 'Ingrese el rango final', trigger: 'blur' }]
      }
    };
  },
  computed: {
    pagination() {
      return this.paginationData;
    }
  },
  created() {
    this.prepareResolutions();
  },
  methods: {
    prepareResolutions() {
      this.resolutions = this.resolutionsData.map(res => ({
        ...res,
        type_document_name: res.type_document ? res.type_document.name : '',
        environment_name: this.getEnvironmentName(res.type_environment_id),
        type_document_code: res.type_document ? res.type_document.code : ''
      }));
    },
    getEnvironmentName(envId) {
      switch (String(envId)) {
        case '1': return 'Producción';
        case '2': return 'Habilitación';
        default: return null;
      }
    },
    isSimpleType(formType) {
      const form = formType === 'new' ? this.newForm : this.editForm;
      const type = this.typeDocuments.find(t => t.id === form.type_document_id);
      return type && this.simpleTypeCodes.includes(type.code);
    },
    handleTypeChange(formType) {
      // Trigger validation or UI updates when type changes
    },
    resetNewForm() {
      this.newForm = {
        type_document_id: '',
        prefix: '',
        resolution: '',
        resolution_date: '',
        technical_key: '',
        from: '',
        to: '',
        date_from: '',
        date_to: ''
      };
      if (this.$refs.newForm) {
        this.$refs.newForm.clearValidate();
      }
    },
    resetEditForm() {
      this.editForm = {
        id: null,
        type_document_id: '',
        prefix: '',
        resolution: '',
        resolution_date: '',
        technical_key: '',
        from: '',
        to: '',
        date_from: '',
        date_to: ''
      };
      if (this.$refs.editForm) {
        this.$refs.editForm.clearValidate();
      }
    },
    saveResolution() {
      this.$refs.newForm.validate(valid => {
        if (!valid) return;

        this.saving = true;
        this.$http.post(`/companies/${this.company.identification_number}/configuration/resolutions`, this.newForm, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
            this.showNewModal = false;
            location.reload();
          } else {
            this.$message.error(response.data.message || 'Error al crear la resolución');
          }
        })
        .catch(error => {
          if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.entries(errors).forEach(([field, messages]) => {
              this.$message.error(messages[0]);
            });
          } else {
            this.$message.error('Error interno del servidor');
          }
        })
        .finally(() => {
          this.saving = false;
        });
      });
    },
    editResolution(row) {
      this.editForm = {
        id: row.id,
        type_document_id: row.type_document_id,
        prefix: row.prefix,
        resolution: row.resolution,
        resolution_date: row.resolution_date,
        technical_key: row.technical_key,
        from: row.from,
        to: row.to,
        date_from: row.date_from,
        date_to: row.date_to
      };
      this.showEditModal = true;
    },
    updateResolution() {
      this.$refs.editForm.validate(valid => {
        if (!valid) return;

        this.saving = true;
        this.$http.put(`/companies/${this.company.identification_number}/configuration/resolutions/${this.editForm.id}`, this.editForm, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => {
          if (response.data.success) {
            this.$message.success(response.data.message);
            this.showEditModal = false;
            location.reload();
          } else {
            this.$message.error(response.data.message || 'Error al actualizar la resolución');
          }
        })
        .catch(error => {
          if (error.response && error.response.status === 422) {
            const errors = error.response.data.errors;
            Object.entries(errors).forEach(([field, messages]) => {
              this.$message.error(messages[0]);
            });
          } else {
            this.$message.error('Error interno del servidor');
          }
        })
        .finally(() => {
          this.saving = false;
        });
      });
    },
    updateEnvironment(row) {
      this.$http.patch(`/companies/${this.company.identification_number}/configuration/resolutions/${row.id}`, {
        type_environment_id: 2
      }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      })
      .then(response => {
        if (response.data.success) {
          this.$message.success('Ambiente actualizado');
          location.reload();
        } else {
          this.$message.error(response.data.message || 'Error al actualizar');
        }
      })
      .catch(() => {
        this.$message.error('Error al actualizar el ambiente');
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
</style>
