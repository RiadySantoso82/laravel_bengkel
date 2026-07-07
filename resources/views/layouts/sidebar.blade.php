<aside class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-car-side"></i>
        <h2>Bengkel</h2>
        <p>Sistem Manajemen</p>
    </div>
    <div class="sidebar-user">
        <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
        <small><i class="fas fa-tag"></i> {{ ucfirst(Auth::user()->role) }}</small>
    </div>
    <nav class="sidebar-nav">

@if (Auth::user()->role === 'mekanik')
        <a href="{{ route('mechanic.dashboard') }}"><i class="fas fa-th-large"></i> Dashboard</a>

        <div class="nav-section">Servis</div>
        <a href="{{ route('mechanic.services') }}"><i class="fas fa-list"></i> Daftar Servis Saya</a>
        <a href="{{ route('mechanic.history') }}"><i class="fas fa-history"></i> Riwayat Servis</a>

        <div class="nav-section">Akun</div>
        <a href="{{ route('mechanic.profile') }}"><i class="fas fa-user-circle"></i> Profil Saya</a>

@else
        <a href="{{ route('dashboard') }}"><i class="fas fa-th-large"></i> Dashboard</a>

        <div class="nav-section">Transaksi</div>
        <a href="{{ route('service-orders.index') }}"><i class="fas fa-wrench"></i> Service Order</a>
        <a href="{{ route('invoices.index') }}"><i class="fas fa-file-invoice-dollar"></i> Invoice</a>

        <div class="nav-section">Data Master</div>
        <a href="{{ route('customers.index') }}"><i class="fas fa-users"></i> Pelanggan</a>
        <a href="{{ route('vehicles.index') }}"><i class="fas fa-truck"></i> Kendaraan</a>
        <a href="{{ route('spareparts.index') }}"><i class="fas fa-cogs"></i> Sparepart</a>
        <a href="{{ route('mechanics.index') }}"><i class="fas fa-user-cog"></i> Mekanik</a>
        <a href="{{ route('suppliers.index') }}"><i class="fas fa-truck-loading"></i> Supplier</a>
        <a href="{{ route('sparepart-categories.index') }}"><i class="fas fa-tags"></i> Kategori Sparepart</a>
        <a href="{{ route('units.index') }}"><i class="fas fa-ruler"></i> Satuan</a>
        <a href="{{ route('service-categories.index') }}"><i class="fas fa-toolbox"></i> Kategori Servis</a>
        <a href="{{ route('payment-methods.index') }}"><i class="fas fa-credit-card"></i> Metode Bayar</a>
        <a href="{{ route('checklist-items.index') }}"><i class="fas fa-clipboard-list"></i> Checklist Item</a>

        <div class="nav-section">Laporan</div>
        <a href="#"><i class="fas fa-chart-bar"></i> Laporan</a>

        @if (Auth::user()->role === 'admin')
        <div class="nav-section">Sistem</div>
        <a href="{{ route('users.index') }}"><i class="fas fa-users-cog"></i> User</a>
        <a href="{{ route('settings.index') }}"><i class="fas fa-cog"></i> Pengaturan</a>
        @endif
@endif

    </nav>
    <div class="sidebar-footer">
        <a href="#" onclick="event.preventDefault(); showLogoutModal();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
    </div>
</aside>
