<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bengkel')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }
        .dashboard { display: flex; min-height: 100vh; }
        .sidebar {
            width: 250px; background: #1a1a2e; color: #fff;
            display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100;
        }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; }
        .sidebar-header i { font-size: 32px; color: #e94560; }
        .sidebar-header h2 { font-size: 18px; margin-top: 8px; }
        .sidebar-header p { font-size: 12px; color: #aaa; margin-top: 2px; }
        .sidebar-user { padding: 16px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 14px; }
        .sidebar-user i { margin-right: 8px; color: #e94560; }
        .sidebar-user small { display: block; color: #888; font-size: 12px; margin-top: 4px; }
        .sidebar-nav { flex: 1; padding: 8px 0; overflow-y: auto; }
        .sidebar-nav .nav-section {
            padding: 12px 20px 4px; font-size: 11px; font-weight: 700; color: #e94560;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .sidebar-nav a {
            display: flex; align-items: center; padding: 10px 20px; color: #ccc;
            text-decoration: none; font-size: 14px; transition: all 0.3s;
        }
        .sidebar-nav a i { width: 24px; margin-right: 12px; font-size: 16px; }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-footer {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer a { color: #e94560; text-decoration: none; font-size: 14px; }
        .sidebar-footer a i { margin-right: 8px; }
        .main-content {
            margin-left: 250px; flex: 1; padding: 30px; overflow-y: auto; min-height: 100vh;
        }
        .main-content h1 { font-size: 28px; color: #1a1a2e; margin-bottom: 6px; }
        .main-content p.subtitle { color: #666; margin-bottom: 30px; }
    </style>
    @stack('styles')
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
