@extends('admin.dashboard.adminDashboard')

@section('content')

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">
    
    <!-- Back Button -->
    <a href="{{ route('admin.update') }}" class="text-gray-600 hover:text-gray-800 flex items-center mb-4">
        <i class="fas fa-arrow-left mr-2" style="font-size: 44px; font-weight: bold"> ←</i>
    </a>

    <h1 class="text-3xl font-semibold mb-4">Edit File Version</h1>

    <form action="{{ route('admin.updateFileVersion', $fileVersion->version_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">Filename</label>
            <input type="text" name="filename" value="{{ $fileVersion->filename }}" class="border rounded p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold">File Type</label>
            <input type="text" name="file_type" value="{{ $fileVersion->file_type }}" class="border rounded p-2 w-full">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Upload New File (Optional)</label>
            <p class="italic text-gray-500">If you want to upload a modified version of the same file, please select a new file.</p>
            <input type="file" name="file" class="border rounded p-2 w-full">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update Version</button>
    </form>
</div>

@endsection
