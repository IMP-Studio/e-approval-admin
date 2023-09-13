@extends('layouts.master')

@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Standup
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="dropdown">
                <button class="dropdown-toggle btn btn-primary px-2" aria-expanded="false" data-tw-toggle="dropdown">
                    More <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i></span>
                </button>
                <div class="dropdown-menu w-40">
                    <ul class="dropdown-content">
                        <li>
                            <a href="{{ route('standup.excel',['year' => $today->year]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Excel {{ $today->year }} </a>
                        </li>
                        <li>
                            <a href="{{ route('standup.excel',['year' => $today->subyear()->year ]) }}" class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Excel {{ $today->year }} </a>
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
                        <th class="text-center whitespace-nowrap">Position</th>
                        <th class="text-center whitespace-nowrap">Doing</th>
                        <th class="text-center whitespace-nowrap">Blocker</th>
                        <th class="text-center whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($standup_today as $item)
                    <tr class="intro-x h-16">
                        <td class="w-4 text-center">
                            {{ $loop->iteration }}
                        </td>
                        <td class="w-50 text-center">
                            {{ $item->user->name }}
                        </td>
                        <td class="text-center capitalize">
                            {{ $item->user->employee->position->name }}
                        </td>
                        <td class="text-center">
                            {{ $item->doing }}
                        </td>
                        <td class="w-40 text-center text-warning">
                            {{ $item->blocker ? $item->blocker : '-' }}
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center text-success delete-button mr-3" href="javascript:;" data-tw-toggle="modal" data-tw-target="#detail-{{$item->id}}-modal">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    <div id="detail-{{$item->id}}-modal" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="font-medium text-base mx-auto">Detail Standup {{ $item->user->name }}</h1>
                                </div>
                                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                                    <div class="col-span-12">
                                        <label for="modal-form-1" class="form-label">Done :</label>
                                        <textarea disabled name="" class="form-control" id="" rows="3">{{ $item->done }}</textarea>
                                    </div>
                                    <div class="col-span-12">
                                        <label for="modal-form-2" class="form-label">Doing :</label>
                                        <textarea disabled name="" class="form-control" id="" rows="3">{{ $item->doing }}</textarea>
                                    </div>
                                    @if ($item->blocker)
                                    <div class="col-span-12">
                                        <label for="modal-form-2" class="form-label">Blocker :</label>
                                        <textarea disabled name="" class="form-control" id="" rows="3">{{ $item->blocker }}</textarea>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
            @if ($standup_today->count() > 0)
            <div class="flex justify-center items-center">
                {{ $standup_today->links('pagination.custom', [
                    'paginator' => $standup_today,
                    'prev_text' => 'Previous',
                    'next_text' => 'Next',
                    'slider_text' => 'Showing items from {start} to {end} out of {total}',
                ]) }}
            </div>
            @else
            <h1 class="text-center">Tidak ada standup hari ini</h1>
            @endif
        </div>



    </div>
</div>
@endsection
