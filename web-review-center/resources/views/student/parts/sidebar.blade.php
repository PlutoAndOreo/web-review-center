<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">


    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="{{ route('student.info') }}" class="d-block">
                    {{ auth('student')->user()->first_name ?? 'Student' }} {{ auth('student')->user()->last_name ?? '' }}
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('student.dashboard') }}" class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Profile -->
                <li class="nav-item">
                    <a href="{{ route('student.info') }}" class="nav-link {{ request()->routeIs('student.info') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>

                <!-- Videos -->
                <li class="nav-item">
                    <a href="{{ route('student.videos.list') }}" class="nav-link {{ request()->routeIs('student.videos.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-video"></i>
                        <p>Videos</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                    <form id="logout-form" action="{{ route('student.logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>