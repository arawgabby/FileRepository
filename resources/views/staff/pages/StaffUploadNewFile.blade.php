@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px">Upload New File</h1>

    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        
        <!-- Drag and Drop Area -->
        <div id="dropArea" class="mb-4 flex flex-col items-center justify-center border-2 border-dashed border-gray-400 p-6 rounded-lg cursor-pointer bg-gray-100">
            <p class="text-gray-600">Drag & Drop your file here or click to select</p>
            <input type="file" name="file" id="file" class="hidden" required>
        </div>

        <div class="mb-2 text-gray-600 text-sm mb-4">
            <p><strong>Allowed files:</strong> PPT, DOCX, JPG, PNG, SVG, PDF</p>
        </div>


        <!-- File Details Display (Initially Hidden) -->
        <div id="fileDetails" class="mt-4 text-gray-600 hidden">
            <p><strong>File Name:</strong> <span id="fileName"></span></p>
            <p><strong>File Type:</strong> <span id="fileType"></span></p>
            <p><strong>File Size:</strong> <span id="fileSize"></span></p>
        </div>

        <div class="mb-4">
            <label for="category" class="block text-1xl font-medium text-gray-700">File Category</label>
            <select name="category" id="category" class="mt-1 p-2 border rounded w-full" required>
                <option value="capstone">Capstone</option>
                <option value="thesis">Thesis</option>
                <option value="faculty_request">Faculty Request</option>
                <option value="accreditation">Accreditation</option>
                <option value="admin_docs">Admin Documents</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="published_by" class="block text-1xl font-medium text-gray-700">Published By</label>
            <input type="text" name="published_by" id="published_by" class="p-2 border rounded w-full" required>
        </div>

        <div class="mb-4">
            <label for="year_published" class="block text-1xl font-medium text-gray-700">Year Published</label>
            <input type="number" name="year_published" id="year_published" class="p-2 border rounded w-full" 
            required min="1900" max="{{ date('Y') }}" placeholder="YYYY">
        </div>

        <div class="mb-4">
            <label for="description" class="block text-1xl font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" class="p-2 border rounded w-full" rows="3" placeholder="Enter file description..."></textarea>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload File</button>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropArea = document.getElementById("dropArea");
        const fileInput = document.getElementById("file");
        const fileDetails = document.getElementById("fileDetails");

        // Click on drop area triggers file selection
        dropArea.addEventListener("click", () => fileInput.click());

        // File selection event
        fileInput.addEventListener("change", handleFileSelect);

        // Drag over event
        dropArea.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropArea.classList.add("border-blue-500");
        });

        // Drag leave event
        dropArea.addEventListener("dragleave", () => dropArea.classList.remove("border-blue-500"));

        // Drop event
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
                let fileSizeInMB = (file.size / (1024 * 1024)).toFixed(2); // Convert size to MB

                document.getElementById("fileName").textContent = file.name;
                document.getElementById("fileType").textContent = file.type || "Unknown";
                document.getElementById("fileSize").textContent = fileSizeInMB + " MB";

                fileDetails.classList.remove("hidden");
            }
        }

        $('#uploadForm').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
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
