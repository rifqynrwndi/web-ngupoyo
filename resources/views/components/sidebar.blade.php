<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route ('dashboard.index') }}">Ngupoyo Rejeki</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route ('dashboard.index') }}">NR </a>
        </div>
        <ul class="sidebar-menu">

            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link">
                    <i class="fas fa-user-tie"></i>
                    <span>Pegawai</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contacts.index') }}" class="nav-link">
                    <i class="fas fa-address-book"></i>
                    <span>Kontak</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('attendances.index') }}" class="nav-link">
                    <i class="fas fa-calendar-check"></i>
                    <span>Absensi</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('permissions.index') }}" class="nav-link">
                    <i class="fas fa-user-clock"></i>
                    <span>Izin</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('statistics.index') }}" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span>Rekap Absensi</span>
                </a>
            </li>
    </aside>
</div>
