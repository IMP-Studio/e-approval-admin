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
            <a href="{{ route('presence') }}" class="menu {{ Request::is('presence*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="users"></i> </div>
                <div class="menu__title"> Presence </div>
            </a>
        </li>
        <li>
            <a href="{{ route('standup') }}" class="menu {{ Request::is('standup*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="message-square"></i> </div>
                <div class="menu__title"> Standup </div>
            </a>
        </li>
        <li>
            <a href="{{ route('divisi') }}" class="menu {{ Request::is('division*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="clipboard"></i> </div>
                <div class="menu__title"> Division </div>
            </a>
        </li>
        <li>
            <a href="{{ route('position') }}" class="menu {{ Request::is('position*') ? 'menu--active' : '' }}">
                <div class="menu__icon"> <i data-lucide="contact"></i> </div>
                <div class="menu__title"> Position </div>
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
