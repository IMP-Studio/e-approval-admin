@extends('layouts.master')

@section('content')
<div class="content">
    <div class="intro-y flex items-center h-10">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Kehadiran
        </h2>
        <a href="" class="ml-auto flex items-center text-primary"> <i data-lucide="refresh-ccw"
            class="w-4 h-4 mr-3"></i> Reload Data </a>
    </div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            @can('export_presences')
            <div class="dropdown" data-tw-placement="bottom-start">
                <button class="dropdown-toggle btn btn-primary px-2" aria-expanded="false" data-tw-toggle="dropdown">
                    Export <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i></span>
                </button>
                <div class="dropdown-menu w-40 items-end">
                    <ul class="dropdown-content">
                        <li>
                            <a class="dropdown-item ExcelByRange" href="javascript:;" data-tw-toggle="modal" data-tw-target="#rangeDateModal"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Range Date </a>
                        </li>
                        <li>
                            <a href="{{ route('presence.excel',['year' => $today->year]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Excel {{ $today->year }} </a>
                        </li>
                        <li>
                            <a href="{{ route('presence.excel',['year' => $today->subyear()->year ]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Excel {{ $today->year }} </a>
                        </li>
                    </ul>
                </div>
            </div>
            @endcan
            <div class="hidden md:block mx-auto text-slate-500"></div>
            <div class="w-full sm:w-auto mt-3 mr-2 sm:mt-0 sm:ml-0 md:mr-0 sm:ml-2">
                <div class="w-24 relative text-slate-500">
                    <div class="text-center"> <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#datepicker-dataTable-modal" class="btn btn-primary">Filter Date</a> </div>
                </div>                
            </div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="search" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="myTable" class="table table-report">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">No</th>
                        <th class="text-center whitespace-nowrap">Date</th>
                        <th class="text-center whitespace-nowrap">Username</th>
                        <th class="text-center whitespace-nowrap">Position</th>
                        <th class="text-center whitespace-nowrap">Jenis Kehadiran</th>
                        <th class="text-center whitespace-nowrap">Entry time</th>
                        <th class="text-center whitespace-nowrap" data-orderable="false">Actions</th>
                    </tr>
                    
                </thead>
                <tbody> 
                    {{-- jika merubak struktur table ini pastikan untuk memperbarui juga di dalam script filter date range di js --}}
                    @foreach ($presenceData as $item)
                    <tr class="intro-x h-16">
                        <td class="w-4 text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="text-center dateStandup">
                            {{ $item->date }}
                        </td>
                        <td class="w-50 text-center">
                            {{ $item->user->name }}
                        </td>
                        <td class="w-50 text-center">
                            {{ $item->user->employee->position->name }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->entry_time }}
                        </td>
                        <td class="table-report__action w-46">
                            <div class="flex justify-center items-center">
                                @if($item->category === 'WFO')
                                <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-wfo"
                                   data-avatar="{{ $item->user->employee->avatar }}"
                                   data-gender="{{ $item->user->employee->gender }}"
                                   data-firstname="{{ $item->user->employee->first_name }}"
                                   data-LastName="{{ $item->user->employee->last_name }}"
                                   data-stafId="{{ $item->user->employee->id_number }}"
                                   data-Category="{{ ($item->category === 'work_trip' ? 'Work Trip' : $item->category) }}"
                                   data-Position="{{ $item->user->employee->position->name }}"
                                   data-entryTime="{{ $item->entry_time }}"
                                   data-exitTime="{{ $item->exit_time }}"
                                   href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-wfo">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>
                                @elseif($item->category == 'telework')
                                    <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-telework"
                                    data-avatar="{{ $item->user->employee->avatar }}"
                                    data-gender="{{ $item->user->employee->gender }}"
                                    data-firstname="{{ $item->user->employee->first_name }}"
                                    data-LastName="{{ $item->user->employee->last_name }}"
                                    data-stafId="{{ $item->user->employee->id_number }}"
                                    data-Category="{{ ($item->category === 'work_trip' ? 'Work Trip' : $item->category) }}"
                                    data-Position="{{ $item->user->employee->position->name }}"
                                    data-teleCategory="{{ $item->telework->telework_category }}"
                                    data-tempoEntry="{{ $item->temporary_entry_time }}"
                                    data-catDesc="{{ $item->telework->category_description }}"
                                    href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-telework">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                    </a>
                                @elseif($item->category == 'work_trip')
                                    <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-worktrip"
                                    data-avatar="{{ $item->user->employee->avatar }}"
                                    data-gender="{{ $item->user->employee->gender }}"
                                    data-firstname="{{ $item->user->employee->first_name }}"
                                    data-LastName="{{ $item->user->employee->last_name }}"
                                    data-stafId="{{ $item->user->employee->id_number }}"
                                    data-Category="{{ ($item->category === 'work_trip' ? 'Work Trip' : $item->category) }}"
                                    data-Position="{{ $item->user->employee->position->name }}"
                                    data-startDate="{{ $item->start_date }}"
                                    data-endDate="{{ $item->end_date }}"
                                    data-enrtyDate="{{ $item->entry_date }}"
                                    data-file="{{ $item->worktrip->file }}"
                                    href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-worktrip">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                    </a>
                                @elseif ($item->category == 'leave')
                                <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-leave"
                                    data-file="{{ $item->file }}"
                                    data-avatar="{{ $item->user->employee->avatar }}"
                                    data-gender="{{ $item->user->employee->gender }}"
                                    data-firstname="{{ $item->user->employee->first_name }}"
                                    data-LastName="{{ $item->user->employee->last_name }}"
                                    data-stafId="{{ $item->user->employee->id_number }}"
                                    data-Category="{{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}"
                                    data-Position="{{ $item->user->employee->position->name }}"
                                    data-startDate="{{ $item->start_date }}"
                                    data-endDate="{{ $item->end_date }}"
                                    data-entryDate="{{ $item->entry_date }}"
                                    data-typeLeave="{{ $item->leavedetail->typeOfLeave->leave_name }}"
                                    data-typeDesc="{{ $item->leavedetail->description_leave }}"
                                    data-submisDate="{{ $item->submission_date }}"
                                    data-totalDays="{{ $item->total_leave_days }}" href="javascript:;"
                                    data-tw-toggle="modal" data-tw-target="#show-modal-search-leave">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>
                                @endif
                
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Filter data by range date modal --}}
<div id="datepicker-dataTable-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Filter by Date</h2> 
            </div> <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3 input-daterange">
                <div class="col-span-12 sm:col-span-6"> <label for="modal-datepicker-1" class="form-label">From</label> <input type="text" name="from_date" id="start_date" class="datepicker form-control" data-single-mode="true"> </div>
                <div class="col-span-12 sm:col-span-6"> <label for="modal-datepicker-2" class="form-label">To</label> <input type="text" name="to_date" id="end_date" class="datepicker form-control" data-single-mode="true"> </div>
            </div> <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer text-right"> <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button> <button type="button" id="filter_button" class="btn btn-primary w-20">Submit</button> </div> <!-- END: Modal Footer -->
        </div>
    </div>
