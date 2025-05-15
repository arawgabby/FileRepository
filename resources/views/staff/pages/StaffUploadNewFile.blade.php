@extends('staff.dashboard.staffDashboard')
@section('title', 'Upload New File')
@section('content')

<div class="container mx-auto p-6 bg-white  shadow-md">

    <h1 class="-m-6 mb-6 pb-2 text-3xl font-bold border-b border-gray-300 p-6">
        Upload New File
    </h1>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Left Column - Form Fields -->
        <div class="w-full md:w-1/2">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="category" class="block text-lg font-bold text-gray-700">Enter File Category Type</label>
                    <select name="category" id="category" class="mt-1 p-2 border rounded w-full" required>
                        <option value="">Select Category</option>
                        <option value="capstone">Capstone</option>
                        <option value="thesis">Thesis</option>
                        <option value="faculty_request">Faculty Request</option>
                        <option value="accreditation">Accreditation</option>
                        <option value="admin_docs">Admin Documents</option>
                    </select>
                </div>

                <!-- Accreditation Extra Fields (hidden by default) -->
                <div class="mb-4" id="accreditationFields" style="display: none;">
                    <label class="block text-lg font-bold text-gray-700 mb-2">Accreditation Details</label>
                    <select name="level" id="level" class="mt-1 p-2 border rounded w-full mb-2">
                        <option value="">Select Level</option>
                        <option value="Level 1">Level 1</option>
                        <option value="Level 2">Level 2</option>
                        <option value="Level 3">Level 3</option>
                        <option value="phase 1">Phase 1</option>
                        <option value="phase 2">Phase 2</option>
                        <option value="Level 4">Level 4</option>
                    </select>
                    <select name="area" id="area" class="mt-1 p-2 border rounded w-full mb-2">
                        <option value="">Select Area</option>
                        <option value="1">1-VISION, MISION, GOALS AND OBJECTIVES</option>
                        <option value="2">2-FACULTY</option>
                        <option value="3">3-CURRICULUM AND INSTRUCTIONS</option>
                        <option value="4">4-SUPPORT TO STUDENTS</option>
                        <option value="5">5-RESEARCH</option>
                        <option value="6">6-EXTENSION AND COMMUNITY ENVOLVEMENT</option>
                        <option value="7">7-LIBRARY</option>
                        <option value="8">8-PHYSICAL PLANT AND FACILITIES</option>
                        <option value="9">9-LABORATORIES</option>
                        <option value="10">10-ADMINISTRATION</option>
                    </select>
                    <!-- Merged Parameter + Character -->
                    <select name="parameter_character" id="parameter_character" class="mt-1 p-2 border rounded w-full mb-2">
                        <option value="">Select Parameter A-Z</option>
                        @foreach(range('A','Z') as $char)
                        <option value="{{ $char }}">{{ $char }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Authors Field (hidden by default) -->
                <div class="mb-4" id="authorsField" style="display: none;">
                    <label for="authors" class="block text-lg font-bold text-gray-700">Authors</label>
                    <input type="text" name="authors" id="authors" class="p-2 border rounded w-full" placeholder="Enter authors, separated by comma">
                </div>

                <select name="folder" id="folder" class="mt-1 p-2 border rounded w-full">
                    <option value="">Root (uploads/)</option>
                    @foreach($subfolders as $folder)
                    @if($folder->status === 'private' && !$folder->user_has_access)
                    <option value="{{ $folder->name }}" disabled class="text-red-500">
                        {{ $folder->name }} (Private – cannot insert file)
                    </option>
                    @else
                    <option value="{{ $folder->name }}">
                        {{ $folder->name }}{{ $folder->status === 'private' ? ' (Private – access approved)' : '' }}
                    </option>
                    @endif
                    @endforeach
                </select>

                <div class="mb-4" id="publishedByField">
                    <label for="published_by" class="block text-lg font-bold text-gray-700">Published By</label>
                    <input type="text" name="published_by" id="published_by" class="p-2 border rounded w-full" value="{{ auth()->user()->name }}" readonly>
                </div>

                <div class="mb-4">
                    <label for="year_published" class="block text-lg font-bold text-gray-700">Year Published</label>
                    <input type="number" name="year_published" id="year_published" class="p-2 border rounded w-full"
                        required min="1900" max="{{ date('Y') }}" placeholder="YYYY">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-lg font-bold text-gray-700">Description</label>
                    <textarea name="description" id="description" class="p-2 border rounded w-full" rows="3"
                        placeholder="Enter file description..."></textarea>
                </div>

                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full md:w-auto">
                    Upload File
                </button>
            </form>
        </div>

        <!-- Right Column - Drag & Drop File Upload -->
        <div class="w-full md:w-1/2 flex flex-col items-center">
            <div id="dropArea"
                class="mb-4 flex flex-col items-center justify-center border-2 border-dashed border-gray-400 p-6
             rounded-lg cursor-pointer bg-gray-100 w-full h-64">
                <p class="text-gray-600">Drag & Drop your file here or click to select</p>
                <input type="file" name="file" id="file" class="hidden" required>
            </div>

            <div class="text-gray-600 text-1xl mb-4 text-center">
                <p><strong>Allowed files:</strong> PPT, DOCX, PNG, SVG, PDF</p>
                <p>File Upload limited to <strong>500MB only</strong></p>
            </div>

            <!-- File Details Display (Initially Hidden) -->
            <div id="fileDetails" class="text-gray-600 hidden">
                <p><strong>File Name:</strong> <span id="fileName"></span></p>
                <p><strong>File Type:</strong> <span id="fileType"></span></p>
                <p><strong>File Size:</strong> <span id="fileSize"></span></p>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("file");
        const fileDetails = document.getElementById("fileDetails");
        const categorySelect = document.getElementById("category");
        const accreditationFields = document.getElementById("accreditationFields");
        const publishedByInput = document.getElementById("published_by");
        const authorsField = document.getElementById("authorsField");

        categorySelect.addEventListener("change", function() {
            if (this.value === "accreditation") {
                accreditationFields.style.display = "block";
                authorsField.style.display = "none";
                publishedByInput.readOnly = true;
                publishedByInput.value = "{{ auth()->user()->name }}";
            } else if (this.value === "capstone" || this.value === "thesis") {
                accreditationFields.style.display = "none";
                authorsField.style.display = "block";
                document.getElementById("level").selectedIndex = 0;
                publishedByInput.readOnly = false;
                publishedByInput.value = "";
            } else {
                accreditationFields.style.display = "none";
                authorsField.style.display = "none";
                document.getElementById("level").selectedIndex = 0;
                publishedByInput.readOnly = true;
                publishedByInput.value = "{{ auth()->user()->name }}";
            }
        });

        dropArea.addEventListener("click", () => fileInput.click());

        fileInput.addEventListener("change", handleFileSelect);

        dropArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropArea.classList.add("border-blue-500");
        });

        dropArea.addEventListener("dragleave", () => dropArea.classList.remove("border-blue-500"));

        dropArea.addEventListener("drop", (e) => {
            e.preventDefault();
            dropArea.classList.remove("border-blue-500");

            let files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect();
            }
        });

        function handleFileSelect() {
            let file = fileInput.files[0];
            if (file) {
                let fileSizeInMB = (file.size / (1024 * 1024)).toFixed(2);

                document.getElementById("fileName").textContent = file.name;
                document.getElementById("fileType").textContent = file.type || "Unknown";
                document.getElementById("fileSize").textContent = fileSizeInMB + " MB";

                fileDetails.classList.remove("hidden");
            }
        }

        $('#uploadForm').submit(function(e) {
            e.preventDefault();

            let selectedFolder = $('#folder').val();
            if (selectedFolder === "") {
                Swal.fire({
                    title: "Folder Required",
                    text: "Please select a specific folder (not Root).",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            let formData = new FormData(this);
            let fileInput = document.getElementById("file");

            if (fileInput.files.length === 0) {
                Swal.fire({
                    title: "No file selected",
                    text: "Please choose a file to upload.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            formData.append("file", fileInput.files[0]);

            if (categorySelect.value === "accreditation") {
                formData.append("level", document.getElementById("level").value);
                formData.append("area", document.getElementById("area").value);
                formData.append("parameter_character", document.getElementById("parameter_character").value);
            }

            // Append authors if visible
            if (categorySelect.value === "capstone" || categorySelect.value === "thesis") {
                formData.append("authors", document.getElementById("authors").value);
            }

            let uploadUrl = "{{ route('staff.uploadFile') }}";

            $.ajax({
                url: uploadUrl,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: "Uploading...",
                        text: "Please wait while your file is being uploaded.",
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || "File upload failed.";
                    Swal.fire({
                        title: "Error!",
                        text: errorMessage,
                        icon: "error",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "Try Again"
                    });
                }
            });
        });
    });
</script>

@endsection