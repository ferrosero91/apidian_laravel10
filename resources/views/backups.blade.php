@extends('layouts.app')
@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <h2>Backups</h2>
    <button class="btn btn-primary btn-sm" onclick="createBackup()">
        <i class="fas fa-plus"></i> Crear Backup
    </button>
</header>

<div class="card">
    <div class="card-body">
        @if(count($backups) > 0)
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Archivo</th>
                        <th>Tamaño</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($backups as $backup)
                        <tr>
                            <td>{{ $backup['name'] }}</td>
                            <td>{{ $backup['size'] }}</td>
                            <td>{{ $backup['date'] }}</td>
                            <td>
                                <a href="{{ route('backups.download', $backup['name']) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="deleteBackup('{{ $backup['name'] }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info mb-0">No hay backups disponibles.</div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function createBackup() {
    if (!confirm('¿Desea crear un backup de la base de datos?')) return;
    
    $.ajax({
        url: '{{ route("backups.create") }}',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                new PNotify({ text: response.message, type: 'success', delay: 3000 });
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                new PNotify({ text: response.message, type: 'error', delay: 3000 });
            }
        },
        error: function() {
            new PNotify({ text: 'Error al crear backup', type: 'error', delay: 3000 });
        }
    });
}

function deleteBackup(filename) {
    if (!confirm('¿Está seguro de eliminar este backup?')) return;
    
    $.ajax({
        url: '/backups/' + filename,
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                new PNotify({ text: response.message, type: 'success', delay: 3000 });
                setTimeout(function() { location.reload(); }, 1000);
            }
        },
        error: function() {
            new PNotify({ text: 'Error al eliminar', type: 'error', delay: 3000 });
        }
    });
}
</script>
@endpush
