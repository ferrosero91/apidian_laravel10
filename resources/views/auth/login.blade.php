@extends('layouts.guest')
@section('content')
<style>
    body {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .login-wrapper {
        width: 100%;
        max-width: 420px;
        padding: 20px;
    }
    .login-brand {
        text-align: center;
        margin-bottom: 32px;
    }
    .login-brand-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
    }
    .login-brand-icon svg {
        width: 28px;
        height: 28px;
        color: white;
    }
    .login-brand h1 {
        color: #f8fafc;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.5px;
    }
    .login-brand p {
        color: #94a3b8;
        font-size: 14px;
        margin: 6px 0 0 0;
    }
    .login-card {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }
    .login-card h2 {
        color: #f1f5f9;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .login-card .subtitle {
        color: #94a3b8;
        font-size: 13px;
        margin-bottom: 24px;
    }
    .login-card label {
        color: #cbd5e1;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
    }
    .login-card .form-control {
        background: #0f172a;
        border: 1px solid #334155;
        border-radius: 10px;
        color: #f1f5f9;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.2s;
    }
    .login-card .form-control:focus {
        background: #0f172a;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        color: #f1f5f9;
    }
    .login-card .form-control::placeholder {
        color: #64748b;
    }
    .login-card .form-group {
        margin-bottom: 18px;
    }
    .btn-login {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        border-radius: 10px;
        padding: 11px 24px;
        font-weight: 600;
        font-size: 14px;
        width: 100%;
        color: white;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-login:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.4);
        color: white;
    }
    .btn-login:active {
        transform: translateY(0);
    }
    .login-footer {
        text-align: center;
        margin-top: 24px;
        color: #64748b;
        font-size: 12px;
    }
    .remember-check {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #94a3b8;
        font-size: 13px;
    }
    .remember-check input[type="checkbox"] {
        accent-color: #3b82f6;
        width: 16px;
        height: 16px;
    }
    .error-text {
        color: #f87171;
        font-size: 12px;
        margin-top: 4px;
    }
</style>

<div class="login-wrapper">
    <div class="login-brand">
        <div class="login-brand-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                <path d="M9 17h6"/>
                <path d="M9 13h6"/>
            </svg>
        </div>
        <h1>APIDIAN</h1>
        <p>Facturación Electrónica DIAN</p>
    </div>

    <div class="login-card">
        <h2>Iniciar Sesión</h2>
        <p class="subtitle">Ingresa tus credenciales para acceder</p>

        <form method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                       name="email" placeholder="tu@correo.com" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                    <span class="error-text">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                       name="password" placeholder="••••••••" required>
                @if ($errors->has('password'))
                    <span class="error-text">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="remember-check">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Recordarme
                </label>
            </div>

            <button type="submit" class="btn btn-login">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2"/>
                    <path d="M3 12h13l-3 -3"/>
                    <path d="M13 15l3 -3"/>
                </svg>
                Iniciar Sesión
            </button>
        </form>
    </div>

    <div class="login-footer">
        <p>Facturación Electrónica para la DIAN — Colombia</p>
    </div>
</div>
@endsection
