@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px">Upload New File</h1>

    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-4 flex flex-col">
            <label for="file" class="block text-1xl font-medium text-gray-700 mb-1">Select File</label>
            <input type="file" name="file" id="file" class="p-2 border rounded w-full" required>

            <!-- File Details Display (Initially Hidden) -->
            <div id="fileDetails" class="mt-2 text-gray-600 hidden">
                <p><strong>File Name:</strong> <span id="fileName"></span></p>
                <p><strong>File Type:</strong> <span id="fileType"></span></p>
                <p><strong>File Size:</strong> <span id="fileSize"></span></p>
            </div>
        </div>

        <script>
            document.getElementById("file").addEventListener("change", function(event) {
                let file = event.target.files[0]; // Get the selected file

                if (file) {
                    let fileSizeInMB = (file.size / (1024 * 1024)).toFixed(2); // Convert size to MB

                    // Update file details
                    document.getElementById("fileName").textContent = file.name;
                    document.getElementById("fileType").textContent = file.type || "Unknown";
                    document.getElementById("fileSize").textContent = fileSizeInMB + " MB";

                    // Show the file details section
                    document.getElementById("fileDetails").classList.remove("hidden");
                } else {
                    document.getElementById("fileDetails").classList.add("hidden");
                }
            });
        </script>



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
    $(document).ready(function() {
        $('#uploadForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            let formData = new FormData(this); // Create FormData object
            let uploadUrl = "{{ route('staff.uploadFile') }}"; // Laravel route

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
                        location.reload(); // Reload page after success
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
