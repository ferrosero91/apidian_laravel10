<template>
  <div class="p-3">
    <!-- Tabs for Environment -->
    <el-tabs v-model="activeTab" @tab-click="handleTabClick">
      <el-tab-pane label="Habilitación" name="hab">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0">Resoluciones en Habilitación</h5>
          <el-button
            v-if="companyEnvId === 2"
            type="primary"
            size="small"
            @click="openNewModal(2)"
          >
            <i class="fas fa-plus"></i> Nueva resolución
          </el-button>
          <small v-else class="text-muted">
            <i class="fas fa-info-circle"></i>
            Solo se pueden crear en el ambiente actual de la empresa ({{ companyEnvLabel }}).
          </small>
        </div>

        <el-table :data="resolutionsHab" stripe style="width: 100%" empty-text="No hay resoluciones en Habilitación.">
          <el-table-column prop="prefix" label="Prefijo" width="100"></el-table-column>
          <el-table-column prop="resolution" label="Número" width="150"></el-table-column>
          <el-table-column prop="type_document_name" label="Tipo de Documento" width="180"></el-table-column>
          <el-table-column prop="resolution_date" label="Fecha" width="120"></el-table-column>
          <el-table-column label="Rango" width="150">
            <template slot-scope="scope">
              {{ scope.row.from }} - {{ scope.row.to }}
            </template>
          </el-table-column>
          <el-table-column label="Vigencia" width="200">
            <template slot-scope="scope">
              {{ scope.row.date_from }} → {{ scope.row.date_to }}
            </template>
          </el-table-column>
          <el-table-column prop="technical_key" label="Clave Técnica" min-width="200" show-overflow-tooltip></el-table-column>
          <el-table-column label="Acciones" width="100" align="center" fixed="right">
            <template slot-scope="scope">
              <el-button size="mini" type="primary" icon="el-icon-edit" circle @click="openEditModal(scope.row)"></el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>

      <el-tab-pane label="Producción" name="prod">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="m-0">Resoluciones en Producción</h5>
          <el-button
            v-if="companyEnvId === 1"
            type="primary"
            size="small"
            @click="openNewModal(1)"
          >
            <i class="fas fa-plus"></i> Nueva resolución
          </el-button>
          <small v-else class="text-muted">
            <i class="fas fa-info-circle"></i>
            Solo se pueden crear en el ambiente actual de la empresa ({{ companyEnvLabel }}).
          </small>
        </div>

        <el-table :data="resolutionsProd" stripe style="width: 100%" empty-text="No hay resoluciones en Producción.">
          <el-table-column prop="prefix" label="Prefijo" width="100"></el-table-column>
          <el-table-column prop="resolution" label="Número" width="150"></el-table-column>
          <el-table-column prop="type_document_name" label="Tipo de Documento" width="180"></el-table-column>
          <el-table-column prop="resolution_date" label="Fecha" width="120"></el-table-column>
          <el-table-column label="Rango" width="150">
            <template slot-scope="scope">
              {{ scope.row.from }} - {{ scope.row.to }}
            </template>
          </el-table-column>
          <el-table-column label="Vigencia" width="200">
            <template slot-scope="scope">
              {{ scope.row.date_from }} → {{ scope.row.date_to }}
            </template>
          </el-table-column>
          <el-table-column prop="technical_key" label="Clave Técnica" min-width="200" show-overflow-tooltip></el-table-column>
          <el-table-column label="Acciones" width="100" align="center" fixed="right">
            <template slot-scope="scope">
              <el-button size="mini" type="primary" icon="el-icon-edit" circle @click="openEditModal(scope.row)"></el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-tab-pane>
    </el-tabs>

    <!-- Modal Nueva Resolución -->
    <el-dialog :title="'Nueva Resolución (' + companyEnvLabel + ')'" :visible.sync="showNewModal" width="700px" @close="resetNewForm">
      <el-form :model="newForm" ref="newForm" label-position="top">
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Tipo de Documento" prop="type_document_id" :rules="[{required: true, message: 'Seleccione el tipo', trigger: 'change'}]">
              <el-select v-model="newForm.type_document_id" placeholder="Seleccionar" style="width: 100%;">
                <el-option v-for="td in typeDocuments" :key="td.id" :label="td.name" :value="td.id"></el-option>
              </el-select>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Prefijo" prop="prefix" :rules="[{required: true, message: 'Ingrese el prefijo', trigger: 'blur'}]">
              <el-input v-model="newForm.prefix" maxlength="10"></el-input>
            </el-form-item>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Número de Resolución">
              <el-input v-model="newForm.resolution"></el-input>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Fecha de Resolución">
              <el-date-picker v-model="newForm.resolution_date" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
            </el-form-item>
          </div>
        </div>
        <el-form-item label="Clave Técnica">
          <el-input v-model="newForm.technical_key" :disabled="type === 'support'" :placeholder="type === 'support' ? 'No aplica para Documento Soporte' : ''"></el-input>
        </el-form-item>
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Rango Inicial" prop="from" :rules="[{required: true, message: 'Ingrese el rango', trigger: 'blur'}]">
              <el-input-number v-model="newForm.from" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Rango Final" prop="to" :rules="[{required: true, message: 'Ingrese el rango', trigger: 'blur'}]">
              <el-input-number v-model="newForm.to" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Vigencia Desde">
              <el-date-picker v-model="newForm.date_from" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Vigencia Hasta">
              <el-date-picker v-model="newForm.date_to" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
            </el-form-item>
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
      <el-form :model="editForm" ref="editForm" label-position="top">
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Tipo de Documento">
              <el-select v-model="editForm.type_document_id" placeholder="Seleccionar" style="width: 100%;">
                <el-option v-for="td in typeDocuments" :key="td.id" :label="td.name" :value="td.id"></el-option>
              </el-select>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Prefijo">
              <el-input v-model="editForm.prefix" disabled style="background-color: #f8f9fa;"></el-input>
            </el-form-item>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Número de Resolución">
              <el-input v-model="editForm.resolution"></el-input>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Fecha de Resolución">
              <el-date-picker v-model="editForm.resolution_date" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
            </el-form-item>
          </div>
        </div>
        <el-form-item label="Clave Técnica">
          <el-input v-model="editForm.technical_key" :disabled="type === 'support'" :placeholder="type === 'support' ? 'No aplica para Documento Soporte' : ''"></el-input>
        </el-form-item>
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Rango Inicial">
              <el-input-number v-model="editForm.from" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Rango Final">
              <el-input-number v-model="editForm.to" :min="1" style="width: 100%;"></el-input-number>
            </el-form-item>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <el-form-item label="Vigencia Desde">
              <el-date-picker v-model="editForm.date_from" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
            </el-form-item>
          </div>
          <div class="col-md-6">
            <el-form-item label="Vigencia Hasta">
              <el-date-picker v-model="editForm.date_to" type="date" format="yyyy-MM-dd" value-format="yyyy-MM-dd" style="width: 100%;"></el-date-picker>
            </el-form-item>
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
  name: 'ResolutionsTab',
  props: {
    company: { type: Object, required: true },
    resolutionsHabData: { type: Array, default: () => [] },
    resolutionsProdData: { type: Array, default: () => [] },
    typeDocuments: { type: Array, required: true },
    type: { type: String, default: 'invoice' }
  },
  data() {
    return {
      activeTab: 'hab',
      resolutionsHab: [],
      resolutionsProd: [],
      showNewModal: false,
      showEditModal: false,
      saving: false,
      newForm: { type_document_id: '', prefix: '', resolution: '', resolution_date: '', technical_key: '', from: '', to: '', date_from: '', date_to: '' },
      editForm: { id: null, type_document_id: '', prefix: '', resolution: '', resolution_date: '', technical_key: '', from: '', to: '', date_from: '', date_to: '' }
    };
  },
  computed: {
    companyEnvId() { return Number(this.company.type_environment_id || 2); },
    companyEnvLabel() { return this.companyEnvId === 1 ? 'Producción' : 'Habilitación'; }
  },
  created() {
    this.resolutionsHab = this.resolutionsHabData.map(r => ({ ...r, type_document_name: r.type_document ? r.type_document.name : '' }));
    this.resolutionsProd = this.resolutionsProdData.map(r => ({ ...r, type_document_name: r.type_document ? r.type_document.name : '' }));
  },
  methods: {
    handleTabClick() {},
    openNewModal(envId) {
      this.newForm = { type_document_id: '', prefix: '', resolution: '', resolution_date: '', technical_key: '', from: '', to: '', date_from: '', date_to: '' };
      this.showNewModal = true;
    },
    openEditModal(row) {
      this.editForm = { id: row.id, type_document_id: row.type_document_id, prefix: row.prefix, resolution: row.resolution, resolution_date: row.resolution_date, technical_key: row.technical_key, from: row.from, to: row.to, date_from: row.date_from, date_to: row.date_to };
      this.showEditModal = true;
    },
    resetNewForm() { if (this.$refs.newForm) this.$refs.newForm.clearValidate(); },
    resetEditForm() { if (this.$refs.editForm) this.$refs.editForm.clearValidate(); },
    saveResolution() {
      this.$refs.newForm.validate(valid => {
        if (!valid) return;
        this.saving = true;
        this.$http.post(`/companies/${this.company.identification_number}/configuration/resolutions`, this.newForm, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(r => {
          if (r.data.success) { this.$message.success(r.data.message); this.showNewModal = false; location.reload(); }
          else this.$message.error(r.data.message || 'Error');
        }).catch(e => { this.$message.error('Error del servidor'); }).finally(() => { this.saving = false; });
      });
    },
    updateResolution() {
      this.saving = true;
      this.$http.put(`/companies/${this.company.identification_number}/configuration/resolutions/${this.editForm.id}`, this.editForm, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      }).then(r => {
        if (r.data.success) { this.$message.success(r.data.message); this.showEditModal = false; location.reload(); }
        else this.$message.error(r.data.message || 'Error');
      }).catch(e => { this.$message.error('Error del servidor'); }).finally(() => { this.saving = false; });
    }
  }
};
</script>
