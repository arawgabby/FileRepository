@extends('admin.dashboard.adminDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px">Upload New File</h1>

    <div class="mt-2 flex items-center text-blue-600 text-sm mb-6">
        <i class="fas fa-info-circle mr-2"></i>
        <span>Only PDF, DOCX, PPT, and ZIP files are allowed.</span>
    </div>

    <form action="{{ route('admin.uploadFile') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4 flex flex-col">
            <label for="file" class="block text-2xl font-medium text-gray-700 mb-1">Select File</label>
            <input type="file" name="file" id="file" class="p-2 border rounded w-full" required 
                onchange="displayFileInfo(event)">
            <p id="fileInfo" class="mt-2 text-sm text-gray-600"></p>
        </div>

        <script>
            function displayFileInfo(event) {
                const file = event.target.files[0];
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert size to MB
                    const fileType = file.type || 'Unknown';
                    document.getElementById('fileInfo').textContent = 
                        `📁 File Name: ${file.name}  
                        📏 Size: ${fileSize} MB  
                        🏷️ Type: ${fileType}`;
                } else {
                    document.getElementById('fileInfo').textContent = "";
                }
            }
        </script>


        <div class="mb-4">
            <label for="category" class="block  text-2xl  font-medium text-gray-700">File Category</label>
            <select name="category" id="category" class="mt-1 p-2 border rounded w-full" required>
                <option value="capstone">Capstone</option>
                <option value="thesis">Thesis</option>
                <option value="faculty_request">Faculty Request</option>
                <option value="accreditation">Accreditation</option>
                <option value="admin_docs">Admin Documents</option>
            </select>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload File</button>
    </form>

</div>
@endsection
