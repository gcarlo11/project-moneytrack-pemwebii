<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker - @yield('title', 'Dashboard')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-bg: #f8fafc;
            --sidebar-active: #eff6ff;
            --sidebar-border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
            color: var(--text-primary);
        }
        
        .sidebar {
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
        }
        
        .sidebar-brand {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid var(--sidebar-border);
        }
        
        .sidebar-brand h5 {
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }
        
        .nav-link {
            color: var(--text-secondary);
            border-radius: 0.375rem;
            margin: 0.25rem 0.75rem;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .nav-link:hover {
            background-color: rgba(79, 70, 229, 0.05);
            color: var(--primary-color);
        }
        
        .nav-link.active {
            background-color: var(--sidebar-active);
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .nav-link i {
            width: 1.25rem;
            text-align: center;
        }
        
        .main-content {
            transition: all 0.3s ease;
            min-height: 100vh;
            background-color: white;
            border-radius: 0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        
        @media (min-width: 992px) {
            .main-content {
                border-radius: 0.5rem;
                margin: 1rem;
                min-height: calc(100vh - 2rem);
            }
        }
        
        .page-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--sidebar-border);
        }
        
        .page-header h1 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .page-content {
            padding: 1.5rem;
        }
        
        .alert {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        
        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
        }
        
        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        /* Mobile sidebar toggle */
        .sidebar-toggle {
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1030;
            display: block;
            padding: 0.5rem;
            background-color: white;
            border-radius: 0.375rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid var(--sidebar-border);
            color: var(--text-secondary);
        }
        
        @media (min-width: 768px) {
            .sidebar-toggle {
                display: none;
            }
        }
        
        /* Mobile sidebar */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1020;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Mobile sidebar toggle -->
    <button class="sidebar-toggle d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div id="sidebarMenu" class="col-md-3 col-lg-2 sidebar collapse d-md-block">
                <div class="sidebar-brand">
                    <h5><i class="fas fa-wallet me-2"></i>Finance Tracker</h5>
                </div>
                
                <ul class="nav flex-column mt-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('transactions.index') ? 'active' : '' }}" href="{{ route('transactions.index') }}">
                            <i class="fas fa-exchange-alt me-2"></i>
                            Transactions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('categories.index') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                            <i class="fas fa-tags me-2"></i>
                            Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            <i class="fas fa-chart-bar me-2"></i>
                            Reports
                        </a>
                    </li>
                </ul>
                
                <div class="position-absolute bottom-0 start-0 w-100 p-3 border-top">
                    <a href="#" class="nav-link d-flex align-items-center">
                        <i class="fas fa-user-circle me-2"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="#" class="nav-link d-flex align-items-center">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="page-header d-flex justify-content-between align-items-center">
                    <h1>@yield('header', 'Dashboard')</h1>
                    <div>
                        @yield('actions')
                    </div>
                </div>
                
                <div class="page-content">
                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-check-circle fa-lg"></i>
                                </div>
                                <div>
                                    {{ session('success') }}
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger mb-4">
                            <div class="d-flex">
                                <div class="me-3">
                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                </div>
                                <div>
                                    {{ session('error') }}
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Set up Axios with CSRF token
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>