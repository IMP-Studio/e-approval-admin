@extends('layouts.master')

@section('content')
<div class="content">
    <h2 class="intro-y text-lg font-medium mt-10">
        Data Standup
    </h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            @can('export_standups')
            <div class="dropdown" data-tw-placement="bottom-start">
                <button class="dropdown-toggle btn btn-primary px-2" aria-expanded="false" data-tw-toggle="dropdown">
                    Export <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i></span>
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
            @endcan
            <div class="hidden md:block mx-auto text-slate-500"></div>
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <div class="w-56 relative text-slate-500">
                    <input type="text" class="form-control w-56 box pr-10" placeholder="Search..." id="search">
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
                        <th class="text-center whitespace-nowrap">Project name</th>
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
                        <td class="text-start">
                            {{ $item->project->name }}
                        </td>
                        <td class="text-center">
                            {{ $item->doing }}
                        </td>
                        <td class="w-40 text-center text-warning">
                            {{ $item->blocker ? $item->blocker : '-' }}
                        </td>
                        <td class="table-report__action w-56">
                            <div class="flex justify-center items-center">
                                <a class="flex items-center text-warning  mr-3 detail-standup-modal-search" href="javascript:;" data-tw-toggle="modal" data-StandupDetailId="{{ $item->id }}" data-StandupDetailName="{{ $item->user->name }}" data-StandupDetailDone="{{ $item->done }}" data-StandupDetailDoing="{{ $item->doing }}" data-StandupDetailBlocker="{{ $item->blocker }}" data-tw-target="#detail-modal-search">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                </a>
                                <a class="flex items-center text-danger delete-standup-modal-search" data-DeleteStandupId="{{ $item->id }}" data-DeleteStandupName="{{ $item->user->name }}" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-search">
                                    <i data-lucide="trash-2" class="w-4 h-4 mr-1"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div id="delete-confirmation-modal-{{ $item->id }}" class="modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form id="delete-form" method="POST" action="{{ route('standup.destroy',$item->id) }}">
                                    @csrf
                                    @method('delete')
                                    <div class="modal-body p-0">
                                        <div class="p-5 text-center">
                                            <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                            <div class="text-3xl mt-5">Are you sure?</div>
                                            <div class="text-slate-500 mt-2">
                                                Please type the username "{{ $item->user->employee->first_name }} {{ $item->user->employee->last_name }}" of the data to confrim.
                                            </div>
                                             <input name="validName" id="crud-form-2" type="text" class="form-control w-full" placeholder="User name" required>
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

{{-- detail modal search --}}
<div id="detail-modal-search" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="font-medium text-base mx-auto" id="show-name-standup-search"></h1>
            </div>
            <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                <div class="col-span-12">
                    <label for="modal-form-1" class="form-label">Done :</label>
                    <textarea disabled name="" class="form-control" id="show-done-standup-search" rows="3"></textarea>
                </div>
                <div class="col-span-12">
                    <label for="modal-form-2" class="form-label">Doing :</label>
                    <textarea disabled name="" class="form-control" id="show-doing-standup-search" rows="3"></textarea>
                </div>
                <div class="col-span-12" id="show-blocker-standup-search">
                    
                </div>
            </div>
        </div>
    </div>
</div>
{{-- detail modal search end --}}

{{-- delete modal search--}}
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
                        <input name="validName" id="crud-form-2" type="text" class="form-control w-full" placeholder="User name" required>
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
{{-- delete modal search end --}}

<script type="text/javascript">
    jQuery(document).ready(function($) {
            $('#search').on('keyup', function() {
                var query = $(this).val();
                $.ajax({
                type: 'GET',
                url: '{{ route('standup') }}',
                data: { query: query },
                success: function(data) {
                    $('tbody').html(data);
                }
            });
        });
    });

    $(document).on("click", ".detail-standup-modal-search", function () {
        var showitemid = $(this).attr('data-StandupDetailId');
        var showName = $(this).attr('data-StandupDetailName');
        var ShowDone = $(this).attr('data-StandupDetailDone');
        var ShowDoing= $(this).attr('data-StandupDetailDoing');
        var ShowBlocker = $(this).attr('data-StandupDetailBlocker');


        var BlockerContent;
        if(ShowBlocker){
            BlockerContent = '<label for="modal-form-2" class="form-label">Blocker :</label> <textarea disabled name="" class="form-control" id="" rows="3">'+ ShowBlocker +'</textarea>';
        }

        $("#show-blocker-standup-search").html(BlockerContent);
        $("#show-name-standup-search").text('Detail Standup '+ showName);
        $("#show-done-standup-search").text(ShowDone);
        $("#show-doing-standup-search").text(ShowDoing);
    });

         $(document).on("click", ".delete-standup-modal-search", function () {
            var DeleteStandupModalid = $(this).attr('data-DeleteStandupId');
            var DeleteStandupModalName = $(this).attr('data-DeleteStandupName');

            var formAction;
            formAction = '{{ route("standup.destroy",":id") }}'.replace(':id',  DeleteStandupModalid);

            $("#subjuduldelete-confirmation").text('Please type the name "'+ DeleteStandupModalName +'" of the data to confrim.');
            $("#delete-form-search").attr('action', formAction);
        });
 </script>
@endsection
