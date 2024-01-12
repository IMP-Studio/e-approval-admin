@extends('layouts.master')
@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Telework
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <div class="hidden md:block mx-auto text-slate-500"></div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchTeleworkHt">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table id="tableWorktrip" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Name</th>
                            <th class="text-center whitespace-nowrap">Divisi</th>
                            <th class="text-center whitespace-nowrap">Jensi Kehadiran</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tablePartner">
                         @foreach ($teleworkData as $item)
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
                                    {{ $item->telework->statusCommit->first()->status }}
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a data-teleHtid="{{ $item->telework->statusCommit->first()->id }}" data-messageTele="{{ $item->user->name }} {{ $item->category }}" class="flex items-center text-success mr-3 approve_tele_Ht"
                                            data-Positionid="" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#modal-apprv-teleHt-search">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Approve
                                        </a>
                                        <a class="flex items-center text-warning delete-button mr-3 show-attendance-modal-search-telework"
                                            data-avatar="{{ $item->user->employee->avatar }}"
                                            data-divisi="{{ $item->user->employee->division->name }}"
                                            data-gender="{{ $item->user->employee->gender }}"
                                            data-date=" {{ $item->telework->presence->date }}"
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
                                        @can('reject_presence')                                        
                                        <a data-rejectTeleHtid="{{ $item->telework->statusCommit->first()->id }}" data-rejectmessageTele="{{ $item->user->name }} {{ $item->category }}" class="flex items-center text-danger reject_tele_Ht" href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#reject-confirmation-teleHt-modal">
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
    <div id="modal-apprv-teleHt-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="approve-tele-dataHt" method="POST" action="">
                    @csrf
                    @method('put')
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-success mx-auto mt-3"></i>
                            <div class="text-3xl mt-5">Are you sure?</div>
                            <div class="text-slate-500 mt-2" id="subjuduldelete-confirmation">
                                Are you sure you want to approve this absence request?
                            </div>
                            <input hidden name="message" type="text" id="messageTele-approveHt">
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
    <div id="reject-confirmation-teleHt-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reject-tele-dataHt" method="POST" action="">
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
                            <input hidden name="message" type="text" id="messageTele-rejectHt">
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
                        <label class="text-xs">Divisi :</label>
                        <input disabled id="Show-Divisi-tele" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Category :</label>
                        <input disabled id="Show-Category-tele" type="text" class="form-control capitalize" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Telework Category  :</label>
                        <input disabled id="Show-Telecat-tele" type="text" class="form-control capitalize" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Date  :</label>
                        <input disabled id="Show-Date" type="text" class="form-control capitalize" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Temporary Entry Time  :</label>
                        <input disabled id="Show-TempoEntry-tele" type="text" class="form-control capitalize" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-12" id="divCatDesc">
                        <label class="text-xs">Category Description :</label>
                        <textarea disabled id="Show-CatDesc" class="form-control capitalize p-5"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- detail modal attendance search TeleWork end--}}

<script type="text/javascript">
    $(document).on("click", ".approve_tele_Ht", function() {
        var ApproveTeleModalid = $(this).attr('data-teleHtid');
        var ApproveTeleModalMessage = $(this).attr('data-messageTele');

        var formAction;
        formAction = '{{ route('approveht.approvedTeleHt', ':id') }}'.replace(':id', ApproveTeleModalid);

        $("#approve-tele-dataHt").attr('action', formAction);
        $("#messageTele-approveHt").attr('value', ApproveTeleModalMessage);


    });

     // telework modal detail
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
        var ShowDivisi = $(this).attr('data-divisi');
        var ShowDate = $(this).attr('data-date');



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
        $("#Show-CatDesc").text(ShowCatDesc);
        $("#Show-CatDesc").parent().show();
        } else {
        $("#divCatDesc").hide();
        }

        $("#show-modal-image-tele").attr('src', imgSrc);
        $("#Show-firstname-tele").attr('value', ShowFirstname);
        $("#Show-LastName-tele").attr('value', ShowLastName);
        $("#Show-StafId-tele").attr('value', ShowStafId);
        $("#Show-Posisi-tele").attr('value', ShowPosisi);
        $("#Show-Divisi-tele").attr('value', ShowDivisi);
        $("#Show-Category-tele").attr('value', ShowCategory);
        $("#Show-Telecat-tele").attr('value',ShowTeleCat);
        $("#Show-TempoEntry-tele").attr('value',ShowTempoEntry);
        $("#Show-Date").attr('value',ShowDate);
    });

    $(document).on("click", ".reject_tele_Ht", function() {
        var rejectWkModalid = $(this).attr('data-rejectTeleHtid');
        var rejectWkModalMessage = $(this).attr('data-rejectmessageTele');

        var formAction;
        formAction = '{{ route('approveht.rejectTeleHt', ':id') }}'.replace(':id', rejectWkModalid);

        $("#reject-tele-dataHt").attr('action', formAction);
        $("#messageTele-rejectHt").attr('value', rejectWkModalMessage);


    });

      // data table
         jQuery(document).ready(function($) {
            var dataTable = new DataTable('#tableWorktrip', {
                buttons: ['showSelected'],
                dom: 'rtip',
                select: true, 
                pageLength: 5,
                border: false,
            });

            $('#searchTeleworkHt').on('keyup', function() {
                dataTable.search($(this).val()).draw();
            });
        });
</script>
@endsection