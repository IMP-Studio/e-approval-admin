@extends('layouts.master')
@push('css')
    <style>
        .hasImage:hover section {
            background-color: rgba(5, 5, 5, 0.4);
        }

        .hasImage:hover button:hover {
            background: rgba(5, 5, 5, 0.45);
        }

        #overlay p,
        i {
            opacity: 0;
        }

        #overlay.draggedover {
            background-color: rgba(255, 255, 255, 0.7);
        }

        #overlay.draggedover p,
        #overlay.draggedover i {
            opacity: 1;
        }

        .group:hover .group-hover\:text-blue-800 {
            color: #2b6cb0;
        }
        .close-img{
            display: none;
        }
    </style>
@endpush
@section('content')
    <div class="content">
        <div class="intro-y flex items-center mt-8">
            <h2 class="text-lg font-medium mr-auto">
                Form Edit {{ $employee->first_name }}
            </h2>
        </div>
        <div class="intro-y box p-5">
            <form action="{{ route('employee.update',$employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 xl:col-span-6">
                            <div>
                                <label for="crud-form-1" class="form-label">Firstname</label>
                                <input value="{{ $employee->first_name }}" name="first_name" id="crud-form-1" type="text" class="form-control w-full" placeholder="Firstname">
                            </div>
                            <div class="mt-3">
                                <label for="crud-form-2" class="form-label">Lastname</label>
                                <input value="{{ $employee->last_name }}" name="last_name" id="crud-form-2" type="text" class="form-control w-full" placeholder="Lastname">
                            </div>
                            <div class="mt-3">
                                <label for="crud-form-3" class="form-label">Staff Id</label>
                                <input value="{{ $employee->id_number }}" name="id_number" id="crud-form-3" type="number" class="form-control" placeholder="Staff Id">
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Upload Image :</label>
                                <div id="upload-img" class="border-2 w-1/2 border-dashed dark:border-darkmode-400 rounded-md pt-4 flex items-center justify-center">
                                    <div class="flex flex-wrap">
                                        <div class="w-16 h-16 relative image-fit mb-5 mr-5 cursor-pointer zoom-in"
                                            id="previewContainer" style="">
                                            @if ($employee->avatar)
                                            <img class="rounded-md" id="preview" src="{{ asset('storage/'.$employee->avatar) }}" style=""
                                            data-action="zoom">
                                            @elseif($employee->gender == 'male')
                                            <img class="rounded-md" id="preview" src="{{ asset('images/default-boy.jpg') }}" style=""
                                            data-action="zoom">
                                            @elseif($employee->gender == 'female')
                                            <img class="rounded-md" id="preview" src="{{ asset('images/default-women.jpg') }}" style=""
                                            data-action="zoom">
                                            @endif
                                                <div title="Remove this image?"
                                                class="tooltip w-5 h-5 flex items-center justify-center absolute rounded-full text-white bg-danger right-0 top-0 -mr-2 -mt-2"
                                                onclick="removeImage()"> <i data-lucide="x" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="desc-img" class="pb-6 flex items-center cursor-pointer relative">
                                        <i data-lucide="image" class="w-8 h-8 mr-2 text-success"></i> <span
                                            class="text-success text-bold font-medium mr-1">Upload a file</span>
                                        <input class="w-full h-full top-0 left-0 absolute opacity-0" type="file"
                                            name="avatar" id="logoInput" onchange="previewImage(event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-12 xl:col-span-6">
                            <div class="mt-3">
                                <label for="crud-form-6" class="form-label">Gender :</label>
                                <div class="flex flex-col sm:flex-row mt-1">
                                    <div class="form-check mr-2">
                                        <input name="gender" id="radio-switch-4" class="form-check-input w-4 h-4" type="radio" name="horizontal_radio_button" value="male" {{ $employee->gender === 'male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radio-switch-4">Male</label>
                                    </div>
                                    <div class="form-check mr-2 mt-2 sm:mt-0">
                                        <input name="gender" id="radio-switch-5" class="form-check-input w-4 h-4" type="radio" name="horizontal_radio_button" value="female" {{ $employee->gender === 'female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="radio-switch-5">Female</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="crud-form-5" class="form-label">Divisi :</label>
                                <select name="position" data-placeholder="Select your favorite actors" data-positionId="{{ $employee->position_id }}" class="tom-select w-full capitalize" id="inputDivisiEmploye">
                                    @foreach ($division as $item)
                                        <option value="{{ $item->id }}" {{ $employee->division_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-3">
                                <label for="crud-form-5" class="form-label">Position :</label>
                                <select name="position" data-placeholder="Select your favorite actors" class="form-select w-full" id="inputPositionEmploye">
                                    @foreach ($position as $item)
                                        <option value="{{ $item->id }}" {{ $employee->position_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mt-3">
                                <div class="relative w-56">
                                    <div class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="birth_date" value="{{ $employee->birth_date }}" class="datepicker form-control pl-12" data-single-mode="true">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label for="crud-form-7" class="form-label">Address :</label>
                                <textarea class="form-control" name="address" id="" rows="4">{{ $employee->address }}</textarea>
                            </div>

                        </div>
                </div>
                <div class="text-right mt-5">
                    <button type="button" class="btn btn-outline-secondary w-24 mr-1"><a href="{{ route('employee') }}">Cancel</a></button>
                    <button type="submit" class="btn btn-primary w-24">Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    <script>
    jQuery(document).ready(function($) {
       $('#inputDivisiEmploye').change(function () {
        var selectedDivisionId = $(this).val();
        var positionid = $(this).attr('data-positionId');

        $.ajax({
            url: '/employee/getPositionEdit',
            method: 'GET',
            data: { 
                division_id: selectedDivisionId, 
                position_id: positionid 
            },
            dataType: 'json',
            success: function (data) {
                console.log(data);
                var positionSelect = $('#inputPositionEmploye');
                    positionSelect.empty();
                $('#inputPositionEmploye').html(data.options);
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
    });


    function previewImage(event) {
        let input = event.target;
        let reader = new FileReader();
        let previewContainer = document.getElementById("previewContainer");
        let img = document.getElementById("preview");
        let file = document.getElementById("logoInput");

        reader.onload = function(){
            img.src = reader.result;
            previewContainer.style.display = "block";
            document.getElementById('desc-img').classList.add('close-img');
            file.value = "{{ asset('storage/'.$employee->avatar) }}";
        };

        if (input.files && input.files[0]) {
            reader.readAsDataURL(input.files[0]);
        } else {
            img.src = "{{ asset('storage/'.$employee->avatar) }}";
            previewContainer.style.display = "none";
        }
    }
    function removeImage() {
        let img = document.getElementById("preview");
        let previewContainer = document.getElementById("previewContainer");
        let input = document.getElementById("logoInput");
        img.src = "";
        previewContainer.style.display = "none";
        input.value = "";
        document.getElementById('desc-img').classList.remove('close-img');
    }
    </script>
@endpush
