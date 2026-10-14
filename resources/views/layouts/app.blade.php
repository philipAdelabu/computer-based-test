<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="theme-color" content="#4e73df">
    <title>@yield('title', 'CBT System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --dark-color: #2d3748;
            --light-bg: #f8f9fc;
            --sidebar-width: 240px;
            --navbar-height: 60px;
        }
        
        * { box-sizing: border-box; }
        
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            font-size: 14px;
            padding-top: var(--navbar-height);
        }
        
        /* ============ NAVBAR ============ */
        .navbar {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: var(--navbar-height);
            padding: 0 1rem;
        }
        .navbar-brand {
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .navbar-brand img {
            max-height: 32px;
            max-width: 32px;
            object-fit: contain;
        }
        .navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-size: 0.9rem;
        }
        
        /* ============ SIDEBAR ============ */
        .sidebar {
            width: var(--sidebar-width);
            min-height: calc(100vh - var(--navbar-height));
            background: var(--dark-color);
            position: fixed;
            top: var(--navbar-height);
            left: 0;
            bottom: 0;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1020;
            transition: transform 0.3s ease;
            padding-top: 0.5rem;
            padding-bottom: 2rem;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.75rem 1.25rem;
            margin: 0.15rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.12);
        }
        .sidebar .nav-link i {
            width: 1.4rem;
            text-align: center;
            font-size: 1.05rem;
        }
        
        /* Sidebar backdrop (mobile) */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: var(--navbar-height) 0 0 0;
            background: rgba(0,0,0,0.5);
            z-index: 1015;
        }
        .sidebar-backdrop.show {
            display: block;
        }
        
        /* Sidebar toggle button (mobile only) */
        .sidebar-toggle {
            display: none;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.35rem 0.6rem;
            border-radius: 0.375rem;
            font-size: 1.2rem;
        }
        
        /* ============ MAIN CONTENT ============ */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            min-height: calc(100vh - var(--navbar-height));
            max-width: calc(100vw - var(--sidebar-width));
            transition: margin 0.3s ease;
        }
        
        /* ============ CARDS ============ */
        .card {
            border: none;
            border-radius: 0.85rem;
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
        }
        
        /* Stat cards */
        .stat-card {
            border-left: 4px solid var(--primary-color);
            background: white;
            padding: 1.1rem;
            border-radius: 0.75rem;
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.06);
            height: 100%;
        }
        .stat-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .stat-card h6 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: 0.65;
        }
        
        /* ============ BUTTONS ============ */
        .btn {
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.4rem 0.8rem rgba(78, 115, 223, 0.35);
        }
        .btn-lg { padding: 0.7rem 1.75rem; font-size: 0.95rem; }
        .btn-sm { padding: 0.3rem 0.65rem; font-size: 0.78rem; }
        
        /* ============ TABLES ============ */
        .table {
            background: white;
            margin-bottom: 0;
        }
        .table thead th {
            background: var(--dark-color);
            color: white;
            font-weight: 500;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.75rem;
            border: none;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }
        
        /* ============ PAGE HEADER ============ */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e3e6f0;
        }
        .page-header h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }
        
        /* ============ FORMS ============ */
        .form-control, .form-select {
            border-radius: 0.5rem;
            padding: 0.55rem 0.85rem;
            font-size: 0.9rem;
            border: 1.5px solid #e3e6f0;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.15);
        }
        
        /* ============ EXAM CARDS ============ */
        .exam-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.1rem;
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.06);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .exam-card .exam-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.4rem;
        }
        .exam-card .exam-meta {
            color: #6c757d;
            font-size: 0.85rem;
            line-height: 1.7;
            flex: 1;
        }
        
        /* ============ MOBILE BREAKPOINTS ============ */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                max-width: 100vw;
                padding: 1rem;
            }
            .sidebar-toggle {
                display: inline-block;
            }
        }
        
        @media (max-width: 767.98px) {
            body { font-size: 13.5px; }
            .main-content { padding: 0.85rem; }
            
            .page-header h1 { font-size: 1.25rem; }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .page-header .btn {
                width: 100%;
            }
            
            .stat-card {
                padding: 0.9rem;
            }
            .stat-card h3 { font-size: 1.35rem; }
            .stat-card .stat-icon { font-size: 1.7rem; }
            
            .card-body { padding: 1rem; }
            .card-header { padding: 0.85rem 1rem; }
            
            /* Responsive tables — convert to cards */
            .table-responsive-mobile {
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            /* Stack actions */
            .d-flex.gap-2.mobile-stack {
                flex-direction: column;
            }
            .d-flex.gap-2.mobile-stack > * {
                width: 100%;
            }
            
            /* Hide non-essential table columns on mobile */
            .table .hide-mobile {
                display: none;
            }
        }
        
        @media (max-width: 575.98px) {
            .main-content { padding: 0.65rem; }
            body { font-size: 13px; }
            
            .card { border-radius: 0.65rem; }
            
            .page-header h1 { font-size: 1.1rem; }
            
            /* Full width buttons on mobile */
            .btn-mobile-full {
                width: 100%;
                display: block;
                margin-bottom: 0.5rem;
            }
        }
        
        /* ============ CUSTOM SCROLLBAR ============ */
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 3px; }
    </style>
    @stack('styles')
        <!-- TinyMCE Rich Text Editor -->
   
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid d-flex align-items-center">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-laptop"></i> CBT System
            </a> 
            <!-- Mobile toggle -->
            @if(!request()->routeIs('student.exam.continue'))
                <button class="sidebar-toggle me-2" type="button" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
            @endif
            
            <a class="navbar-brand" href="{{ url('/') }}">
                @php $logoUrl = \App\Helpers\SettingHelper::schoolLogo(); @endphp
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo">
                @endif
                <span class="d-none d-sm-inline">{{ \App\Helpers\SettingHelper::schoolName() }}</span>
                <span class="d-inline d-sm-none">CBT</span>
            </a>
            
         
                <div class="ms-auto d-flex align-items-center gap-2">
                    <!-- User avatar -->
                    <div class="d-flex align-items-center gap-2">
                        @if(Auth::user()->has_photo)
                            <img src="{{ Auth::user()->photo_url }}" 
                                alt="{{ Auth::user()->name }}"
                                class="rounded-circle border border-2 border-white"
                                style="width: 36px; height: 36px; object-fit: cover;">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                style="width: 36px; height: 36px; background: rgba(255,255,255,0.25); color: white; font-size: 0.85rem;">
                                {{ Auth::user()->initials }}
                            </div>
                        @endif
                        <span class="nav-link d-none d-md-inline mb-0">
                            {{ Auth::user()->name }}
                        </span>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link" style="color: rgba(255,255,255,0.9);">
                            <i class="bi bi-box-arrow-right"></i> 
                            <span class="d-none d-sm-inline">Logout</span>
                        </button>
                    </form>
                </div>
        </div>
    </nav>

    <!-- SIDEBAR BACKDROP -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- SIDEBAR -->
    @if(!request()->routeIs('student.exam.continue'))
        <nav class="sidebar" id="sidebar">
            @yield('sidebar')
        </nav>
    @endif

    <!-- MAIN CONTENT -->
    <main class="main-content">
        @if(!request()->routeIs('student.exam.continue'))
            <div class="page-header">
                <h1 class="h3">@yield('page-title')</h1>
            </div>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        // Sidebar toggle for mobile
        $(document).ready(function() {
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('show');
                $('#sidebarBackdrop').toggleClass('show');
            });
            
            $('#sidebarBackdrop').on('click', function() {
                $('#sidebar').removeClass('show');
                $(this).removeClass('show');
            });
            
            // Close sidebar on link click (mobile)
            $('#sidebar .nav-link').on('click', function() {
                if ($(window).width() < 992) {
                    $('#sidebar').removeClass('show');
                    $('#sidebarBackdrop').removeClass('show');
                }
            });
            
            // Auto-dismiss alerts
            setTimeout(function() {
                $('.alert-dismissible').fadeOut('slow');
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>
</html>