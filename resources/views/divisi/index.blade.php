@extends('layouts.master')


@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Divisi
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                @can('add_divisions')
                <div class="text-center">
                    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-store-divisi"
                        class="btn btn-primary mr-2">Add New Division</a>
                </div>
                @endcan
                <div class="dropdown" data-tw-placement="bottom">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            @can('export_divisions')
                            <li>
                                <a href="{{ route('division.excel') }}" class="dropdown-item"> <i data-lucide="file-text"
                                        class="w-4 h-4 mr-2"></i> Export to Excel </a>
                            </li>
                            @endcan
                            @can('import_divisions')
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
                        <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchDivisi">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table id="myTable" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Divisi</th>
                            <th class="text-center whitespace-nowrap">Total</th>
                            <th class="text-center whitespace-nowrap" data-orderable="false">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($divisi as $item)
                            <tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->jumlah_posisi }} Posisi
                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        @can('edit_divisions')
                                        <a class="flex items-center text-success mr-3 edit-modal-divisi-search-class" data-Divisiid="{{ $item->id }}" data-DivisiName="{{ $item->name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-divisi-search">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                        </a>
                                        @endcan

                                        <a data-divisionId="{{ $item->id }}" class="mr-3 flex items-center text-warning detail-division-modal-search" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-division-modal">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>

                                        @can('delete_divisions')
                                        <a class="flex items-center text-danger delete-divisi-modal-search" data-DeleteDivisiId="{{ $item->id }}" data-DeleteDivisiName="{{ $item->name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                            <i data-lucide="trash-2" class="w-4 h-4  mr-1"></i> Delete
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

    <div id="modal-store-divisi" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Create-Division</h2>
                </div>
                <form action="{{ route('divisi.store') }}" method="post">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Nama Divisi</label>
                            <input id="modal-form-1" name="divisi" type="text" class="form-control capitalize"
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
                    <h2 class="font-medium text-base mr-auto">Import Data Division</h2>
                    <a class="btn btn-outline-secondary hidden sm:flex" href="{{ route('division.downloadTemplate') }}">
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
                <form action="{{ route('division.import') }}" method="POST" enctype="multipart/form-data">
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

    {{-- detail modal divis --}}
    <div id="detail-division-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-lg mx-auto">Divisi</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                </div>
                <table id="table" class="table table-striped">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Position</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Pegawai</th>
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
    {{-- detail modal divis end --}}

    {{-- delete modal search --}}
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
                            <input name="validName" id="crud-form-2" type="text" class="form-control w-full"
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
    {{-- delete modal search end --}}

    {{-- edit modal search --}}
    <div id="modal-edit-divisi-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Division</h2>
                </div>
                <form id="edit-form-divisi-search" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Divisi</label>
                            <input id="edit-modal-DivisiName" value="" name="divisi" type="text"
                                class="form-control" placeholder="nama divisi">
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
    {{-- Modal edit search end --}}
<script type="text/javascript">

    jQuery(document).ready(function($) {
        var dataTable = new DataTable('#myTable', {
            buttons: ['showSelected'],
            dom: 'rtip',
            select: true,
            pageLength: 5,
            border: false,
        });

        $('#searchDivisi').on('keyup', function() {
            dataTable.search($(this).val()).draw();
        });
    });

    $(document).on("click", ".detail-division-modal-search", function() {
        var divisionId = $(this).data('divisionId');
        var totalPages = 0;
        var currentPage = 1;
        var perPage = 5;

        jQuery(document).ready(function($) {
            function updatePaginationInfo() {
                // $("#pagination-info").text("Page " + currentPage + "/" + totalPages);
                $("#page-numbers").text(currentPage + " / " + totalPages);
            }

            function updatePaginationControls() {
                $("#prev-page").prop('disabled', currentPage === 1);
                $("#next-page").prop('disabled', currentPage === totalPages || totalPages === 0);
            }

            function loadPage(page) {
                $.ajax({
                    url: '{{ route('division.detail', ':id') }}'.replace(':id', divisionId),
                    type: 'GET',
                    data: { page: page },
                    success: function(response) {
                        console.log(response);
                        totalPages = response.lastPage;
                        var positionList = $('#positionList');
                        positionList.empty();

                        var startIndex = (page - 1) * perPage;

                        $.each(response.positionData, function(index, positionData) {
                            var row = '<tr>' +
                                '<td class="w-4 text-center">' + (startIndex + index + 1) +
                                '.</td>' +
                                '<td class="w-50 text-center capitalize">' +
                                positionData.position.name + '</td>' +
                                '<td class="w-50 text-center capitalize">' +
                                positionData.positionCount + ' pegawai' + '</td>' +
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


    $(document).on("click", ".edit-modal-divisi-search-class", function() {
        var EditModalid = $(this).attr('data-Divisiid');
        var EditModalDivisiName = $(this).attr('data-DivisiName');



        console.log(EditModalDivisiName);
        var formAction;
        formAction = '{{ route('divisi.update', ':id') }}'.replace(':id', EditModalid);

        $("#edit-modal-DivisiName").attr('value', EditModalDivisiName);
        $("#edit-form-divisi-search").attr('action', formAction);
    });

    $(document).on("click", ".delete-divisi-modal-search", function() {
        var DeleteDivisiModalid = $(this).attr('data-DeleteDivisiId');
        var DeleteDivisiModalName = $(this).attr('data-DeleteDivisiName');



        var formAction;
        formAction = '{{ route('divisi.destroy', ':id') }}'.replace(':id', DeleteDivisiModalid);

        $("#subjuduldelete-confirmation").text('Please type the Divisi name "' + DeleteDivisiModalName +
            '" of the data to confrim.');
        $("#delete-form-search").attr('action', formAction);
    });
</script>
@endsection
