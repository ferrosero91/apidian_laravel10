<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="fixed sidebar-light no-mobile-device custom-scroll">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Facturación Electrónica</title>

    <!-- Scripts -->

    <!-- Fonts -->
    {{--<link rel="dns-prefetch" href="https://fonts.gstatic.com">--}}
    {{--<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">--}}

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="{{ asset('porto-light/vendor/bootstrap/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/animate/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/font-awesome/css/fontawesome-all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/select2/css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/select2-bootstrap-theme/select2-bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/vendor/datatables/media/css/dataTables.bootstrap4.css') }}" />

    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" />--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.26.29/sweetalert2.min.css" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/bootstrap-datepicker/css/bootstrap-datepicker3.css')}}" />

    <!-- Specific Page Vendor CSS -->
    <link rel="stylesheet" href="{{asset('porto-light/vendor/jquery-ui/jquery-ui.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/jquery-ui/jquery-ui.theme.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/select2/css/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/select2-bootstrap-theme/select2-bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{asset('porto-light/vendor/pnotify/pnotify.custom.css')}}" />

    <!-- Daterange picker plugins css -->
    <link href="{{ asset('porto-light/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('porto-light/vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('porto-light/vendor/bootstrap-timepicker/css/bootstrap-timepicker.css')}}" />

    <link rel="stylesheet" href="{{asset('porto-light/vendor/jquery-loading/dist/jquery.loading.css')}}" />

    <link rel="stylesheet" href="{{ asset('porto-light/css/theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('porto-light/css/custom.css') }}" />

    {{-- theme black --}}
    <link rel="stylesheet" href="{{ asset('porto-light/css/skins/theme-black.css')}}" />

    @if (file_exists(public_path('theme/custom_styles.css')))
        <link rel="stylesheet" href="{{ asset('theme/custom_styles.css') }}" />
    @endif


    @stack('styles')


    <script src="{{ asset('porto-light/vendor/modernizr/modernizr.js') }}"></script>

    <style>
        :root {
            --primary: #0088CC;
            --primary-dark: #006fa0;
            --sidebar-width: 230px;
            --header-height: 56px;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Sidebar */
        .sidebar-left {
            width: var(--sidebar-width) !important;
            background: #1a1f2e !important;
            border-right: none !important;
        }
        .sidebar-left .sidebar-header {
            padding: 16px 20px !important;
            background: #151925 !important;
            border-bottom: 1px solid rgba(255,255,255,0.06) !important;
        }
        .sidebar-left .sidebar-title-text {
            color: #e2e8f0 !important;
            font-weight: 700 !important;
            font-size: 16px !important;
        }
        .sidebar-left .sidebar-title-text-second {
            color: #64748b !important;
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .sidebar-left .icon-title {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #0088CC, #006fa0);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .sidebar-left .icon-title svg {
            color: white;
            width: 18px;
            height: 18px;
        }
        .sidebar-left ul.nav-main > li > a {
            color: #94a3b8 !important;
            padding: 10px 20px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            border-radius: 0 !important;
            margin: 0 !important;
            transition: all 0.15s ease !important;
            border-left: 3px solid transparent !important;
        }
        .sidebar-left ul.nav-main > li > a:hover {
            color: #e2e8f0 !important;
            background: rgba(0, 136, 204, 0.08) !important;
        }
        .sidebar-left ul.nav-main > li > a svg,
        .sidebar-left ul.nav-main > li > a i {
            width: 18px !important;
            height: 18px !important;
            margin-right: 10px !important;
            opacity: 0.6;
        }
        .sidebar-left ul.nav-main > li.nav-active > a {
            color: #fff !important;
            background: rgba(0, 136, 204, 0.12) !important;
            border-left: 3px solid #0088CC !important;
        }
        .sidebar-left ul.nav-main > li.nav-active > a svg,
        .sidebar-left ul.nav-main > li.nav-active > a i {
            opacity: 1 !important;
            color: #0088CC !important;
        }

        /* Header */
        .header {
            background: #fff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            height: var(--header-height) !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        }

        /* Content */
        html.fixed .inner-wrapper {
            padding-top: var(--header-height) !important;
        }
        html.fixed .sidebar-left {
            top: var(--header-height) !important;
        }
        .content-body {
            padding: 24px !important;
            background: #f1f5f9 !important;
        }

        /* Cards */
        .card {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04) !important;
        }
        .card-header {
            background: #fff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            font-weight: 600 !important;
        }

        /* Page header */
        .page-header {
            padding: 0 0 16px 0 !important;
            margin-bottom: 20px !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .page-header h2 {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
        }

        /* Tables */
        .table thead th {
            background: #f8fafc !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #64748b !important;
            padding: 12px 14px !important;
        }
        .table td {
            padding: 10px 14px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .table-hover tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Buttons */
        .btn-primary {
            background: #0088CC !important;
            border-color: #0088CC !important;
            border-radius: 6px !important;
            font-weight: 500 !important;
        }
        .btn-primary:hover {
            background: #006fa0 !important;
            border-color: #006fa0 !important;
        }
        .btn-secondary {
            border-radius: 6px !important;
        }

        /* Dropdown */
        .dropdown-menu {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08) !important;
            border-radius: 8px !important;
            padding: 6px 0 !important;
        }
        .dropdown-item {
            padding: 8px 16px !important;
            font-size: 13px !important;
        }
        .dropdown-item:hover {
            background-color: #f0f7ff !important;
        }

        /* Profile avatar */
        .name-initials-container {
            width: 36px !important;
            height: 36px !important;
            background: #0088CC !important;
            border: 2px solid #e2e8f0 !important;
        }
        .name-initials {
            color: white !important;
            font-weight: 600 !important;
            font-size: 13px !important;
        }

        /* Badges */
        .badge {
            padding: 4px 10px !important;
            border-radius: 4px !important;
            font-size: 11px !important;
            font-weight: 600 !important;
        }

        /* Pagination */
        .pagination .page-link {
            border-radius: 6px !important;
            margin: 0 2px !important;
            font-size: 13px !important;
        }
        .pagination .page-item.active .page-link {
            background: #0088CC !important;
            border-color: #0088CC !important;
        }

        /* Search/Filter */
        .form-control {
            border-radius: 6px !important;
            border-color: #e2e8f0 !important;
            font-size: 13px !important;
        }
        .form-control:focus {
            border-color: #0088CC !important;
            box-shadow: 0 0 0 3px rgba(0, 136, 204, 0.1) !important;
        }

        /* Logout */
        .header-right a[href*="logout"] {
            color: #64748b !important;
            font-size: 13px !important;
        }
        .header-right a[href*="logout"]:hover {
            color: #dc3545 !important;
        }

        .descarga { color: black; padding: 5px; }
        .header .logo { height: 100%; margin-top: 5px; }
        .header .logo img { height: 45px; }
        html.sidebar-light:not(.dark) ul.nav-main > li.nav-active > a { color: #0088CC; }
        .el-checkbox__label { font-size: 13px; }
        .center-el-checkbox { display: flex; align-items: center; }
        .center-el-checkbox .el-checkbox { margin-bottom: 0; }
        a:hover { color: #0088CC; }
        a:visited { color: #585858; }
        a:link { color: #0088CC; }
    </style>

        @guest
        .guest-brand {
            width: 100%;
            text-align: center;
            font-weight: 700;
            color: #0088CC;
            cursor: default;
        }

        @media only screen and (min-width: 768px) {
            html.fixed .inner-wrapper {
                padding-top: 60px !important;
            }

            html.fixed .content-body {
                margin-left: 0 !important;
            }

            html.fixed .page-header {
                left: 0 !important;
            }
        }
        @endguest
    </style>

</head>
<body class="pr-0">
    <section class="body">
        <!-- start: header -->
        @include('layouts.partials.header')
        <!-- end: header -->
        <div class="inner-wrapper">
            <!-- start: sidebar -->
            @auth
                @if(isset($is_seller) && ($is_seller == true))
                    @include('layouts.partials.sidebar-seller')
                @else
                    @if(isset($is_owner) && ($is_owner == true))
                        @include('layouts.partials.sidebar-owner')
                    @else
                        @include('layouts.partials.sidebar')
                    @endif
                @endif
            @endauth
            <!-- end: sidebar -->
            <section role="main" class="content-body" id="main-wrapper">
              @yield('content')
            </section>
        </div>
    </section>

    <!-- Vendor -->
    <script src="{{ asset('porto-light/vendor/jquery/jquery.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/jquery-browser-mobile/jquery.browser.mobile.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/jquery-cookie/jquery-cookie.js')}}"></script>
        {{--<script src="{{asset('master/style-switcher/style.switcher.js')}}"></script>--}}
    <script src="{{ asset('porto-light/vendor/popper/umd/popper.min.js')}}"></script>
    <!-- <script src="{{ asset('porto-light/vendor/bootstrap/js/bootstrap.js')}}"></script> -->
    {{-- <script src="{{ asset('porto-light/vendor/common/common.js')}}"></script> --}}
    <script src="{{ asset('porto-light/vendor/nanoscroller/nanoscroller.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/magnific-popup/jquery.magnific-popup.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/jquery-placeholder/jquery-placeholder.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/select2/js/select2.js') }}"></script>
    <script src="{{ asset('porto-light/vendor/datatables/media/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('porto-light/vendor/datatables/media/js/dataTables.bootstrap4.min.js')}}"></script>

    {{-- Specific Page Vendor --}}
    <script src="{{asset('porto-light/vendor/jquery-ui/jquery-ui.js')}}"></script>
    <script src="{{asset('porto-light/vendor/jqueryui-touch-punch/jqueryui-touch-punch.js')}}"></script>
    <!--<script src="{{asset('porto-light/vendor/select2/js/select2.js')}}"></script>-->

    <script src="{{asset('porto-light/vendor/jquery-loading/dist/jquery.loading.js')}}"></script>

    <!--<script src="assets/vendor/select2/js/select2.js"></script>-->
    {{--<script src="{{asset('porto-light/vendor/bootstrap-multiselect/bootstrap-multiselect.js')}}"></script>--}}

    <!-- Moment -->
    {{--<script src="{{ asset('porto-light/vendor/moment/moment.js') }}"></script>--}}

    <!-- DatePicker -->
    {{--<script src="{{asset('porto-light/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js')}}"></script>--}}

    <!-- Date range Plugin JavaScript -->
    {{--<script src="{{ asset('porto-light/vendor/bootstrap-timepicker/bootstrap-timepicker.js') }}"></script>--}}
    {{--<script src="{{ asset('porto-light/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>--}}

    <!-- Theme Custom -->
    <script src="{{asset('porto-light/js/custom.js')}}"></script>

    <!-- Theme Initialization Files -->
    {{-- <script src="{{asset('porto-light/js/theme.init.js')}}"></script> --}}

    {{--<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>--}}
    {{--<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>--}}

    @stack('scripts')

    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Theme Base, Components and Settings -->
    <script src="{{asset('porto-light/js/theme.js')}}"></script>
    <script>

    </script>
    <!-- <script src="//code.tidio.co/1vliqewz9v7tfosw5wxiktpkgblrws5w.js"></script> -->

    <script src="{{asset('porto-light/vendor/pnotify/pnotify.custom.js')}}"></script>
    <script>
        $('#default-primary').click(function() {
            document.getElementById("copyToken").select();
            document.execCommand('copy');
            // let input = document.getElementById("copytoken");
            // input.select();
            // document.execCommand("copy");
            new PNotify({
                // title: 'Token copiado al portapapeles',
                text: 'Token copiado al portapapeles',
                type: 'success',
                addclass: 'notification-success',
                // icon: 'fab fa-twitter'
                delay: 500
            });
        });
    </script>
</body>
</html>
