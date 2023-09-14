@extends('layouts.master')

@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Kehadiran
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="dropdown" data-tw-placement="bottom-start">
                <button class="dropdown-toggle btn btn-primary px-2" aria-expanded="false" data-tw-toggle="dropdown">
                    Export <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i></span>
                </button>
                <div class="dropdown-menu w-40 items-end">
                    <ul class="dropdown-content">
                        <li>
                            <a href="{{ route('presence.excel',['year' => $today->year]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Excel {{ $today->year }} </a>
                        </li>
                        <li>
                            <a href="{{ route('presence.excel',['year' => $today->subyear()->year ]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Excel {{ $today->year }} </a>
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
                        <th class="text-center whitespace-nowrap">Image</th>
                        <th class="text-center whitespace-nowrap">Username</th>
                        <th class="text-center whitespace-nowrap">Position</th>
                        <th class="text-center whitespace-nowrap">Jenis Kehadiran</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($presenceData as $item)

                    <tr class="intro-x h-16">
                        <td class="w-4 text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="flex justify-center align-center">
                            <div class="w-12 h-12 image-fit zoom-in">
                                @if ($item->user->employee->avatar)
                                    <img data-action="zoom" class="rounded-full" src="{{ asset('storage/'.$item->user->employee->avatar) }}">
                                @elseif($item->user->employee->gender == 'male')
                                    <img data-action="zoom" class="rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                @elseif($item->user->employee->gender == 'female')
                                    <img data-action="zoom" class="rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                @endif
                            </div>
                        </td>
                        <td class="w-50 text-center">
                            {{ $item->user->name }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item['user']['employee']['position']['name'] }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center text-success delete-button mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-{{$item->id}}-modal">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div id="detail-{{ $item->id }}-modal" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h2 class="font-medium text-lg mx-auto">Detail Kehadiran</h2>
                                </div>
                                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                    <div class="col-span-12 mx-auto">
                                        <div class="w-24 h-24 image-fit zoom-in">
                                            @if ($item->user->employee->avatar)
                                                <img class="tooltip rounded-full" src="{{ asset('storage/'.$item->user->employee->avatar) }}">
                                            @elseif($item->user->employee->gender == 'male')
                                                <img class="tooltip rounded-full" src="{{ asset('images/default-boy.jpg') }}">
                                            @elseif($item->user->employee->gender == 'female')
                                                <img class="tooltip rounded-full" src="{{ asset('images/default-women.jpg') }}">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-1" class="text-xs">Firstname :</label>
                                        <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->user->employee->first_name }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-2" class="text-xs">Lastname :</label>
                                        <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->last_name }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-2" class="text-xs">Staff Id :</label>
                                        <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->id_number }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-2" class="text-xs">Position :</label>
                                        <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->user->employee->position->name }}">
                                    </div>
                                    <div class="col-span-12 sm:col-span-6">
                                        <label for="modal-form-1" class="text-xs">Category :</label>
                                        <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $item->category === 'work_trip' ? 'Work Trip' : $item->category }}">
                                    </div>
                                    @if ($item->category == 'WFO')
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-2" class="text-xs">Entry Time  :</label>
                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->entry_time }} WIB">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-2" class="text-xs">Exit Time  :</label>
                                            <input disabled id="modal-form-2" type="text" class="form-control" value="{{ $item->exit_time }} WIB">
                                        </div>
                                    @endif
                                    @if ($item->category == 'telework')
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-2" class="text-xs">Telework Category  :</label>
                                            <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $item->telework->telework_category }}">
                                        </div>
                                        @if ($item->telework->category_description)
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Category Description :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $item->telework->category_description }}">
                                        </div>
                                        @endif
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-2" class="text-xs">Temporary Entry Time  :</label>
                                            <input disabled id="modal-form-2" type="text" class="form-control capitalize" value="{{ $item->temporary_entry_time }}">
                                        </div>
                                    @endif
                                    @if ($item->category == 'work_trip' )
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Start Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->start_date }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">End Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->end_date }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Entry Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->entry_date }}">
                                        </div>
                                    @elseif ($item->category == 'leave')
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Type Leave :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control capitalize" value="{{ $item->type }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Type Description :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->type_description }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Submission Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->submission_date }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Start Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->start_date }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">End Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->end_date }}">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Total Leave Days :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->total_leave_days }} Days">
                                        </div>
                                        <div class="col-span-12 sm:col-span-6">
                                            <label for="modal-form-1" class="text-xs">Entry Date :</label>
                                            <input disabled id="modal-form-1" type="text" class="form-control" value="{{ $item->entry_date }}">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @endforeach



                </tbody>
            </table>
            @if ($presenceData->count() > 0)
            <div class="flex justify-center items-center">
                {{ $presenceData->links('pagination.custom', [
                    'paginator' => $presenceData,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                    'slider_text' => 'Showing items from {start} to {end} out of {total}',
                ]) }}
            </div>
            @else
                <h1 class="text-center">Tidak ada data absensi hari ini</h1>
            @endif
        </div>
    </div>
</div>
@endsection
