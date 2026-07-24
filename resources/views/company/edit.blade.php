@extends('layouts.app')

@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>{{ $company->user->name }} — {{ $company->identification_number }}-{{ $company->dv }}</h2>
    </div>
    <div>
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</header>

<ul class="nav nav-tabs" id="companyEditTabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">
            <i class="fas fa-building mr-1"></i> Empresa
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="cert-tab" data-toggle="tab" href="#certificado" role="tab">
            <i class="fas fa-shield-alt mr-1"></i> Certificado
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="software-tab" data-toggle="tab" href="#software" role="tab">
            <i class="fas fa-server mr-1"></i> Software
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="resolution-tab" data-toggle="tab" href="#resoluciones" role="tab">
            <i class="fas fa-file-alt mr-1"></i> Resoluciones
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="email-tab" data-toggle="tab" href="#correo" role="tab">
            <i class="fas fa-envelope mr-1"></i> Correo
        </a>
    </li>
</ul>

<div class="tab-content" id="companyEditContent" style="border: 1px solid #dee2e6; border-top: none; padding: 20px; background: #fff;">

    <!-- TAB: Información de la Empresa -->
    <div class="tab-pane fade show active" id="info" role="tabpanel">
        <form id="editCompanyForm">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo de Documento</label>
                        <select name="type_document_identification_id" class="form-control" required>
                            @foreach ($type_document_identifications as $type)
                                <option value="{{ $type->id }}" {{ $company->type_document_identification_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Número de Documento</label>
                        <input type="text" name="identification_number" class="form-control" value="{{ $company->identification_number }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>DV</label>
                        <input type="text" name="dv" class="form-control" value="{{ $company->dv }}" required readonly style="background-color: #f8f9fa;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nombre / Razón Social</label>
                        <input type="text" name="name" class="form-control" value="{{ $company->user->name }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" value="{{ $company->user->email }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="phone" class="form-control" value="{{ $company->phone }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Matrícula Mercantil</label>
                        <input type="text" name="merchant_registration" class="form-control" value="{{ $company->merchant_registration }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Ambiente</label>
                        <select name="type_environment_id" class="form-control">
                            <option value="1" {{ $company->type_environment_id == 1 ? 'selected' : '' }}>Producción</option>
                            <option value="2" {{ $company->type_environment_id == 2 ? 'selected' : '' }}>Habilitación (Pruebas)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Dirección</label>
                        <input type="text" name="address" class="form-control" value="{{ $company->address }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo de Régimen</label>
                        <select name="type_regime_id" class="form-control">
                            @foreach ($type_regimes as $regime)
                                <option value="{{ $regime->id }}" {{ $company->type_regime_id == $regime->id ? 'selected' : '' }}>{{ $regime->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo de Responsabilidad</label>
                        <select name="type_liability_id" class="form-control">
                            @foreach ($type_liabilities as $liability)
                                <option value="{{ $liability->id }}" {{ $company->type_liability_id == $liability->id ? 'selected' : '' }}>{{ $liability->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Municipio</label>
                        <select name="municipality_id" class="form-control">
                            @foreach ($municipalities as $municipality)
                                <option value="{{ $municipality->id }}" {{ $company->municipality_id == $municipality->id ? 'selected' : '' }}>{{ $municipality->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right mt-3">
                <button type="submit" class="btn btn-primary" id="saveCompanyBtn">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <!-- TAB: Certificado -->
    <div class="tab-pane fade" id="certificado" role="tabpanel">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header"><strong>Certificado Actual</strong></div>
                    <div class="card-body">
                        @if($company->certificate)
                            <p><i class="fas fa-check-circle text-success"></i> <strong>{{ $company->certificate->name }}</strong></p>
                            @if($company->certificate->expiration_date)
                                <p>Vence: <strong>{{ \Carbon\Carbon::parse($company->certificate->expiration_date)->format('d/m/Y') }}</strong></p>
                            @endif
                        @else
                            <p class="text-muted"><i class="fas fa-exclamation-triangle text-warning"></i> No hay certificado cargado</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header"><strong>Actualizar Certificado</strong></div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Contraseña del certificado</label>
                            <input type="password" id="cert-password" class="form-control" placeholder="Contraseña del .pfx/.p12">
                        </div>
                        <div class="form-group">
                            <label>Archivo (.pfx / .p12)</label>
                            <input type="file" id="cert-file" class="form-control-file" accept=".pfx,.p12">
                        </div>
                        <button type="button" class="btn btn-warning" id="saveCertBtn">
                            <i class="fas fa-upload"></i> Actualizar Certificado
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: Software -->
    <div class="tab-pane fade" id="software" role="tabpanel">
        <div class="card">
            <div class="card-header"><strong>Configuración de Software</strong></div>
            <div class="card-body">
                @if($company->software)
                    <div class="row">
                        <div class="col-md-4">
                            <label>Software ID (Invoice)</label>
                            <input type="text" class="form-control" value="{{ $company->software->identifier }}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-4">
                            <label>PIN (Invoice)</label>
                            <input type="text" class="form-control" value="{{ $company->software->pin }}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-4">
                            <label>URL Test</label>
                            <input type="text" class="form-control" value="{{ $company->software->url_test }}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label>URL Production</label>
                            <input type="text" class="form-control" value="{{ $company->software->url_production }}" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>
                @else
                    <p class="text-muted">No hay software configurado para esta empresa.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB: Resoluciones -->
    <div class="tab-pane fade" id="resoluciones" role="tabpanel">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Resoluciones</strong>
                <a href="{{ route('company.resolutions.index', $company->identification_number) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Gestionar Resoluciones
                </a>
            </div>
            <div class="card-body">
                @if($company->resolutions && $company->resolutions->count() > 0)
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Prefijo</th>
                                <th>Desde</th>
                                <th>Hasta</th>
                                <th>Vigencia</th>
                                <th>Ambiente</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->resolutions as $resolution)
                                <tr>
                                    <td>{{ $resolution->prefix }}</td>
                                    <td>{{ $resolution->from }}</td>
                                    <td>{{ $resolution->to }}</td>
                                    <td>{{ $resolution->date_from }} - {{ $resolution->date_to }}</td>
                                    <td>{{ $resolution->type_environment_id == 1 ? 'Producción' : 'Habilitación' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">No hay resoluciones registradas.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB: Correo -->
    <div class="tab-pane fade" id="correo" role="tabpanel">
        <div class="card">
            <div class="card-header"><strong>Configuración de Correo Electrónico</strong></div>
            <div class="card-body">
                <form id="emailConfigForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Host SMTP</label>
                                <input type="text" name="mail_host" class="form-control" value="{{ $company->user->mail_host ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Puerto</label>
                                <input type="text" name="mail_port" class="form-control" value="{{ $company->user->mail_port ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Usuario</label>
                                <input type="text" name="mail_username" class="form-control" value="{{ $company->user->mail_username ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contraseña</label>
                                <input type="password" name="mail_password" class="form-control" value="{{ $company->user->mail_password ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cifrado</label>
                                <select name="mail_encryption" class="form-control">
                                    <option value="tls" {{ ($company->user->mail_encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($company->user->mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary" id="saveEmailBtn">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    var companyId = {{ $company->id }};
    var apiToken = '{{ $company->user->api_token }}';

    // Guardar información de empresa
    $('#editCompanyForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#saveCompanyBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: '/companies/' + companyId,
            method: 'POST',
            data: $(this).serialize() + '&_method=PUT',
            success: function(response) {
                if (response.success) {
                    new PNotify({ text: response.message, type: 'success', addclass: 'notification-success', delay: 3000 });
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    new PNotify({ text: response.message || 'Error', type: 'error', addclass: 'notification-danger', delay: 5000 });
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON?.message || 'Error al guardar';
                new PNotify({ text: msg, type: 'error', addclass: 'notification-danger', delay: 5000 });
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Cambios');
            }
        });
    });

    // Guardar certificado
    $('#saveCertBtn').on('click', function() {
        var file = $('#cert-file')[0].files[0];
        var password = $('#cert-password').val().trim();
        if (!file) { new PNotify({ text: 'Selecciona el archivo', type: 'error', delay: 3000 }); return; }
        if (!password) { new PNotify({ text: 'Ingresa la contraseña', type: 'error', delay: 3000 }); return; }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');

        var reader = new FileReader();
        reader.onload = function(e) {
            $.ajax({
                url: '/api/ubl2.1/config/certificate',
                method: 'PUT',
                headers: { 'Authorization': 'Bearer ' + apiToken, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                data: JSON.stringify({ certificate: e.target.result.split(',')[1], password: password }),
                success: function(r) {
                    new PNotify({ text: r.message || 'Certificado actualizado', type: 'success', delay: 3000 });
                    setTimeout(function() { location.reload(); }, 1500);
                },
                error: function(xhr) {
                    new PNotify({ text: xhr.responseJSON?.message || 'Error', type: 'error', delay: 5000 });
                },
                complete: function() { btn.prop('disabled', false).html('<i class="fas fa-upload"></i> Actualizar Certificado'); }
            });
        };
        reader.readAsDataURL(file);
    });

    // Guardar configuración de correo
    $('#saveEmailBtn').on('click', function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Guardando...');

        $.ajax({
            url: '/api/ubl2.1/emailconfig',
            method: 'GET',
            headers: { 'Authorization': 'Bearer ' + apiToken, 'Accept': 'application/json' },
            success: function() {
                // First get current config, then update
                var formData = $('#emailConfigForm').serialize();
                $.ajax({
                    url: '/companies/' + companyId + '/configuration/email',
                    method: 'POST',
                    data: formData,
                    success: function(r) {
                        new PNotify({ text: r.message || 'Correo configurado', type: 'success', delay: 3000 });
                    },
                    error: function(xhr) {
                        new PNotify({ text: xhr.responseJSON?.message || 'Error', type: 'error', delay: 5000 });
                    },
                    complete: function() { btn.prop('disabled', false).html('<i class="fas fa-save"></i> Guardar Configuración'); }
                });
            }
        });
    });
});
</script>
@endpush
