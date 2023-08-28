@extends('layouts.master')


@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Divisi
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="text-center">
                <a href="javascript:;" data-tw-toggle="modal"
                data-tw-target="#modal-store-divisi" class="btn btn-primary mr-2">Add New Division</a>
            </div>
            <div class="dropdown">
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
                    <input type="text" class="form-control w-56 box pr-10" placeholder="Search...">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="table" class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th data-priority="1" class="whitespace-nowrap">No</th>
                        <th data-priority="2" class="text-center whitespace-nowrap">Divisi</th>
                        <th class="text-center whitespace-nowrap">Total</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
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
                                <a class="flex items-center text-warning mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-divisi-{{ $item->id }}">
                                    <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                </a>

                                <a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-{{ $item->id }}">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div id="modal-edit-divisi-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="font-medium text-base mr-auto">Edit Division</h2>
                                </div>
                                <form id="edit-form" method="POST" action="{{ route('divisi.update',$item->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                        <div class="col-span-12">
                                            <label for="modal-form-2" class="form-label">Nama Divisi</label>
                                            <input id="modal-form-2" value="{{ $item->division }}" name="divisi" type="text" class="form-control" placeholder="nama divisi">
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
                                <form id="delete-form" method="POST" action="{{ route('divisi.destroy',$item->id) }}">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-body p-0">
                                        <div class="p-5 text-center">
                                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                            <div class="text-3xl mt-5">Are you sure?</div>
                                            <div class="text-slate-500 mt-2">
                                                Do you really want to delete {{ $item->division }}?
                                                <br>
                                                This process cannot be undone.
                                            </div>
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
            <div class="flex justify-center items-center">
                {{ $divisi->links('pagination.custom', [
                    'paginator' => $divisi,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                    'slider_text' => 'Showing items from {start} to {end} out of {total}',
                ]) }}
            </div>
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
                            <input id="modal-form-1" name="divisi" type="text" class="form-control capitalize" placeholder="nama divisi" autocomplete="off">
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


@endsection



