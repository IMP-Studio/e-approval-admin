@extends('layouts.master')

@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Kehadiran
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="dropdown">
                <button class="dropdown-toggle btn btn-primary px-2" aria-expanded="false" data-tw-toggle="dropdown">
                    More <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i></span>
                </button>
                <div class="dropdown-menu w-40 items-end">
                    <ul class="dropdown-content">
                        <li>
                            <a href="" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to Excel </a>
                        </li>
                        <li>
                            <a href="" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Export to PDF </a>
                        </li>
                        <li>
                            <a href="javascript:;" class="dropdown-item" data-tw-target="#import-modal" data-tw-toggle="modal"><i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Import Excel </a>
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
        <!-- BEGIN: Data List -->
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
            <table class="table table-report -mt-2">
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">No</th>
                        <th class="text-center whitespace-nowrap">Username</th>
                        <th class="text-center whitespace-nowrap">Image</th>
                        <th class="text-center whitespace-nowrap">Position</th>
                        <th class="text-center whitespace-nowrap">Jenis Kehadiran</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($absensi as $item)
                        <tr class="intro-x h-16">
                            <td class="w-4 text-center">
                                {{ $loop->iteration }}
                            </td>
                            <td class="w-50 text-center">
                                {{ $item->user->name }}
                            </td>
                            <td class="flex justify-center align-center">
                                <div class="w-12 h-12 image-fit zoom-in">
                                    <img data-action="zoom" class="tooltip rounded-full" src="{{ asset('images/'.$item->user->employee->avatar) }}" title="uploaded at {{ $item->updated_at->format('j F Y')  }}">
                                </div>
                            </td>
                            <td class="text-center capitalize">
                                {{ $item->user->employee->position->name }}
                            </td>
                            <td class="text-center capitalize">
                                {{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}
                            </td>

                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    {{-- <a class="flex items-center text-pending mr-3" href=""> <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit </a> --}}
                                    <a class="flex items-center text-warning delete-button mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#show-modal-{{ $item->id }}">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Show
                                    </a>
                                    <a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-">
                                        <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <div id="show-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h2 class="font-medium text-lg mx-auto">Detail Kehadiran {{ $item->user->name }}</h2>
                                    </div>
                                    <form action="" method="post">
                                        <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                            <div class="col-span-12 mx-auto">
                                                <div class="w-24 h-24 image-fit zoom-in">
                                                    <img class="tooltip rounded-full" src="{{ asset('images/'.$item->user->employee->avatar) }}">
                                                </div>
                                            </div>
                                            {{-- <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Firstname :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->user->employee->firstname }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Lastname :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->user->employee->lastname }}">
                                            </div> --}}
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Staff Id :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->id_number }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-2" class="text-xs">Division :</label>
                                                <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->division->name }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Jenis Kehadiran :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->category }}">
                                            </div>
                                            @if ($item->keterangan_wfa)
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-2" class="text-xs">keterangan Kehadiran  :</label>
                                                    <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->keterangan_wfa }}">
                                                </div>
                                            @endif
                                            @if ($item->alasan_wfa)
                                                <div class="col-span-12 sm:col-span-6">
                                                    <label for="modal-form-1" class="text-xs">Alasan Kehadiran :</label>
                                                    <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->alasan_wfa }}">
                                                </div>
                                            @endif
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Tanggal :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->date }}">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Waktu Masuk :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->entry_time }} WIB">
                                            </div>
                                            <div class="col-span-12 sm:col-span-6">
                                                <label for="modal-form-1" class="text-xs">Waktu Keluar :</label>
                                                <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->exit_time ? $item->exit_time : '-' }}">
                                            </div>


                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach


                    <div id="delete-confirmation-modal-" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="delete-form" method="POST" action="">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-body p-0">
                                        <div class="p-5 text-center">
                                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                            <div class="text-3xl mt-5">Are you sure?</div>
                                            <div class="text-slate-500 mt-2">
                                                Do you really want to delete?
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
                </tbody>
            </table>
            <div class="flex justify-center items-center">
                {{ $absensi->links('pagination.custom', [
                    'paginator' => $absensi,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                    'slider_text' => 'Showing items from {start} to {end} out of {total}',
                ]) }}
            </div>
        </div>
    </div>
</div>
@endsection
