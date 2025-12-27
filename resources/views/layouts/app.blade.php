<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }
        
        .navbar {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }
        
        .navbar-nav {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }
        
        .navbar-nav a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }
        
        .navbar-nav a:hover {
            opacity: 0.8;
        }
        
        .navbar-nav button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1rem;
            transition: opacity 0.3s;
        }
        
        .navbar-nav button:hover {
            opacity: 0.8;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .card-header {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: #3498db;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .invalid-feedback {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
            list-style: none;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination .active span {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .pagination a:hover {
            background-color: #f8f9fa;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #3498db;
        }
        
        .stat-card h3 {
            font-size: 0.875rem;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .section-divider {
            border: none;
            border-top: 2px dashed #e0e0e0;
            margin: 2.5rem 0;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .clickable-row:hover {
            background-color: #eef5ff;
        }

        .json-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 9999;
        }

        .json-modal {
            background: white;
            border-radius: 8px;
            max-width: 960px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
            max-height: 80vh;
            display: flex;
            flex-direction: column;
        }

        .json-modal header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .json-modal pre {
            margin: 0;
            padding: 1.25rem 1.5rem;
            white-space: pre-wrap;
            overflow-y: auto;
            background: #0f172a;
            color: #e0f2fe;
            height: 100%;
            font-size: 0.95rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="navbar-content">
            <a href="{{ route('dashboard') }}" class="navbar-brand">
                {{ config('app.name', 'Laravel') }}
            </a>
            <ul class="navbar-nav">
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('domains') }}">Domains</a></li>
                <li><a href="{{ route('whois-records') }}">WHOIS Records</a></li>
                @if(auth()->user()?->hasRole('admin'))
                    <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                @endif
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>
    @endauth

    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <div id="jsonModal" class="json-modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="jsonModalTitle">
        <div class="json-modal">
            <header>
                <strong id="jsonModalTitle">Record JSON</strong>
                <button type="button" id="jsonModalClose" class="btn btn-secondary" aria-label="Close JSON modal" style="padding: 0.4rem 0.9rem;">Close</button>
            </header>
            <pre id="jsonModalContent"></pre>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('jsonModal');
            const modalContent = document.getElementById('jsonModalContent');
            const modalTitle = document.getElementById('jsonModalTitle');
            const closeBtn = document.getElementById('jsonModalClose');

            const closeModal = () => {
                modal.style.display = 'none';
                modalContent.textContent = '';
            };

            document.addEventListener('click', (event) => {
                const trigger = event.target.closest('[data-json-modal]');
                if (trigger) {
                    const encoded = trigger.dataset.json || '';
                    const title = trigger.dataset.jsonTitle || 'Record JSON';
                    modalTitle.textContent = title;

                    try {
                        const decoded = atob(encoded);
                        try {
                            const parsed = JSON.parse(decoded);
                            modalContent.textContent = JSON.stringify(parsed, null, 2);
                        } catch {
                            modalContent.textContent = decoded;
                        }
                    } catch (error) {
                        modalContent.textContent = 'Unable to load JSON payload.';
                    }

                    modal.style.display = 'flex';
                }
            });

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.style.display === 'flex') {
                    closeModal();
                }
            });
        })();
    </script>
</body>
</html>
