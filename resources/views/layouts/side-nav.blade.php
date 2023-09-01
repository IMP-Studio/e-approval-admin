<nav class="side-nav">
    <ul>
        <li>
            <a href="{{ route('home') }}" class="side-menu {{ Request::is('home*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                <div class="side-menu__title"> Home </div>
            </a>
        </li>
        <li>
            <a href="{{ route('kehadiran') }}" class="side-menu {{ Request::is('attendance*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> Absensi Kehadiran </div>
            </a>
        </li>
        <li>
            <a href="{{ route('cuti') }}" class="side-menu {{ Request::is('cuti*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="tag"></i> </div>
                <div class="side-menu__title"> Cuti </div>
            </a>
        </li>
        <li>
            <a href="{{ route('standup') }}" class="side-menu {{ Request::is('standup*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="message-square"></i> </div>
                <div class="side-menu__title"> Standup </div>
            </a>
        </li>
        <li>
            <a href="{{ route('divisi') }}" class="side-menu {{ Request::is('divisi*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="clipboard"></i> </div>
                <div class="side-menu__title"> Divisi </div>
            </a>
        </li>
        <li>
            <a href="{{ route('position') }}" class="side-menu {{ Request::is('position*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="contact"></i> </div>
                <div class="side-menu__title"> Posisi </div>
            </a>
        </li>
        <li>
            <a href="{{ route('employee') }}" class="side-menu {{ Request::is('employee*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> Pegawai </div>
            </a>
        </li>
    </ul>
</nav>
