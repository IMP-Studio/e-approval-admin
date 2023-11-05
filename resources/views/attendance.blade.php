@extends('layouts.master')

@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Kehadiran
    </h2>
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
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" id="search" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">No</th>
                        <th class="text-center whitespace-nowrap">Image</th>
                        <th class="text-center whitespace-nowrap">Username</th>
                        <th class="text-center whitespace-nowrap">Entry time</th>
                        <th class="text-center whitespace-nowrap">Jenis Kehadiran</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody> 
                    @foreach ($presenceData as $item)
                    <tr class="intro-x h-16">
                        <td class="w-4 text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="flex justify-center align-center">
                            <div class="w-12 h-12 image-fit zoom-in">
                                @if ($item->user->employee->avatar)
                                    <img data-action="zoom" class="rounded-full" src="{{ asset('storage/'.$item->user->employee->avatar) }}">
                                @elseif($item->user->employee->gender == 'male')
                                    <img data-action="zoom" class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                @elseif($item->user->employee->gender == 'female')
                                    <img data-action="zoom" class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                @endif
                            </div>
                        </td>
                        <td class="w-50 text-center">
                            {{ $item->user->name }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->entry_time }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}
                        </td>
                        <td class="table-report__action w-56">
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
                                    href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-worktrip">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                    </a>
                                @elseif ($item->category == 'leave')
                                    <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-leave" data-avatar="{{ $item->user->employee->avatar }}" data-gender="{{ $item->user->employee->gender }}" data-firstname="{{ $item->user->employee->first_name }}" data-LastName="{{ $item->user->employee->last_name }}" data-stafId="{{ $item->user->employee->id_number }}" data-Category="{{ ($item->category === 'work_trip' ? 'Work Trip' : $item->category) }}" data-Position="{{ $item->user->employee->position->name }}" data-startDate="{{ $item->start_date }}" data-endDate="{{ $item->end_date }}" data-entryDate="{{ $item->entry_date }}" data-typeLeave="{{ $item->leavedetail->typeOfLeave->leave_name }}" data-typeDesc="{{ $item->leavedetail->description_leave }}" data-submisDate="{{ $item->submission_date }}" data-totalDays="{{ $item->total_leave_days }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search-leave">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                    </a>
                                @endif
                
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($presenceData->count() > 0)
            <div class="flex justify-center items-center">
                {{ $presenceData->links('pagination.custom', [
                    'paginator' => $presenceData,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                    'slider_text' => 'Showing items from {start} to {end} out of {total}',
                ]) }}
            </div>
            @else
                <h1 class="text-center">Tidak ada data absensi hari ini</h1>
            @endif
        </div>
    </div>
</div>

{{-- date range modal  --}}
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
{{-- date range modal  end --}}

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
                {{-- if work trip --}}
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Start Date :</label>
                    <input disabled id="Show-StartDate-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">End Date :</label>
                    <input disabled id="Show-EndDate-work" type="text" class="form-control" value="">
                </div>
                <div class="col-span-12 sm:col-span-6">
                    <label class="text-xs">Entry Date :</label>
                    <input disabled id="Show-EntryDate-work" type="text" class="form-control" value="">
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
            </div>
        </div>
    </div>
</div>
{{-- detail modal attendance search leave end--}}

<script type="text/javascript">
   jQuery(document).ready(function($) {
            $('#search').on('keyup', function() {
                var query = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('presence') }}',
                    data: { query: query },
                    success: function(data) {
                        $('tbody').html(data);
                    }
                });
            });
    });

    // $(document).on("click", ".ExcelByRange", function () {
    // var startDate = $(".startdateinput").val();
    // var endDate = $(".enddateinput").val();

    // var formAction = '{{ route("presence.excelByRange", ["startDate" => "start", "endDate" => "end"]) }}';
    // formAction = formAction.replace("start", startDate).replace("end", endDate);

    // $("#exportExcelByRangeID").attr('action', formAction);
    // });


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
            imgSrc = '{{ asset('storage/'.$item->user->employee->avatar) }}';
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
            imgSrc = '{{ asset('storage/'.$item->user->employee->avatar) }}';
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
        var ShowStartDate = $(this).attr('data-startDate');
        var ShowEndDate = $(this).attr('data-endDate');
        var ShowEntryDate = $(this).attr('data-enrtyDate');


        console.log(ShowFirstname);
        var imgSrc;
        if(showAvatar){
            imgSrc = '{{ asset('storage/'.$item->user->employee->avatar) }}';
        }else if(ShowGender == 'male'){
            imgSrc = '{{ asset('images/default-boy.jpg') }}';
        }else if(ShowGender == 'female'){
            imgSrc = '{{ asset('images/default-women.jpg') }}';
        };


        $("#show-modal-image-work").attr('src', imgSrc);
        $("#Show-firstname-work").attr('value', ShowFirstname);
        $("#Show-LastName-work").attr('value', ShowLastName);
        $("#Show-StafId-work").attr('value', ShowStafId);
        $("#Show-Posisi-work").attr('value', ShowPosisi);
        $("#Show-Category-work").attr('value', ShowCategory);
        $("#Show-StartDate-work").attr('value', ShowStartDate);
        $("#Show-EndDate-work").attr('value', ShowEndDate);
        $("#Show-EntryDate-work").attr('value', ShowEntryDate);
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


        console.log(ShowFirstname);
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
