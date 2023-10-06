@extends('layouts.master')


@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Partner
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                @can('add_partners')
                    <div class="text-center">
                        <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-store-divisi"
                            class="btn btn-primary mr-2">Add New Partner</a>
                    </div>
                @endcan
                <div class="dropdown" data-tw-placement="bottom">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            @can('export_partners')
                                <li>
                                    <a href="" class="dropdown-item"> <i data-lucide="file-text"
                                            class="w-4 h-4 mr-2"></i> Export to Excel </a>
                                </li>
                                <li>
                                    <a href="" class="dropdown-item"> <i data-lucide="file-text"
                                            class="w-4 h-4 mr-2"></i> Export to PDF </a>
                                </li>
                            @endcan
                            
                            @can('import_partners')
                                <li>
                                    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#import-modal"
                                        class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Import Excel
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </div>
                <div class="hidden md:block mx-auto text-slate-500"></div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" class="form-control w-56 box pr-10" placeholder="Search..."
                            id="searchPartner">
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
                            <th class="text-center whitespace-nowrap">Total Project</th>
                            <th class="text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tablePartner">
                        @foreach ($partner as $item)
                            <tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->jumlah_project }}
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        @can('edit_partners')
                                            <a class="flex items-center text-warning mr-3 edit-modal-partner-search-class" data-partnerName="{{ $item->name }}" data-descId="{{ $item->description }}" data-partnerId="{{ $item->id }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-partner">
                                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                            </a>
                                        @endcan

                                        <a class="mr-3 flex items-center text-success detail-partner-modal-search"
                                            data-partnerId="{{ $item->id }}" data-partnerName="{{ $item->name }}"
                                            data-partnerDesc="{{ $item->description }}" href="javascript:;"
                                            data-tw-toggle="modal" data-tw-target="#detail-partner-modal">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>

                                        @can('delete_partners')
                                            <a class="flex items-center text-danger deletepartnermodal" data-partnerid="{{ $item->id }}" data-partnername="{{ $item->name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-partner-modal-search">
                                                <i data-lucide="trash-2" class="w-4 h-4  mr-1"></i> Delete
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($partner->count() > 0)
                    <div class="flex justify-center items-center">
                        {{ $partner->links('pagination.custom', [
                            'paginator' => $partner,
                            'prev_text' => 'Previous',
                            'next_text' => 'Next',
                            'slider_text' => 'Showing items from {start} to {end} out of {total}',
                        ]) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create partner --}}
    <div id="modal-store-divisi" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Create-Division</h2>
                </div>
                <form action="{{ route('partner.store') }}" method="post">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Nama Partner</label>
                            <input id="modal-form-1" name="partner" type="text" class="form-control capitalize"
                                placeholder="Name Partner" autocomplete="off">
                        </div>

                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Description</label>
                            <input id="modal-form-1" name="description" type="text" class="form-control capitalize"
                                placeholder="Name Partner" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                        <button type="submit" class="kelas btn btn-primary w-20">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="import-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Broadcast Message</h2> <button
                        class="btn btn-outline-secondary hidden sm:flex"> <i data-lucide="file" class="w-4 h-4 mr-2"></i>
                        Download Docs </button>
                    <div class="dropdown sm:hidden"> <a class="dropdown-toggle w-5 h-5 block" href="javascript:;"
                            aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="more-horizontal"
                                class="w-5 h-5 text-slate-500"></i> </a>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li> <a href="javascript:;" class="dropdown-item"> <i data-lucide="file"
                                            class="w-4 h-4 mr-2"></i> Download Docs </a> </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">File Import</label>
                            <input id="modal-form-1" name="import_file" type="file" class="form-control"
                                placeholder="example@gmail.com">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-primary w-20">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- detail modal Partner --}}
    <div id="detail-partner-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-lg mx-auto" id="show-detailName">Detail Partner</h2>
                </div>
                <div class="modal-body grid grid-cols-1 gap-4 gap-y-3">
                    <div class="">
                        <label class="form-label">Partner name :</label>
                        <input disabled id="show-partnername" type="text" class="form-control" value="">
                    </div>

                    <div class="">
                        <label for="modal-form-2" class="form-label">Description :</label>
                        <textarea disabled name="" id="show-partnerDesc" class="form-control" rows="3" value=""></textarea>
                    </div>

                    <div class="">
                        <label class="form-label">Project :</label>
                        <div id="partProjectList">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- detail modal Partner end --}}

    {{-- delete modal partner --}}
    <div id="delete-partner-modal-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="actionconfirmationpartner" method="POST" action="">
                    @csrf
                    @method('delete')
                    <div class="modal-body p-0">
                        <div class="p-5 text-center">
                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                            <div class="text-3xl mt-5">Are you sure?</div>
                            <div class="text-slate-500 mt-2" id="textconfirmationpartner">
                            </div>
                            <input name="validNamePartner" id="crud-form-2" type="text" class="form-control w-full"
                                placeholder="User name" required>
                        </div>
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button type="submit" class="btn btn-danger w-24">Delete</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- delete modal partner end --}}

    {{-- edit modal partner search --}}
    <div id="modal-edit-partner" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Partner</h2>
                </div>
                <form id="edit-partner-action" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Partner</label>
                            <input id="edit-partner-name" value="" name="name" type="text"
                                class="form-control" placeholder="Nama Partner">
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Description</label>
                            <input id="edit-partnerDesc" value="" name="description" type="text"
                                class="form-control" placeholder="Nama Partner">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                        <button type="submit" class="btn btn-primary w-20">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        // search
        jQuery(document).ready(function($) {
            $('#searchPartner').on('keyup', function() {
                var query = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('partner') }}',
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#tablePartner').html(data);
                    }
                });
            });
        });

        // detail
        $(document).on("click", ".detail-partner-modal-search", function() {
            var partnerId = $(this).attr('data-partnerId');
            var partnerName = $(this).attr('data-partnerName');
            var partnerdesc = $(this).attr('data-partnerDesc');


            $("#show-partnername").attr('value', partnerName);
            $("#show-partnerDesc").text(partnerdesc);
            jQuery(document).ready(function($) {
                $.ajax({
                    url: '{{ route('partner.detail', ':id') }}'.replace(':id', partnerId),
                    type: 'GET',

                    success: function(response) {
                        var positionList = $('#partProjectList');
                        positionList.empty();


                        $.each(response.positionData, function(index, positionData) {
                            var row =
                                '<input disabled id="show-firstname" type="text" class="form-control mt-4" value="' +
                                positionData.name + '">';

                            positionList.append(row);
                        });


                        // $('#detail-division-modal').modal('show')
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        // edit partner
        $(document).on("click", ".edit-modal-partner-search-class", function() {
            var EditModalid = $(this).attr('data-partnerId');
            var EditModalPartnerDesch = $(this).attr('data-descId');
            var EditPartnerName = $(this).attr('data-partnerName');



            var formAction;
            formAction = '{{ route('partner.update', ':id') }}'.replace(':id', EditModalid);

            $("#edit-partnerDesc").attr('value', EditModalPartnerDesch);
            $("#edit-partner-action").attr('action', formAction);
            $("#edit-partner-name").attr('value', EditPartnerName);
        });

        //delete
        $(document).on("click", ".deletepartnermodal", function() {
            var DeletePartnerlid = $(this).attr('data-partnerid');
            var DeletePartnerName = $(this).attr('data-partnername');



            var formAction;
            formAction = '{{ route('partner.destroy', ':id') }}'.replace(':id', DeletePartnerlid);

            $("#textconfirmationpartner").text('Please type the username "' + DeletePartnerName +
                '" of the data to confrim.');
            $("#actionconfirmationpartner").attr('action', formAction);
        });
    </script>
@endsection
