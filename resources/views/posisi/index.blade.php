@extends('layouts.master')


@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Posisi
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                @can('add_positions')
                    <div class="text-center">
                        <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-store-position"
                            class="btn btn-primary mr-2">Add New Position</a>
                    </div>
                @endcan
                <div class="dropdown" data-tw-placement="bottom">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            @can('export_positions')
                                <li>
                                    <a href="{{ route('division.excel') }}" class="dropdown-item"> <i data-lucide="file-text"
                                            class="w-4 h-4 mr-2"></i> Export to Excel </a>
                                </li>
                            @endcan
                            @can('import_positions')
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
                            id="searchPosition">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table id="myTable" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">No</th>
                            <th class="text-center whitespace-nowrap">Posisi</th>
                            <th class="text-center whitespace-nowrap">Total</th>
                            <th class="text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posisi as $item)
                            <tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->jumlah_pegawai }} Pegawai
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        @can('edit_positions')
                                            <a class="flex items-center text-success mr-3 edit-modal-search-class" data-Positionid="{{ $item->id }}" data-PositionName="{{ $item->name }}" data-DivisionId="{{ $item->division_id }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-position-search">
                                                <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                            </a>
                                        @endcan

                                        <a data-positionName="{{ $item->name }}" data-presenceId="{{ $item->id }}"
                                            data-DivisionId="{{ $item->division_id }}"
                                            class="mr-3 flex items-center text-warning detail-presence-modal-search"
                                            href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#detail-division-modal">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>

                                        @can('delete_positions')
                                            <a class="flex items-center text-danger delete-modal-search" data-id="{{  $item->id  }}" data-name="{{ $item->name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                                <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
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

    <div id="modal-store-position" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Create-position</h2>
                </div>
                <form action="{{ route('position.store') }}" method="post">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Nama Divisi</label>
                            <select name="division_id" class="tom-select w-full" id="">
                                @foreach ($divisi as $item)
                                    <option value="0" selected disabled>Choose Division</option>
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12">
                            <label for="modal-form-2" class="form-label">Nama Posisi</label>
                            <input id="modal-form-2" name="name" type="text" class="form-control capitalize"
                                placeholder="nama divisi" autocomplete="off">
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
                    <h2 class="font-medium text-base mr-auto">Import Data Position</h2>
                    <a class="btn btn-outline-secondary hidden sm:flex" href="{{ route('position.downloadTemplate') }}">
                        <i data-lucide="file" class="w-4 h-4 mr-2"></i>
                        Download Template
                    </a>
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
                <form action="{{ route('position.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">File Import</label>
                            <input id="modal-form-1" name="import_file" type="file" class="form-control">
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

    {{-- detail modal position --}}
    <div id="detail-division-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-lg mx-auto">Detail <span id="namaPosition"></span> Position</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                </div>
                <table id="table" class="table table-striped" style="max-height: 10px; overflow-y: auto;">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">#</th>
                            <th class="whitespace-nowrap">Id number</th>
                            <th class="whitespace-nowrap">Name</th>
                            <th class="whitespace-nowrap">Divisi</th>
                            <th class="whitespace-nowrap">Address</th>
                            <th class="whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody id="positionList">
                    </tbody>
                </table>
                <div id="pagination-controls" class="mt-4 text-center">
                    <button id="prev-page" class="btn btn-primary">Prev</button>
                    <span id="page-numbers">Page 1 of 1</span>
                    <button id="next-page" class="btn btn-primary">Next</button>
                </div>
            </div>
        </div>
    </div>

    {{-- detail modal position end --}}

    {{-- edit modal live search --}}
    <div id="modal-edit-position-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Position</h2>
                </div>
                <form id="edit-form-search" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Posisi</label>
                            <input id="edit-modal-PositionName" value="" name="name" type="text"
                                class="form-control" placeholder="nama divisi">
                        </div>
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Nama Divisi</label>
                            <select name="division_id" id="project-position-select" class="w-full"
                                style="background-color: #1B253b;">
                                @foreach ($divisi as $itemDivisi)
                                    <option value="{{ $itemDivisi->id }}">
                                        {{ $itemDivisi->name }}
                                    </option>
                                @endforeach
                            </select>
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
    {{-- edit modal live search end --}}

    {{-- delete modal live search --}}
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
                            <input name="validNamePosition" id="crud-form-2" type="text" class="form-control w-full"
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
    {{-- delete modal live search end --}}

    <script type="text/javascript">
        //detail
        $(document).on("click", ".detail-presence-modal-search", function() {
        var divisionId = $(this).data('presenceId');
        var positionName = $(this).attr('data-positionName');
        var totalPages = 0;
        var currentPage = 1;
        var perPage = 5;

        jQuery(document).ready(function($) {
            function updatePaginationInfo() {
                $("#page-numbers").text("Page " + currentPage + " of " + totalPages);
            }

            function updatePaginationControls() {
                $("#prev-page").prop('disabled', currentPage === 1);
                $("#next-page").prop('disabled', currentPage === totalPages || totalPages === 0);
            }

            function loadPage(page) {
                $.ajax({
                    url: '{{ route('position.detail', ':id') }}'.replace(':id', divisionId),
                    type: 'GET',
                    data: { page: page },
                    success: function(response) {
                        console.log(response);
                        totalPages = response.positionData.last_page;
                        var positionList = $('#positionList');
                        positionList.empty();

                        var startIndex = (page - 1) * perPage;

                        $.each(response.positionData.data, function(index, positionData) {
                            var row = '<tr>' +
                                '<td class="w-4 text-center">' + (startIndex + index + 1) +
                                '.</td>' +
                                '<td class="w-50 text-center capitalize">' +
                                positionData.id_number + '</td>' +
                                '<td class="w-50 text-left capitalize">' +
                                positionData.first_name + ' ' + positionData.last_name +
                                '</td>' +
                                '<td class="w-50 text-left capitalize">' +
                                positionData.division.name + '</td>' +
                                '<td class="w-50 text-left capitalize">' +
                                positionData.address + '</td>' +
                                '<td class="w-50 text-center capitalize">' + (
                                    positionData.is_active == '1' ? 'active' : 'non-active') + '</td>' +
                                '</tr>';
                            positionList.append(row);
                        });

                        currentPage = page;
                        updatePaginationInfo();
                        updatePaginationControls();
                    }
                });
            }

                loadPage(currentPage);

                $("#next-page").click(function() {
                    if (currentPage < totalPages) {
                        loadPage(currentPage + 1);
                    }
                });

                $("#prev-page").click(function() {
                    if (currentPage > 1) {
                        loadPage(currentPage - 1);
                    }
                });
            });
        });

        $(document).on("click", ".edit-modal-search-class", function() {
            var EditModalid = $(this).attr('data-Positionid');
            var EditModalPositionName = $(this).attr('data-PositionName');
            var EditModalDivisiID = $(this).attr('data-DivisionId');



            console.log(EditModalDivisiID);
            var formAction;
            formAction = '{{ route('position.update', ':id') }}'.replace(':id', EditModalid);

            $("#edit-modal-PositionName").attr('value', EditModalPositionName);
            $("#edit-form-search").attr('action', formAction);

            $("#project-position-select option").each(function() {
                if ($(this).val() == EditModalDivisiID) {
                    $(this).attr("selected", true);
                } else {
                    $(this).removeAttr("selected");
                }
            });
        });

        // delete
        $(document).on("click", ".delete-modal-search", function() {
            var DeleteModalid = $(this).attr('data-id');
            var DeleteModalName = $(this).attr('data-name');

            var formAction;
            formAction = '{{ route('position.destroy', ':id') }}'.replace(':id', DeleteModalid);

            $("#subjuduldelete-confirmation").text('Please type the username "' + DeleteModalName +
                '" of the data to confrim.');
            $("#delete-form-search").attr('action', formAction);
        });

        // data table
        jQuery(document).ready(function($) {
            var dataTable = new DataTable('#myTable', {
                buttons: ['showSelected'],
                dom: 'rtip',
                select: true, 
                pageLength: 5,
                border: false,
            });

            $('#searchPosition').on('keyup', function() {
                dataTable.search($(this).val()).draw();
            });
        });
    </script>
@endsection
