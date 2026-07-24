<template>
  <div>
    <!-- Header -->
    <header class="page-header d-flex justify-content-between align-items-center">
      <div>
        <h2>Listado de Empresas</h2>
      </div>
      <div class="right-wrapper text-end mt-auto pb-1">
        <a v-if="isAdmin" href="/configuration_admin" class="btn btn-primary btn-sm text-white mr-2">
          <i class="fas fa-plus"></i> Nueva empresa
        </a>
      </div>
    </header>

    <!-- Filters -->
    <div class="card mb-3">
      <div class="card-body">
        <div class="row align-items-end">
          <div class="col-md-4">
            <label><strong>Filtrar por</strong></label>
            <el-select v-model="filterType" placeholder="Seleccionar" style="width: 100%;">
              <el-option label="NIT" value="nit"></el-option>
              <el-option label="Correo" value="email"></el-option>
              <el-option label="Nombre" value="name"></el-option>
            </el-select>
          </div>
          <div class="col-md-8">
            <label><strong>Búsqueda</strong></label>
            <el-input
              v-model="filterText"
              placeholder="Buscar empresa por NIT, nombre o correo..."
              prefix-icon="el-icon-search"
              clearable
            ></el-input>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="card">
      <div class="card-body p-0">
        <el-table
          :data="filteredCompanies"
          stripe
          style="width: 100%"
          empty-text="No hay empresas registradas"
        >
          <el-table-column prop="index" label="#" width="60" align="center"></el-table-column>
          <el-table-column label="NIT" width="150">
            <template slot-scope="scope">
              <strong>{{ scope.row.identification_number }}-{{ scope.row.dv }}</strong>
            </template>
          </el-table-column>
          <el-table-column prop="company_name" label="Empresa" min-width="180"></el-table-column>
          <el-table-column prop="email" label="Email" min-width="200"></el-table-column>
          <el-table-column label="Ambiente" width="130" align="center">
            <template slot-scope="scope">
              <el-tag :type="scope.row.type_environment_id === 1 ? 'success' : 'warning'" size="small">
                {{ scope.row.type_environment_id === 1 ? 'Producción' : 'Habilitación' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column label="Estado" width="100" align="center">
            <template slot-scope="scope">
              <el-tag :type="scope.row.state ? 'success' : 'danger'" size="small">
                {{ scope.row.state ? 'Activa' : 'Inactiva' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="total_documents" label="Docs" width="80" align="center">
            <template slot-scope="scope">
              <el-tag type="info" size="small">{{ scope.row.total_documents }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="Fecha" width="120">
            <template slot-scope="scope">
              <span style="font-size: 12px; color: #666;">
                {{ scope.row.created_at_formatted }}
              </span>
            </template>
          </el-table-column>
          <el-table-column label="Acciones" width="120" align="center" fixed="right">
            <template slot-scope="scope">
              <el-dropdown trigger="click" @command="handleCommand($event, scope.row)">
                <el-button size="mini" type="primary" plain>
                  Acciones <i class="el-icon-arrow-down el-icon--right"></i>
                </el-button>
                <el-dropdown-menu slot="dropdown">
                  <el-dropdown-item :command="'edit-' + scope.row.id">
                    <i class="fas fa-edit text-primary mr-2"></i> Editar
                  </el-dropdown-item>
                  <el-dropdown-item :command="'documents-' + scope.row.id">
                    <i class="fas fa-file-alt text-success mr-2"></i> Ver Documentos
                  </el-dropdown-item>
                  <el-dropdown-item :command="'environment-' + scope.row.id">
                    <i class="fas fa-exchange-alt text-info mr-2"></i> Cambiar Ambiente
                  </el-dropdown-item>
                  <el-dropdown-item :command="'toggle-' + scope.row.id">
                    <i :class="scope.row.state ? 'fas fa-ban text-warning' : 'fas fa-check-circle text-success'" class="mr-2"></i>
                    {{ scope.row.state ? 'Deshabilitar' : 'Habilitar' }}
                  </el-dropdown-item>
                  <el-dropdown-item divided :command="'delete-' + scope.row.id">
                    <i class="fas fa-trash text-danger mr-2"></i> Eliminar
                  </el-dropdown-item>
                </el-dropdown-menu>
              </el-dropdown>
            </template>
          </el-table-column>
        </el-table>
      </div>
      <div class="card-footer text-center">
        <span class="text-muted">Cantidad de empresas registradas: {{ companies.length }}</span>
      </div>
    </div>

    <!-- Modal Cambiar Ambiente -->
    <el-dialog title="Cambiar Ambiente" :visible.sync="showEnvironmentModal" width="400px">
      <p>Empresa: <strong>{{ selectedCompany.nit }}</strong></p>
      <el-form label-position="top">
        <el-form-item label="Ambiente">
          <el-select v-model="selectedCompany.environment" style="width: 100%;">
            <el-option label="Producción" :value="1"></el-option>
            <el-option label="Habilitación (Pruebas)" :value="2"></el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <span slot="footer" class="dialog-footer">
        <el-button @click="showEnvironmentModal = false">Cancelar</el-button>
        <el-button type="primary" @click="saveEnvironment" :loading="saving">Guardar</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
export default {
  name: 'CompaniesIndex',
  props: {
    companiesData: {
      type: Array,
      required: true
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      companies: [],
      filterType: 'nit',
      filterText: '',
      showEnvironmentModal: false,
      saving: false,
      selectedCompany: {
        id: null,
        nit: '',
        environment: 1
      }
    };
  },
  computed: {
    filteredCompanies() {
      let filtered = this.companies.map((company, index) => ({
        ...company,
        index: index + 1
      }));

      if (this.filterText) {
        const search = this.filterText.toLowerCase().trim();
        filtered = filtered.filter(company => {
          if (this.filterType === 'nit') {
            return (company.identification_number + '-' + company.dv).toLowerCase().includes(search);
          } else if (this.filterType === 'email') {
            return (company.email || '').toLowerCase().includes(search);
          } else if (this.filterType === 'name') {
            return (company.company_name || '').toLowerCase().includes(search);
          }
          return true;
        });
      }

      return filtered;
    }
  },
  created() {
    this.companies = this.companiesData.map(company => ({
      ...company,
      company_name: company.user ? company.user.name.toUpperCase() : '',
      email: company.user ? company.user.email : '',
      created_at_formatted: company.created_at
        ? new Date(company.created_at).toLocaleDateString('es-CO', { year: 'numeric', month: '2-digit', day: '2-digit' })
        : ''
    }));
  },
  methods: {
    handleCommand(command, row) {
      const [action, id] = command.split('-');
      switch (action) {
        case 'edit':
          window.location.href = `/companies/${row.identification_number}/production`;
          break;
        case 'documents':
          window.location.href = `/company/${row.identification_number}`;
          break;
        case 'environment':
          this.openChangeEnvironmentModal(row);
          break;
        case 'toggle':
          this.toggleState(row);
          break;
        case 'delete':
          this.deleteCompany(row);
          break;
      }
    },
    openChangeEnvironmentModal(row) {
      this.selectedCompany = {
        id: row.id,
        nit: row.identification_number,
        environment: row.type_environment_id
      };
      this.showEnvironmentModal = true;
    },
    saveEnvironment() {
      this.saving = true;
      this.$http.put(`/companies/${this.selectedCompany.id}/environment`, {
        type_environment_id: this.selectedCompany.environment
      }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      })
      .then(response => {
        if (response.data.success) {
          this.$message.success('Ambiente cambiado exitosamente');
          this.showEnvironmentModal = false;
          location.reload();
        } else {
          this.$message.error(response.data.message || 'Error al cambiar ambiente');
        }
      })
      .catch(() => {
        this.$message.error('Error al cambiar ambiente');
      })
      .finally(() => {
        this.saving = false;
      });
    },
    toggleState(row) {
      const action = row.state ? 'deshabilitar' : 'habilitar';
      this.$confirm(`¿Está seguro de ${action} esta empresa?`, 'Confirmar', {
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        type: 'warning'
      })
      .then(() => {
        this.$http.put(`/companies/${row.id}/toggle-state`, {
          state: !row.state
        }, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => {
          if (response.data.success) {
            this.$message.success('Estado cambiado exitosamente');
            location.reload();
          } else {
            this.$message.error(response.data.message || 'Error al cambiar estado');
          }
        })
        .catch(() => {
          this.$message.error('Error al cambiar estado de la empresa');
        });
      })
      .catch(() => {});
    },
    deleteCompany(row) {
      this.$confirm(`¿Está seguro de eliminar la empresa ${row.identification_number}? Esta acción no se puede deshacer.`, 'Eliminar Empresa', {
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        type: 'error'
      })
      .then(() => {
        this.$http.delete(`/companies/${row.id}`, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(response => {
          if (response.data.success) {
            this.$message.success('Empresa eliminada exitosamente');
            this.companies = this.companies.filter(c => c.id !== row.id);
          } else {
            this.$message.error(response.data.message || 'Error al eliminar');
          }
        })
        .catch(() => {
          this.$message.error('Error al eliminar la empresa');
        });
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