</div>
{{-- Filter data by range date modal end --}}

{{-- date range excel modal  --}}
<div id="rangeDateModal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route("presence.excelByRange") }}" id="exportExcelByRangeID">
                @csrf
                @method('POST')
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="calendar" class="w-16 h-16 text-success mx-auto mt-3"></i>
                        <div class="text-xl mt-2 mb-2">Date range</div>
                        {{-- <div class="text-slate-500 mt-2 mb-2" id="subjuduldelete-confirmation">
                            Please input date range
                        </div> --}}
                        <div class="relative w-56 mx-auto">
                            <div
                                class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                <i data-lucide="calendar" class="w-4 h-4"></i> </div> <input name="startDate" type="text"
                                class="datepicker form-control pl-12 startdateinput" data-single-mode="true">
                        </div>
                        <div class="relative w-56 mx-auto">
                            <div
                                class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                <i data-lucide="calendar" class="w-4 h-4"></i> </div> <input name="endDate" type="text"
                                class="datepicker form-control pl-12 enddateinput" data-single-mode="true">
                        </div>
                        {{-- <input type="text" data-daterange="true" class="datepicker form-control w-56 block mx-auto" value="">  --}}
                    </div>
                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-success w-24">Export</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- date range excel modal  end --}}

{{-- delete modal live search --}}
<div id="delete-confirmation-modal-search-presence" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-form-search-presence" method="POST" action="">
                @csrf
                @method('delete')
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2" id="subjuduldelete-confirmation-presence">
                        </div>
                        <input name="validName" id="crud-form-2" type="text" class="form-control w-full" placeholder="User name" required>
                    </div>
                    <div class="px-5 pb-8 text-center">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-danger w-24">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- delete modal live search end --}}

