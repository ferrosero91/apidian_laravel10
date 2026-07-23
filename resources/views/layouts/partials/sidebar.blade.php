@php
$path = explode('/', request()->path());
$path[1] = (array_key_exists(1, $path)> 0)?$path[1]:'';
$path[2] = (array_key_exists(2, $path)> 0)?$path[2]:'';
$path[0] = ($path[0] === '')?'documents':$path[0];
$comp_id = $path[1];
$cust_id = $path[2];
@endphp

<style>
    .sidebar-custom {
        background: #0f172a !important;
        border-right: 1px solid #1e293b !important;
    }
    .sidebar-custom .sidebar-header {
        padding: 16px 14px !important;
        border-bottom: 1px solid #1e293b !important;
    }
    .sidebar-custom .sidebar-brand-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .sidebar-custom .sidebar-brand-icon svg {
        width: 17px;
        height: 17px;
        color: white;
    }
    .sidebar-custom .sidebar-title-text {
        color: #f1f5f9 !important;
        font-weight: 700 !important;
        font-size: 15px !important;
        letter-spacing: -0.3px;
        white-space: nowrap;
    }
    .sidebar-custom .sidebar-title-text-second {
        color: #64748b !important;
        font-size: 10px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .sidebar-custom .nav-main > li > a {
        color: #94a3b8 !important;
        padding: 9px 14px !important;
        margin: 2px 8px !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 500 !important;
        transition: all 0.15s ease !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        white-space: nowrap;
    }
    .sidebar-custom .nav-main > li > a:hover {
        color: #e2e8f0 !important;
        background: rgba(59, 130, 246, 0.08) !important;
    }
    .sidebar-custom .nav-main > li > a svg,
    .sidebar-custom .nav-main > li > a i {
        width: 18px !important;
        height: 18px !important;
        min-width: 18px;
        font-size: 15px !important;
        opacity: 0.7;
    }
    .sidebar-custom .nav-main > li.nav-active > a {
        color: #f1f5f9 !important;
        background: rgba(59, 130, 246, 0.12) !important;
        box-shadow: 3px 0 0 #3b82f6 inset !important;
    }
    .sidebar-custom .nav-main > li.nav-active > a svg,
    .sidebar-custom .nav-main > li.nav-active > a i {
        opacity: 1 !important;
        color: #3b82f6 !important;
    }
</style>

<aside id="sidebar-left" class="sidebar-left sidebar-custom">
    <div class="sidebar-header">
        <a href="{{route('home')}}" class="sidebar-title p-0 nav-link d-flex align-items-center" style="text-decoration:none; overflow:hidden;">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"/>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"/>
                    <path d="M9 17h6"/>
                    <path d="M9 13h6"/>
                </svg>
            </div>
            <div style="margin-left: 10px; overflow: hidden;">
                <div class="sidebar-title-text">APIDIAN</div>
                <div class="sidebar-title-text-second">Panel de Control</div>
            </div>
        </a>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle" style="display: none !important">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    @if(!Request::is('company*'))

                    <li class="{{ ($path[0] === 'home')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('home')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18" /><path d="M5 21v-12l5 4v-4l5 4h4" /><path d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" /><path d="M9 17h1" /><path d="M14 17h1" /></svg>
                            <span>Empresas</span>
                        </a>
                    </li>

                    <li class="{{ ($path[0] === 'tools')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('tools')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6l0 13" /><path d="M12 6l0 13" /><path d="M21 6l0 13" /></svg>
                            <span>Documentación</span>
                        </a>
                    </li>

                    @endif
                    @if(Request::is('company*'))
                        <li class="{{ Route::is('company') ? 'nav-active' : '' }}">
                            <a class="nav-link" href="{{route('company', request()->segment(2))}}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16" /><path d="M4 12h16" /><path d="M4 18h16" /></svg>
                                <span>Listado de documentos</span>
                            </a>
                        </li>
                        <li class="{{ Route::is('company.events') ? 'nav-active' : '' }}">
                            <a class="nav-link" href="{{route('company.events', request()->segment(2))}}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M17 10h2a2 2 0 0 1 2 2v1" /><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M3 13v-1a2 2 0 0 1 2 -2h2" /></svg>
                                <span>Eventos RADIAN</span>
                            </a>
                        </li>
                    @endif
                    @if(!Request::is('company*'))
                    <li class="">
                        <a href="{{route('logs')}}" class="nav-link" target="BLANK">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 9v-1a3 3 0 0 1 6 0v1" /><path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3" /><path d="M3 13l4 0" /><path d="M17 13l4 0" /><path d="M12 20l0 -6" /><path d="M4 19l3.35 -2" /><path d="M20 19l-3.35 -2" /><path d="M4 7l3.75 2.4" /><path d="M20 7l-3.75 2.4" /></svg>
                            <span>Logs</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
        <script>
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>
</aside>
