@extends('layouts.master')


@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Pegawai
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <button class="btn btn-primary shadow-md mr-2"><a href="{{ route('employee.create') }}">Add Employee</a></button>
            <div class="dropdown">
                <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                    <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i> </span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="{{ route('employee.excel') }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel </a>
                        </li>
                        <li>
                            <a href="{{ route('employee.pdf') }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF </a>
                        </li>
                        <li>
                            <a href="javascript:;" class="dropdown-item" data-tw-target="#import-modal" data-tw-toggle="modal"><i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Import Excel </a>
                        </li>
                        <li>
                            <a href="{{ route('employee.trash') }}" class="dropdown-item"><i data-lucide="archive" class="w-4 h-4 mr-2"></i> Restore Data </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="hidden md:block mx-auto text-slate-500"></div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" id="search" class="form-control w-56 pr-10" placeholder="Search...">
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
                    @foreach ($employee as $item)
                    <tr class="intro-x h-16" id="data-search">
                        <td class="w-4 text-center">
                            {{ $loop->iteration }}.
                        </td>
                        <td class="flex justify-center align-center">
                            <div class="w-12 h-12 image-fit zoom-in">
                                @if ($item->avatar)
                                    <img data-action="zoom" class="tooltip rounded-full" src="{{ asset('storage/'.$item->avatar) }} " title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                @elseif($item->gender == 'male')
                                    <img data-action="zoom" class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }} " title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
                                @elseif($item->gender == 'female')
                                    <img data-action="zoom" class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }} " title="Uploaded at {{ $item->updated_at ? $item->updated_at->format('d M Y') : '?' }}">
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
                                <a class="flex items-center text-pending mr-3" href="{{ route('employee.edit',$item->id) }}"> <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit </a>
                                <a class="flex items-center delete-button mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-{{ $item->id }}">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Show
                                </a>
                                <a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-{{ $item->id }}">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div id="delete-confirmation-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="delete-form" method="POST" action="{{ route('employee.destroy',$item->id) }}">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-body p-0">
                                        <div class="p-5 text-center">
                                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                            <div class="text-3xl mt-5">Are you sure?</div>
                                            <div class="text-slate-500 mt-2">
                                                Do you really want to delete {{ $item->first_name }}?
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

                    <div id="show-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="font-medium text-lg mx-auto">Detail {{ $item->user->name }}</h2>
                                </div>
                                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                    <div class="col-span-12 mx-auto">
                                        <div class="w-24 h-24 image-fit zoom-in">
                                            @if ($item->avatar)
                                                <img class="tooltip rounded-full" src="{{ asset('storage/'.$item->avatar) }}">
                                            @elseif($item->gender == 'male')
                                                <img class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                            @elseif($item->gender == 'female')
                                                <img class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                            @endif
                                        </div>
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
                                        <label for="modal-form-1" class="text-xs">Staff Id :</label>
                                        <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->id_number }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-2" class="text-xs">Gender :</label>
                                        <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->gender }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-1" class="text-xs">Divisi :</label>
                                        <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->division->name }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-2" class="text-xs">Posisi :</label>
                                        <input disabled id="modal-form-2" type="text" class="form-control" value="{{$item->position->name}}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-1" class="text-xs">Address :</label>
                                        <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->address }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-2" class="text-xs">Date of Birth :</label>
                                        <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->birth_date }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
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
                <h2 class="font-medium text-base mr-auto">Broadcast Message</h2> <button class="btn btn-outline-secondary hidden sm:flex"> <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs </button>
                <div class="dropdown sm:hidden"> <a class="dropdown-toggle w-5 h-5 block" href="javascript:;" aria-expanded="false" data-tw-toggle="dropdown"> <i data-lucide="more-horizontal" class="w-5 h-5 text-slate-500"></i> </a>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            <li> <a href="javascript:;" class="dropdown-item"> <i data-lucide="file" class="w-4 h-4 mr-2"></i> Download Docs </a> </li>
                        </ul>
                    </div>
                </div>
            </div>
            <form action="{{ route('employee.import') }}" method="POST" enctype="multipart/form-data">
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