{{-- detail modal attendance search wfo --}}
<div id="show-modal-search-wfo" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-lg mx-auto">Detail Kehadiran</h2>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mx-auto">
                    <div class="w-24 h-24 image-fit zoom-in">
                        <img id="show-modal-image-wfo" class="tooltip rounded-full" src="">
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Firstname :</label>
                    <input disabled id="Show-firstname-wfo" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Lastname :</label>
                    <input disabled id="Show-LastName-wfo" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Staff Id :</label>
                    <input disabled id="Show-StafId-wfo" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Position :</label>
                    <input disabled id="Show-Posisi-wfo" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Category :</label>
                    <input disabled id="Show-Category-wfo" type="text" class="form-control capitalize" value="">
                </div>
                {{-- if wfo --}}
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Entry Time  :</label>
                    <input disabled id="Show-EntryTime-wfo" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label  class="text-xs">Exit Time  :</label>
                    <input disabled id="Show-categoryWfo2" type="text" class="form-control" value="">
                </div>
            </div>
        </div>
    </div>
</div>
{{-- detail modal attendance search Wfo --}}

{{-- detail modal attendance search TeleWork --}}
<div id="show-modal-search-telework" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-lg mx-auto">Detail Kehadiran</h2>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mx-auto">
                    <div class="w-24 h-24 image-fit zoom-in">
                        <img id="show-modal-image-tele" class="tooltip rounded-full" src="">
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Firstname :</label>
                    <input disabled id="Show-firstname-tele" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Lastname :</label>
                    <input disabled id="Show-LastName-tele" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Staff Id :</label>
                    <input disabled id="Show-StafId-tele" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Position :</label>
                    <input disabled id="Show-Posisi-tele" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Category :</label>
                    <input disabled id="Show-Category-tele" type="text" class="form-control capitalize" value="">
                </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Telework Category  :</label>
                        <input disabled id="Show-Telecat-tele" type="text" class="form-control capitalize" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6" id="divCatDesc">
                        <label class="text-xs">Category Description :</label>
                        <input disabled id="Show-CatDesc" type="text" class="form-control capitalize" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Temporary Entry Time  :</label>
                        <input disabled id="Show-TempoEntry-tele" type="text" class="form-control capitalize" value="">
                    </div>
            </div>
        </div>
    </div>
</div>
{{-- detail modal attendance search TeleWork end--}}

{{-- detail modal attendance search work_trip --}}
<div id="show-modal-search-worktrip" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-lg mx-auto">Detail Kehadiran</h2>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mx-auto">
                    <div class="w-24 h-24 image-fit zoom-in">
                        <img id="show-modal-image-work" class="tooltip rounded-full" src="">
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Firstname :</label>
                    <input disabled id="Show-firstname-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Lastname :</label>
                    <input disabled id="Show-LastName-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Gender :</label>
                    <input disabled id="Show-gender-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Staff Id :</label>
                    <input disabled id="Show-StafId-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Position :</label>
                    <input disabled id="Show-Posisi-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Category :</label>
                    <input disabled id="Show-Category-work" type="text" class="form-control capitalize" value="">
                </div>
                <div class="col-span-12 sm:col-span-12" id="detail-file">
                    <div class="flex items-center p-5 form-control">
                        <div class="file"> <div class="w-6 file__icon file__icon--directory"></div></div>
                        <div class="ml-4">
                            <p id="filename" class="font-medium"></p> 
                            <div id="file-size" class="text-slate-500 text-xs mt-0.5"></div>
                        </div>
                        <div class="dropdown ml-auto">
                            <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i> </a>
                            <div class="dropdown-menu w-40">
                                <ul class="dropdown-content">
                                    <li>
                                        <a id="put-href-file" href="" class="dropdown-item "> <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- detail modal attendance search work_trip end--}}

