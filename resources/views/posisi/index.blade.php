@extends('layouts.master')


@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Posisi
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="text-center">
                <a href="javascript:;" data-tw-toggle="modal"
                data-tw-target="#modal-store-position" class="btn btn-primary mr-2">Add New Position</a>
            </div>
            <div class="dropdown" data-tw-placement="bottom">
                <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                    <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i> </span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="{{ route('division.excel') }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel </a>
                        </li>
                        <li>
                            <a href="" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#import-modal" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Import Excel </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="hidden md:block mx-auto text-slate-500"></div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchPosition">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="table" class="table table-report -mt-2">
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
                                <a class="flex items-center text-warning mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-position-{{ $item->id }}">
                                    <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                </a>

                                <a data-presenceId="{{ $item->id }}" class="mr-3 flex items-center text-success detail-presence-modal-search" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-division-modal">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>

                                <a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-{{ $item->id }}">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                </a>

                            </div>
                        </td>
                    </tr>

                    <div id="modal-edit-position-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="font-medium text-base mr-auto">Edit Division</h2>
                                </div>
                                <form id="edit-form" method="POST" action="{{ route('position.update',$item->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                        <div class="col-span-12">
                                            <label for="modal-form-1" class="form-label">Nama Divisi</label>
                                            <select name="division_id" class="tom-select w-full" id="modal-form-1">
                                                @foreach ($divisi as $itemDivisi)
                                                <option value="{{ $itemDivisi->id }}" {{ $itemDivisi->id == $item->division_id ? 'selected' : '' }}>
                                                    {{ $itemDivisi->name }}
                                                </option>
                                               @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-12">
                                            <label for="modal-form-2" class="form-label">Nama Posisi</label>
                                            <input id="modal-form-2" value="{{ $item->name }}" name="name" type="text" class="form-control" placeholder="nama divisi">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
                                        <button type="submit" class="btn btn-primary w-20">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div id="delete-confirmation-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="delete-form" method="POST" action="{{ route('position.destroy',$item->id) }}">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-body p-0">
                                        <div class="p-5 text-center">
                                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                            <div class="text-3xl mt-5">Are you sure?</div>
                                            <div class="text-slate-500 mt-2">
                                                Please type the username "{{ $item->name }}" of the data to confrim.
                                            </div>
                                             <input name="validNamePosisi" id="crud-form-2" type="text" class="form-control w-full" placeholder="User name" required>
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
                    @endforeach
                </tbody>
            </table>
            @if ($posisi->count() > 0)
                <div class="flex justify-center items-center">
                    {{ $posisi->links('pagination.custom', [
                        'paginator' => $posisi,
                        'prev_text' => 'Previous',
                        'next_text' => 'Next',
                        'slider_text' => 'Showing items from {start} to {end} out of {total}',
                    ]) }}
                </div>
            @endif
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
                            <input id="modal-form-2" name="name" type="text" class="form-control capitalize" placeholder="nama divisi" autocomplete="off">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
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
                    <h2 class="font-medium text-base mr-auto">Broadcast Message</h2> <button class="btn btn-outline-secondary hidden sm:flex"> <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs </button>
                    <div class="dropdown sm:hidden"> <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i> </a>
                        <div class="dropdown-menu w-40">
                            <ul class="dropdown-content">
                                <li> <a href="javascript:;" class="dropdown-item"> <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs </a> </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form action="{{ route('division.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">File Import</label>
                            <input id="modal-form-1" name="import_file" type="file" class="form-control" placeholder="example@gmail.com">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
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
                    <h2 class="font-medium text-lg mx-auto">Detail Position</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                </div>
                <table id="table" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Id number</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Name</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Divisi</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Gender</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Address</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Birth Date</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                        <tbody id="positionList">
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
    {{-- detail modal position end --}}

    {{-- edit modal live search --}}
    <div id="modal-edit-position-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Division</h2>
                </div>
                <form id="edit-form-search" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Nama Divisi</label>
                            <select name="division_id" class="tom-select w-full" id="modal-form-1">
                                @foreach ($divisi as $itemDivisi)
                                <option value="{{ $itemDivisi->id }}" {{ $itemDivisi->id == $item->division_id ? 'selected' : '' }}>
                                    {{ $itemDivisi->name }}
                                </option>
                               @endforeach
                            </select>
                        </div>
                        <div class="col-span-12">
                            <label class="form-label">Nama Posisi</label>
                            <input id="edit-modal-PositionName" value="" name="name" type="text" class="form-control" placeholder="nama divisi">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal" class="btn btn-outline-secondary w-20 mr-1">Cancel</button>
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
                            <input name="validNameEmployee" id="crud-form-2" type="text" class="form-control w-full" placeholder="User name" required>
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

    <script type="text/javascript">
        jQuery(document).ready(function($) {
                    $('#searchPosition').on('keyup', function() {
                    var query = $(this).val();
                    $.ajax({
                    type: 'GET',
                    url: '{{ route('position') }}',
                    data: { query: query },
                    success: function(data) {
                    $('tbody').html(data);
                    }
                });
            });
        });

        $(document).on("click", ".detail-presence-modal-search", function () {
            var divisionId = $(this).data('presenceId');
            jQuery(document).ready(function($) {
            $.ajax({
                url: '{{ route('position.detail', ":id") }}'.replace(':id', divisionId),
                type: 'GET',
                success: function (response) {
                    var positionList = $('#positionList');
                    positionList.empty();
                    
                
                    $.each(response.positionData, function(index, positionData) {
                    var row = '<tr>' +
                        '<td class="w-4 text-center">' + (index + 1) + '.</td>' +
                        '<td class="w-50 text-center capitalize">' + positionData.id_number + '</td>' +
                        '<td class="w-50 text-center capitalize">' + positionData.first_name + ' ' + positionData.last_name + '</td>' +
                        '<td class="w-50 text-center capitalize">' + positionData.division.name + '</td>' +
                        '<td class="w-50 text-center capitalize">' + positionData.gender + '</td>' +
                        '<td class="w-50 text-center capitalize">' + positionData.address + '</td>' +
                        '<td class="w-50 text-center capitalize">' + positionData.birth_date + '</td>' +
                        '<td class="w-50 text-center capitalize">' + (positionData.is_active == '1' ? 'active' : 'non-active') + '</td>'
                        '</tr>';
                    positionList.append(row);
                });


                    
                    // $('#detail-division-modal').modal('show')
                }
            });
            });
        });

        $(document).on("click", ".edit-modal-search-class", function () {
            var EditModalid = $(this).attr('data-Positionid');
            var EditModalPositionName = $(this).attr('data-PositionName');


    
            console.log(EditModalPositionName);
            var formAction;
            formAction = '{{ route("position.update",":id") }}'.replace(':id', EditModalid);
            
            $("#edit-modal-PositionName").attr('value', EditModalPositionName);
            $("#edit-form-search").attr('action', formAction);
        });
        
        $(document).on("click", ".delete-modal-search", function () {
            var DeleteModalid = $(this).attr('data-id');
            var DeleteModalName = $(this).attr('data-name');


    
            var formAction;
            formAction = '{{ route("position.destroy",":id") }}'.replace(':id', DeleteModalid);

            $("#subjuduldelete-confirmation").text('Please type the username "'+ DeleteModalName +'" of the data to confrim.');
            $("#delete-form-search").attr('action', formAction);
        });
    </script>
@endsection



