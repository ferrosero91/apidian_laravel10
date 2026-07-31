@extends('layouts.app')

@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Almacenamiento S3</h2>
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
            <div class="card-header"><strong>Configuración de Almacenamiento</strong></div>
            <div class="card-body">
                <form id="storageForm">
                    @csrf
                    <div class="form-group">
                        <label>Modo de Almacenamiento</label>
                        <select name="storage_mode" class="form-control" id="storageMode">
                            <option value="local" {{ ($company->storage_mode ?? 'local') === 'local' ? 'selected' : '' }}>Local (Servidor)</option>
                            <option value="s3" {{ ($company->storage_mode ?? 'local') === 's3' ? 'selected' : '' }}>Amazon S3</option>
                            <option value="dual" {{ ($company->storage_mode ?? 'local') === 'dual' ? 'selected' : '' }}>Dual (Local + S3)</option>
                        </select>
                        <small class="form-text text-muted">
                            <strong>Local:</strong> Archivos en el servidor<br>
                            <strong>S3:</strong> Archivos solo en Amazon S3<br>
                            <strong>Dual:</strong> Archivos en ambos (recomendado)
                        </small>
                    </div>

                    <div id="s3Config" style="display: {{ ($company->storage_mode ?? 'local') !== 'local' ? 'block' : 'none' }};">
                        <hr>
                        <h5>Configuración Amazon S3</h5>
                        <div class="form-group">
                            <label>Access Key ID</label>
                            <input type="text" name="aws_access_key_id" class="form-control" value="{{ $company->aws_access_key_id ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Secret Access Key</label>
                            <input type="password" name="aws_secret_access_key" class="form-control" value="{{ $company->aws_secret_access_key ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>Región</label>
                            <input type="text" name="aws_default_region" class="form-control" value="{{ $company->aws_default_region ?? 'us-east-1' }}">
                        </div>
                        <div class="form-group">
                            <label>Bucket</label>
                            <input type="text" name="aws_bucket" class="form-control" value="{{ $company->aws_bucket ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label>URL Base (opcional)</label>
                            <input type="text" name="aws_url" class="form-control" value="{{ $company->aws_url ?? '' }}">
                            <small class="form-text text-muted">Dejar vacío para usar la URL por defecto de S3</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-save"></i> Guardar Configuración
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Estado del Almacenamiento</strong></div>
            <div class="card-body">
                <div class="alert {{ ($company->storage_mode ?? 'local') === 'local' ? 'alert-info' : 'alert-success' }}">
                    <i class="fas fa-hdd mr-2"></i>
                    <strong>Modo Actual:</strong> 
                    @switch($company->storage_mode ?? 'local')
                        @case('local')
                            Local (Servidor)
                            @break
                        @case('s3')
                            Amazon S3
                            @break
                        @case('dual')
                            Dual (Local + S3)
                            @break
                    @endswitch
                </div>

                <h5 class="mt-4">Información del Bucket</h5>
                @if(($company->storage_mode ?? 'local') !== 'local')
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Bucket:</strong></td>
                            <td>{{ $company->aws_bucket ?? 'No configurado' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Región:</strong></td>
                            <td>{{ $company->aws_default_region ?? 'No configurada' }}</td>
                        </tr>
                    </table>

                    <button class="btn btn-info btn-sm" onclick="testConnection()">
                        <i class="fas fa-plug mr-2"></i> Probar Conexión
                    </button>
                    <div id="connectionResult" class="mt-2"></div>
                @else
                    <p class="text-muted">Configure S3 para ver la información del bucket.</p>
                @endif

                <hr>
                <h5>Uso de Almacenamiento</h5>
                <div class="progress mb-2">
                    <div class="progress-bar" role="progressbar" style="width: {{ $usagePercentage ?? 0 }}%">
                        {{ $usagePercentage ?? 0 }}%
                    </div>
                </div>
                <small class="text-muted">{{ $usedSpace ?? '0 MB' }} usados de {{ $totalSpace ?? '1 GB' }}</small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$('#storageMode').on('change', function() {
    if ($(this).val() === 'local') {
        $('#s3Config').hide();
    } else {
        $('#s3Config').show();
    }
});

$('#storageForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '/companies/{{ $company->id }}/storage',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                new PNotify({ text: 'Configuración guardada', type: 'success', delay: 3000 });
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                new PNotify({ text: response.message || 'Error', type: 'error', delay: 3000 });
            }
        },
        error: function(xhr) {
            new PNotify({ text: xhr.responseJSON?.message || 'Error al guardar', type: 'error', delay: 3000 });
        }
    });
});

function testConnection() {
    $('#connectionResult').html('<i class="fas fa-spinner fa-spin"></i> Probando conexión...');
    
    $.ajax({
        url: '/companies/{{ $company->id }}/storage/test',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                $('#connectionResult').html('<div class="alert alert-success py-2"><i class="fas fa-check"></i> Conexión exitosa</div>');
            } else {
                $('#connectionResult').html('<div class="alert alert-danger py-2"><i class="fas fa-times"></i> ' + response.message + '</div>');
            }
        },
        error: function() {
            $('#connectionResult').html('<div class="alert alert-danger py-2"><i class="fas fa-times"></i> Error de conexión</div>');
        }
    });
}
</script>
@endpush