{{-- detail modal attendance search leave --}}
<div id="show-modal-search-leave" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="font-medium text-lg mx-auto">Detail Kehadiran</h2>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12 mx-auto">
                    <div class="w-24 h-24 image-fit zoom-in">
                        <img id="show-modal-image-leave" class="tooltip rounded-full" src="">
                    </div>
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Firstname :</label>
                    <input disabled id="Show-firstname-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Lastname :</label>
                    <input disabled id="Show-LastName-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Staff Id :</label>
                    <input disabled id="Show-StafId-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Position :</label>
                    <input disabled id="Show-Posisi-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Category :</label>
                    <input disabled id="Show-Category-leave" type="text" class="form-control capitalize" value="">
                </div>
                {{-- if leave --}}
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Type Leave :</label>
                    <input disabled id="Show-TypeLeave-leave" type="text" class="form-control capitalize" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Submission Date :</label>
                    <input disabled id="Show-SubmissDesch-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label  class="text-xs">Start Date :</label>
                    <input disabled id="Show-StartDate-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">End Date :</label>
                    <input disabled id="Show-EndDate-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Total Leave Days :</label>
                    <input disabled id="Show-TotalDesch-leave" type="text" class="form-control" value="Days">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Entry Date :</label>
                    <input disabled id="Show-EntryDate-leave" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-12">
                    <label class="text-xs">Type Description :</label>
                    <textarea disabled id="Show-TypeDesc-leave" type="text" class="form-control" ></textarea>
                </div>
                <div class="col-span-12 sm:col-span-12" id="detail-file-leave">
                    <div class="flex items-center p-5 form-control">
                        <div class="file"> <div class="w-6 file__icon file__icon--directory"></div></div>
                        <div class="ml-4">
                            <p id="filename-leave" class="font-medium"></p> 
                            <div id="file-size-leave" class="text-slate-500 text-xs mt-0.5"></div>
                        </div>
                        <div class="dropdown ml-auto">
                            <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i> </a>
                            <div class="dropdown-menu w-40">
                                <ul class="dropdown-content">
                                    <li>
                                        <a id="put-href-file-leave" href="" class="dropdown-item "> <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
{{-- detail modal attendance search leave end--}}

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        var dateCells = document.querySelectorAll('.dateStandup');
        dateCells.forEach(function (cell) {
            var originalDate = cell.textContent.trim();
            var formattedDate = new Date(originalDate).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
            cell.textContent = formattedDate;
        });
    });
    // search & date filter
    jQuery(document).ready(function($) {
        var dataTable = new DataTable('#myTable', {
            buttons: ['showSelected'],
            dom: 'rtip',
            select: true,
            pageLength: 5,
            border: false,
        });

        $('#search').on('keyup', function() {
            dataTable.search($(this).val()).draw();
        });

        // filter data by date range
        $('#filter_button').on('click', function() {
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            dataTable.clear().destroy();

            $.ajax({
                url: '{{ route('presence') }}',
                type: 'GET',
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                dataType: 'json',
                success: function(data) {
                    var htmlContent = '';
                    $('tbody').empty();

                    data.forEach(function(item, index) {
                        var formattedDate = new Date(item.date).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });

                        htmlContent += '<tr class="intro-x h-16">';
                        htmlContent += '<td class="w-4 text-center">' + (index +
                            1) + '</td>';
                        htmlContent += '<td class="text-center capitalize">' + formattedDate + '</td>';
                        htmlContent += '<td class="w-50 text-center">' + item.user
                            .name + '</td>';
                        htmlContent += '<td class="w-50 text-center">' + item.user
                            .employee.position.name + '</td>';
                        htmlContent += '<td class="text-center capitalize">' + (item
                            .category === 'work_trip' ? 'Work Trip' : item
                            .category) + '</td>';
                        htmlContent += '<td class="text-center capitalize">' + item
                            .entry_time + '</td>';

                        htmlContent += '<td class="table-report__action w-46">';
                        htmlContent +=
                            '<div class="flex justify-center items-center">';

                        if (item.category === 'WFO') {
                            htmlContent +=
                                '<a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-wfo" ' +
                                (item.user.employee.avatar ? 'data-avatar="' + item.user.employee.avatar + '"' : '') +
                                'data-gender="' + item.user.employee.gender + '" ' +
                                'data-firstname="' + item.user.employee.first_name +
                                '" ' +
                                'data-LastName="' + item.user.employee.last_name +
                                '" ' +
                                'data-stafId="' + item.user.employee.id_number +
                                '" ' +
                                'data-Category="' + (item.category === 'work_trip' ?
                                    'Work Trip' : item.category) + '" ' +
                                'data-Position="' + item.user.employee.position
                                .name + '" ' +
                                'data-entryTime="' + item.entry_time + '" ' +
                                'data-exitTime="' + item.exit_time + '" ' +
                                'href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-wfo">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>Detail</a>';
                        } else if (item.category === 'telework') {
                            htmlContent +=
                                '<a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-telework" ' +
                                (item.user.employee.avatar ? 'data-avatar="' + item.user.employee.avatar + '"' : '') +
                                'data-gender="' + item.user.employee.gender + '" ' +
                                'data-firstname="' + item.user.employee.first_name +
                                '" ' +
                                'data-LastName="' + item.user.employee.last_name +
                                '" ' +
                                'data-stafId="' + item.user.employee.id_number +
                                '" ' +
                                'data-Category="' + (item.category === 'work_trip' ?
                                    'Work Trip' : item.category) + '" ' +
                                'data-Position="' + item.user.employee.position
                                .name + '" ' +
                                'data-teleCategory="' + item.telework
                                .telework_category + '" ' +
                                'data-tempoEntry="' + item.temporary_entry_time +
                                '" ' +
                                'data-catDesc="' + item.telework
                                .category_description + '" ' +
                                'href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-telework">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail</a>';
                        } else if (item.category === 'work_trip') {
                            htmlContent +=
                                '<a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-worktrip" ' +
                                (item.user.employee.avatar ? 'data-avatar="' + item.user.employee.avatar + '"' : '') +
                                'data-gender="' + item.user.employee.gender + '" ' +
                                (item.worktrip.file ? 'data-file="' + item.worktrip.file + '"' : '') +
                                'data-firstname="' + item.user.employee.first_name +
                                '" ' +
                                'data-LastName="' + item.user.employee.last_name +
                                '" ' +
                                'data-stafId="' + item.user.employee.id_number +
                                '" ' +
                                'data-Category="' + (item.category === 'work_trip' ?
                                    'Work Trip' : item.category) + '" ' +
                                'data-Position="' + item.user.employee.position
                                .name + '" ' +
                                'data-startDate="' + item.start_date + '" ' +
                                'data-endDate="' + item.end_date + '" ' +
                                'data-enrtyDate="' + item.entry_date + '" ' +
                                'href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-worktrip">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail</a>';
                        } else if (item.category === 'leave') {
                            htmlContent +=
                                '<a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-leave" ' +
                                (item.user.employee.avatar ? 'data-avatar="' + item.user.employee.avatar + '"' : '') +
                                'data-gender="' + item.user.employee.gender + '" ' +
                                (item.file ? 'data-file="' + item.file + '"' : 'data-file ') +
                                'data-firstname="' + item.user.employee.first_name +
                                '" ' +
                                'data-LastName="' + item.user.employee.last_name +
                                '" ' +
                                'data-stafId="' + item.user.employee.id_number +
                                '" ' +
                                'data-Category="' + (item.category === 'work_trip' ?
                                    'Work Trip' : item.category) + '" ' +
                                'data-Position="' + item.user.employee.position
                                .name + '" ' +
                                'data-startDate="' + item.start_date + '" ' +
                                'data-endDate="' + item.end_date + '" ' +
                                'data-entryDate="' + item.entry_date + '" ' +
                                'data-typeLeave="' + item.leavedetail.typeofleave.leave_name + '" ' +
                                'data-typeDesc="' + item.leavedetail
                                .description_leave + '" ' +
                                'data-submisDate="' + item.submission_date + '" ' +
                                'data-totalDays="' + item.total_leave_days + '" ' +
                                'href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-leave">' +
                                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>Detail</a>';
                        }

                        htmlContent += '</div>';
                        htmlContent += '</td>';
                        htmlContent += '</tr>';
                    });
  
                    $('tbody').html(htmlContent);

                    dataTable = new DataTable('#myTable', {
                        buttons: ['showSelected'],
                        dom: 'rtip',
                        select: true,
                        pageLength: 5,
                        border: false,
                    });

                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });



    $(document).on("click", ".delete-modal-search-presence", function () {
        var DeleteModalid = $(this).attr('data-id');
        var DeleteModalName = $(this).attr('data-name');


    
        var formAction;
        formAction = '{{ route("presence.destroy", ":id") }}'.replace(':id', DeleteModalid);

        $("#subjuduldelete-confirmation-presence").text('Please type the username "'+ DeleteModalName +'" of the data to confrim.');
        $("#delete-form-search-presence").attr('action', formAction);
    });

    $(document).on("click", ".show-attendance-modal-search-wfo", function () {
        var ShowGender = $(this).attr('data-gender');
        var showAvatar = $(this).attr('data-avatar');
        var ShowFirstname = $(this).attr('data-firstname');
        var ShowLastName = $(this).attr('data-LastName');
        var ShowStafId = $(this).attr('data-stafId');
        var ShowPosisi = $(this).attr('data-Position');
        var ShowCategory = $(this).attr('data-Category');
        var ShowEntryTime = $(this).attr('data-entryTime');
        var ShowExitTime = $(this).attr('data-exitTime');


        console.log(ShowFirstname);
        var imgSrc;
        if(showAvatar){
            imgSrc = '{{ asset('storage/') }}/' + showAvatar;
        }else if(ShowGender == 'male'){
            imgSrc = '{{ asset('images/default-boy.jpg') }}';
        }else if(ShowGender == 'female'){
            imgSrc = '{{ asset('images/default-women.jpg') }}';
        };


        $("#show-modal-image-wfo").attr('src', imgSrc);
        $("#Show-firstname-wfo").attr('value', ShowFirstname);
        $("#Show-LastName-wfo").attr('value', ShowLastName);
        $("#Show-StafId-wfo").attr('value', ShowStafId);
        $("#Show-Posisi-wfo").attr('value', ShowPosisi);
        $("#Show-Category-wfo").attr('value', ShowCategory);
        $("#Show-EntryTime-wfo").attr('value',ShowEntryTime + ' WIB');
        $("#Show-categoryWfo2").attr('value',ShowExitTime + ' WIB');
    });

    // telework modal
    $(document).on("click", ".show-attendance-modal-search-telework", function () {
        var ShowGender = $(this).attr('data-gender');
        var showAvatar = $(this).attr('data-avatar');
        var ShowFirstname = $(this).attr('data-firstname');
        var ShowLastName = $(this).attr('data-LastName');
        var ShowStafId = $(this).attr('data-stafId');
        var ShowPosisi = $(this).attr('data-Position');
        var ShowCategory = $(this).attr('data-Category');
        var ShowTeleCat = $(this).attr('data-teleCategory');
        var ShowTempoEntry = $(this).attr('data-tempoEntry');
        var ShowCatDesc = $(this).attr('data-catDesc');



        console.log(ShowFirstname);
        var imgSrc;
        if(showAvatar){
            imgSrc = '{{ asset('storage/') }}/' + showAvatar;
        }else if(ShowGender == 'male'){
            imgSrc = '{{ asset('images/default-boy.jpg') }}';
        }else if(ShowGender == 'female'){
            imgSrc = '{{ asset('images/default-women.jpg') }}';
        };

        if (ShowCatDesc) {
        $("#Show-CatDesc").attr('value', ShowCatDesc);
        $("#Show-CatDesc").parent().show();
        } else {
        $("#divCatDesc").hide();
        }

        $("#show-modal-image-tele").attr('src', imgSrc);
        $("#Show-firstname-tele").attr('value', ShowFirstname);
        $("#Show-LastName-tele").attr('value', ShowLastName);
        $("#Show-StafId-tele").attr('value', ShowStafId);
        $("#Show-Posisi-tele").attr('value', ShowPosisi);
        $("#Show-Category-tele").attr('value', ShowCategory);
        $("#Show-Telecat-tele").attr('value',ShowTeleCat);
        $("#Show-TempoEntry-tele").attr('value',ShowTempoEntry);
    });

    // work trip modal
    $(document).on("click", ".show-attendance-modal-search-worktrip", function () {
        var ShowGender = $(this).attr('data-gender');
        var showAvatar = $(this).attr('data-avatar');
        var ShowFirstname = $(this).attr('data-firstname');
        var ShowLastName = $(this).attr('data-LastName');
        var ShowStafId = $(this).attr('data-stafId');
        var ShowPosisi = $(this).attr('data-Position');
        var ShowCategory = $(this).attr('data-Category');

        var fileUrl = $(this).attr('data-file');
        var deleteUrl = fileUrl.split('/').pop();

        var regex = /^\d+/;
        var fileName = deleteUrl.replace(regex, '');
        
        if (fileUrl) {
            var fileInput = '{{ asset('storage/') }}/' + fileUrl + ''
            
            $("#put-href-file").attr('href', fileInput);
            $("#filename").text(fileName);

            jQuery(document).ready(function($) {
                $.ajax({
                    type: "HEAD",
                    url: fileInput,
                    success: function (message, text, jqXhr) {
                        var fileSize = jqXhr.getResponseHeader('Content-Length');
                        var fileSizeKB = (fileSize / 1024).toFixed(2) + ' KB';
                        $("#file-size").text(fileSizeKB);
                    },
                });
            })
        }

        var imgSrc;
        if(showAvatar){
            imgSrc = '{{ asset('storage/') }}/' + showAvatar;
        }else if(ShowGender == 'male'){
            imgSrc = '{{ asset('images/default-boy.jpg') }}';
        }else if(ShowGender == 'female'){
            imgSrc = '{{ asset('images/default-women.jpg') }}';
        };


        $("#show-modal-image-work").attr('src', imgSrc);
        $("#Show-firstname-work").attr('value', ShowFirstname);
        $("#Show-LastName-work").attr('value', ShowLastName);
        $("#Show-gender-work").attr('value', ShowGender);
        $("#Show-StafId-work").attr('value', ShowStafId);
        $("#Show-Posisi-work").attr('value', ShowPosisi);
        $("#Show-Category-work").attr('value', ShowCategory);
    });

    // leave modal
    $(document).on("click", ".show-attendance-modal-search-leave", function () {
        var ShowGender = $(this).attr('data-gender');
        var showAvatar = $(this).attr('data-avatar');
        var ShowFirstname = $(this).attr('data-firstname');
        var ShowLastName = $(this).attr('data-LastName');
        var ShowStafId = $(this).attr('data-stafId');
        var ShowPosisi = $(this).attr('data-Position');
        var ShowCategory = $(this).attr('data-Category');
        var ShowStartDate = $(this).attr('data-startDate');
        var ShowEndDate = $(this).attr('data-endDate');
        var ShowEntryDate = $(this).attr('data-entryDate');
        var ShowtypeLeave = $(this).attr('data-typeLeave');
        var ShowTypeDesc = $(this).attr('data-typeDesc');
        var ShowSubmisDate = $(this).attr('data-submisDate');
        var ShowTotalDays = $(this).attr('data-totalDays');

        var fileUrl = $(this).attr('data-file');
        var deleteUrl = fileUrl.split('/').pop();

        var regex = /^\d+/;
        var fileName = deleteUrl.replace(regex, '');
        
        if (fileUrl) {
            $("#detail-file-leave").show();
            var fileInput = '{{ asset('storage/') }}/' + fileUrl + ''
            
            $("#put-href-file-leave").attr('href', fileInput);
            $("#filename-leave").text(fileName);

            jQuery(document).ready(function($) {
                $.ajax({
                    type: "HEAD",
                    url: fileInput,
                    success: function (message, text, jqXhr) {
                        var fileSize = jqXhr.getResponseHeader('Content-Length');
                        var fileSizeKB = (fileSize / 1024).toFixed(2) + ' KB';
                        $("#file-size-leave").text(fileSizeKB);
                    },
                });
            })
        }else{
            $("#detail-file-leave").hide();
        }

        var imgSrc;
        if(showAvatar){
            imgSrc = '{{ asset('storage/') }}/' + showAvatar;
        }else if(ShowGender == 'male'){
            imgSrc = '{{ asset('images/default-boy.jpg') }}';
        }else if(ShowGender == 'female'){
            imgSrc = '{{ asset('images/default-women.jpg') }}';
        };


        $("#show-modal-image-leave").attr('src', imgSrc);
        $("#Show-firstname-leave").attr('value', ShowFirstname);
        $("#Show-LastName-leave").attr('value', ShowLastName);
        $("#Show-StafId-leave").attr('value', ShowStafId);
        $("#Show-Posisi-leave").attr('value', ShowPosisi);
        $("#Show-Category-leave").attr('value', ShowCategory);
        $("#Show-StartDate-leave").attr('value', ShowStartDate);
        $("#Show-EndDate-leave").attr('value', ShowEndDate);
        $("#Show-EntryDate-leave").attr('value', ShowEntryDate);
        $("#Show-TypeLeave-leave").attr('value', ShowtypeLeave);
        $("#Show-TypeDesc-leave").text(ShowTypeDesc);
        $("#Show-SubmissDesch-leave").attr('value', ShowSubmisDate);
        $("#Show-TotalDesch-leave").attr('value', ShowTotalDays + ' Days');
    });
</script>
@endsection
