@extends('layouts.master')
@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Work Trip
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
                        @foreach ($workTripData as $item)
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
                                    {{ $item->worktrip->statusCommit->first()->status }}
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a data-wkHrid="{{ $item->worktrip->statusCommit->first()->id }}" data-messageWK="{{ $item->user->name }} {{ $item->category }}" class="flex items-center text-success mr-3 approve_wk_Ht"
                                            href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#modal-apprv-wt-search">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Approve
                                        </a>
                                        {{-- <a class="mr-3 flex items-center text-warning delete-modal-search" data-id=""
                                            data-name="" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#rejected-confirmation-modal">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a> --}}
                                        @can('reject_presence')                                            
                                        <a data-rejectwkHresid="{{ $item->worktrip->statusCommit->first()->id }}" data-rejectmessageWK="{{ $item->user->name }} {{ $item->category }}" class="flex items-center text-danger reject_wk_Hr" data-id=""
                                            data-name="" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#reject-confirmation-modal">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Reject
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($workTripData->count() > 0)
                    <div class="flex justify-center items-center">
                        {{ $workTripData->links('pagination.custom', [
                            'paginator' => $workTripData,
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
    <div id="modal-apprv-wt-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="approve-wk-dataHr" method="POST" action="">
                    @csrf
                    @method('post')
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-success mx-auto mt-3"></i>
                            <div class="text-3xl mt-5">Are you sure?</div>
                            <div class="text-slate-500 mt-2" id="subjuduldelete-confirmation">
                                Are you sure you want to approve this absence request?
                            </div>
                            <input name="description" id="crud-form-2" type="text" class="form-control w-full"
                            placeholder="description" required>
                            <input hidden name="message" type="text" id="messageWk-approveHr">
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
    <div id="reject-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reject-wk-dataHres" method="POST" action="">
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
                            <input hidden name="message" type="text" id="messageWk-rejectHres">
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

    <script type="text/javascript">
        $(document).on("click", ".approve_wk_Ht", function() {
            var ApproveWkModalid = $(this).attr('data-wkHrid');
            var ApproveWkModalMessage = $(this).attr('data-messageWK');

            var formAction;
            formAction = '{{ route('approvehr.approvedWorkTripHr', ':id') }}'.replace(':id', ApproveWkModalid);

            $("#approve-wk-dataHr").attr('action', formAction);
            $("#messageWk-approveHr").attr('value', ApproveWkModalMessage);
        });

        $(document).on("click", ".reject_wk_Hr", function() {
            var rejectWkModalid = $(this).attr('data-rejectwkHresid');
            var rejectWkModalMessage = $(this).attr('data-rejectmessageWK');

            var formAction;
            formAction = '{{ route('approvehr.rejectWorokTripHr', ':id') }}'.replace(':id', rejectWkModalid);

            $("#reject-wk-dataHres").attr('action', formAction);
            $("#messageWk-rejectHres").attr('value', rejectWkModalMessage);
        });

    </script>
@endsection
