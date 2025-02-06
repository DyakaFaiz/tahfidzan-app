<div class="sidebar-wrapper active">
    <div class="sidebar-header">
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <span class="avatar-content" type="button"
                        id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-person-circle"></i>
                    </span>
                    <div class="dropdown-menu bg-danger" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item bg-danger text-white rounded-circle" href="{{ route('logout') }}">Logout</a>
                    </div>
                </div>
                <div class="ms-3 name">
                    <h5 class="font-bold">{{ session('namaUser') }}</h5>
                    <h6 class="text-muted mb-0">{{ '@'. session('username') }}</h6>
                </div>
            </div>
            <div class="toggler">
                <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            <li class="sidebar-title">Menu</li>

            <li class="sidebar-item {{ Route::is('dashboard.') ? 'active' : '' }}">
                <a href="{{ route('dashboard.') }}" class='sidebar-link'>
                    <i class="bi bi-grid-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if (session('idRole') == 1)                
                <li class="sidebar-item {{ Route::is('user.index') ? 'active' : '' }}">
                    <a href="{{ route('user.index') }}" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>User</span>
                    </a>
                </li>
                <li class="sidebar-item {{ Route::is('santri.index') ? 'active' : '' }}">
                    <a href="{{ route('santri.index') }}" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>Santri</span>
                    </a>
                </li>
                <li class="sidebar-title">Ketahfidzan</li>

                <li class="sidebar-item {{ Route::is('ketahfidzan.ustad-tahfidz.index') ? 'active' : '' }}">
                    <a href="{{ route('ketahfidzan.ustad-tahfidz.index') }}" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>Ustad Tahfidz</span>
                    </a>
                </li>

                <li class="sidebar-item has-sub {{ Route::is('ketahfidzan.tahfidzan-admin.deresan-a.index') || Route::is('ketahfidzan.tahfidzan-admin.murojaah.index') || Route::is('ketahfidzan.tahfidzan-admin.tahsin-binnadhor.index') || Route::is('ketahfidzan.tahfidzan-admin.ziyadah.index') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-ruled-fill"></i>
                        <span>Tahfidzan Harian</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.deresan-a.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.deresan-a.index') }}">Deresan A</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.murojaah.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.murojaah.index') }}">Murojaah</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.tahsin-binnadhor.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.tahsin-binnadhor.index') }}">Tahsin Binnadhor</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.ziyadah.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.ziyadah.index') }}">Ziyadah</a>
                        </li>
                    </ul>
                </li>

            @endif

            @if (session('idRole') == 2)
                <li class="sidebar-title">Harian Tahfidzan</li>

                <li class="sidebar-item has-sub {{ Route::is('ketahfidzan.tahfidzan-admin.deresan-a.index') || Route::is('ketahfidzan.tahfidzan-admin.murojaah.index') || Route::is('ketahfidzan.tahfidzan-admin.tahsin-binnadhor.index') || Route::is('ketahfidzan.tahfidzan-admin.ziyadah.index') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-people"></i>
                        <span>Tahfidzan Harian</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.deresan-a.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.deresan-a.index') }}">Deresan A</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.murojaah.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.murojaah.index') }}">Murojaah</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.tahsin-binnadhor.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.tahsin-binnadhor.index') }}">Tahsin Binnadhor</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.tahfidzan-admin.ziyadah.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.tahfidzan-admin.ziyadah.index') }}">Ziyadah</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-title">Evaluasi Tahfidzan</li>

                <li class="sidebar-item has-sub {{ Route::is('ketahfidzan.evaluasi.deresan-a.index') || Route::is('ketahfidzan.evaluasi.murojaah.index') || Route::is('ketahfidzan.evaluasi.tahsin-binnadhor.index') || Route::is('ketahfidzan.evaluasi.ziyadah.index') ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-file-earmark-ruled-fill"></i>
                        <span>Evaluasi</span>
                    </a>
                    <ul class="submenu ">
                        <li class="submenu-item {{ Route::is('ketahfidzan.evaluasi.deresan-a.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.evaluasi.deresan-a.index') }}">Deresan A</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.evaluasi.murojaah.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.evaluasi.murojaah.index') }}">Murojaah</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.evaluasi.tahsin-binnadhor.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.evaluasi.tahsin-binnadhor.index') }}">Tahsin Binnadhor</a>
                        </li>
                        <li class="submenu-item {{ Route::is('ketahfidzan.evaluasi.ziyadah.index') ? 'active' : '' }}">
                            <a href="{{ route('ketahfidzan.evaluasi.ziyadah.index') }}">Ziyadah</a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
    <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
</div>