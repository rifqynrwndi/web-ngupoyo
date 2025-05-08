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
                    <span>Users</span></a>
            </li>
            <li class="nav-item">
                <a href="{{ route('contacts.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Contacts</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('attendances.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Attendances</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('permissions.index') }}" class="nav-link">
                    <i class="fas fa-columns"></i>
                    <span>Permission</span>
                </a>
            </li>

    </aside>
</div>
