<nav class="side-nav">
    <ul>
        <li>
            <a href="{{ route('home') }}" class="side-menu {{ Request::is('home*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="home"></i> </div>
                <div class="side-menu__title"> Home </div>
            </a>
        </li>
        @can('view_presences')
            <li>
                <a href="{{ route('presence') }}" class="side-menu {{ Request::is('presence*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                    <div class="side-menu__title"> Presence </div>
                </a>
            </li>
        @endcan
        @can('view_standups')
            <li>
                <a href="{{ route('standup') }}" class="side-menu {{ Request::is('standup*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="message-square"></i> </div>
                    <div class="side-menu__title"> Standup </div>
                </a>
            </li>
        @endcan
        @can('view_divisions')            
            <li>
                <a href="{{ route('divisi') }}" class="side-menu {{ Request::is('division*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="clipboard"></i> </div>
                    <div class="side-menu__title"> Division </div>
                </a>
            </li>
        @endcan
        @can('view_positions')
            <li>
                <a href="{{ route('position') }}" class="side-menu {{ Request::is('position*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="contact"></i> </div>
                    <div class="side-menu__title"> Position </div>
                </a>
            </li>
        @endcan
        @can('view_employees')
            <li>
                <a href="{{ route('employee') }}" class="side-menu {{ Request::is('employee*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                    <div class="side-menu__title"> Employee </div>
                </a>
            </li>
        @endcan
        @can('view_partners')
            <li>
                <a href="{{ route('partner') }}" class="side-menu {{ Request::is('partner*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                    <div class="side-menu__title"> Partner </div>
                </a>
            </li>
        @endcan
        @can('view_projects')
            <li>
                <a href="{{ route('project') }}" class="side-menu {{ Request::is('project*') ? 'side-menu--active' : '' }}">
                    <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                    <div class="side-menu__title"> Project </div>
                </a>
            </li>
        @endcan
        @can('view_permission')
        <li>
            <a href="{{ route('permission') }}" class="side-menu {{ Request::is('permission*') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon"> <i data-lucide="users"></i> </div>
                <div class="side-menu__title"> User Permission </div>
            </a>
        </li>
    @endcan

        @can('approve_preliminary')
        {{-- Approve head of tired --}}
        <li>
            <a href="javascript:;" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                <div class="side-menu__title">
                    Approve HT
                    <div class="side-menu__sub-icon">  <i data-lucide="chevron-down"></i>  </div>
                </div>
            </a>
            <ul class="">
                <li>
                    <a href="{{ route('approveht.worktripHt') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Work Trip </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('approveht.teleworkHt') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Telework  </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('approveht.leaveHt') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Leave </div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan

        @can('approve_allowed')
         {{-- Approve human Resource --}}
         <li>
            <a href="javascript:;" class="side-menu">
                <div class="side-menu__icon"> <i data-lucide="box"></i> </div>
                <div class="side-menu__title">
                    Approve HR
                    <div class="side-menu__sub-icon"> <i data-lucide="chevron-down"></i> </div>
                </div>
            </a>
            <ul class="">
                <li>
                    <a href="{{ route('approvehr.worktripHr') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Work Trip </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('approvehr.teleworkHr') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Telework  </div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('approvehr.leaveHr') }}" class="side-menu">
                        <div class="side-menu__icon"> <i data-lucide="activity"></i> </div>
                        <div class="side-menu__title"> Leave </div>
                    </a>
                </li>
            </ul>
        </li>
        @endcan



    </ul>
</nav>
