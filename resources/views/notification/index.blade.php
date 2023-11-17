@extends('layouts.master')
@section('content')
<div class="content">
    <div class="grid grid-cols-12 gap-6 mt-8">
        <div class="col-span-12 lg:col-span-3 2xl:col-span-2">
            <h2 class="intro-y text-lg font-medium mr-auto mt-2">
                Notification
            </h2>
            <!-- BEGIN: Inbox Menu -->
            <div class="intro-y box bg-primary p-5 mt-6">
                <button type="button" class="btn text-slate-600 dark:text-slate-300 w-full bg-white dark:bg-darkmode-300 dark:border-darkmode-300 mt-1"> 
                    @if(Auth::user()->getRoleNames()->first() == 'super-admin')
                        Super Admin
                    @elseif(Auth::user()->getRoleNames()->first() == 'employee')
                        @if(Auth::user()->hasPermissionTo('approve_allowed'))
                            Human Resource
                        @elseif(Auth::user()->hasPermissionTo('approve_preliminary'))
                            Head Of Tribe
                        @else
                            Ordinary Employee
                        @endif
                    @endif
                </button>
                <div class="border-t border-white/10 dark:border-darkmode-400 mt-6 pt-6 text-white">
                    <a href="" class="flex items-center px-3 py-2 rounded-md bg-white/10 dark:bg-darkmode-700 font-medium"> <i class="w-4 h-4 mr-2" data-lucide="layout-dashboard"></i> All </a>
                    <a href="" class="flex items-center px-3 py-2 mt-2 rounded-md"> <i class="w-4 h-4 mr-2" data-lucide="monitor"></i> WFA </a>
                    <a href="" class="flex items-center px-3 py-2 mt-2 rounded-md"> <i class="w-4 h-4 mr-2" data-lucide="building"></i> Work Trip </a>
                    <a href="" class="flex items-center px-3 py-2 mt-2 rounded-md"> <i class="w-4 h-4 mr-2" data-lucide="power-off"></i> Cuti </a>
                    <a href="" class="flex items-center px-3 py-2 mt-2 rounded-md"> <i class="w-4 h-4 mr-2" data-lucide="archive"></i> Archive </a>
                </div>

                <div class="border-t border-white/10 dark:border-darkmode-400 mt-4 pt-4 text-white">
                    <a href="" class="flex items-center px-3 py-2 mt-2 rounded-md"> <i class="w-4 h-4 mr-2" data-lucide="book-open"></i> Read </a>
                    <a href="" class="flex items-center px-3 py-2 mt-2 rounded-md"> <i class="w-4 h-4 mr-2" data-lucide="book"></i> Unread </a>
                </div>
                
            </div>
            <!-- END: Inbox Menu -->
        </div>
        <div class="col-span-12 lg:col-span-9 2xl:col-span-10">
            <!-- BEGIN: Inbox Filter -->
            <div class="intro-y flex flex-col-reverse sm:flex-row items-center">
                <div class="w-full sm:w-auto relative mr-auto mt-3 sm:mt-0">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 ml-3 left-0 z-10 text-slate-500" data-lucide="search"></i> 
                    <input type="text" class="form-control w-full sm:w-64 box px-10" placeholder="Search notification">
                    
                </div>
                
            </div>
            <!-- END: Inbox Filter -->
            <!-- BEGIN: Inbox Content -->
            <div class="intro-y inbox box mt-5">
                <div class="p-5 flex flex-col-reverse sm:flex-row text-slate-500 border-b border-slate-200/60">
                    <div class="flex items-center mt-3 sm:mt-0 border-t sm:border-0 border-slate-200/60 pt-5 sm:pt-0 mt-5 sm:mt-0 -mx-5 sm:mx-0 px-1 pr-5 sm:px-0">
                        <div class="dropdown" data-tw-placement="bottom-start">
                            <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="chevron-down" class="w-5 h-5"></i> </a>
                            <div class="dropdown-menu w-32">
                                <ul class="dropdown-content">
                                    <li> <a href="" class="dropdown-item">All</a> </li>
                                    <li> <a href="" class="dropdown-item">Read</a> </li>
                                    <li> <a href="" class="dropdown-item">Unread</a> </li>
                                </ul>
                            </div>
                        </div>
                        <a href="" class="w-5 h-5 ml-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="refresh-cw"></i> </a>
                    </div>
                    <div class="flex items-center sm:ml-auto">
                        <div class="">1 - 50 of 5,238</div>
                        <a href="javascript:;" class="w-5 h-5 ml-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="chevron-left"></i> </a>
                        <a href="javascript:;" class="w-5 h-5 ml-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="chevron-right"></i> </a>
                    </div>
                </div>
                <div class="overflow-x-auto sm:overflow-x-visible">
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-4.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mahesa Alfian Dhika</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> Mahesa Alfian Dhika Telah Mengajukan Work Form Anywhere Pada Tanggal 17 November 2023 </div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">17 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-8.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Ibrahim Khalish</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Ibrahim Khalish Telah Mengajukan Work Form Anywhere Pada Tanggal 17 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">17 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-3.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mukhamad Arrafi</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mukhamad Arrafi Telah Mengajukan Perjalanan Dinas Pada Tanggal 17 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">17 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-2.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fathir Akmal Burhanudin</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fathir Akmal Burhanudin Telah Mengajukan Perjalanan Dinas Pada Tanggal 17 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">17 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-10.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mahesa Alfian Dhika</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mahesa Alfian Dhika Telah Mengajukan Cuti Darurat Pada Tanggal 16 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">16 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-2.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fathir Akmal Burhanudin</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fathir Akmal Burhanudin Telah Mengajukan Cuti Darurat Pada Tanggal 16 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">16 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-9.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Ibrahim Khalish</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Ibrahim Khalish Telah Mengajukan Perjalanan Dinas Pada Tanggal 16 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">16 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-2.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mukhamad Arrafi</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mukhamad Arrafi Telah Mengajukan Cuti Tahunan Pada Tanggal 16 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">16 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-1.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mahesa Alfian Dhika</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mahesa Alfian Dhika Telah Mengajukan Cuti Khusus Pada Tanggal 15 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">15 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-8.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mukhamad Arrafi</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mukhamad Arrafi Telah Mengajukan Cuti Darurat Pada Tanggal 15 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">15 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-7.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fathir Akmal Burhanudin</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fathir Akmal Burhanudin Telah Mengajukan Work From Anywhere Pada Tanggal 15 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">15 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-1.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Ibrahim Khalish</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Ibrahim Khalish Telah Mengajukan Cuti Darurat Pada Tanggal 15 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">15 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-12.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fauzan Alghifari</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fauzan Alghifari Telah Mengajukan Cuti Khusus Pada Tanggal 14 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">14 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-13.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mukhamad Arrafi</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mukhamad Arrafi Telah Mengajukan Work From Anywhere Pada Tanggal 14 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">14 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-10.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mahesa Alfian Dhika</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mahesa Alfian Dhika Telah Mengajukan Perjalanan Dinas Pada Tanggal 14 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">14 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-4.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fathir Akmal Burhanudin</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fathir Akmal Burhanudin Telah Mengajukan Cuti Khusus Pada Tanggal 14 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">14 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-2.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Mukhamad Arrafi</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Mukhamad Arrafi Telah Mengajukan Work From Anywhere Pada Tanggal 13 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">13 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-12.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Ibrahim Khalish</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Ibrahim Khalish Telah Mengajukan Cuti Tahunan Pada Tanggal 13 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">13 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-13.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fathir Akmal Burhanudin</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fathir Akmal Burhanudin Telah Mengajukan Cuti Darurat Pada Tanggal 13 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">13 Nov</div>
                            </div>
                        </div>
                    </div>
                    <div class="intro-y">
                        <div class="inbox__item inbox__item--active inline-block sm:block text-slate-600 dark:text-slate-500 bg-slate-100 dark:bg-darkmode-400/70 border-b border-slate-200/60 dark:border-darkmode-400">
                            <div class="flex px-1 pr-5 py-3">
                                <div class="w-72 flex-none flex items-center mr-5">
                                    <a href="javascript:;" class="w-5 h-5 flex-none ml-4 flex items-center justify-center text-slate-400"> ◉ </a>
                                    <div class="w-6 h-6 flex-none image-fit relative ml-5">
                                        <img alt="Midone - HTML Admin Template" class="rounded-full" src="dist/images/profile-1.jpg">
                                    </div>
                                    <div class="inbox__item--sender truncate ml-3">Fauzan Alghifari</div>
                                </div>
                                <div class="w-64 sm:w-auto truncate"> <span class="inbox__item--highlight">Fauzan Alghifari Telah Mengajukan Work From Anywhere Pada Tanggal 13 November 2023</div>
                                <div class="inbox__item--time whitespace-nowrap ml-auto pl-10">13 Nov</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5 flex flex-col sm:flex-row items-center text-center sm:text-left text-slate-500">
                    <div>1 - 50 of 5,238</div>
                    <div class="sm:ml-auto mt-2 sm:mt-0">Last notification: 36 minutes ago</div>
                </div>
            </div>
            <!-- END: Inbox Content -->
        </div>
    </div>
</div>
@endsection