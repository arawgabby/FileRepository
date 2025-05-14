@extends('admin.dashboard.adminDashboard')
@section('title', 'Edit File View')
@section('content')

<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <a href="{{ route('admin.files') }}" class="text-gray-600 hover:text-gray-800 flex items-center mb-4">
        <i class="fas fa-arrow-left mr-2" style="font-size: 34px; font-weight: bold"> </i>
    </a>

    <h1 class="text-3xl font-semibold mb-4">Upload New File Based on this Primary File</h1>

    <form action="{{ route('admin.updateFile', $file->file_id) }}" method="POST" enctype="multipart/form-data" onsubmit="return validateFileUpload()">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Filename</label>
            <input type="text" name="filename" value="{{ $file->filename }}" class="border rounded p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">File Type</label>
            <input type="text" name="file_type" value="{{ $file->file_type }}" class="border rounded p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Category</label>
            <input type="text" name="category" value="{{ $file->category }}" class="border rounded p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Upload New Version</label>
            <p class="text-gray-500 italic">
                When you upload a new file, it will serve as a new version of this primary file.
            </p>
            <input type="file" name="file" id="fileUpload" class="border rounded p-2 w-full">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Confirm</button>
    </form>

</div>

<script>
    function validateFileUpload() {
        let fileInput = document.getElementById("fileUpload");
        if (fileInput.files.length === 0) {
            alert("Please upload a new file before proceeding.");
            return false;
        }
        return true;
    }
</script>

@endsection