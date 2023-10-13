@extends('layouts.master')


@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Pegawai
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                @can('add_employees')
                <button class="btn btn-primary shadow-md mr-2"><a href="{{ route('employee.create') }}">Add
                        Employee</a>
                </button>
                @endcan
                <div class="dropdown" data-tw-placement="bottom">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            @can('export_employees')
                                <li>
                                    <a href="{{ route('employee.excel') }}" class="dropdown-item"> <i data-lucide="file-text"
                                            class="w-4 h-4 mr-2"></i> Export to Excel </a>
                                </li>
                                <li>
                                    <a href="{{ route('employee.pdf') }}" class="dropdown-item"> <i data-lucide="file-text"
                                            class="w-4 h-4 mr-2"></i> Export to PDF </a>
                                </li>
                            @endcan
                            @can('import_employees')
                                <li>
                                    <a href="javascript:;" class="dropdown-item" data-tw-target="#import-modal"
                                        data-tw-toggle="modal"><i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Import Excel
                                    </a>
                                </li>
                            @endcan
                            <li>
                                <a href="{{ route('employee.trash') }}" class="dropdown-item"><i data-lucide="archive"
                                        class="w-4 h-4 mr-2"></i> Restore Data </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="hidden md:block mx-auto text-slate-500"></div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" class="form-control w-56 pr-10" placeholder="Search..." id="searchEmployee">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <!-- BEGIN: Data List -->
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">No</th>
                            <th class="text-center whitespace-nowrap">Image-Profile</th>
                            <th class="text-center whitespace-nowrap">Username</th>
                            <th class="text-center whitespace-nowrap">Staff Id</th>
                            <th class="text-center whitespace-nowrap">Position</th>
                            <th class="text-center whitespace-nowrap">Gender</th>
                            <th class="text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="result">
                        @if (request()->has('searchEmployee'))
                        @else
                            @foreach ($employee as $item)
                                <tr class="intro-x h-16" id="data-search-{{ $item->id }}">
                                    <td class="w-4 text-center">
                                        {{ $loop->iteration }}.
                                    </td>
                                    <td class="flex justify-center align-center">
                                        <div class="w-12 h-12 image-fit zoom-in">
                                            @if ($item->avatar)
                                                <img data-action="zoom" class="tooltip rounded-full"
                                                    src="{{ asset('storage/' . $item->avatar) }} "
                                                    title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                            @elseif($item->gender == 'male')
                                                <img data-action="zoom" class="tooltip rounded-full"
                                                    src="{{ asset('images/default-boy.jpg') }} "
                                                    title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                            @elseif($item->gender == 'female')
                                                <img data-action="zoom" class="tooltip rounded-full"
                                                    src="{{ asset('images/default-women.jpg') }} "
                                                    title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="w-50 text-center">
                                        {{ $item->user->name }}
                                    </td>
                                    <td class="text-center">
                                        {{ $item->id_number }}
                                    </td>
                                    <td class="text-center capitalize">
                                        {{ $item->position ? $item->position->name : '-' }}
                                    </td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center">
                                            @if ($item->gender === 'male')
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
                                            @can('edit_employees')
                                                <a class="flex items-center text-success mr-3"
                                                    href="{{ route('employee.edit', $item->id) }}">
                                                    <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                                </a>
                                            @endcan

                                            <a class="flex items-center text-warning delete-button mr-3 show-modal-search" data-email="{{ $item->user->email }}" data-name="{{ $item->user->name }}" data-avatar="{{ $item->avatar }}" data-gender="{{ $item->gender }}" data-firstname="{{ $item->first_name }}" data-LastName="{{ $item->last_name }}" data-stafId="{{ $item->id_number }}" data-Divisi="{{ $item->division->name }}" data-Posisi="{{ $item->position->name }}" data-Address="{{ $item->address }}" data-BirthDate="{{ $item->birth_date }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-search">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Show
                                            </a>
                                            
                                            @can('delete_employees')
                                            <a class="flex items-center text-danger delete-modal-search" data-id="{{ $item->id  }}" data-name="{{ $item->first_name }} {{ $item->last_name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                @if ($employee->count() > 0)
                    <div class="flex justify-center items-center">
                        {{ $employee->links('pagination.custom', [
                            'paginator' => $employee,
                            'prev_text' => 'Previous',
                            'next_text' => 'Next',
                            'slider_text' => 'Showing items from {start} to {end} out of {total}',
                        ]) }}
                    </div>
                @endif
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
                <form action="{{ route('employee.import') }}" method="POST" enctype="multipart/form-data">
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
                            <input name="validNameEmployee" id="crud-form-2" type="text" class="form-control w-full"
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

    {{-- show modallive search --}}
    <div id="show-modal-search" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-lg mx-auto" id="show-detailName"></h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                    <div class="col-span-12 mx-auto">
                        <div class="w-24 h-24 image-fit zoom-in">
                            <img id="show-modal-image" class="tooltip rounded-full" src="">
                        </div>
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Firstname :</label>
                        <input disabled id="show-firstname" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Lastname :</label>
                        <input disabled id="Show-LastName"" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Email :</label>
                        <input disabled id="Show-Email" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Staff Id :</label>
                        <input disabled id="Show-StafId" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Gender :</label>
                        <input disabled id="Show-Gender" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Divisi :</label>
                        <input disabled id="Show-Divisi" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Posisi :</label>
                        <input disabled id="Show-Posisi" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Address :</label>
                        <input disabled id="Show-Address" type="text" class="form-control" value="">
                    </div>
                    <div class="col-span-12 sm:col-span-6">
                        <label class="text-xs">Date of Birth :</label>
                        <input disabled id="Show-BirthDate" type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- end show modal --}}

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#searchEmployee').on('keyup', function() {
                var query = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('employee') }}',
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#result').html(data);
                    }
                });
            });
        });

        $(document).on("click", ".delete-modal-search", function() {
            var DeleteModalid = $(this).attr('data-id');
            var DeleteModalName = $(this).attr('data-name');



            var formAction;
            formAction = '{{ route('employee.destroy', ':id') }}'.replace(':id', DeleteModalid);

            $("#subjuduldelete-confirmation").text('Please type the username "' + DeleteModalName +
                '" of the data to confrim.');
            $("#delete-form-search").attr('action', formAction);
        });

        $(document).on("click", ".show-modal-search", function() {
            var showitemid = $(this).attr('data-name');
            var showAvatar = $(this).attr('data-avatar');
            var ShowGender = $(this).attr('data-gender');
            var ShowFirstname = $(this).attr('data-firstname');
            var ShowLastName = $(this).attr('data-LastName');
            var ShowStafId = $(this).attr('data-stafId');
            var ShowDivisi = $(this).attr('data-Divisi');
            var ShowPosisi = $(this).attr('data-Posisi');
            var ShowAddress = $(this).attr('data-Address');
            var ShowBirthDate = $(this).attr('data-BirthDate');
            var ShowEmail = $(this).attr('data-email');


            var imgSrc;
            if (showAvatar) {
                imgSrc = '{{ asset('storage/' . $item->avatar) }}';
            } else if (ShowGender == 'male') {
                imgSrc = '{{ asset('images/default-boy.jpg') }}';
            } else if (ShowGender == 'female') {
                imgSrc = '{{ asset('images/default-women.jpg') }}';
            }

            $("#show-detailName").text('Detail ' + showitemid);
            $("#show-modal-image").attr('src', imgSrc);
            $("#show-firstname").attr('value', ShowFirstname);
            $("#Show-LastName").attr('value', ShowLastName);
            $("#Show-StafId").attr('value', ShowStafId);
            $("#Show-Gender").attr('value', ShowGender);

            $("#Show-Divisi").attr('value', ShowDivisi);
            $("#Show-Posisi").attr('value', ShowPosisi);
            $("#Show-Address").attr('value', ShowAddress);
            $("#Show-BirthDate").attr('value', ShowBirthDate);
            $("#Show-Email").attr('value', ShowEmail);
        });
    </script>
@endsection
