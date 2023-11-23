<div class="top-bar-boxed h-[70px] md:h-[65px] z-[51] border-b border-white/[0.08] -mt-7 md:mt-0 -mx-3 sm:-mx-8 md:-mx-0 px-3 md:border-b-0 relative md:fixed md:inset-x-0 md:top-0 sm:px-8 md:px-10 md:pt-10 md:bg-gradient-to-b md:from-slate-100 md:to-transparent dark:md:from-darkmode-700">
    <div class="h-full flex items-center">
        <a href="" class="logo -intro-x hidden md:flex xl:w-[180px] block">
            <img alt="IMP" class="logo__image w-6 rounded-full" src="{{ asset('images/IMP-location.png') }}">
            <span class="logo__text text-white text-lg ml-3"> IMP-Studio </span>
        </a>
        <nav aria-label="breadcrumb" class="-intro-x h-[45px] mr-auto">
            <ol class="breadcrumb breadcrumb-light">
                <li class="breadcrumb-item"><a href="#">Application</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>

        <div class="intro-x relative mr-3 sm:mr-6">
            <div class="search hidden sm:block">
                <input type="text" class="search__input form-control border-transparent" placeholder="Search...">
                <i data-lucide="search" class="search__icon dark:text-slate-500"></i>
            </div>
            <a class="notification notification--light sm:hidden" href=""> <i data-lucide="search" class="notification__icon dark:text-slate-500"></i> </a>

        </div>

        <div class="intro-x dropdown mr-4 sm:mr-6">
            <div class="dropdown-toggle notification notification--bullet cursor-pointer" role="button" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="bell" class="notification__icon dark:text-slate-500"></i> </div>
            <div class="notification-content pt-2 dropdown-menu">
                <div class="notification-content__box dropdown-content">
                    <div class="notification-content__title">Notifications</div>
                    <div class="cursor-pointer relative flex items-center ">
                        
                        <div class="overflow-hidden">
                            <div class="flex items-center">
                                <a href="javascript:;" class="font-medium truncate mr-5">Mahesa Alfian Dhika</a> 
                                <div class="text-xs text-slate-400 ml-auto whitespace-nowrap">01:10 PM</div>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Mahesa Alfian Dhika telah mengajukan Work From Anywhere Pada Tanggal 17 November 2023</div>
                        </div>
                    </div>
                    <div class="cursor-pointer relative flex items-center mt-5">
                        
                        <div class="overflow-hidden">
                            <div class="flex items-center">
                                <a href="javascript:;" class="font-medium truncate mr-5">Ibrahim Khalish</a> 
                                <div class="text-xs text-slate-400 ml-auto whitespace-nowrap">06:05 PM</div>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Ibrahim Khalish telah mengajukan Work From Anywhere Pada Tanggal 17 November 2023</div>
                        </div>
                    </div>
                    <div class="cursor-pointer relative flex items-center mt-5">
                        
                        <div class="overflow-hidden">
                            <div class="flex items-center">
                                <a href="javascript:;" class="font-medium truncate mr-5">Mukhamad Arrafi</a> 
                                <div class="text-xs text-slate-400 ml-auto whitespace-nowrap">05:09 AM</div>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Mukhamad Arrafi telah mengajukan Perjalanan Dinas Pada Tanggal 17 November 2023</div>
                        </div>
                    </div>
                    <div class="cursor-pointer relative flex items-center mt-5">
                        
                        <div class="overflow-hidden">
                            <div class="flex items-center">
                                <a href="javascript:;" class="font-medium truncate mr-5">Fathir Akmal Burhanudin</a> 
                                <div class="text-xs text-slate-400 ml-auto whitespace-nowrap">05:09 AM</div>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Fathir Akmal Burhanudin telah mengajukan Perjalanan Dinas Pada Tanggal 17 November 2023</div>
                        </div>
                    </div>
                    <div class="cursor-pointer relative flex items-center mt-5">
                        
                        <div class="overflow-hidden">
                            <div class="flex items-center">
                                <a href="javascript:;" class="font-medium truncate mr-5">Mahesa Alfian Dhika</a> 
                                <div class="text-xs text-slate-400 ml-auto whitespace-nowrap">01:10 PM</div>
                            </div>
                            <div class="w-full truncate text-slate-500 mt-0.5">Mahesa Alfian Dhika telah mengajukan Cuti Darurat Pada Tanggal 16 November 2023</div>
                        </div>
                    </div>
                    <hr class="mt-2" style="border-top: solid 2px #64748b">
                    <a class="flex items-center mt-1 py-2 hover:bg-white/5 font-medium" href="{{ route('notification')}}"><i data-lucide="info" class="w-4 h-4 mr-2"></i>Show All</a>
                </div>
            </div>
        </div>

        <div class="intro-x dropdown w-8 h-8">
            <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110" role="button" aria-expanded="false" data-tw-toggle="dropdown">
                <img alt="profile" src="{{ asset('images/IMP-location.png') }}">
            </div>
            <div class="dropdown-menu w-56">
                <ul class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                    <li class="p-2">
                        <div class="font-medium">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-white/60 mt-0.5 dark:text-slate-500">{{ Auth::user()->email }}</div>
                    </li>
                   <li>
                        <a class="dropdown-item hover:bg-white/5" href="javascript:;" data-tw-toggle="modal" data-tw-target="#logout-modal"><i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i>Logout</a>
                   </li>
                </ul>
            </div>
        </div>
    </div>
</div>

 <div id="logout-modal" class="modal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog">
         <div class="modal-content">
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <div class="modal-body p-0">
                     <div class="p-5 text-center"> <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                         <div class="text-3xl mt-5">Are you sure?</div>
                         <div class="text-slate-500 mt-2">Do you really want to logout?</div>
                     </div>
                     <div class="px-5 pb-8 text-center">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-danger w-24">Logout</button> </div>
                </div>
            </form>
         </div>
     </div>
 </div>
