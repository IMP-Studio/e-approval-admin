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
                            General Report {{ auth()->user()->getPermissionNames()->first() }}
                        </h2>
                        <a href="" class="ml-auto flex items-center text-primary"> <i data-lucide="refresh-ccw"
                                class="w-4 h-4 mr-3"></i> Reload Data </a>
                    </div>
                    <div class="grid grid-cols-12 gap-6 mt-5">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="calendar" class="report-box__icon text-primary"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $attendance_percentage < 50 ? 'bg-danger' : ($attendance_percentage < 70 ? 'bg-pending' : 'bg-success') }} tooltip cursor-pointer"
                                                title="{{ $attendance_percentage < 50 ? '50% Based on employees' : ($attendance_percentage < 70 ? 'Below 70% Based on employees' : '70% Based on employees') }}">
                                                {{ $attendance_percentage }}% <i
                                                    data-lucide="chevron-{{ $attendance_percentage < 50 ? 'down' : 'up' }}"
                                                    class="w-4 h-4 ml-0.5"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">{{ $presenceDataCount }}</div>
                                    <div class="text-base text-slate-500 mt-1">Check In</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-12 sm:col-span-6 xl:col-span-3 intro-y">
                            <div class="report-box zoom-in">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="home" class="report-box__icon text-pending"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $telework_percentage < 50 ? 'bg-success' : ($telework_percentage < 70 ? 'bg-pending' : 'bg-danger') }} tooltip cursor-pointer"
                                                title="{{ $telework_percentage < 50 ? '50% Lower based on check-in' : ($telework_percentage < 70 ? 'Below 70% based on check-in' : '70% Higher based on check-in') }}">
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
                                        <i data-lucide="building" class="report-box__icon text-warning"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $workTrip_percentage < 50 ? 'bg-success' : ($workTrip_percentage < 70 ? 'bg-pending' : 'bg-danger') }} tooltip cursor-pointer"
                                                title="{{ $workTrip_percentage < 50 ? '50% Lower Based on check-in' : ($workTrip_percentage < 70 ? 'Below 70%' : '70% Higher based on check-in') }}">
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
                                        <i data-lucide="logout" class="report-box__icon text-success"></i>
                                        <div class="ml-auto">
                                            <div class="report-box__indicator {{ $leave_percentage < 50 ? 'bg-success' : ($leave_percentage < 70 ? 'bg-pending' : 'bg-danger') }} tooltip cursor-pointer"
                                                title="{{ $leave_percentage < 50 ? '50% Lower based on check-in' : ($leave_percentage < 70 ? 'Below 70%' : '70% Higher based on check-in') }}">
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


                @if ($wfo_yearly > 0 || $telework_yearly > 0 || $workTrip_yearly > 0 || $leave_yearly)
                <!-- BEGIN: Allowed Stack Bar Chart Report -->
                    <div class="col-span-12 xl:col-span-8 mt-8">
                        <div class="intro-y block sm:flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5 capitalize">
                                Monthly presence report this year
                            </h2>
                            <div class="sm:ml-auto mt-3 sm:mt-0 relative text-slate-500">
                                <i data-lucide="calendar" class="w-4 h-4 z-10 absolute my-auto inset-y-0 ml-3 left-0"></i>
                                <input type="text" class="datepicker form-control sm:w-56 box pl-10">
                            </div>
                        </div>
                        <div class="intro-y box p-5 mt-12 sm:mt-5">
                            <div class="preview">
                                <div class="">
                                    <canvas id="allowed-stack-bar-chart" class="mt-6 mb-6"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END: Allowed Stack Bar Chart Report -->

                <!-- BEGIN: Pie Chart Report -->
                    <div class="col-span-12 sm:col-span-6 lg:col-span-4 mt-8">
                        <div class="intro-y flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5 capitalize">
                                Presence Report This Year
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
                @endif

                @if ($telework_rejected > 0 || $workTrip_rejected > 0 || $leave_rejected > 0)
                <!-- START DONUT CHART REJECTED -->
                    <div class="col-span-12 sm:col-span-6 lg:col-span-4 mt-8">
                        <div class="intro-y flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5">
                                Presence Rejected This Year
                            </h2>
                        </div>
                        <div id="chart" class="intro-y box p-5 mt-5">
                            <div class="mt-3">
                                <div class="h-[213px]">
                                    <canvas id="donut-chart"></canvas>
                                </div>
                            </div>
                            <div class="w-52 sm:w-auto mx-auto mt-8">
                                @if ($telework_rejected > 0)
                                    <div class="flex items-center mt-2">
                                        <div class="w-2 h-2 bg-primary rounded-full mr-3"></div>
                                        <span class="truncate">Telework</span> <span
                                            class="font-medium ml-auto">{{ round(($telework_rejected / ( $telework_rejected + $workTrip_rejected + $leave_rejected)) * 100, 1) }}%</span>
                                    </div>
                                @endif
                                @if ($workTrip_rejected > 0)
                                    <div class="flex items-center mt-2">
                                        <div class="w-2 h-2 bg-warning rounded-full mr-3"></div>
                                        <span class="truncate">Work Trip</span> <span
                                            class="font-medium ml-auto">{{ round(($workTrip_rejected / ( $workTrip_rejected + $telework_rejected + $leave_rejected)) * 100, 1) }}%</span>
                                    </div>
                                @endif
                                @if ($leave_rejected > 0)
                                    <div class="flex items-center mt-2">
                                        <div class="w-2 h-2 bg-pending rounded-full mr-3"></div>
                                        <span class="truncate">Leave</span> <span
                                            class="font-medium ml-auto">{{ round(($leave_rejected / ( $leave_rejected + $workTrip_rejected + $telework_rejected)) * 100, 1) }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                <!-- END DONUT CHART REJECTED -->

                <!-- START STACK BAR CHART REJECTED -->
                    <div class="col-span-12 xl:col-span-8 mt-8">
                        <div class="intro-y block sm:flex items-center h-10">
                            <h2 class="text-lg font-medium truncate mr-5 capitalize">
                                Monthly presence report this year (Rejected)
                            </h2>
                        </div>
                        <div class="intro-y box p-5 mt-12 sm:mt-5">
                            <div class="preview">
                                <div class="">
                                    <canvas id="rejected-stack-bar-chart" class="mt-6 mb-6"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- END STACK BAR CHART REJECTED -->
                @endif
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
                                    @if ($data->user && $data->user->employee)
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    @if ($data->user->employee->avatar)
                                                        <img class="rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                    @elseif($data->user->employee->gender == 'male')
                                                        <img class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                    @elseif($data->user->employee->gender == 'female')
                                                        <img class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                    @endif
                                                </div>
                                                <div class="ml-4 mr-auto overflow-hidden">
                                                    <div class="font-medium truncate">{{ $data->user->name }}</div>
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
                                                                <img class="rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                            @elseif($data->user->employee->gender == 'male')
                                                                <img class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                            @elseif($data->user->employee->gender == 'female')
                                                                <img class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
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
                                                            <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $data->telework_category }}">
                                                        </div>
                                                        @if ($data->category_description)
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Category Description :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->category_description }}">
                                                        </div>
                                                        @endif
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Temporary Entry Time :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{$data->presence->temporary_entry_time}}">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
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
                                    @if ($data->user && $data->user->employee)
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    @if ($data->user->employee->avatar)
                                                        <img class="rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                    @elseif($data->user->employee->gender == 'male')
                                                        <img class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                    @elseif($data->user->employee->gender == 'female')
                                                        <img class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                    @endif
                                                </div>
                                                <div class="ml-4 mr-auto overflow-hidden">
                                                    <div class="font-medium truncate">{{ $data->user->name }}sssssssssd</div>
                                                    @php
                                                    $start_date = \Carbon\Carbon::parse($data->start_date);
                                                    @endphp
                                                    <div class="text-slate-500 text-xs mt-0.5">{{ $start_date->format('d M Y') }}
                                                    </div>
                                                </div>
                                                <a class="flex items-center text-success" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-wt-{{ $data->id }}">
                                                    <i data-lucide="eye" class="w-4 h-4 ml-1"></i> Show
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
                                                                @elseif($data->user->employee->gender == 'male')
                                                                    <img class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                                @elseif($data->user->employee->gender == 'female')
                                                                    <img class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->user->employee->first_name }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $data->user->employee->last_name }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Staff Id :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $data->user->employee->id_number }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Position :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $data->user->employee->position->name }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Category :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->presence->category === 'work_trip' ? ' Work Trip' : $data->presence->category }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Start Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->start_date }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">End Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->end_date }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Entry Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->entry_date }}">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
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
                                    @if ($data->user && $data->user->employee)
                                        <div class="intro-x">
                                            <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                <div class="w-10 h-10 flex-none image-fit rounded-full overflow-hidden">
                                                    @if ($data->user->employee->avatar)
                                                        <img class="rounded-full" src="{{ asset('storage/'.$data->user->employee->avatar) }}">
                                                    @elseif($data->user->employee->gender == 'male')
                                                        <img class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                    @elseif($data->user->employee->gender == 'female')
                                                        <img class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                    @endif
                                                </div>
                                                <div class="ml-4 mr-auto overflow-hidden">
                                                    <div class="font-medium truncate">{{ $data->user->name }}</div>
                                                    @php
                                                        $start_date = \Carbon\Carbon::parse($data->start_date);
                                                    @endphp
                                                    <div class="text-slate-500 text-xs mt-0.5">{{ $start_date->format('d M Y') }}</div>
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
                                                                @elseif($data->user->employee->gender == 'male')
                                                                    <img class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                                @elseif($data->user->employee->gender == 'female')
                                                                    <img class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->user->employee->first_name }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $data->user->employee->last_name }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Staff Id :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $data->user->employee->id_number }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-2" class="text-xs">Position :</label>
                                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $data->user->employee->position->name }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Category :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->presence->category }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Type Leave :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $data->type }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Type Description :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->type_description }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Submission Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->submission_date }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Start Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->start_date }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">End Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->end_date }}">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Total Leave Days :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->total_leave_days }} Days">
                                                        </div>
                                                        <div class="col-span-12 sm:col-span-6">
                                                            <label for="modal-form-1" class="text-xs">Entry Date :</label>
                                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $data->entry_date }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <!-- BEGIN: List WFO Today -->
                    @if (count($wfo_today) > 0)
                        <div class="col-span-12 md:col-span-6 xl:col-span-4 2xl:col-span-12 mt-3">
                            <div class="intro-y">
                                <h2 class="text-lg font-medium truncate mr-5">
                                    Work From Office
                                </h2>
                            </div>
                            <div class="intro-y chat grid grid-cols-12 gap-5 mt-5">
                                <!-- BEGIN: Chat Side Menu -->
                                <div class="col-span-12 lg:col-span-8 2xl:col-span-12">
                                    <div class="chat__chat-list overflow-y-auto scrollbar-hidden pr-1 pt-1">
                                        @foreach ($wfo_today as $item)
                                            @if ($item->user && $item->user->employee)
                                                <div class="overflow-hidden">
                                                    <div class="box px-5 py-3 mb-3 flex items-center zoom-in">
                                                        <div class="w-10 h-10 flex-none image-fit">
                                                            @if ($item->user->employee->avatar)
                                                                <img class="rounded-full" src="{{ asset('storage/'.$item->user->employee->avatar) }}">
                                                            @elseif($item->user->employee->gender == 'male')
                                                                <img class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                            @elseif($item->user->employee->gender == 'female')
                                                                <img class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                            @endif
                                                            <div class="w-3 h-3 bg-success absolute right-0 bottom-0 rounded-full z-50 border-2 border-white dark:border-darkmode-600"></div>
                                                        </div>
                                                        <div class="ml-4 mr-auto overflow-hidden">
                                                            <div class="font-medium truncate">{{ $item->user->name }}</div>
                                                            @php
                                                                $entry_time = \Carbon\Carbon::parse($item->entry_time);
                                                            @endphp
                                                            <div class="text-slate-500 text-xs mt-0.5">{{ $entry_time->format('d M Y') }}
                                                            </div>
                                                        </div>
                                                        <a class="flex items-center text-success" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-wfo-{{$item->id}}">
                                                            <i data-lucide="eye" class="w-4 h-4 ml-2"></i> Show
                                                        </a>
                                                    </div>
                                                </div>
                                                <div id="show-modal-wfo-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h2 class="font-medium text-lg mx-auto">Detail {{ $item->user->name }}</h2>
                                                            </div>
                                                            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                                                <div class="col-span-12 mx-auto">
                                                                    <div class="w-24 h-24 image-fit zoom-in">
                                                                        @if ($item->user->employee->avatar)
                                                                            <img class="tooltip rounded-full" src="{{ asset('storage/'.$item->user->employee->avatar) }}">
                                                                        @elseif($item->user->employee->gender == 'male')
                                                                            <img class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                                                        @elseif($item->user->employee->gender == 'female')
                                                                            <img class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                                    <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->user->employee->first_name }}">
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->last_name }}">
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-2" class="text-xs">Staff Id :</label>
                                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->id_number }}">
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-2" class="text-xs">Position :</label>
                                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->position->name }}">
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-1" class="text-xs">Category :</label>
                                                                    <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}">
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-2" class="text-xs">Entry Time  :</label>
                                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->entry_time }} WIB">
                                                                </div>
                                                                <div class="col-span-12 sm:col-span-6">
                                                                    <label for="modal-form-2" class="text-xs">Exit Time  :</label>
                                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->exit_time }} WIB">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <!-- END: Chat Side Menu -->
                            </div>
                        </div>
                    @endif
                    <!-- END: List WFO Today -->

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @if ($wfo_yearly > 0 || $telework_yearly > 0 || $workTrip_yearly > 0 || $leave_yearly)
    <script>
        // Get the canvas element
        const ctx_allowed_stack_bar = document.getElementById('allowed-stack-bar-chart').getContext('2d');
        const ctx_pie = document.getElementById('pie-chart').getContext('2d');

        const dataAllowed_stack_bar = {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sept","Oct","Nov","Dec"],
            datasets: [{
                label: "WFO",
                maxBarThickness: 20,
                data: [{{ implode(', ', $wfo_data) }}],
                backgroundColor: 'rgba(13, 148, 136,0.9)',
                hoverBackgroundColor: 'rgba(13, 148, 136)'
            }, {
                label: "Telework",
                maxBarThickness: 20,
                data: [{{ implode(', ', $telework_data) }}],
                backgroundColor: 'rgba(22, 78, 99,0.9)',
                hoverBackgroundColor: 'rgba(22, 78, 99)',
            }, {
                label: "Work Trip",
                maxBarThickness: 20,
                data: [{{ implode(', ', $workTrip_data) }}],
                backgroundColor: 'rgba(245, 158, 11,0.9)',
                hoverBackgroundColor: 'rgba(245, 158, 11)'
            }, {
                label: "Leave",
                maxBarThickness: 20,
                data: [{{ implode(', ', $leave_data) }}],
                backgroundColor: 'rgba(217, 119, 6,0.9)',
                hoverBackgroundColor: 'rgba(217, 119, 6)'
            }]
        };
        // Create the bar chart
        const allowedBarChart = new Chart(ctx_allowed_stack_bar, {
            type: 'bar', // Specify the chart type
            data: dataAllowed_stack_bar, // Set the data
            options: {
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        stacked: true
                    },

                }
            }
        });

        const pieChart = new Chart(ctx_pie, {
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
    @endif
    @if ($telework_rejected > 0 || $workTrip_rejected > 0 || $leave_rejected > 0)
    <script>
        const ctx_donut = document.getElementById('donut-chart').getContext('2d');
        const ctx_rejected_vertical_bar = document.getElementById('rejected-stack-bar-chart').getContext('2d');

        const donutChart = new Chart(ctx_donut, {
            type: 'doughnut',
            data: {
                labels: ["Telework", "Work Trip","Leave"],
                datasets: [{
                    data: [ {{ $telework_rejected }}, {{ $workTrip_rejected }},{{ $leave_rejected }}],
                    backgroundColor: [
                        'rgba(22, 78, 99,0.9)',
                        'rgba(245, 158, 11,0.9)',
                        'rgba(217, 119, 6,0.9)'
                    ],
                    hoverBackgroundColor: [
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
                },
                cutout: "70%"
            }
        });

        const dataRejected_stack_bar = {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug","Sept","Oct","Nov","Dec"],
            datasets: [{
                label: "Telework",
                maxBarThickness: 20,
                data: [{{ implode(', ', $telework_data_month_rejected) }}],
                backgroundColor: 'rgba(22, 78, 99,0.9)',
                hoverBackgroundColor: 'rgba(22,78,99)'
            }, {
                label: "Work Trip",
                maxBarThickness: 20,
                data: [{{ implode(', ', $workTrip_data_month_rejected) }}],
                backgroundColor: 'rgba(245, 158, 11, 0.9)',
                hoverBackgroundColor: 'rgba(245,158,11)'
            }, {
                label: "Leave",
                maxBarThickness: 20,
                data: [{{ implode(', ', $leave_data_month_rejected) }}],
                backgroundColor: 'rgba(217, 119, 6, 0.9)',
                hoverBackgroundColor: 'rgba(217,119,6)'
            }]
        };
        const stackBarChart = new Chart(ctx_rejected_vertical_bar, {
            type: 'bar', // Specify the chart type
            data: dataRejected_stack_bar, // Set the data
            options: {
                scales: {
                    x: {
                        stacked: true,
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        stacked: true
                    },

                }
            } // Set the configuration
        });
    </script>
    @endif
@endpush
