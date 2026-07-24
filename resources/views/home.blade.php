@extends('layouts.app')
@section('content')

<style>
    .filter-card { border: 1px solid #e2e8f0; border-radius: 10px; background: #fff; }
    .filter-card .card-body { padding: 16px 20px; }
    .filter-card label { font-size: 12px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; margin-bottom: 6px; }
    .filter-card .form-control { border-radius: 6px; border-color: #e2e8f0; padding: 8px 12px; font-size: 13px; }
    .filter-card .form-control:focus { border-color: #0088CC; box-shadow: 0 0 0 3px rgba(0,136,204,0.08); }
    .btn-nueva { background: #0088CC; border: none; border-radius: 6px; padding: 8px 16px; font-weight: 600; font-size: 13px; color: #fff; transition: all 0.15s; }
    .btn-nueva:hover { background: #006fa0; color: #fff; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,136,204,0.25); }
</style>

<header class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Listado de Empresas</h2>
    </div>
    <div class="right-wrapper text-end mt-auto pb-1">
        @if(auth()->user() && method_exists(auth()->user(), 'isPlatformAdmin') && auth()->user()->isPlatformAdmin())
            <a href="{{ route('configuration_admin') }}" class="btn btn-nueva text-white mr-2">
                <i class="fas fa-plus"></i>
                Nueva empresa
            </a>
        @endif
    </div>
</header>

<div class="card filter-card mb-3">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label><strong>Filtrar por</strong></label>
                <select id="filter-type" class="form-control" clearable>
                    <option value="nit">NIT</option>
                    <option value="email">Correo</option>
                    <option value="name">Nombre</option>
                </select>
            </div>
            <div class="col-md-8">
                <label><strong>Búsqueda</strong></label>
                <input type="text" id="filter-text" class="form-control" placeholder="Buscar empresa por NIT, nombre o correo...">
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
                @foreach ($companies as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $row->identification_number }}-{{ $row->dv }}</strong></td>
                        <td>{{ strtoupper($row->user->name) }}</td>
                        <td>{{ $row->user->email }}</td>
                        <td>
                            @if($row->type_environment_id == 1)
                                <span class="badge badge-success" style="background-color: #28a745; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px;">Producción</span>
                            @else
                                <span class="badge badge-warning" style="background-color: #ffc107; color: #000; padding: 4px 10px; border-radius: 4px; font-size: 12px;">Habilitación</span>
                            @endif
                        </td>
                        <td>
                            @if($row->state)
                                <span class="badge badge-success" style="background-color: #28a745; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px;">Activa</span>
                            @else
                                <span class="badge badge-danger" style="background-color: #dc3545; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px;">Inactiva</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="badge badge-info" style="background-color: #17a2b8; color: #fff; padding: 4px 10px; border-radius: 4px; font-size: 12px;">{{ $row->total_documents }}</span>
                        </td>
                        <td style="font-size: 12px; color: #666;">
                            {{ $row->created_at ? $row->created_at->format('Y-m-d') : '' }}<br>
                            {{ $row->created_at ? $row->created_at->format('H:i') : '' }}
                        </td>
                        <td style="text-align: right;">
                            <div class="dropdown" style="display: inline-block;">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acciones
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" style="min-width: 200px;">
                                    <a class="dropdown-item" href="{{ route('company.edit', $row->identification_number) }}">
                                        <i class="fas fa-edit text-primary mr-2"></i> Editar
                                    </a>
                                    <a class="dropdown-item" href="{{ route('company', $row->identification_number) }}">
                                        <i class="fas fa-file-alt text-success mr-2"></i> Ver Documentos
                                    </a>
                                    <a class="dropdown-item" href="javascript:;" onclick="openChangeEnvironmentModal({{ $row->id }}, '{{ $row->identification_number }}', {{ $row->type_environment_id }})">
                                        <i class="fas fa-exchange-alt text-info mr-2"></i> Cambiar Ambiente
                                    </a>
                                    <a class="dropdown-item" href="javascript:;" onclick="toggleState({{ $row->id }}, {{ $row->state ? 'false' : 'true' }})">
                                        @if($row->state)
                                            <i class="fas fa-ban text-warning mr-2"></i> Deshabilitar
                                        @else
                                            <i class="fas fa-check-circle text-success mr-2"></i> Habilitar
                                        @endif
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:;" onclick="deleteCompany({{ $row->id }}, '{{ $row->identification_number }}')">
                                        <i class="fas fa-trash text-danger mr-2"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9" class="text-center" style="padding: 12px;">
                        <span class="text-muted">Cantidad de empresas registradas: {{ $companies->count() }}</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Modal Cambiar Ambiente -->
<div class="modal fade" id="changeEnvironmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cambiar Ambiente</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Empresa: <strong id="env-company-name"></strong></p>
                <input type="hidden" id="env-company-id">
                <div class="form-group">
                    <label>Ambiente</label>
                    <select id="env-type" class="form-control">
                        <option value="1">Producción</option>
                        <option value="2">Habilitación (Pruebas)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveEnvironment()">Guardar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    var filterText = document.getElementById("filter-text");
    var filterType = document.getElementById("filter-type");
    var tableRows = document.querySelectorAll("table tbody tr");

    function applyFilter() {
        var value = filterText.value.toLowerCase().trim();
        var type = filterType.value;

        tableRows.forEach(function(row) {
            var columnText = "";
            if (type === "nit") {
                columnText = row.children[1].textContent.toLowerCase();
            } else if (type === "email") {
                columnText = row.children[3].textContent.toLowerCase();
            } else if (type === "name") {
                columnText = row.children[2].textContent.toLowerCase();
            }
            row.style.display = columnText.includes(value) ? "" : "none";
        });
    }

    filterText.addEventListener("keyup", applyFilter);
    filterType.addEventListener("change", applyFilter);
});

function toggleState(companyId, newState) {
    var action = newState === 'true' ? 'habilitar' : 'deshabilitar';
    if (!confirm('¿Está seguro de ' + action + ' esta empresa?')) return;

    $.ajax({
        url: '/companies/' + companyId + '/toggle-state',
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Error al cambiar estado');
            }
        },
        error: function() {
            alert('Error al cambiar estado de la empresa');
        }
    });
}

function deleteCompany(companyId, nit) {
    if (!confirm('¿Está seguro de eliminar la empresa ' + nit + '? Esta acción no se puede deshacer.')) return;

    $.ajax({
        url: '/companies/' + companyId,
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Error al eliminar');
            }
        },
        error: function() {
            alert('Error al eliminar la empresa');
        }
    });
}

function openChangeEnvironmentModal(companyId, nit, currentEnv) {
    $('#env-company-id').val(companyId);
    $('#env-company-name').text(nit);
    $('#env-type').val(currentEnv);
    $('#changeEnvironmentModal').modal('show');
}

function saveEnvironment() {
    var companyId = $('#env-company-id').val();
    var envType = $('#env-type').val();

    $.ajax({
        url: '/companies/' + companyId + '/environment',
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        data: JSON.stringify({ type_environment_id: envType }),
        contentType: 'application/json',
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Error al cambiar ambiente');
            }
        },
        error: function() {
            alert('Error al cambiar ambiente');
        }
    });
}
</script>
@endpush
