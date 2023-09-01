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
        <h2 class="text-lg font-medium mr-auto mb-2">
            Form Layout
        </h2>
    </div>
    <div class="intro-y box p-5">
        <form action="{{ route('employee.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-12 xl:col-span-6">
                        <div>
                            <label for="crud-form-1" class="form-label">Firstname</label>
                            <input name="first_name" id="crud-form-1" type="text" class="form-control w-full" placeholder="Firstname" required>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-2" class="form-label">Lastname</label>
                            <input name="last_name" id="crud-form-2" type="text" class="form-control w-full" placeholder="Lastname" required>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-3" class="form-label">ID Number</label>
                            <input name="id_number" id="crud-form-3" type="number" class="form-control" placeholder="ID Number" required>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-5" class="form-label">Division :</label>
                            <select name="division" id="crud-form-5" class="tom-select w-full capitalize" onchange="dapetPosisi()" required>
                                <option value="0" selected disabled>Choose</option>
                                @foreach ($division as $division)
                                    <option value="{{ $division->id }}">{{ $division->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-6" class="form-label">Position :</label>
                            <select name="position" id="position" class="form-select w-full capitalize" required>
                            </select>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Upload Image :</label>
                            <div id="upload-img" class="border-2 w-1/2 border-dashed dark:border-darkmode-400 rounded-md pt-4 flex items-center justify-center">
                                <div class="flex flex-wrap">
                                    <div class="w-16 h-16 relative image-fit mb-5 mr-5 cursor-pointer zoom-in"
                                        id="previewContainer" style="display: none;">
                                        <img class="rounded-md" id="preview" src="" style=""
                                            data-action="zoom">
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
                                        name="avatar" id="logoInput" onchange="previewImage(event)" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-12 xl:col-span-6">
                        <div class="mt-3">
                            <label for="crud-form-6" class="form-label">Gender :</label>
                            <div class="flex flex-col sm:flex-row mt-1">
                                <div class="form-check mr-2">
                                    <input name="gender" id="radio-switch-4" class="form-check-input w-4 h-4" type="radio" name="horizontal_radio_button" value="male">
                                    <label class="form-check-label" for="radio-switch-4">Male</label>
                                </div>
                                <div class="form-check mr-2 mt-2 sm:mt-0">
                                    <input name="gender" id="radio-switch-5" class="form-check-input w-4 h-4" type="radio" name="horizontal_radio_button" value="female">
                                    <label class="form-check-label" for="radio-switch-5">Female</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="" class="form-label">Birth Date</label>
                            <input class="form-control" type="date" name="birth_date" id="">
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-7" class="form-label">Address :</label>
                            <textarea class="form-control" name="address" id="" rows="4" required></textarea>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-7" class="form-label">Username :</label>
                            <input type="text" name="username" placeholder="username" class="form-control" required>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-7" class="form-label">Email :</label>
                            <input type="email" name="email" placeholder="email" class="form-control" required>
                        </div>
                        <div class="mt-3">
                            <label for="crud-form-7" class="form-label">Password :</label>
                            <input type="text" name="password" value="password@123" class="form-control" required>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript">
    let jQ = $.noConflict();

    function dapetPosisi() {
        let divisionId = jQ("#crud-form-5").val();
        let positionSelect = jQ("#position");

        positionSelect.empty().append('<option value="" selected disabled>Loading...</option>');

        if (divisionId) {
            jQ.ajax({
                url: "{{ url('employee/get-positions/') }}" + "/" + divisionId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    positionSelect.empty().append('<option value="0" selected disabled>Choose Position</option>');
                    response.data.forEach(function(position) {
                        positionSelect.append('<option value="' + position.id + '">' + position.name + '</option>');
                        console.log(position.name)
                    });
                },
                error: function() {
                    positionSelect.empty().append('<option value="0" selected disabled>Error loading positions</option>');
                }
            });
        } else {
            positionSelect.empty().append('<option value="0" selected disabled>Choose Division first</option>');
        }
    }

    function previewImage(event) {
            const input = event.target;
            const reader = new FileReader();
            const previewContainer = document.getElementById("previewContainer");

            reader.onload = function(){
                const img = document.getElementById("preview");
                img.src = reader.result;
                previewContainer.style.display = "block";
                document.getElementById('desc-img').classList.add('close-img');
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
            } else {
                const img = document.getElementById("preview");
                img.src = "";
                previewContainer.style.display = "none";
            }
    }
    function removeImage() {
        const img = document.getElementById("preview");
        const previewContainer = document.getElementById("previewContainer");
        const input = document.getElementById("logoInput");
        img.src = "";
        previewContainer.style.display = "none";
        input.value = "";
        document.getElementById('desc-img').classList.remove('close-img');
    }


</script>
@endpush
