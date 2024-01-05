@extends('layouts.master')

@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Standup
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            @can('export_standups')
            <div class="dropdown" data-tw-placement="bottom-start">
                <button class="dropdown-toggle btn btn-primary px-2" aria-expanded="false" data-tw-toggle="modal" data-tw-target="#exportstandup">
                    Export <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i></span>
                </button>
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
                    <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="search">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="myTable" class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">No</th>
                        <th class="text-center whitespace-nowrap">Date</th>
                        <th class="text-center whitespace-nowrap">Username</th>
                        <th class="text-center whitespace-nowrap">Position</th>
                        <th class="text-center whitespace-nowrap">Project name</th>
                        <th class="text-center whitespace-nowrap">Doing</th>
                        <th class="text-center whitespace-nowrap">Blocker</th>
                        <th class="text-center whitespace-nowrap" data-orderable=>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- jika merubak struktur table ini pastikan untuk memperbarui juga di dalam script filter date range di js --}}
                    @foreach ($standup_today as $item)
                    <tr class="intro-x h-16">
                        <td class="w-4 text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="w-20 text-center dateStandup">
                            {{ $item->presence->date }}
                        </td>
                        <td class="w-50 text-center">
                            {{ $item->user->name }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->user->employee->position->name }}
                        </td>
                        <td class="text-start">
                            {{ $item->project->name }}
                        </td>
                        <td class="text-center">
                            {{ $item->doing }}
                        </td>
                        <td class="w-32 text-center text-warning">
                            {{ $item->blocker ? $item->blocker : '-' }}
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center text-warning  mr-3 detail-standup-modal-search" href="javascript:;" data-tw-toggle="modal" data-standupProject="{{ $item->project->name }}" data-StandupDetailId="{{ $item->id }}" data-StandupDetailName="{{ $item->user->name }}" data-StandupDetailDone="{{ $item->done }}" data-StandupDetailDoing="{{ $item->doing }}" data-StandupDetailBlocker="{{ $item->blocker }}" data-tw-target="#detail-modal-search">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>
                                <a class="flex items-center text-danger delete-standup-modal-search" data-DeleteStandupId="{{ $item->id }}" data-DeleteStandupName="{{ $item->user->name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                </a>
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
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Filter by Date</h2>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3 input-daterange">
                <div class="col-span-12 sm:col-span-6"> <label for="modal-datepicker-1"
                        class="form-label">From</label> <input type="text" name="from_date" id="start_date"
                        class="datepicker form-control" data-single-mode="true"> 
                </div>
                <div class="col-span-12 sm:col-span-6"> <label for="modal-datepicker-2"
                        class="form-label">To</label> <input type="text" name="to_date" id="end_date"
                        class="datepicker form-control" data-single-mode="true"> 
                </div>
            </div>
            <div class="modal-footer text-right"> <button type="button" data-tw-dismiss="modal"
                    class="btn btn-outline-secondary w-20 mr-1">Cancel</button> <button type="button"
                    id="filter_button" class="btn btn-primary w-20">Submit</button> 
            </div>
        </div>
    </div>
</div>
{{-- Filter data by range date modal end --}}
{{-- export modal --}}
 <!-- BEGIN: Modal Content -->
<div id="exportstandup" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- BEGIN: Modal Header -->
            <div class="modal-header">
                <h2 class="font-medium text-base mr-auto">Export excel standup</h2>
                <div class="dropdown sm:hidden"> <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i> </a>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            <li> <a href="javascript:;" class="dropdown-item"> <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs </a> </li>
                        </ul>
                    </div>
                </div>
            </div> <!-- END: Modal Header -->
            <!-- BEGIN: Modal Body -->
            <div class="text-center mt-2">
                @foreach ($months as $index => $month)                    
                    <div class="dropdown inline-block" data-tw-placement="top-start"> <button class="dropdown-toggle btn btn-primary w-32 mr-1 mb-2" aria-expanded="false" data-tw-toggle="dropdown">{{ $month }}</button>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li>
                                    <a href="{{ route('standup.excel',['year' => $today->year, 'month' => $index + 1]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> {{ $month }} {{ $today->year }} </a>
                                </li>
                                <li>
                                    <a href="{{ route('standup.excel',['year' => $subYear, 'month' => $index + 1]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> {{ $month }} {{ $subYear }} </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endforeach
            </div>
            <!-- END: Modal Body -->
            <!-- BEGIN: Modal Footer -->
            <div class="modal-footer"> <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button></div> <!-- END: Modal Footer -->
        </div>
    </div>
</div> 
<!-- END: Modal Content -->
{{-- end export modal --}}

