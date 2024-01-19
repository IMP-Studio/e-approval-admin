@extends('layouts.master')
@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Leave
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <div class="hidden md:block mx-auto text-slate-500"></div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchLeave">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table id="myTable" class="table table-report mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th class="text-center whitespace-nowrap">Start Date</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Name</th>
                            <th class="text-center whitespace-nowrap">Position</th>
                            <th class="text-center whitespace-nowrap">Jensi Kehadiran</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap" data-orderable="false">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leavekData as $item)
                            <tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="w-40 text-center capitalize dateleave">
                                    {{ $item->leave->start_date }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->user->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->user->employee->division->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->category }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->leave->statusCommit->first()->status }}
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a data-leaveHtid="{{ $item->leave->statusCommit->first()->id }}" data-messageLeaveHt="{{ $item->user->name }} {{ $item->category }}" class="flex items-center text-success mr-3 approve_leave_Ht"
                                            data-Positionid="" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#modal-apprv-leave-search">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Approve
                                        </a>
                                        <a class="flex items-center text-warning mr-3 show-modal-search-leave"
                                            data-avatar="{{ $item->user->employee->avatar }}"
                                            data-gender="{{ $item->user->employee->gender }}"
                                            data-firstname="{{ $item->user->employee->first_name }}"
                                            data-LastName="{{ $item->user->employee->last_name }}"
                                            data-stafId="{{ $item->user->employee->id_number }}"
                                            data-Category="{{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}"
                                            data-Position="{{ $item->user->employee->position->name }}"
                                            data-startDate="{{ $item->leave->start_date }}" 
                                            data-endDate="{{ $item->leave->end_date }}"
                                            data-entryDate="{{ $item->leave->entry_date }}" 
                                            data-typeLeave="{{ $item->leave->leavedetail->description_leave }}"
                                            data-typeDesc="{{ $item->leave->leavedetail->typeofleave->leave_name }}"
                                            data-submisDate="{{ $item->leave->submission_date }}"
                                            data-file="{{ $item->leave->file }}" 
                                            data-totalDays="{{ $item->leave->leavedetail->days }}" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#show-modal-leaveht">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>
                                        @can('reject_presence')                                        
                                        <a data-rejectLeaveHrid="{{ $item->leave->statusCommit->first()->id }}" data-rejectmessageLeaveHt="{{ $item->user->name }} {{ $item->category }}" class="flex items-center text-danger reject_leave_Hr" data-id=""
                                            data-name="" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#reject-confirmation-leave-modal">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Reject
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- modal approve --}}
    <div id="modal-apprv-leave-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="approve-leave-dataHt" method="POST" action="">
                    @csrf
                    @method('put')
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-success mx-auto mt-3"></i>
                            <div class="text-3xl mt-5">Are you sure?</div>
                            <div class="text-slate-500 mt-2" id="subjuduldelete-confirmation">
                                Are you sure you want to approve this absence request?
                            </div>
                            <input name="description" id="crud-form-2" type="text" class="form-control w-full"
                            placeholder="description" required>
                            <input hidden name="message" type="text" id="messageLeave-approveHt">
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button type="submit" class="btn btn-success w-24">Approve</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- moda approve end --}}

    {{-- modal rejected --}}
    <div id="reject-confirmation-leave-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reject-leave-dataHr" method="POST" action="">
                    @csrf
                    @method('put')
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                            <div class="text-3xl mt-5">Are you sure?</div>
                            <div class="text-slate-500 mt-2" id="subjuduldelete-confirmation">
                                Are you sure you want to reject this absence request?
                            </div>
                            <input name="description" id="crud-form-2" type="text" class="form-control w-full"
                                placeholder="description" required>
                            <input hidden name="message" type="text" id="messageLeave-rejectHr">
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button type="submit" class="btn btn-danger w-24">reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- moda rejected end --}}

     {{-- detail modal attendance search leave --}}
     <div id="show-modal-leaveht" class="modal" tabindex="-1" aria-hidden="true">
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
                        <input disabled id="Show-Category-leave" type="text" class="form-control capitalize"
                            value="">
                    </div>
                    {{-- if leave --}}
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Type Leave :</label>
                        <input disabled id="Show-TypeLeave-leave" type="text" class="form-control capitalize"
                            value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Type :</label>
                        <input disabled id="Show-TypeDesc-leave" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Submission Date :</label>
                        <input disabled id="Show-SubmissDate-leave" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Start Date :</label>
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
                    <div id="detaildiv-file" class="col-span-12 sm:col-span-12" hidden>
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
    {{-- detail modal attendance search leave end --}}

    <script type="text/javascript">
        // format date
        document.addEventListener('DOMContentLoaded', function () {
            var dateCells = document.querySelectorAll('.dateleave');
            dateCells.forEach(function (cell) {
                var originalDate = cell.textContent.trim();
                var formattedDate = new Date(originalDate).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
                cell.textContent = formattedDate;
            });
        });
        function formatDateString(dateString) {
            var dateObj = new Date(dateString);
            return dateObj.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
        }

       // search
        jQuery(document).ready(function($) {
            var dataTable = new DataTable('#myTable', {
                buttons: ['showSelected'],
                dom: 'rtip',
                select: true, 
                pageLength: 5,
                border: false,
            });

            $('#searchLeave').on('keyup', function() {
                dataTable.search($(this).val()).draw();
            });
        });

        // approve
        $(document).on("click", ".approve_leave_Ht", function() {
            var ApproveWkModalid = $(this).attr('data-leaveHtid');
            var ApproveWkModalMessage = $(this).attr('data-messageLeaveHt');

            var formAction;
            formAction = '{{ route('approvehr.approvedLeaveHr', ':id') }}'.replace(':id', ApproveWkModalid);

            $("#approve-leave-dataHt").attr('action', formAction);
            $("#messageLeave-approveHt").attr('value', ApproveWkModalMessage);


        });

        // leave modal detail
        $(document).on("click", ".show-modal-search-leave", function() {
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
            var fileName = fileUrl.split('/').pop();
           
            if (fileUrl && fileUrl.trim() !== '') {
                $('#detaildiv-file').removeAttr('hidden');
                var fileInput = '{{ asset('storage/') }}/' + fileUrl + ''
                console.log(fileInput);

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
            }else{
                $('#detaildiv-file').attr('hidden', 'hidden');
            }

            var formattedStartDate = formatDateString(ShowStartDate);
            var formattedEndDate = formatDateString(ShowEndDate);
            var formattedEntryDate = formatDateString(ShowEntryDate);
            var formattedSubmisDate = formatDateString(ShowSubmisDate);

            var imgSrc;
            if (showAvatar) {
                imgSrc = '{{ asset('storage/') }}/' + showAvatar;
            } else if (ShowGender == 'male') {
                imgSrc = '{{ asset('images/default-boy.jpg') }}';
            } else if (ShowGender == 'female') {
                imgSrc = '{{ asset('images/default-women.jpg') }}';
            };


            $("#show-modal-image-leave").attr('src', imgSrc);
            $("#Show-firstname-leave").attr('value', ShowFirstname);
            $("#Show-LastName-leave").attr('value', ShowLastName);
            $("#Show-StafId-leave").attr('value', ShowStafId);
            $("#Show-Posisi-leave").attr('value', ShowPosisi);
            $("#Show-Category-leave").attr('value', ShowCategory);
            $("#Show-StartDate-leave").attr('value', formattedStartDate);
            $("#Show-EndDate-leave").attr('value', formattedEndDate);
            $("#Show-EntryDate-leave").attr('value', formattedEntryDate);
            $("#Show-TypeLeave-leave").attr('value', ShowtypeLeave);
            $("#Show-TypeDesc-leave").attr('value', ShowTypeDesc);
            $("#Show-SubmissDate-leave").attr('value', formattedSubmisDate);
            $("#Show-TotalDesch-leave").attr('value', ShowTotalDays + ' Days');
        });

        $(document).on("click", ".reject_leave_Hr", function() {
            var rejectWkModalid = $(this).attr('data-rejectLeaveHrid');
            var rejectWkModalMessage = $(this).attr('data-rejectmessageLeaveHt');

            var formAction;
            formAction = '{{ route('approvehr.rejectLeaveHr', ':id') }}'.replace(':id', rejectWkModalid);

            $("#reject-leave-dataHr").attr('action', formAction);
            $("#messageLeave-rejectHr").attr('value', rejectWkModalMessage);
        });
    </script>
@endsection
