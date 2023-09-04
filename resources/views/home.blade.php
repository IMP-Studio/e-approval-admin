@extends('layouts.master')

@section('content')
<div class="content">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-9">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            General Report
                        </h2>
                        <a href="" class="ml-auto flex items-center text-primary"> <i data-lucide="refresh-ccw"
                                class="w-4 h-4 mr-3"></i> Reload Data </a>
                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-5">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="shopping-cart" class="report-box__icon text-primary"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $attendance_percentage < 50 ? 'bg-danger' : ($attendance_percentage < 70 ? 'bg-pending' : 'bg-success') }} tooltip cursor-pointer"
                                                title="{{ $attendance_percentage < 50 ? '50% Lower than last month' : ($attendance_percentage < 70 ? 'Below 70%' : '33% Higher than last month') }}">
                                                {{ $attendance_percentage }}% <i
                                                    data-lucide="chevron-{{ $attendance_percentage < 50 ? 'down' : 'up' }}"
                                                    class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ $presence_today }}</div>
                                    <div class="text-base text-slate-500 mt-1">Check In</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="credit-card" class="report-box__icon text-pending"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $telework_percentage < 50 ? 'bg-success' : ($telework_percentage < 70 ? 'bg-pending' : 'bg-danger') }} tooltip cursor-pointer"
                                                title="{{ $telework_percentage < 50 ? '50% Lower than last month' : ($telework_percentage < 70 ? 'Below 70%' : '33% Higher than last month') }}">
                                                {{ $telework_percentage }}% <i
                                                    data-lucide="chevron-{{ $telework_percentage < 50 ? 'down' : 'up' }}"
                                                    class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ $telework_today->count() }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Telework</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="monitor" class="report-box__icon text-warning"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $workTrip_percentage < 50 ? 'bg-success' : ($workTrip_percentage < 70 ? 'bg-pending' : 'bg-danger') }} tooltip cursor-pointer"
                                                title="{{ $workTrip_percentage < 50 ? '50% Lower than last month' : ($workTrip_percentage < 70 ? 'Below 70%' : '33% Higher than last month') }}">
                                                {{ $workTrip_percentage }}% <i
                                                    data-lucide="chevron-{{ $workTrip_percentage < 50 ? 'down' : 'up' }}"
                                                    class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ $workTrip_today->count() }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Work Trip</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="user" class="report-box__icon text-success"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $leave_percentage < 50 ? 'bg-success' : ($leave_percentage < 70 ? 'bg-pending' : 'bg-danger') }} tooltip cursor-pointer"
                                                title="{{ $leave_percentage < 50 ? '50% Lower than last month' : ($leave_percentage < 70 ? 'Below 70%' : '33% Higher than last month') }}">
                                                {{ $leave_percentage }}% <i
                                                    data-lucide="chevron-{{ $leave_percentage < 50 ? 'down' : 'up' }}"
                                                    class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ $leave_today->count() }}</div>
                                    <div class="text-base text-slate-500 mt-1">Leave</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BEGIN: Vertical Bar Chart Report -->
                <div class="col-span-12 xl:col-span-8 mt-8">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            This Month's Attendance Report
                        </h2>
                        <div class="sm:ml-auto mt-3 sm:mt-0 relative text-slate-500">
                            <i data-lucide="calendar" class="w-4 h-4 z-10 absolute my-auto inset-y-0 ml-3 left-0"></i>
                            <input type="text" class="datepicker form-control sm:w-56 box pl-10">
                        </div>
                    </div>
                    <div class="intro-y box p-5 mt-12 sm:mt-5">
                        <div class="preview">
                            <div class="">
                                <canvas id="bar-chart" class="mt-6 mb-6"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: Vertical Bar Chart Report -->

                <!-- BEGIN: Pie Chart Report -->
                <div class="col-span-12 sm:col-span-6 lg:col-span-4 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            This Year's Attendance Report
                        </h2>
                    </div>
                    <div id="chart" class="intro-y box p-5 mt-5">
                        <div class="mt-3">
                            <div class="h-[213px]">
                                <canvas id="pie-chart"></canvas>
                            </div>
                        </div>
                        <div class="w-52 sm:w-auto mx-auto mt-8">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-success rounded-full mr-3"></div>
                                <span class="truncate">WFO</span>
                                <span class="font-medium ml-auto">{{ round(($wfo_yearly / ($wfo_yearly + $telework_yearly + $workTrip_yearly + $leave_yearly)) * 100, 1) }}%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                <span class="truncate">Telework</span>
                                <span class="font-medium ml-auto">{{ round(($telework_yearly / ($wfo_yearly + $telework_yearly + $workTrip_yearly + $leave_yearly)) * 100, 1) }}%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                <span class="truncate">Work Trip</span>
                                <span class="font-medium ml-auto">{{ round(($workTrip_yearly / ($wfo_yearly + $telework_yearly + $workTrip_yearly + $leave_yearly)) * 100, 1) }}%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                <span class="truncate">Leave</span>
                                <span class="font-medium ml-auto">{{ round(($leave_yearly / ($wfo_yearly + $telework_yearly + $workTrip_yearly + $leave_yearly)) * 100, 1) }}%</span>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- END: Pie Chart Report -->

                <div class="col-span-12 sm:col-span-6 lg:col-span-4 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            This Year's Attendance Report
                        </h2>
                    </div>
                    <div id="chart" class="intro-y box p-5 mt-5">
                        <div class="mt-3">
                            <div class="h-[213px]">
                                <canvas id="report-donut-chart"></canvas>
                            </div>
                        </div>
                        <div class="w-52 sm:w-auto mx-auto mt-8">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                <span class="truncate">WFO</span> <span
                                    class="font-medium ml-auto">62%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                <span class="truncate">Telework</span> <span
                                    class="font-medium ml-auto">33%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                <span class="truncate">Work Trip</span> <span
                                    class="font-medium ml-auto">10%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                <span class="truncate">Leave</span> <span
                                    class="font-medium ml-auto">10%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6 lg:col-span-4 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            This Year's Attendance Report
                        </h2>
                    </div>
                    <div id="chart" class="intro-y box p-5 mt-5">
                        <div class="mt-3">
                            <div class="h-[213px]">
                                <canvas id="report-donut-chart"></canvas>
                            </div>
                        </div>
                        <div class="w-52 sm:w-auto mx-auto mt-8">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                <span class="truncate">WFO</span> <span
                                    class="font-medium ml-auto">62%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                <span class="truncate">Telework</span> <span
                                    class="font-medium ml-auto">33%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                <span class="truncate">Work Trip</span> <span
                                    class="font-medium ml-auto">10%</span>
                            </div>
                            <div class="flex items-center mt-2">
                                <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                <span class="truncate">Leave</span> <span
                                    class="font-medium ml-auto">10%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- END: Sales Report -->
                <!-- BEGIN: Official Store -->
                <div class="col-span-12 xl:col-span-8 mt-6">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            Official Store
                        </h2>
                        <div class="sm:ml-auto mt-3 sm:mt-0 relative text-slate-500">
                            <i data-lucide="map-pin" class="w-4 h-4 z-10 absolute my-auto inset-y-0 ml-3 left-0"></i>
                            <input type="text" class="form-control sm:w-56 box pl-10" placeholder="Filter by city">
                        </div>
                    </div>
                    <div class="intro-y box p-5 mt-12 sm:mt-5">
                        <div>250 Official stores in 21 countries, click the marker to see location details.</div>
                        <div class="report-maps mt-5 bg-slate-200 rounded-md" data-center="-6.2425342, 106.8626478"
                            data-sources="/dist/json/location.json"></div>
                    </div>
                </div>
                <!-- END: Official Store -->
                <!-- BEGIN: Weekly Best Sellers -->
                <div class="col-span-12 xl:col-span-4 mt-6">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            Weekly Best Sellers
                        </h2>
                    </div>
                    <div class="mt-5">
                        <div class="intro-y">
                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-10.jpg">
                                </div>
                                <div class="ml-4 mr-auto">
                                    <div class="font-medium">Johnny Depp</div>
                                    <div class="text-slate-500 text-xs mt-0.5">28 May 2020</div>
                                </div>
                                <div
                                    class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                    137 Sales</div>
                            </div>
                        </div>
                        <div class="intro-y">
                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-13.jpg">
                                </div>
                                <div class="ml-4 mr-auto">
                                    <div class="font-medium">Arnold Schwarzenegger</div>
                                    <div class="text-slate-500 text-xs mt-0.5">25 August 2022</div>
                                </div>
                                <div
                                    class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                    137 Sales</div>
                            </div>
                        </div>
                        <div class="intro-y">
                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-1.jpg">
                                </div>
                                <div class="ml-4 mr-auto">
                                    <div class="font-medium">Kate Winslet</div>
                                    <div class="text-slate-500 text-xs mt-0.5">18 March 2022</div>
                                </div>
                                <div
                                    class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                    137 Sales</div>
                            </div>
                        </div>
                        <div class="intro-y">
                            <div class="box px-4 py-4 mb-3 flex items-center zoom-in">
                                <div class="w-10 h-10 flex-none image-fit rounded-md overflow-hidden">
                                    <img alt="Midone - HTML Admin Template" src="dist/images/profile-5.jpg">
                                </div>
                                <div class="ml-4 mr-auto">
                                    <div class="font-medium">Johnny Depp</div>
                                    <div class="text-slate-500 text-xs mt-0.5">10 February 2022</div>
                                </div>
                                <div
                                    class="py-1 px-2 rounded-full text-xs bg-success text-white cursor-pointer font-medium">
                                    137 Sales</div>
                            </div>
                        </div>
                        <a href=""
                            class="intro-y w-full block text-center rounded-md py-4 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">View
                            More</a>
                    </div>
                </div>
                <!-- END: Weekly Best Sellers -->
                <!-- BEGIN: General Report -->
                <div class="col-span-12 grid grid-cols-12 gap-6 mt-8">
                    <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                        <div class="box p-5 zoom-in">
                            <div class="flex items-center">
                                <div class="w-2/4 flex-none">
                                    <div class="text-lg font-medium truncate">Target Sales</div>
                                    <div class="text-slate-500 mt-1">300 Sales</div>
                                </div>
                                <div class="flex-none ml-auto relative">
                                    <div class="w-[90px] h-[90px]">
                                        <canvas id="report-donut-chart-1"></canvas>
                                    </div>
                                    <div
                                        class="font-medium absolute w-full h-full flex items-center justify-center top-0 left-0">
                                        20%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                        <div class="box p-5 zoom-in">
                            <div class="flex">
                                <div class="text-lg font-medium truncate mr-3">Social Media</div>
                                <div
                                    class="py-1 px-2 flex items-center rounded-full text-xs bg-slate-100 dark:bg-darkmode-400 text-slate-500 cursor-pointer ml-auto truncate">
                                    320 Followers</div>
                            </div>
                            <div class="mt-1">
                                <div class="h-[58px]">
                                    <canvas class="simple-line-chart-1 -ml-1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                        <div class="box p-5 zoom-in">
                            <div class="flex items-center">
                                <div class="w-2/4 flex-none">
                                    <div class="text-lg font-medium truncate">New Products</div>
                                    <div class="text-slate-500 mt-1">1450 Products</div>
                                </div>
                                <div class="flex-none ml-auto relative">
                                    <div class="w-[90px] h-[90px]">
                                        <canvas id="report-donut-chart-2"></canvas>
                                    </div>
                                    <div
                                        class="font-medium absolute w-full h-full flex items-center justify-center top-0 left-0">
                                        45%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6 2xl:col-span-3 intro-y">
                        <div class="box p-5 zoom-in">
                            <div class="flex">
                                <div class="text-lg font-medium truncate mr-3">Posted Ads</div>
                                <div
                                    class="py-1 px-2 flex items-center rounded-full text-xs bg-slate-100 dark:bg-darkmode-400 text-slate-500 cursor-pointer ml-auto truncate">
                                    180 Campaign</div>
                            </div>
                            <div class="mt-1">
                                <div class="h-[58px]">
                                    <canvas class="simple-line-chart-1 -ml-1"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END: General Report -->
                <!-- BEGIN: Weekly Top Products -->
                <div class="col-span-12 mt-6">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">
                            Weekly Top Products
                        </h2>
                        <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
                            <button class="btn box flex items-center text-slate-600 dark:text-slate-300"> <i
                                    data-lucide="file-text" class="hidden sm:block w-4 h-4 mr-2"></i> Export to Excel
                            </button>
                            <button class="ml-3 btn box flex items-center text-slate-600 dark:text-slate-300"> <i
                                    data-lucide="file-text" class="hidden sm:block w-4 h-4 mr-2"></i> Export to PDF
                            </button>
                        </div>
                    </div>
                    <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
                        <table class="table table-report sm:mt-2">
                            <thead>
                                <tr>
                                    <th class="whitespace-nowrap">IMAGES</th>
                                    <th class="whitespace-nowrap">PRODUCT NAME</th>
                                    <th class="text-center whitespace-nowrap">STOCK</th>
                                    <th class="text-center whitespace-nowrap">STATUS</th>
                                    <th class="text-center whitespace-nowrap">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="intro-x">
                                    <td class="w-40">
                                        <div class="flex">
                                            <div class="w-10 h-10 image-fit zoom-in">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-5.jpg" title="Uploaded at 28 May 2020">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-12.jpg" title="Uploaded at 23 April 2022">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-1.jpg" title="Uploaded at 30 April 2020">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="" class="font-medium whitespace-nowrap">Samsung Q90 QLED TV</a>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">Electronic</div>
                                    </td>
                                    <td class="text-center">97</td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center text-success"> <i
                                                data-lucide="check-square" class="w-4 h-4 mr-2"></i> Active </div>
                                    </td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href=""> <i data-lucide="check-square"
                                                    class="w-4 h-4 mr-1"></i> Edit </a>
                                            <a class="flex items-center text-danger" href=""> <i data-lucide="trash-2"
                                                    class="w-4 h-4 mr-1"></i> Delete </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="intro-x">
                                    <td class="w-40">
                                        <div class="flex">
                                            <div class="w-10 h-10 image-fit zoom-in">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-6.jpg" title="Uploaded at 25 August 2022">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-12.jpg" title="Uploaded at 11 April 2022">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-6.jpg" title="Uploaded at 9 January 2022">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="" class="font-medium whitespace-nowrap">Samsung Q90 QLED TV</a>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">Electronic</div>
                                    </td>
                                    <td class="text-center">146</td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center text-success"> <i
                                                data-lucide="check-square" class="w-4 h-4 mr-2"></i> Active </div>
                                    </td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href=""> <i data-lucide="check-square"
                                                    class="w-4 h-4 mr-1"></i> Edit </a>
                                            <a class="flex items-center text-danger" href=""> <i data-lucide="trash-2"
                                                    class="w-4 h-4 mr-1"></i> Delete </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="intro-x">
                                    <td class="w-40">
                                        <div class="flex">
                                            <div class="w-10 h-10 image-fit zoom-in">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-8.jpg" title="Uploaded at 18 March 2022">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-7.jpg" title="Uploaded at 30 July 2022">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-1.jpg" title="Uploaded at 14 June 2022">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="" class="font-medium whitespace-nowrap">Nike Tanjun</a>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">Sport &amp; Outdoor
                                        </div>
                                    </td>
                                    <td class="text-center">61</td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center text-danger"> <i
                                                data-lucide="check-square" class="w-4 h-4 mr-2"></i> Inactive </div>
                                    </td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href=""> <i data-lucide="check-square"
                                                    class="w-4 h-4 mr-1"></i> Edit </a>
                                            <a class="flex items-center text-danger" href=""> <i data-lucide="trash-2"
                                                    class="w-4 h-4 mr-1"></i> Delete </a>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="intro-x">
                                    <td class="w-40">
                                        <div class="flex">
                                            <div class="w-10 h-10 image-fit zoom-in">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-5.jpg"
                                                    title="Uploaded at 10 February 2022">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-3.jpg" title="Uploaded at 22 June 2020">
                                            </div>
                                            <div class="w-10 h-10 image-fit zoom-in -ml-5">
                                                <img alt="Midone - HTML Admin Template" class="tooltip rounded-full"
                                                    src="dist/images/preview-2.jpg" title="Uploaded at 25 March 2021">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="" class="font-medium whitespace-nowrap">Nike Air Max 270</a>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">Sport &amp; Outdoor
                                        </div>
                                    </td>
                                    <td class="text-center">50</td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center text-success"> <i
                                                data-lucide="check-square" class="w-4 h-4 mr-2"></i> Active </div>
                                    </td>
                                    <td class="table-report__action w-56">
                                        <div class="flex justify-center items-center">
                                            <a class="flex items-center mr-3" href=""> <i data-lucide="check-square"
                                                    class="w-4 h-4 mr-1"></i> Edit </a>
                                            <a class="flex items-center text-danger" href=""> <i data-lucide="trash-2"
                                                    class="w-4 h-4 mr-1"></i> Delete </a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="intro-y flex flex-wrap sm:flex-row sm:flex-nowrap items-center mt-3">
                        <nav class="w-full sm:w-auto sm:mr-auto">
                            <ul class="pagination">
                                <li class="page-item">
                                    <a class="page-link" href="#"> <i class="w-4 h-4" data-lucide="chevrons-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#"> <i class="w-4 h-4" data-lucide="chevron-left"></i>
                                    </a>
                                </li>
                                <li class="page-item"> <a class="page-link" href="#">...</a> </li>
                                <li class="page-item"> <a class="page-link" href="#">1</a> </li>
                                <li class="page-item active"> <a class="page-link" href="#">2</a> </li>
                                <li class="page-item"> <a class="page-link" href="#">3</a> </li>
                                <li class="page-item"> <a class="page-link" href="#">...</a> </li>
                                <li class="page-item">
                                    <a class="page-link" href="#"> <i class="w-4 h-4" data-lucide="chevron-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="#"> <i class="w-4 h-4" data-lucide="chevrons-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <select class="w-20 form-select box mt-3 sm:mt-0">
                            <option>10</option>
                            <option>25</option>
                            <option>35</option>
                            <option>50</option>
                        </select>
                    </div>
                </div>
                <!-- END: Weekly Top Products -->
            </div>
        </div>
        <div class="col-span-12 2xl:col-span-3">
            <div class="2xl:border-l -mb-10 pb-10">
                <div class="2xl:pl-6 grid grid-cols-12 gap-x-6 2xl:gap-x-0 gap-y-6">
                    <!-- BEGIN: Data Personal -->
                    <div class="col-span-12 md:col-span-6 xl:col-span-4 2xl:col-span-12 mt-3 2xl:mt-8">
                        @if (count($telework_today) > 0)
                            <div class="intro-x flex items-center h-10">
                                <h2 class="text-lg font-medium truncate mr-5">
                                    Telework
                                </h2>
                            </div>
                            <div class="mt-2">
                                @foreach ($telework_today as $data)
                                <div class="intro-x">
                                    <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                        <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                            <img alt="Midone - HTML Admin Template"
                                                src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                        </div>
                                        <div class="ml-4 mr-auto">
                                            <div class="font-medium">{{ $data->user->name }}</div>
                                            @php
                                            $start_date = \Carbon\Carbon::parse($data->start_date);
                                            @endphp
                                            <div class="text-slate-500 text-xs mt-0.5">{{ $start_date->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a class="flex items-center text-success" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-{{ $data->id }}">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Show
                                        </a>
                                    </div>
                                </div>
                                <div id="show-modal-{{ $data->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="font-medium text-lg mx-auto">Detail {{ $data->user->name }}</h2>
                                            </div>
                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                                <div class="col-span-12 mx-auto">
                                                    <div class="w-24 h-24 image-fit zoom-in">
                                                    @if ($data->user->employee->avatar)
                                                        <img class="tooltip rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                    @else
                                                        <img class="tooltip rounded-full" src="{{ asset('images/user.png') }}">
                                                    @endif
                                                    </div>
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->user->employee->first_name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->user->employee->last_name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Position :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->user->employee->position->name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">ID Number :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->user->employee->id_number }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Presence :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->presence->category }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Category :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->category }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Category Description :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->category_description ? $data->category_description : '-' }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Temporary Entry Time :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{$data->presence->temporary_entry_time}}">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                {{-- <a href="" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">View More</a> --}}
                            </div>

                        @endif
                        @if (count($workTrip_today) > 0)
                            <div class="intro-x flex items-center h-10">
                                <h2 class="text-lg font-medium truncate mr-5">
                                    Work Trip
                                </h2>
                            </div>
                            <div class="mt-2">
                                @foreach ($workTrip_today as $data)
                                <div class="intro-x">
                                    <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                        <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                            <img alt="Midone - HTML Admin Template"
                                                src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                        </div>
                                        <div class="ml-4 mr-auto">
                                            <div class="font-medium">{{ $data->user->name }}</div>
                                            @php
                                            $start_date = \Carbon\Carbon::parse($data->start_date);
                                            @endphp
                                            <div class="text-slate-500 text-xs mt-0.5">{{ $start_date->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a class="flex items-center text-success" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-wt-{{ $data->id }}">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Show
                                        </a>
                                    </div>
                                </div>
                                <div id="show-modal-wt-{{ $data->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="font-medium text-lg mx-auto">Detail {{ $data->user->name }}</h2>
                                            </div>
                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                                <div class="col-span-12 mx-auto">
                                                    <div class="w-24 h-24 image-fit zoom-in">
                                                    @if ($data->user->employee->avatar)
                                                        <img class="tooltip rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                    @else
                                                        <img class="tooltip rounded-full" src="{{ asset('images/user.png') }}">
                                                    @endif
                                                    </div>
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->user->employee->first_name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->user->employee->last_name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Position :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->user->employee->position->name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">ID Number :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->user->employee->id_number }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Presence :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->presence->category === 'work_trip' ? 'Work Trip' : $data->presence->category }}
                                                    ">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Start Date :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->start_date }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">End Date :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->end_date }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Temporary Entry Time :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{$data->presence->temporary_entry_time}}">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                {{-- <a href="" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">View More</a> --}}
                            </div>
                        @endif
                        @if (count($leave_today) > 0)
                            <div class="intro-x flex items-center h-10">
                                <h2 class="text-lg font-medium truncate mr-5">
                                    Leave
                                </h2>
                            </div>
                            <div class="mt-2">
                                @foreach ($leave_today as $data)
                                <div class="intro-x">
                                    <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                        <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                            <img alt="Midone - HTML Admin Template"
                                                src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                        </div>
                                        <div class="ml-4 mr-auto">
                                            <div class="font-medium">{{ $data->user->name }}</div>
                                            @php
                                            $start_date = \Carbon\Carbon::parse($data->start_date);
                                            @endphp
                                            <div class="text-slate-500 text-xs mt-0.5">{{ $start_date->format('d M Y') }}
                                            </div>
                                        </div>
                                        <a class="flex items-center text-success" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-le-{{$data->id}}">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Show
                                        </a>
                                    </div>
                                </div>
                                <div id="show-modal-le-{{ $data->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h2 class="font-medium text-lg mx-auto">Detail {{ $data->user->name }}</h2>
                                            </div>
                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                                <div class="col-span-12 mx-auto">
                                                    <div class="w-24 h-24 image-fit zoom-in">
                                                    @if ($data->user->employee->avatar)
                                                        <img class="tooltip rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                    @else
                                                        <img class="tooltip rounded-full" src="{{ asset('images/user.png') }}">
                                                    @endif
                                                    </div>
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->user->employee->first_name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->user->employee->last_name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Position :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->user->employee->position->name }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">ID Number :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->user->employee->id_number }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Presence :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->presence->category === 'work_trip' ? 'Work Trip' : $data->presence->category }}
                                                    ">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Description :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->description }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">Start Date :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->start_date }}">
                                                </div>
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">End Date :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->end_date }}">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                {{-- <a href="" class="intro-x w-full block text-center rounded-md py-3 border border-dotted border-slate-400 dark:border-darkmode-300 text-slate-500">View More</a> --}}
                            </div>
                        @endif

                    </div>
                    <!-- END: Data Personal -->
                    <!-- BEGIN: Recent Activities -->
                    <div class="col-span-12 md:col-span-6 xl:col-span-4 2xl:col-span-12 mt-3">
                        <div class="intro-x flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5">
                                Recent Activities
                            </h2>
                            <a href="" class="ml-auto text-primary truncate">Show More</a>
                        </div>
                        <div
                            class="mt-5 relative before:block before:absolute before:w-px before:h-[85%] before:bg-slate-200 before:dark:bg-darkmode-400 before:ml-5 before:mt-5">
                            <div class="intro-x relative flex items-center mb-3">
                                <div
                                    class="before:block before:absolute before:w-20 before:h-px before:bg-slate-200 before:dark:bg-darkmode-400 before:mt-5 before:ml-5">
                                    <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                        <img alt="Midone - HTML Admin Template" src="dist/images/profile-15.jpg">
                                    </div>
                                </div>
                                <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                    <div class="flex items-center">
                                        <div class="font-medium">Tom Cruise</div>
                                        <div class="text-xs text-slate-500 ml-auto">07:00 PM</div>
                                    </div>
                                    <div class="text-slate-500 mt-1">Has joined the team</div>
                                </div>
                            </div>
                            <div class="intro-x relative flex items-center mb-3">
                                <div
                                    class="before:block before:absolute before:w-20 before:h-px before:bg-slate-200 before:dark:bg-darkmode-400 before:mt-5 before:ml-5">
                                    <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                        <img alt="Midone - HTML Admin Template" src="dist/images/profile-2.jpg">
                                    </div>
                                </div>
                                <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                    <div class="flex items-center">
                                        <div class="font-medium">Sylvester Stallone</div>
                                        <div class="text-xs text-slate-500 ml-auto">07:00 PM</div>
                                    </div>
                                    <div class="text-slate-500">
                                        <div class="mt-1">Added 3 new photos</div>
                                        <div class="flex mt-2">
                                            <div class="tooltip w-8 h-8 image-fit mr-1 zoom-in"
                                                title="Samsung Q90 QLED TV">
                                                <img alt="Midone - HTML Admin Template"
                                                    class="rounded-md border border-white"
                                                    src="dist/images/preview-13.jpg">
                                            </div>
                                            <div class="tooltip w-8 h-8 image-fit mr-1 zoom-in"
                                                title="Samsung Q90 QLED TV">
                                                <img alt="Midone - HTML Admin Template"
                                                    class="rounded-md border border-white"
                                                    src="dist/images/preview-3.jpg">
                                            </div>
                                            <div class="tooltip w-8 h-8 image-fit mr-1 zoom-in" title="Nike Tanjun">
                                                <img alt="Midone - HTML Admin Template"
                                                    class="rounded-md border border-white"
                                                    src="dist/images/preview-3.jpg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intro-x text-slate-500 text-xs text-center my-4">12 November</div>
                            <div class="intro-x relative flex items-center mb-3">
                                <div
                                    class="before:block before:absolute before:w-20 before:h-px before:bg-slate-200 before:dark:bg-darkmode-400 before:mt-5 before:ml-5">
                                    <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                        <img alt="Midone - HTML Admin Template" src="dist/images/profile-6.jpg">
                                    </div>
                                </div>
                                <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                    <div class="flex items-center">
                                        <div class="font-medium">Denzel Washington</div>
                                        <div class="text-xs text-slate-500 ml-auto">07:00 PM</div>
                                    </div>
                                    <div class="text-slate-500 mt-1">Has changed <a class="text-primary" href="">Apple
                                            MacBook Pro 13</a> price and description</div>
                                </div>
                            </div>
                            <div class="intro-x relative flex items-center mb-3">
                                <div
                                    class="before:block before:absolute before:w-20 before:h-px before:bg-slate-200 before:dark:bg-darkmode-400 before:mt-5 before:ml-5">
                                    <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                        <img alt="Midone - HTML Admin Template" src="dist/images/profile-6.jpg">
                                    </div>
                                </div>
                                <div class="box px-5 py-3 ml-4 flex-1 zoom-in">
                                    <div class="flex items-center">
                                        <div class="font-medium">Tom Cruise</div>
                                        <div class="text-xs text-slate-500 ml-auto">07:00 PM</div>
                                    </div>
                                    <div class="text-slate-500 mt-1">Has changed <a class="text-primary" href="">Samsung
                                            Galaxy S20 Ultra</a> description</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END: Recent Activities -->
                    <!-- BEGIN: Important Notes -->
                    <div
                        class="col-span-12 md:col-span-6 xl:col-span-12 xl:col-start-1 xl:row-start-1 2xl:col-start-auto 2xl:row-start-auto mt-3">
                        <div class="intro-x flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-auto">
                                Important Notes
                            </h2>
                            <button data-carousel="important-notes" data-target="prev"
                                class="tiny-slider-navigator btn px-2 border-slate-300 text-slate-600 dark:text-slate-300 mr-2">
                                <i data-lucide="chevron-left" class="w-4 h-4"></i> </button>
                            <button data-carousel="important-notes" data-target="next"
                                class="tiny-slider-navigator btn px-2 border-slate-300 text-slate-600 dark:text-slate-300 mr-2">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i> </button>
                        </div>
                        <div class="mt-5 intro-x">
                            <div class="box zoom-in">
                                <div class="tiny-slider" id="important-notes">
                                    <div class="p-5">
                                        <div class="text-base font-medium truncate">Lorem Ipsum is simply dummy text
                                        </div>
                                        <div class="text-slate-400 mt-1">20 Hours ago</div>
                                        <div class="text-slate-500 text-justify mt-1">Lorem Ipsum is simply dummy text
                                            of the printing and typesetting industry. Lorem Ipsum has been the
                                            industry's standard dummy text ever since the 1500s.</div>
                                        <div class="font-medium flex mt-5">
                                            <button type="button" class="btn btn-secondary py-1 px-2">View
                                                Notes</button>
                                            <button type="button"
                                                class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto">Dismiss</button>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <div class="text-base font-medium truncate">Lorem Ipsum is simply dummy text
                                        </div>
                                        <div class="text-slate-400 mt-1">20 Hours ago</div>
                                        <div class="text-slate-500 text-justify mt-1">Lorem Ipsum is simply dummy text
                                            of the printing and typesetting industry. Lorem Ipsum has been the
                                            industry's standard dummy text ever since the 1500s.</div>
                                        <div class="font-medium flex mt-5">
                                            <button type="button" class="btn btn-secondary py-1 px-2">View
                                                Notes</button>
                                            <button type="button"
                                                class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto">Dismiss</button>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <div class="text-base font-medium truncate">Lorem Ipsum is simply dummy text
                                        </div>
                                        <div class="text-slate-400 mt-1">20 Hours ago</div>
                                        <div class="text-slate-500 text-justify mt-1">Lorem Ipsum is simply dummy text
                                            of the printing and typesetting industry. Lorem Ipsum has been the
                                            industry's standard dummy text ever since the 1500s.</div>
                                        <div class="font-medium flex mt-5">
                                            <button type="button" class="btn btn-secondary py-1 px-2">View
                                                Notes</button>
                                            <button type="button"
                                                class="btn btn-outline-secondary py-1 px-2 ml-auto ml-auto">Dismiss</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END: Important Notes -->
                    <!-- BEGIN: Schedules -->
                    <div
                        class="col-span-12 md:col-span-6 xl:col-span-4 2xl:col-span-12 xl:col-start-1 xl:row-start-2 2xl:col-start-auto 2xl:row-start-auto mt-3">
                        <div class="intro-x flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5">
                                Schedules
                            </h2>
                            <a href="" class="ml-auto text-primary truncate flex items-center"> <i data-lucide="plus"
                                    class="w-4 h-4 mr-1"></i> Add New Schedules </a>
                        </div>
                        <div class="mt-5">
                            <div class="intro-x box">
                                <div class="p-5">
                                    <div class="flex">
                                        <i data-lucide="chevron-left" class="w-5 h-5 text-slate-500"></i>
                                        <div class="font-medium text-base mx-auto">April</div>
                                        <i data-lucide="chevron-right" class="w-5 h-5 text-slate-500"></i>
                                    </div>
                                    <div class="grid grid-cols-7 gap-4 mt-5 text-center">
                                        <div class="font-medium">Su</div>
                                        <div class="font-medium">Mo</div>
                                        <div class="font-medium">Tu</div>
                                        <div class="font-medium">We</div>
                                        <div class="font-medium">Th</div>
                                        <div class="font-medium">Fr</div>
                                        <div class="font-medium">Sa</div>
                                        <div class="py-0.5 rounded relative text-slate-500">29</div>
                                        <div class="py-0.5 rounded relative text-slate-500">30</div>
                                        <div class="py-0.5 rounded relative text-slate-500">31</div>
                                        <div class="py-0.5 rounded relative">1</div>
                                        <div class="py-0.5 rounded relative">2</div>
                                        <div class="py-0.5 rounded relative">3</div>
                                        <div class="py-0.5 rounded relative">4</div>
                                        <div class="py-0.5 rounded relative">5</div>
                                        <div class="py-0.5 bg-success/20 dark:bg-success/30 rounded relative">6</div>
                                        <div class="py-0.5 rounded relative">7</div>
                                        <div class="py-0.5 bg-primary text-white rounded relative">8</div>
                                        <div class="py-0.5 rounded relative">9</div>
                                        <div class="py-0.5 rounded relative">10</div>
                                        <div class="py-0.5 rounded relative">11</div>
                                        <div class="py-0.5 rounded relative">12</div>
                                        <div class="py-0.5 rounded relative">13</div>
                                        <div class="py-0.5 rounded relative">14</div>
                                        <div class="py-0.5 rounded relative">15</div>
                                        <div class="py-0.5 rounded relative">16</div>
                                        <div class="py-0.5 rounded relative">17</div>
                                        <div class="py-0.5 rounded relative">18</div>
                                        <div class="py-0.5 rounded relative">19</div>
                                        <div class="py-0.5 rounded relative">20</div>
                                        <div class="py-0.5 rounded relative">21</div>
                                        <div class="py-0.5 rounded relative">22</div>
                                        <div class="py-0.5 bg-pending/20 dark:bg-pending/30 rounded relative">23</div>
                                        <div class="py-0.5 rounded relative">24</div>
                                        <div class="py-0.5 rounded relative">25</div>
                                        <div class="py-0.5 rounded relative">26</div>
                                        <div class="py-0.5 bg-primary/10 dark:bg-primary/50 rounded relative">27</div>
                                        <div class="py-0.5 rounded relative">28</div>
                                        <div class="py-0.5 rounded relative">29</div>
                                        <div class="py-0.5 rounded relative">30</div>
                                        <div class="py-0.5 rounded relative text-slate-500">1</div>
                                        <div class="py-0.5 rounded relative text-slate-500">2</div>
                                        <div class="py-0.5 rounded relative text-slate-500">3</div>
                                        <div class="py-0.5 rounded relative text-slate-500">4</div>
                                        <div class="py-0.5 rounded relative text-slate-500">5</div>
                                        <div class="py-0.5 rounded relative text-slate-500">6</div>
                                        <div class="py-0.5 rounded relative text-slate-500">7</div>
                                        <div class="py-0.5 rounded relative text-slate-500">8</div>
                                        <div class="py-0.5 rounded relative text-slate-500">9</div>
                                    </div>
                                </div>
                                <div class="border-t border-slate-200/60 p-5">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                        <span class="truncate">UI/UX Workshop</span> <span
                                            class="font-medium xl:ml-auto">23th</span>
                                    </div>
                                    <div class="flex items-center mt-4">
                                        <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                        <span class="truncate">VueJs Frontend Development</span> <span
                                            class="font-medium xl:ml-auto">10th</span>
                                    </div>
                                    <div class="flex items-center mt-4">
                                        <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                        <span class="truncate">Laravel Rest API</span> <span
                                            class="font-medium xl:ml-auto">31th</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END: Schedules -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Get the canvas element
        const ctx_bar = document.getElementById('bar-chart').getContext('2d');
        const ctx_pie = document.getElementById('pie-chart').getContext('2d');

        // Set up data
        const data_bar = {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sept","Oct","Nov","Dec"],
            datasets: [{
                label: "WFO",
                barPercentage: 0.8,
                barThickness: 10,
                maxBarThickness: 8,
                minBarLength: 2,
                data: [{{ implode(', ', $wfo_data) }}],
                backgroundColor: 'rgba(13, 148, 136,0.9)'
            }, {
                label: "Telework",
                barPercentage: 0.8,
                barThickness: 10,
                maxBarThickness: 8,
                minBarLength: 2,
                data: [{{ implode(', ', $telework_data) }}],
                backgroundColor: 'rgba(22, 78, 99,0.9)'
            }, {
                label: "Work Trip",
                barPercentage: 0.8,
                barThickness: 10,
                maxBarThickness: 8,
                minBarLength: 2,
                data: [{{ implode(', ', $workTrip_data) }}],
                backgroundColor: 'rgba(245, 158, 11,0.9)'
            }, {
                label: "Leave",
                barPercentage: 0.8,
                barThickness: 10,
                maxBarThickness: 8,
                minBarLength: 2,
                data: [{{ implode(', ', $leave_data) }}],
                backgroundColor: 'rgba(217, 119, 6,0.9)'
            }]
        };

        // Set up configuration
        const options_bar = {
            scales: {
          x: {
            ticks: {
              font: {
                size: 12
              },
              color: 'rgba(100, 116, 139,0.8)'
            },
            grid: {
              display: false,
              drawBorder: false
            }
          }
        }
        };

        // Create the bar chart
        const barChart = new Chart(ctx_bar, {
            type: 'bar', // Specify the chart type
            data: data_bar, // Set the data
            options: options_bar // Set the configuration
        });



        var pieChart = new Chart(ctx_pie, {
            type: 'pie',
            data: {
                labels: ["WFO", "Telework", "Work Trip","Leave"],
                datasets: [{
                    data: [{{ $wfo_yearly }}, {{ $telework_yearly }}, {{ $workTrip_yearly }},{{ $leave_yearly }}],
                    backgroundColor: [
                        'rgba(13, 148, 136,0.9)',
                        'rgba(22, 78, 99,0.9)',
                        'rgba(245, 158, 11,0.9)',
                        'rgba(217, 119, 6,0.9)'
                    ],
                    hoverBackgroundColor: [
                        'rgba(13, 148, 136)',
                        'rgba(22, 78, 99)',
                        'rgba(245, 158, 11)',
                        'rgba(217, 119, 6)'
                    ],
                    borderWidth: 5,
                    borderColor: 'rgba(41 53 82)',
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                }
            }
        });


    </script>
@endpush