{{-- detail modal --}}
<div id="detail-modal-search" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="font-medium text-base mx-auto" id="show-name-standup-search"></h1>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12">
                    <label for="modal-form-1" class="form-label">Done :</label>
                    <textarea disabled name="" class="form-control" id="show-done-standup-search" rows="3"></textarea>
                </div>
                <div class="col-span-12">
                    <label for="modal-form-2" class="form-label">Doing :</label>
                    <textarea disabled name="" class="form-control" id="show-doing-standup-search" rows="3"></textarea>
                </div>
                <div class="col-span-12" id="show-blocker-standup-search">
                </div>
                <div class="col-span-12">
                    <label class="form-label">Project :</label>
                    <input disabled id="ProjectList" type="text" class="form-control">
                </div>
            </div>
        </div>
    </div>
</div>
{{-- detail modal end --}}

{{-- delete modal --}}
<div id="delete-confirmation-modal-search" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="delete-form-search" method="POST" action="">
                @csrf
                @method('delete')
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2" id="subjuduldelete-confirmation">
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
{{-- delete modal end --}}

<script type="text/javascript">
     document.addEventListener('DOMContentLoaded', function () {
        var dateCells = document.querySelectorAll('.dateStandup');
        dateCells.forEach(function (cell) {
            var originalDate = cell.textContent.trim();
            var formattedDate = new Date(originalDate).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
            cell.textContent = formattedDate;
        });
    });

     // search
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
                url: '{{ route('standup') }}',
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
                        var formattedDate = new Date(item.presence.date).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });

                        htmlContent += '<tr class="intro-x h-16">';
                        htmlContent += '<td class="w-4 text-center">' + (index + 1) + '</td>';
                        htmlContent += '<td class="w-20 text-center">' + formattedDate + '</td>';
                        htmlContent += '<td class="w-50 text-center">' + item.user.name + '</td>';
                        htmlContent += '<td class="text-center capitalize">' + item.user.employee.position.name + '</td>';
                        htmlContent += '<td class="text-start">' + item.project.name + '</td>';
                        htmlContent += '<td class="text-center">' + item.doing + '</td>';
                        htmlContent += '<td class="w-32 text-center text-warning">' + (item.blocker ? item.blocker : '-') + '</td>';
                        htmlContent += '<td class="table-report__action w-56">';
                        htmlContent += '<div class="flex justify-center items-center">';
                        htmlContent += '<a class="flex items-center text-warning  mr-3 detail-standup-modal-search" href="javascript:;" data-tw-toggle="modal" data-standupProject="' + item.project.name + '" data-StandupDetailId="' + item.id + '" data-StandupDetailName="' + item.user.name + '" data-StandupDetailDone="' + item.done + '" data-StandupDetailDoing="' + item.doing + '" data-StandupDetailBlocker="' + (item.blocker ? item.blocker : '-') + '" data-tw-target="#detail-modal-search">';
                        htmlContent += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail</a>';
                        htmlContent += '<a class="flex items-center text-danger delete-standup-modal-search" data-DeleteStandupId="' + item.id + '" data-DeleteStandupName="' + item.user.name + '" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">';
                        htmlContent += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="eye" data-lucide="eye" class="lucide lucide-eye w-4 h-4 mr-1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Delete</a>';
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

    $(document).on("click", ".detail-standup-modal-search", function () {
        var showitemid = $(this).attr('data-StandupDetailId');
        var showName = $(this).attr('data-StandupDetailName');
        var ShowDone = $(this).attr('data-StandupDetailDone');
        var ShowDoing= $(this).attr('data-StandupDetailDoing');
        var ShowBlocker = $(this).attr('data-StandupDetailBlocker');
        var ShowProject = $(this).attr('data-standupProject');


        var BlockerContent;
        if(ShowBlocker){
            BlockerContent = '<label for="modal-form-2" class="form-label">Blocker :</label> <textarea disabled name="" class="form-control" id="" rows="3">'+ ShowBlocker +'</textarea>';
        }

        $("#show-blocker-standup-search").html(BlockerContent);
        $("#show-name-standup-search").text('Detail Standup '+ showName);
        $("#show-done-standup-search").text(ShowDone);
        $("#show-doing-standup-search").text(ShowDoing);
        $("#ProjectList").attr('value', ShowProject);
    });

         $(document).on("click", ".delete-standup-modal-search", function () {
            var DeleteStandupModalid = $(this).attr('data-DeleteStandupId');
            var DeleteStandupModalName = $(this).attr('data-DeleteStandupName');

            var formAction;
            formAction = '{{ route('standup.destroy',":id") }}'.replace(':id',  DeleteStandupModalid);

            $("#subjuduldelete-confirmation").text('Please type the name "'+ DeleteStandupModalName +'" of the data to confrim.');
            $("#delete-form-search").attr('action', formAction);
        });
 </script>
@endsection