<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Bengkel')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; min-height: 100vh; }

        .dashboard { display: flex; min-height: 100vh; }

        .sidebar-backdrop {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.4); z-index: 199;
        }
        .sidebar-backdrop.active { display: block; }

        .sidebar {
            width: 260px; background: #1a1a2e; color: #fff;
            display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh;
            z-index: 200; transition: transform 0.3s ease;
        }
        .sidebar-header { padding: 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); text-align: center; flex-shrink: 0; }
        .sidebar-header i { font-size: 28px; color: #e94560; }
        .sidebar-header h2 { font-size: 16px; margin-top: 6px; }
        .sidebar-header p { font-size: 11px; color: #aaa; margin-top: 2px; }
        .sidebar-user { padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); font-size: 14px; flex-shrink: 0; }
        .sidebar-user i { margin-right: 8px; color: #e94560; }
        .sidebar-user small { display: block; color: #888; font-size: 12px; margin-top: 4px; }
        .sidebar-nav { flex: 1; padding: 6px 0; overflow-y: auto; }
        .sidebar-nav .nav-section {
            padding: 10px 20px 4px; font-size: 11px; font-weight: 700; color: #e94560;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .sidebar-nav a {
            display: flex; align-items: center; padding: 10px 20px; color: #ccc;
            text-decoration: none; font-size: 14px; transition: all 0.3s;
        }
        .sidebar-nav a i { width: 24px; margin-right: 12px; font-size: 16px; }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-footer { padding: 14px 20px; border-top: 1px solid rgba(255,255,255,0.1); flex-shrink: 0; }
        .sidebar-footer a { color: #e94560; text-decoration: none; font-size: 14px; }
        .sidebar-footer a i { margin-right: 8px; }

        .main-content {
            flex: 1; padding: 24px 20px; overflow-y: auto; min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .main-content h1 { font-size: 24px; color: #1a1a2e; margin-bottom: 6px; }
        .main-content p.subtitle { color: #666; margin-bottom: 24px; }

        .hamburger {
            display: none; position: fixed; top: 14px; left: 14px; z-index: 300;
            width: 40px; height: 40px; border-radius: 10px; background: #1a1a2e;
            color: #fff; border: none; font-size: 18px; cursor: pointer;
            align-items: center; justify-content: center;
        }
        .hamburger i { pointer-events: none; }

        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #fff; border-radius: 16px; padding: 28px 24px; width: 90%; max-width: 360px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center;
        }
        .modal-box i { font-size: 44px; color: #e94560; margin-bottom: 14px; }
        .modal-box h3 { font-size: 18px; color: #1a1a2e; margin-bottom: 6px; }
        .modal-box p { font-size: 14px; color: #666; margin-bottom: 20px; }
        .modal-actions { display: flex; gap: 12px; justify-content: center; }
        .modal-actions .btn {
            display: inline-flex; align-items: center; gap: 6px; padding: 10px 24px;
            border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer;
            text-decoration: none; transition: all 0.3s;
        }
        .modal-actions .btn-danger { background: #dc2626; color: #fff; }
        .modal-actions .btn-danger:hover { background: #b91c1c; }
        .modal-actions .btn-secondary { background: #e2e8f0; color: #475569; }
        .modal-actions .btn-secondary:hover { background: #cbd5e1; }

        @media (min-width: 769px) {
            .sidebar { transform: translateX(0); }
            .main-content { margin-left: 260px; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 70px 16px 20px; }
            .hamburger { display: flex; }
            .main-content h1 { font-size: 20px; }
            .login-card { width: 90% !important; padding: 28px 20px !important; }
            .page-header { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            table { font-size: 13px; }
            th, td { padding: 8px 10px; }
            .menu-grid { grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)) !important; gap: 12px !important; }
            .menu-card { padding: 16px 12px !important; }
            .menu-card i { font-size: 28px !important; }
            .menu-card h3 { font-size: 13px !important; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    @yield('content')

    <div class="modal-overlay" id="logoutModal">
        <div class="modal-box">
            <i class="fas fa-sign-out-alt"></i>
            <h3>Yakin ingin logout?</h3>
            <p>Anda akan keluar dari sistem dan perlu login kembali.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeLogoutModal()">Batal</button>
                <form id="logoutConfirmForm" method="POST" action="{{ route('logout') }}" style="display:none;">@csrf</form>
                <button class="btn btn-danger" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Ya, Logout</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="confirmModal">
        <div class="modal-box">
            <i class="fas fa-question-circle" style="color:#0f3460;"></i>
            <h3 id="confirmTitle">Konfirmasi</h3>
            <p id="confirmMessage" style="margin-bottom:24px;">Apakah Anda yakin?</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeConfirmModal()">Batal</button>
                <button class="btn btn-danger" id="confirmOkBtn" onclick="executeConfirm()"><i class="fas fa-check"></i> Ya</button>
            </div>
        </div>
    </div>

    <script>
        let confirmCallback = null;

        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.getElementById('sidebarBackdrop').classList.toggle('active');
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('sidebarBackdrop').classList.remove('active');
        }
        document.addEventListener('click', function(e) {
            if (e.target.closest('.sidebar-nav a') || e.target.closest('.sidebar-footer a')) {
                setTimeout(closeSidebar, 150);
            }
        });
        function showLogoutModal() { document.getElementById('logoutModal').classList.add('active'); }
        function closeLogoutModal() { document.getElementById('logoutModal').classList.remove('active'); }
        function confirmLogout() { document.getElementById('logoutConfirmForm').submit(); }
        document.getElementById('logoutModal').addEventListener('click', function(e) { if (e.target === this) closeLogoutModal(); });

        function showConfirmModal(message, cb) {
            document.getElementById('confirmMessage').textContent = message;
            confirmCallback = cb;
            document.getElementById('confirmModal').classList.add('active');
        }
        function closeConfirmModal() {
            document.getElementById('confirmModal').classList.remove('active');
            confirmCallback = null;
        }
        function executeConfirm() {
            if (typeof confirmCallback === 'function') confirmCallback();
            closeConfirmModal();
        }
        document.getElementById('confirmModal').addEventListener('click', function(e) { if (e.target === this) closeConfirmModal(); });
        function confirmForm(form, message) {
            event.preventDefault();
            showConfirmModal(message, function() { form.submit(); });
        }
    </script>

    @stack('scripts')
</body>
</html>
