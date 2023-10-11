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
                        <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchPartner">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table id="table" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Name</th>
                            <th class="text-center whitespace-nowrap">Position</th>
                            <th class="text-center whitespace-nowrap">Jensi Kehadiran</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tablePartner">
                        @foreach ($leavekData as $item)
                            <tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
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
                                        <a data-leaveHtid="{{ $item->leave->statusCommit->first()->id }}"
                                            data-messageLeaveHt="{{ $item->user->name }} {{ $item->category }}"
                                            class="flex items-center text-success mr-3 approve_leave_Ht" data-Positionid=""
                                            href="javascript:;" data-tw-toggle="modal"
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
                                            data-totalDays="{{ $item->leave->leavedetail->days }}" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#show-modal-leaveht">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>
                                        @can('reject_presence')
                                            <a data-rejectLeaveHtid="{{ $item->leave->statusCommit->first()->id }}"
                                                data-rejectmessageLeaveHt="{{ $item->user->name }} {{ $item->category }}"
                                                class="flex items-center text-danger reject_leave_Ht" data-id=""
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
                @if ($leavekData->count() > 0)
                    <div class="flex justify-center items-center">
                        {{ $leavekData->links('pagination.custom', [
                            'paginator' => $leavekData,
                            'prev_text' => 'Previous',
                            'next_text' => 'Next',
                            'slider_text' => 'Showing items from {start} to {end} out of {total}',
                        ]) }}
                    </div>
                @endif
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
                            <input hidden name="message" type="text" id="messageLeave-approveHt">
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
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
                <form id="reject-leave-dataHt" method="POST" action="">
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
                            <input hidden name="message" type="text" id="messageLeave-rejectHt">
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
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
                        <input disabled id="Show-SubmissDesch-leave" type="text" class="form-control" value="">
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
                </div>
            </div>
        </div>
    </div>
    {{-- detail modal attendance search leave end --}}

    <script type="text/javascript">
        $(document).on("click", ".approve_leave_Ht", function() {
            var ApproveWkModalid = $(this).attr('data-leaveHtid');
            var ApproveWkModalMessage = $(this).attr('data-messageLeaveHt');

            var formAction;
            formAction = '{{ route('approveht.approvedLeaveHt', ':id') }}'.replace(':id', ApproveWkModalid);

            $("#approve-leave-dataHt").attr('action', formAction);
            $("#messageLeave-approveHt").attr('value', ApproveWkModalMessage);


        });

        // leave modal
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


            console.log(ShowFirstname);
            var imgSrc;
            if (showAvatar) {
                imgSrc = '{{ asset('storage/' . $item->user->employee->avatar) }}';
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
            $("#Show-StartDate-leave").attr('value', ShowStartDate);
            $("#Show-EndDate-leave").attr('value', ShowEndDate);
            $("#Show-EntryDate-leave").attr('value', ShowEntryDate);
            $("#Show-TypeLeave-leave").attr('value', ShowtypeLeave);
            $("#Show-TypeDesc-leave").attr('value', ShowTypeDesc);
            $("#Show-SubmissDesch-leave").attr('value', ShowSubmisDate);
            $("#Show-TotalDesch-leave").attr('value', ShowTotalDays + ' Days');
        });

        $(document).on("click", ".reject_leave_Ht", function() {
            var rejectWkModalid = $(this).attr('data-rejectLeaveHtid');
            var rejectWkModalMessage = $(this).attr('data-rejectmessageLeaveHt');

            var formAction;
            formAction = '{{ route('approveht.rejectLeaveHt', ':id') }}'.replace(':id', rejectWkModalid);

            $("#reject-leave-dataHt").attr('action', formAction);
            $("#messageLeave-rejectHt").attr('value', rejectWkModalMessage);


        });
    </script>
@endsection
