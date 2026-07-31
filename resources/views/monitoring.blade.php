@extends('layouts.app')
@section('content')
<header class="page-header">
    <h2>Monitoreo del Sistema</h2>
</header>

<div class="row">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['total_companies'] }}</h3>
                <p class="mb-0">Empresas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['total_documents'] }}</h3>
                <p class="mb-0">Documentos</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['total_users'] }}</h3>
                <p class="mb-0">Usuarios</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <h3>{{ $stats['documents_today'] }}</h3>
                <p class="mb-0">Docs Hoy</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Información del Servidor</strong></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Hora del servidor:</strong></td><td>{{ $stats['server_time'] }}</td></tr>
                    <tr><td><strong>PHP:</strong></td><td>{{ $stats['php_version'] }}</td></tr>
                    <tr><td><strong>Laravel:</strong></td><td>{{ $stats['laravel_version'] }}</td></tr>
                    <tr><td><strong>Base de datos:</strong></td><td>{{ $stats['database_size'] }}</td></tr>
                    <tr><td><strong>Almacenamiento:</strong></td><td>{{ $stats['storage_usage'] }}</td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><strong>Actividad del Mes</strong></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td><strong>Documentos este mes:</strong></td><td>{{ $stats['documents_this_month'] }}</td></tr>
                    <tr><td><strong>Promedio diario:</strong></td><td>{{ round($stats['documents_this_month'] / max(date('d'), 1), 1) }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
