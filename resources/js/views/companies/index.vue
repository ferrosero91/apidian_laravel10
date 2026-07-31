<template>
  <div>
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

    <div class="card mb-3">
      <div class="card-body">
        <div class="row align-items-end">
          <div class="col-md-4">
            <label><strong>Filtrar por</strong></label>
            <select v-model="filterType" class="form-control">
              <option value="nit">NIT</option>
              <option value="email">Correo</option>
              <option value="name">Nombre</option>
            </select>
          </div>
          <div class="col-md-8">
            <label><strong>Búsqueda</strong></label>
            <input type="text" v-model="filterText" class="form-control" placeholder="Buscar empresa por NIT, nombre o correo...">
          </div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="thead-light">
            <tr>
              <th style="width: 50px;">#</th>
              <th>NIT</th>
              <th>Empresa</th>
              <th>Email</th>
              <th>Ambiente</th>
              <th>Estado</th>
              <th style="text-align: center;">Docs</th>
              <th>Fecha</th>
              <th style="text-align: right;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, index) in filteredCompanies" :key="row.id">
              <td>{{ index + 1 }}</td>
              <td><strong>{{ row.identification_number }}-{{ row.dv }}</strong></td>
              <td>{{ row.company_name }}</td>
              <td>{{ row.email }}</td>
              <td>
                <span :class="row.type_environment_id === 1 ? 'badge badge-success' : 'badge badge-warning'" style="padding: 4px 10px; border-radius: 4px; font-size: 12px;">
                  {{ row.type_environment_id === 1 ? 'Producción' : 'Habilitación' }}
                </span>
              </td>
              <td>
                <span :class="row.state ? 'badge badge-success' : 'badge badge-danger'" style="padding: 4px 10px; border-radius: 4px; font-size: 12px;">
                  {{ row.state ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td style="text-align: center;">
                <span class="badge badge-info" style="padding: 4px 10px; border-radius: 4px; font-size: 12px;">{{ row.total_documents }}</span>
              </td>
              <td style="font-size: 12px; color: #666;">
                {{ row.created_at_formatted }}
              </td>
              <td style="text-align: right;">
                <div class="dropdown" style="display: inline-block;">
                  <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                    Acciones
                  </button>
                  <div class="dropdown-menu dropdown-menu-right" style="min-width: 200px;">
                    <a class="dropdown-item" :href="'/companies/' + row.identification_number + '/production'">
                      <i class="fas fa-edit text-primary mr-2"></i> Editar
                    </a>
                    <a class="dropdown-item" :href="'/company/' + row.identification_number">
                      <i class="fas fa-file-alt text-success mr-2"></i> Ver Documentos
                    </a>
                    <a class="dropdown-item" href="javascript:;" @click="openChangeEnvironmentModal(row)">
                      <i class="fas fa-exchange-alt text-info mr-2"></i> Cambiar Ambiente
                    </a>
                    <a class="dropdown-item" href="javascript:;" @click="toggleState(row)">
                      <i :class="row.state ? 'fas fa-ban text-warning' : 'fas fa-check-circle text-success'" class="mr-2"></i>
                      {{ row.state ? 'Deshabilitar' : 'Habilitar' }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="javascript:;" @click="deleteCompany(row)">
                      <i class="fas fa-trash text-danger mr-2"></i> Eliminar
                    </a>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="9" class="text-center" style="padding: 12px;">
                <span class="text-muted">Cantidad de empresas registradas: {{ companies.length }}</span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div class="modal fade" id="changeEnvironmentModal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Cambiar Ambiente</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <p>Empresa: <strong>{{ selectedCompany.nit }}</strong></p>
            <input type="hidden" id="env-company-id" :value="selectedCompany.id">
            <div class="form-group">
              <label>Ambiente</label>
              <select v-model="selectedCompany.environment" class="form-control">
                <option :value="1">Producción</option>
                <option :value="2">Habilitación (Pruebas)</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="saveEnvironment">Guardar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CompaniesIndex',
  props: {
    companiesData: {
      type: Array,
      required: true,
      default: function() { return []; }
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  data: function() {
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
    filteredCompanies: function() {
      var self = this;
      var filtered = self.companies.map(function(company, index) {
        return Object.assign({}, company, { index: index + 1 });
      });

      if (self.filterText) {
        var search = self.filterText.toLowerCase().trim();
        filtered = filtered.filter(function(company) {
          if (self.filterType === 'nit') {
            return (company.identification_number + '-' + company.dv).toLowerCase().indexOf(search) !== -1;
          } else if (self.filterType === 'email') {
            return (company.email || '').toLowerCase().indexOf(search) !== -1;
          } else if (self.filterType === 'name') {
            return (company.company_name || '').toLowerCase().indexOf(search) !== -1;
          }
          return true;
        });
      }

      return filtered;
    }
  },
  created: function() {
    var self = this;
    self.companies = self.companiesData.map(function(company) {
      return Object.assign({}, company, {
        company_name: company.user ? company.user.name.toUpperCase() : '',
        email: company.user ? company.user.email : '',
        created_at_formatted: company.created_at
          ? new Date(company.created_at).toLocaleDateString('es-CO', { year: 'numeric', month: '2-digit', day: '2-digit' })
          : ''
      });
    });
  },
  methods: {
    openChangeEnvironmentModal: function(row) {
      this.selectedCompany = {
        id: row.id,
        nit: row.identification_number,
        environment: row.type_environment_id
      };
      window.jQuery('#changeEnvironmentModal').modal('show');
    },
    saveEnvironment: function() {
      var self = this;
      self.saving = true;
      self.$http.put('/companies/' + self.selectedCompany.id + '/environment', {
        type_environment_id: self.selectedCompany.environment
      }, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
      })
      .then(function(response) {
        if (response.data.success) {
          self.$message.success('Ambiente cambiado exitosamente');
          window.jQuery('#changeEnvironmentModal').modal('hide');
          location.reload();
        } else {
          self.$message.error(response.data.message || 'Error al cambiar ambiente');
        }
      })
      .catch(function() {
        self.$message.error('Error al cambiar ambiente');
      })
      .finally(function() {
        self.saving = false;
      });
    },
    toggleState: function(row) {
      var self = this;
      var action = row.state ? 'deshabilitar' : 'habilitar';
      self.$confirm('¿Está seguro de ' + action + ' esta empresa?', 'Confirmar', {
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
        type: 'warning'
      })
      .then(function() {
        self.$http.put('/companies/' + row.id + '/toggle-state', {
          state: !row.state
        }, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(function(response) {
          if (response.data.success) {
            self.$message.success('Estado cambiado exitosamente');
            location.reload();
          } else {
            self.$message.error(response.data.message || 'Error al cambiar estado');
          }
        })
        .catch(function() {
          self.$message.error('Error al cambiar estado de la empresa');
        });
      })
      .catch(function() {});
    },
    deleteCompany: function(row) {
      var self = this;
      self.$confirm('¿Está seguro de eliminar la empresa ' + row.identification_number + '? Esta acción no se puede deshacer.', 'Eliminar Empresa', {
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        type: 'error'
      })
      .then(function() {
        self.$http.delete('/companies/' + row.id, {
          headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(function(response) {
          if (response.data.success) {
            self.$message.success('Empresa eliminada exitosamente');
            self.companies = self.companies.filter(function(c) { return c.id !== row.id; });
          } else {
            self.$message.error(response.data.message || 'Error al eliminar');
          }
        })
        .catch(function() {
          self.$message.error('Error al eliminar la empresa');
        });
      })
      .catch(function() {});
    }
  }
};
</script>
