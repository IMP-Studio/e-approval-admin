<nav class="side-nav">
    <ul>
        <li>
            <a href="{{ route('home') }}" class="side-menu {{ Request::is('home*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                <div class="side-menu__title"> Home </div>
            </a>
        </li>
        <li>
            <a href="{{ route('presence') }}" class="side-menu {{ Request::is('presence*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> Presence </div>
            </a>
        </li>
        <li>
            <a href="{{ route('standup') }}" class="side-menu {{ Request::is('standup*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="message-square"></i> </div>
                <div class="side-menu__title"> Standup </div>
            </a>
        </li>
        <li>
            <a href="{{ route('divisi') }}" class="side-menu {{ Request::is('division*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="clipboard"></i> </div>
                <div class="side-menu__title"> Division </div>
            </a>
        </li>
        <li>
            <a href="{{ route('position') }}" class="side-menu {{ Request::is('position*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="contact"></i> </div>
                <div class="side-menu__title"> Position </div>
            </a>
        </li>
        <li>
            <a href="{{ route('employee') }}" class="side-menu {{ Request::is('employee*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> Pegawai </div>
            </a>
        </li>
        <li>
            <a href="{{ route('partner') }}" class="side-menu {{ Request::is('partner*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> Partner </div>
            </a>
        </li>
        <li>
            <a href="{{ route('project') }}" class="side-menu {{ Request::is('project*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> project </div>
            </a>
        </li>
    </ul>
</nav>
