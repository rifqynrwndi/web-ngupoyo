<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route ('home') }}">Ngupoyo Rejeki</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route ('home') }}">>NR </a>
        </div>
        <ul class="sidebar-menu">

            <li class="nav-item ">
                <a href="{{ route('users.index') }}" class="nav-link "><i class="fas fa-columns"></i>
                    <span>Pegawai</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contacts.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Kontak</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('attendances.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Absensi</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('permissions.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Izin</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('statistics.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Rekap Absensi</span>
                </a>
            </li>

    </aside>
</div>
