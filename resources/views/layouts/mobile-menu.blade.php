<div class="mobile-menu md:hidden">
    <div class="mobile-menu-bar">
        <a href="" class="flex mr-auto">
            <img alt="IMP Studio" class="w-6" src="{{ asset('images/IMP-location.png') }}">
        </a>
        <a href="javascript:;" id="mobile-menu-toggler"> <i data-lucide="bar-chart-2" class="w-8 h-8 text-white transform -rotate-90"></i> </a>
    </div>
    <ul class="border-t border-white/[0.08] py-5 hidden">
        <li>
            <a href="{{ route('home') }}" class="menu menu--active {{ Request::is('home*') ? 'active' : '' }}">
                <div class="menu__icon"> <i data-lucide="home"></i> </div>
                <div class="menu__title"> Home </div>
            </a>
        </li>
        <li>
            <a href="{{ route('kehadiran') }}" class="menu {{ Request::is('attendance*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="users"></i> </div>
                <div class="menu__title"> Absensi Kehadiran </div>
            </a>
        </li>
        <li>
            <a href="{{ route('cuti') }}" class="menu {{ Request::is('cuti*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="tag"></i> </div>
                <div class="menu__title"> Cuti </div>
            </a>
        </li>
        <li>
            <a href="{{ route('standup') }}" class="menu {{ Request::is('standup*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="message-square"></i> </div>
                <div class="menu__title"> Standup </div>
            </a>
        </li>
        <li>
            <a href="{{ route('divisi') }}" class="menu {{ Request::is('divisi*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="clipboard"></i> </div>
                <div class="menu__title"> Divisi </div>
            </a>
        </li>
        <li>
            <a href="{{ route('position') }}" class="menu {{ Request::is('position*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="contact"></i> </div>
                <div class="menu__title"> Posisi </div>
            </a>
        </li>
        <li>
            <a href="{{ route('employee') }}" class="menu {{ Request::is('employee*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="users"></i> </div>
                <div class="menu__title"> Pegawai </div>
            </a>
        </li>

    </ul>
</div>
