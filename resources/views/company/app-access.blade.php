@extends('layouts.app')

@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Acceso a la App Móvil</h2>
        <span class="text-muted">{{ $company->user->name }} - {{ $company->identification_number }}</span>
    </div>
    <div>
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</header>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Configuración de Acceso</strong></div>
            <div class="card-body">
                <form id="appAccessForm">
                    @csrf
                    <div class="form-group">
                        <label>Estado del Acceso</label>
                        <select name="app_access_enabled" class="form-control">
                            <option value="1" {{ $company->app_access_enabled ? 'selected' : '' }}>Habilitado</option>
                            <option value="0" {{ !$company->app_access_enabled ? 'selected' : '' }}>Deshabilitado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Límite de Dispositivos</label>
                        <input type="number" name="app_device_limit" class="form-control" value="{{ $company->app_device_limit ?? 1 }}" min="1" max="10">
                    </div>
                    <div class="form-group">
                        <label>Token de Acceso App</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $company->app_access_token ?? 'No generado' }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-warning" type="button" onclick="generateToken()">
                                    <i class="fas fa-sync"></i> Generar
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Este token se usa para autenticar la app móvil.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Información de la App</strong></div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    <strong>App Móvil APIDIAN</strong>
                </div>
                <p>La app móvil permite a los vendedores:</p>
                <ul>
                    <li>Consultar documentos generados</li>
                    <li>Enviar eventos RADIAN</li>
                    <li>Consultar estado de documentos</li>
                    <li>Generar reportes básicos</li>
                </ul>
                <hr>
                <p><strong>Descargar App:</strong></p>
                <a href="https://facturalatam.com/apk/apidian.apk" class="btn btn-success" target="_blank">
                    <i class="fab fa-android mr-2"></i> Descargar APK
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><strong>Dispositivos Registrados</strong></div>
            <div class="card-body">
                @if($devices && $devices->count() > 0)
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Dispositivo</th>
                                <th>Último Acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devices as $device)
                                <tr>
                                    <td>{{ $device->device_name ?? 'Desconocido' }}</td>
                                    <td>{{ $device->last_access ? $device->last_access->diffForHumans() : 'Nunca' }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="removeDevice({{ $device->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No hay dispositivos registrados.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#appAccessForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '/companies/{{ $company->id }}/app-access',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                new PNotify({ text: 'Configuración guardada', type: 'success', delay: 3000 });
            } else {
                new PNotify({ text: response.message || 'Error', type: 'error', delay: 3000 });
            }
        },
        error: function() {
            new PNotify({ text: 'Error al guardar', type: 'error', delay: 3000 });
        }
    });
});

function generateToken() {
    $.ajax({
        url: '/companies/{{ $company->id }}/app-access/generate-token',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        },
        error: function() {
            new PNotify({ text: 'Error al generar token', type: 'error', delay: 3000 });
        }
    });
}

function removeDevice(deviceId) {
    if (!confirm('¿Está seguro de eliminar este dispositivo?')) return;
    
    $.ajax({
        url: '/companies/{{ $company->id }}/app-access/devices/' + deviceId,
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                location.reload();
            }
        },
        error: function() {
            new PNotify({ text: 'Error al eliminar dispositivo', type: 'error', delay: 3000 });
        }
    });
}
</script>
@endpush
