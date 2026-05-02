<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root, [data-bs-theme="light"] {
            --bs-font-sans-serif: 'Inter', system-ui, -apple-system, sans-serif;
            --app-bg: #f8fafc;
            --sidebar-bg: #ffffff;
            --primary-color: #4f46e5;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --card-bg: #ffffff;
            --input-bg: #f1f5f9;
        }

        [data-bs-theme="dark"] {
            --app-bg: #0f172a;
            --sidebar-bg: #1e293b;
            --primary-color: #6366f1;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --card-bg: #1e293b;
            --input-bg: #0f172a;
        }

        body { 
            background-color: var(--app-bg); 
            color: var(--text-main);
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Responsive Sidebar */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            transition: all 0.3s ease;
            z-index: 1040;
        }

        @media (max-width: 991.98px) {
            .sidebar { left: -280px; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0 !important; }
        }

        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* Navigation */
        .nav-link {
            color: var(--text-muted);
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin: 0.125rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            background-color: var(--input-bg);
            color: var(--primary-color);
        }

        .nav-link i { font-size: 1.25rem; }

        /* Modern Elements */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            background-color: var(--card-bg);
            transition: background-color 0.3s, border-color 0.3s;
        }

        .navbar-top {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 1.5rem;
            transition: background-color 0.3s;
        }

        /* Forms & Inputs Unified */
        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            border-radius: 0.75rem;
            padding: 0.6rem 1rem;
            box-shadow: none !important;
            transition: all 0.2s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1) !important;
        }

        /* Buttons Unified */
        .btn {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.5rem 1.25rem;
            transition: all 0.2s;
        }
        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #fff !important;
        }
        .btn-light {
            background-color: var(--input-bg);
            border-color: var(--border-color);
            color: var(--text-main);
        }
        .btn-light:hover { background-color: var(--border-color); color: var(--text-main); }

        /* Tables Unified */
        .table { color: var(--text-main); margin-bottom: 0; }
        .table th {
            background-color: var(--input-bg);
            border-bottom: 1px solid var(--border-color);
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 1rem;
        }
        .table td {
            border-bottom: 1px solid var(--border-color);
            background-color: var(--card-bg);
            padding: 1rem;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover td { background-color: var(--input-bg); }

        /* Soft Badges */
        .badge {
            font-weight: 600;
            padding: 0.4em 0.8em;
            border-radius: 0.375rem;
            letter-spacing: 0.02em;
        }

        .bg-indigo-soft { background-color: rgba(79, 70, 229, 0.1) !important; color: var(--primary-color) !important; }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1030;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.show { display: block; }
        
        #realtime-notifications { position: fixed; bottom: 20px; right: 20px; z-index: 2000; display: flex; flex-direction: column; gap: 10px; pointer-events: none; }
        .rt-toast { border: none; background: var(--card-bg); border-radius: 1rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-left: 4px solid var(--primary-color); }
    </style>
    <script>
        // Theme initialization to prevent flash
        const getPreferredTheme = () => localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', getPreferredTheme());
    </script>
</head>
<body>
    @auth
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column py-4" id="mainSidebar">
        <div class="px-4 mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-primary rounded-3 p-2 text-white" style="background: linear-gradient(135deg, #4f46e5, #6366f1) !important;">
                    <i class="bi bi-rocket-takeoff-fill"></i>
                </div>
                <span class="fs-5 fw-bold tracking-tight">{{ config('app.name') }}</span>
            </div>
            <button class="btn d-lg-none" id="sidebarClose"><i class="bi bi-x-lg"></i></button>
        </div>

        <nav class="nav flex-column mb-auto">
            <small class="text-uppercase fw-bold text-muted px-4 mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('app.menu') }}</small>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> {{ __('app.dashboard') }}
            </a>
            <a href="{{ route('tasks.index') }}" class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <i class="bi bi-layers"></i> {{ __('app.tasks') }}
            </a>
            
            @if(auth()->user()->isSuperadmin())
            <small class="text-uppercase fw-bold text-muted px-4 mt-4 mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('app.management') }}</small>
            <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> {{ __('app.employees') }}
            </a>
            <a href="{{ route('settings.edit') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="bi bi-sliders"></i> {{ __('app.settings') }}
            </a>
            @endif

            <small class="text-uppercase fw-bold text-muted px-4 mt-4 mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">{{ __('app.account') }}</small>
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-circle"></i> {{ __('app.profile') }}
            </a>
        </nav>

        <div class="px-3 mt-4">
            <div class="rounded-4 p-3 d-flex align-items-center gap-3" style="background: var(--input-bg);">
                <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle shadow-sm border" style="width: 40px; height: 40px; object-fit: cover;">
                <div class="overflow-hidden">
                    <div class="text-truncate fw-bold small">{{ auth()->user()->name }}</div>
                    <div class="text-muted" style="font-size: 0.75rem;">{{ auth()->user()->isSuperadmin() ? __('app.role_superadmin') : __('app.role_employer') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <nav class="navbar navbar-top sticky-top d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn d-lg-none me-2 text-body" id="sidebarToggle"><i class="bi bi-list fs-4"></i></button>
                <h6 class="mb-0 fw-bold d-none d-sm-block">@yield('title')</h6>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm text-muted" id="themeToggle" title="Toggle Theme" style="padding: 0.25rem 0.5rem; font-size: 1.25rem;">
                    <i class="bi bi-moon-stars-fill d-none dark-icon"></i>
                    <i class="bi bi-sun-fill light-icon"></i>
                </button>
                <div class="vr mx-2 text-muted opacity-25"></div>
                <div class="btn-group bg-light rounded-3 p-1 d-none d-md-flex" style="background: var(--input-bg) !important;">
                    @foreach(['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $locale => $label)
                        <a href="{{ route('locale', $locale) }}" class="btn btn-sm border-0 {{ app()->getLocale() === $locale ? 'bg-body shadow-sm fw-bold text-body' : 'text-muted' }}" style="font-size: 0.75rem;">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                <div class="vr mx-2 text-muted opacity-25"></div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-body" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ auth()->user()->avatar_url }}" class="rounded-circle me-2 shadow-sm border" style="width: 32px; height: 32px; object-fit: cover;">
                        <span class="d-none d-md-block small fw-bold">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" style="background: var(--card-bg);" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item text-body py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> {{ __('app.profile') }}</a></li>
                        <li><hr class="dropdown-divider opacity-25"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i> {{ __('app.logout') }}</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="p-3 p-md-4 p-lg-5">
            @yield('content')
        </div>
    </div>
    @else
        <div class="container d-flex justify-content-center align-items-center vh-100">
            @yield('content')
        </div>
    @endauth

    <div id="realtime-notifications"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Theme Logic
        const themeToggle = document.getElementById('themeToggle');
        const darkIcon = document.querySelector('.dark-icon');
        const lightIcon = document.querySelector('.light-icon');

        const updateThemeUI = (theme) => {
            if (theme === 'dark') {
                darkIcon.classList.remove('d-none');
                lightIcon.classList.add('d-none');
            } else {
                lightIcon.classList.remove('d-none');
                darkIcon.classList.add('d-none');
            }
        };

        updateThemeUI(getPreferredTheme());

        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                document.documentElement.setAttribute('data-bs-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeUI(newTheme);
            });
        }

        // Sidebar Toggle Logic
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        const close = document.getElementById('sidebarClose');

        if(toggle) {
            toggle.onclick = () => { sidebar.classList.add('show'); overlay.classList.add('show'); }
            overlay.onclick = () => { sidebar.classList.remove('show'); overlay.classList.remove('show'); }
            if(close) close.onclick = overlay.onclick;
        }

        // Realtime Notifications
        function showNotification(message, type = 'info') {
            const container = document.getElementById('realtime-notifications');
            const toast = document.createElement('div');
            toast.className = 'rt-toast p-3 mb-2 animate-slide-in';
            toast.innerHTML = `<div class="d-flex align-items-center gap-3"><i class="bi bi-bell-fill text-primary"></i><div class="small fw-semibold">${message}</div></div>`;
            container.appendChild(toast);
            setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 5000);
        }
    </script>
    @yield('scripts')
</body>
</html>