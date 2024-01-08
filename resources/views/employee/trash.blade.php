@extends('layouts.master')


@section('content')
<div class="content">
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2">
                <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="arrow-left"></i></span>
                <a href="{{ route('employee') }}">
                Kembali ke Halaman Pegawai
                </a>
            </button>
            <h2 class="intro-y text-lg font-medium">
                Data Pegawai Yang Telah di Hapus
            </h2>
            <div class="hidden md:block mx-auto text-slate-500"></div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="searchEmployee">
                    <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                </div>
            </div>
        </div>
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table id="tableEmployee" class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">No</th>
                        <th class="text-center whitespace-nowrap">Image-Profile</th>
                        <th class="text-center whitespace-nowrap">Username</th>
                        <th class="text-center whitespace-nowrap">Staff Id</th>
                        <th class="text-center whitespace-nowrap">Divisi</th>
                        <th class="text-center whitespace-nowrap">Gender</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody id="result">
                    @if (request()->has('searchEmployee'))
                    @else
                        @foreach ($employee as $item)
                            <tr class="intro-x h-16" id="data-search">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="flex justify-center align-center">
                                    <div class="w-12 h-12 image-fit zoom-in">
                                        @if ($item->avatar)
                                            <img data-action="zoom" class="tooltip rounded-full"
                                                src="{{ asset('storage/' . $item->avatar)}} "
                                                title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                        @elseif($item->gender == 'male')
                                            <img data-action="zoom" class="tooltip rounded-full"
                                                src="{{ asset('images/default-boy.jpg') }} "
                                                title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                        @elseif($item->gender == 'female')
                                            <img data-action="zoom" class="tooltip rounded-full"
                                                src="{{ asset('images/default-women.jpg') }} "
                                                title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                        @endif                            </div>
                                </td>
                                <td class="w-50 text-center">
                                    @if ($item->user)
                                        {{ $item->user->name }}
                                    @else
                                    no
                                    @endif

                                </td>
                                <td class="text-center">
                                    {{ $item->id_number }}
                                </td>
                                <td class="text-center capitalize">
                                    {{ $item->position->name ? $item->position->name : '-' }}
                                </td>
                                <td class="w-40">
                                    <div class="flex items-center justify-center">
                                        @if($item->gender === 'male')
                                            <div class="text-success flex">
                                                <i data-lucide="user" class="w-4 h-4 mr-2"></i> {{ $item->gender }}
                                            </div>
                                        @else
                                            <div class="text-warning flex">
                                                <i data-lucide="user" class="w-4 h-4 mr-2"></i> {{ $item->gender }}
                                            </div>
                                        @endif
                                    </div>

                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">
                                        <a class="flex items-center text-success restore-modal-search mr-4" data-id="{{ $item->id  }}" data-name="{{ $item->first_name }} {{ $item->last_name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#restore-confirmation-modal-search">
                                            <i data-lucide="refresh-ccw" class="w-4 h-4 mr-1"></i> Restore
                                        </a>
                                        <a class="flex items-center text-danger delete-modal-search" data-id="{{ $item->id  }}" data-name="{{ $item->first_name }} {{ $item->last_name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                            <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <div id="show-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h2 class="font-medium text-lg mx-auto">Detail </h2>
                                        </div>
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <div class="col-span-12 mx-auto">
                                                <div class="w-24 h-24 image-fit zoom-in">
                                                    @if ($item->avatar)
                                                        <img class="rounded-full"
                                                            src="{{ asset('storage/' . $item->avatar)}} ">
                                                    @elseif($item->gender == 'male')
                                                        <img class="rounded-full"
                                                            src="{{ asset('images/default-boy.jpg') }} ">
                                                    @elseif($item->gender == 'female')
                                                        <img class="rounded-full"
                                                            src="{{ asset('images/default-women.jpg') }} ">
                                                    @endif                                        </div>
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->first_name }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Lastname :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->last_name }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Gender :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->gender }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Staff Id :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->id_number }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Position :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->position->name }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Address :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->address }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Birth Date :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->birth_date }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Deleted At :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->deleted_at }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div id="restore-confirmation-modal-search" class="modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form id="restore-form-search" method="POST" action="">
                            @csrf
                            @method('put')
                            <div class="modal-body p-0">
                                <div class="p-5 text-center">
                                    <i data-lucide="refresh-ccw" class="w-16 h-16 text-success mx-auto mt-3"></i>
                                    <div class="text-3xl mt-5">Are you sure?</div>
                                    <div class="text-slate-500 mt-2" id="subjudul-restore-confirmation">
                                    </div>
                                    <input name="validNameEmployeeRes" id="crud-form-2" type="text" class="form-control w-full"
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
                                    <div class="text-slate-500 mt-2" id="subjudul-delete-confirmation">
                                    </div>
                                    <input name="validNameEmployeeDel" id="crud-form-2" type="text" class="form-control w-full"
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
        </div>
    </div>
</div>

<script>
    $(document).on("click", ".restore-modal-search", function() {
        var RestoreModalid = $(this).attr('data-id');
        var RestoreModalName = $(this).attr('data-name');

        var formAction;
        formAction = '{{ route('employee.restore', ':id') }}'.replace(':id', RestoreModalid);

        $("#subjudul-restore-confirmation").text('Please type the username "' + RestoreModalName +
            '" of the data to confrim.');
        $("#restore-form-search").attr('action', formAction);
    });

    $(document).on("click", ".delete-modal-search", function() {
        var DeleteModalid = $(this).attr('data-id');
        var DeleteModalName = $(this).attr('data-name');

        var formAction;
        formAction = '{{ route('employee.destroy.permanently', ':id') }}'.replace(':id', DeleteModalid);

        $("#subjudul-delete-confirmation").text('Please type the username "' + DeleteModalName +
            '" of the data to confrim.');
        $("#delete-form-search").attr('action', formAction);
    });

    jQuery(document).ready(function($) {
        var dataTable = new DataTable('#tableEmployee', {
            buttons: ['showSelected'],
            dom: 'rtip',
            select: true,
            pageLength: 5,
            border: false,
        });

        $('#searchEmployee').on('keyup', function() {
            dataTable.search($(this).val()).draw();
        });
    });
</script>
@endsection



