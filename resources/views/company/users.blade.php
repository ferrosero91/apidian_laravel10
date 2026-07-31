@extends('layouts.app')

@section('content')
<header class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h2>Gestión de Usuarios</h2>
        <span class="text-muted">{{ $company->user->name }} - {{ $company->identification_number }}</span>
    </div>
    <div>
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</header>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>Usuario Principal</strong></div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $company->user->name }}</p>
                <p><strong>Email:</strong> {{ $company->user->email }}</p>
                <p><strong>Token API:</strong></p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-sm" value="{{ $company->user->api_token }}" readonly id="apiToken">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="copyToken()">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Usuarios Adicionales</strong>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUserModal">
                    <i class="fas fa-plus"></i> Agregar Usuario
                </button>
            </div>
            <div class="card-body">
                @if($company->users && $company->users->count() > 0)
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($company->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge badge-info">{{ $user->pivot->role ?? 'usuario' }}</span></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" onclick="removeUser({{ $user->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted mb-0">No hay usuarios adicionales configurados.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Usuario -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="role" class="form-control">
                            <option value="user">Usuario</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agregar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyToken() {
    var tokenInput = document.getElementById('apiToken');
    tokenInput.select();
    document.execCommand('copy');
    new PNotify({ text: 'Token copiado al portapapeles', type: 'success', delay: 2000 });
}

function removeUser(userId) {
    if (!confirm('¿Está seguro de eliminar este usuario?')) return;
    
    $.ajax({
        url: '/companies/{{ $company->id }}/users/' + userId,
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Error al eliminar usuario');
            }
        },
        error: function() {
            alert('Error al eliminar usuario');
        }
    });
}

$('#addUserForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: '/companies/{{ $company->id }}/users',
        method: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.message || 'Error al agregar usuario');
            }
        },
        error: function(xhr) {
            alert(xhr.responseJSON?.message || 'Error al agregar usuario');
        }
    });
});
</script>
@endpush
