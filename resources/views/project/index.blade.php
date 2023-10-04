@extends('layouts.master')


@section('content')
    <div class="content">
        <h2 class="intro-y text-lg font-medium mt-10">
            Data Project
        </h2>
        <div class="grid grid-cols-12 gap-6 mt-5">
            <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
                <div class="text-center">
                    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-store-partner"
                        class="btn btn-primary mr-2">Add New Poject</a>
                </div>
                <div class="dropdown" data-tw-placement="bottom">
                    <button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">
                        <span class="w-5 h-5 flex items-center justify-center"> <i class="w-4 h-4" data-lucide="plus"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu w-40">
                        <ul class="dropdown-content">
                            <li>
                                <a href="" class="dropdown-item"> <i data-lucide="file-text"
                                        class="w-4 h-4 mr-2"></i> Export to Excel </a>
                            </li>
                            <li>
                                <a href="" class="dropdown-item"> <i data-lucide="file-text"
                                        class="w-4 h-4 mr-2"></i> Export to PDF </a>
                            </li>
                            <li>
                                <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#import-modal"
                                    class="dropdown-item"> <i data-lucide="file-text" class="w-4 h-4 mr-2"></i> Import Excel
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="hidden md:block mx-auto text-slate-500"></div>
                <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                    <div class="w-56 relative text-slate-500">
                        <input type="text" class="form-control w-56 box pr-10" placeholder="Search..."
                            id="searchProject">
                        <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                    </div>
                </div>
            </div>
            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                <table id="table" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Name</th>
                            <th class="text-center whitespace-nowrap">Partner Name</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tableProject">
                        @foreach ($project as $item)
                            <tr class="intro-x h-16">
                                <td class="w-4 text-center">
                                    {{ $loop->iteration }}.
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    {{ $item->partner->name }}
                                </td>
                                <td class="w-50 text-center capitalize">
                                    @if ($item->end_date > now())
                                        Active
                                    @elseif($item->end_date < now())
                                        Inactive
                                    @endif

                                </td>
                                <td class="table-report__action w-56">
                                    <div class="flex justify-center items-center">

                                        <a data-projectId="{{ $item->id }}" data-projectName="{{ $item->name }}"
                                            data-endDate="{{ $item->end_date }}" data-startDate="{{ $item->start_date }}"
                                            data-projectpartnerId="{{ $item->partner_id }}"
                                            data-partnerId="{{ $item->partner->id }}"
                                            data-partnerName="{{ $item->partner->name }}"
                                            class="flex items-center text-warning mr-3 edit-modal-project-search"
                                            href="javascript:;" data-tw-toggle="modal" data-tw-target="#modal-edit-project">
                                            <i data-lucide="check-square" class="w-4 h-4 mr-1"></i> Edit
                                        </a>

                                        <a data-projectId="{{ $item->id }}"
                                            class="mr-3 flex items-center text-success detail-project-modal-search"
                                            href="javascript:;" data-tw-toggle="modal"
                                            data-tw-target="#detail-project-modal">
                                            <i data-lucide="eye" class="w-4 h-4 mr-1"></i> Detail
                                        </a>

                                        {{-- berfungsi --}}
                                        {{-- <a class="flex items-center text-danger delete-button" href="javascript:;" data-tw-toggle="modal" data-tw-target="#delete-confirmation-modal-{{ $item->id }}">
                                        <i data-lucide="trash-2" class="w-4 h-4  mr-1"></i> Delete
                                        </a> --}}
                                    </div>
                                </td>
                            </tr>

                            <div id="delete-confirmation-modal-{{ $item->id }}" class="modal" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form id="delete-form" method="POST" action="item->id) }}">
                                            @csrf
                                            @method('delete')
                                            <div class="modal-body p-0">
                                                <div class="p-5 text-center">
                                                    <i data-lucide="x-circle"
                                                        class="w-16 h-16 text-danger mx-auto mt-3"></i>
                                                    <div class="text-3xl mt-5">Are you sure?</div>
                                                    <div class="text-slate-500 mt-2">
                                                        Please type the Partner name "{{ $item->name }}" of the data to
                                                        confrim.
                                                    </div>
                                                    <input name="validName" id="crud-form-2" type="text"
                                                        class="form-control w-full" placeholder="Divisi name" required>
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
                        @endforeach
                    </tbody>
                </table>
                @if ($project->count() > 0)
                    <div class="flex justify-center items-center">
                        {{ $project->links('pagination.custom', [
                            'paginator' => $project,
                            'prev_text' => 'Previous',
                            'next_text' => 'Next',
                            'slider_text' => 'Showing items from {start} to {end} out of {total}',
                        ]) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Create project --}}
    <div id="modal-store-partner" class="modal overflow-y-auto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Create-project</h2>
                </div>
                <form action="{{ route('project.store') }}" method="post">
                    @csrf
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Nama Project</label>
                            <select name="partner_id" class="tom-select w-full" id="">
                                @foreach ($partnerall as $item)
                                    <option value="0" selected disabled>Choose Division</option>
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12">
                            <label for="modal-form-2" class="form-label">Nama Project</label>
                            <input id="modal-form-2" name="name" type="text" class="form-control capitalize"
                                placeholder="nama divisi" autocomplete="off">
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="modal-form-2" class="form-label">Start Date</label>
                            <div class="relative w-56">
                                <div
                                    class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="start_date" class="datepicker form-control pl-12"
                                    data-single-mode="true">
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="modal-form-2" class="form-label">End Date</label>
                            <div class="relative w-56">
                                <div
                                    class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="end_date" class="datepicker form-control pl-12"
                                    data-single-mode="true">
                            </div>
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

    {{-- detail modal Project --}}
    <div id="detail-project-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-lg mx-auto">Project</h2>
                </div>
                <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                </div>
                <table id="table" class="table table-report -mt-2">
                    <thead>
                        <tr>
                            <th data-priority="1" class="whitespace-nowrap">No</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Project Name</th>
                            <th data-priority="2" class="text-center whitespace-nowrap">Partner</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- detail modal Project end --}}

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
    <div id="modal-edit-project" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Project</h2>
                </div>
                <form id="edit-project-modal" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <div class="modal-body grid grid-cols-12 gap-4 gap-y-3">
                        <div class="col-span-12">
                            <label class="form-label">Nama Project</label>
                            <input id="name-project-modal" value="" name="name" type="text"
                                class="form-control capitalize" placeholder="nama Project" autocomplete="off">
                        </div>
                        <div class="col-span-12">
                            <label for="modal-form-1" class="form-label">Partner</label>
                            <select name="partner_id" id="project-partner-select" class="w-full "
                                style="background-color: #1B253b;">
                                @foreach ($partnerall as $itemDivisi)
                                    <option value="{{ $itemDivisi->id }}">
                                        {{ $itemDivisi->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="modal-form-2" class="form-label">Start Date</label>
                            <div class="relative w-56">
                                <div
                                    class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="start_date" id="start-date-modal" value=""
                                    class="datepicker form-control pl-12" data-single-mode="true">
                            </div>
                        </div>
                        <div class="col-span-12 sm:col-span-6">
                            <label for="modal-form-2" class="form-label">End Date</label>
                            <div class="relative w-56">
                                <div
                                    class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </div>
                                <input type="text" name="end_date" id="end-date-modal" value=""
                                    class="datepicker form-control pl-12" data-single-mode="true">
                            </div>
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

    <script type="text/javascript">
        // search
        jQuery(document).ready(function($) {
            $('#searchProject').on('keyup', function() {
                var query = $(this).val();
                $.ajax({
                    type: 'GET',
                    url: '{{ route('project') }}',
                    data: {
                        query: query
                    },
                    success: function(data) {
                        $('#tableProject').html(data);
                    }
                });
            });
        });

        // edit-modal
        $(document).on("click", ".edit-modal-project-search", function() {
            var EditProjectid = $(this).attr('data-projectid');
            var editProjectName = $(this).attr('data-projectName');
            var editEnddateYMD = $(this).attr('data-endDate');
            var editStartdateYMD = $(this).attr('data-startDate');
            var editPartnerSelectid = $(this).attr('data-projectpartnerId');
            var editPartnerSelectName = $(this).attr('data-partnerName');


            var formAction;
            formAction = '{{ route('project.update', ':id') }}'.replace(':id', EditProjectid);


            $("#edit-project-modal").attr('value', formAction); // bug!! di tampilan di haruskan menggunakan ini
            $("#end-date-modal").val(editEnddateYMD); // bug!! di tampilan di haruskan menggunakan ini
            $("#end-date-modal").attr('value', editEnddateYMD);
            $("#start-date-modal").val(editStartdateYMD); // bug!! di tampilan di haruskan menggunakan ini
            $("#start-date-modal").attr('value', editStartdateYMD);
            $("#name-project-modal").attr('value', editProjectName);


            // Cari option dengan value yang sesuai dan tandai sebagai yang dipilih
            $("#project-partner-select option").each(function() {
                if ($(this).val() == editPartnerSelectid) {
                    $(this).attr("selected", true);
                } else {
                    $(this).removeAttr("selected");
                }
            });



        })
    </script>
@endsection
