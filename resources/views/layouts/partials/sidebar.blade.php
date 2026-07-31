@php
$path = explode('/', request()->path());
$path[1] = (array_key_exists(1, $path)> 0)?$path[1]:'';
$path[2] = (array_key_exists(2, $path)> 0)?$path[2]:'';
$path[0] = ($path[0] === '')?'documents':$path[0];
$comp_id = $path[1];
$cust_id = $path[2];
@endphp

<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <a href="{{route('home')}}" class="sidebar-title p-0 nav-link d-flex align-items-center">
            <div class="icon-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-letter-a-small"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 16v-6a2 2 0 1 1 4 0v6" /><path d="M10 13h4" /></svg>
            </div>            
            <span class="sidebar-title-text-container">
                <span class="sidebar-title-text">APIDIAN</span>
                <br>
                <span class="text-muted sidebar-title-text-second">Menu</span>
            </span>
        </a>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    @if(!Request::is('company*'))

                    <li class="{{ ($path[0] === 'home')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('home')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-factory-2 mr-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21h18" /><path d="M5 21v-12l5 4v-4l5 4h4" /><path d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" /><path d="M9 17h1" /><path d="M14 17h1" /></svg>
                            <span>Empresas</span>
                        </a>
                    </li>

                    <li class="{{ ($path[0] === 'tools')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('tools')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-book mr-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 19a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6a9 9 0 0 1 9 0a9 9 0 0 1 9 0" /><path d="M3 6l0 13" /><path d="M12 6l0 13" /><path d="M21 6l0 13" /></svg>
                            <span>Documentación</span>
                        </a>
                    </li>

                    <li class="{{ ($path[0] === 'monitoring')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('monitoring')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-dashboard mr-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 13m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M13.45 11.55l2.05 -2.05" /><path d="M6.4 20a9 9 0 1 1 12.8 0" /></svg>
                            <span>Monitoreo</span>
                        </a>
                    </li>

                    <li class="{{ ($path[0] === 'backups')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('backups')}}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-database mr-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0" /><path d="M4 6v6a8 3 0 0 0 16 0v-6" /><path d="M4 12v6a8 3 0 0 0 16 0v-6" /></svg>
                            <span>Backups</span>
                        </a>
                    </li>

                    <li class="{{ ($path[0] === 'logs')?'nav-active':'' }}">
                        <a class="nav-link" href="{{route('logs')}}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-bug mr-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 9v-1a3 3 0 0 1 6 0v1" /><path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3" /><path d="M3 13l4 0" /><path d="M17 13l4 0" /><path d="M12 20l0 -6" /><path d="M4 19l3.35 -2" /><path d="M20 19l-3.35 -2" /><path d="M4 7l3.75 2.4" /><path d="M20 7l-3.75 2.4" /></svg>
                            <span>Logs</span>
                        </a>
                    </li>

                    @endif
                    @if(Request::is('company*'))
                        <li class="{{ Route::is('company') ? 'nav-active' : '' }}">
                            <a class="nav-link" href="{{route('company', request()->segment(2))}}">
                                <i class="fas fa-list-alt" aria-hidden="true"></i>
                                <span>Listado de documentos</span>
                            </a>
                        </li>
                        <li class="{{ Route::is('company.events') ? 'nav-active' : '' }}">
                            <a class="nav-link" href="{{route('company.events', request()->segment(2))}}">
                                <i class="fas fa-calendar-check" aria-hidden="true"></i>
                                <span>Eventos RADIAN</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <script>
            // Maintain Scroll Position
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
