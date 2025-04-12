@extends('staff.dashboard.staffDashboard')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-xl shadow-md">
    
    <a href="{{ route('staff.active.files') }}" class="text-gray-600 hover:text-gray-800 flex items-center mb-4">
        <i class="fas fa-arrow-left mr-2" style="font-size: 34px; font-weight: bold"></i>
    </a>

    <h1 class="text-3xl font-bold mb-4">Edit Primary File</h1>

    <!-- Ensure the form submits to the correct route with file_id -->
    <form action="{{ route('staff.files.updatePrimary', ['file_id' => $file->file_id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('POST')

        <!-- Hidden input for the folder path -->
        <input type="hidden" name="folder_path" value="{{ $folder->path }}">

        <!-- Filename Input -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Filename</label>
            <input type="text" name="filename" value="{{ $file->filename }}" class="w-full p-2 border rounded-lg">
        </div>

        <!-- Category Selection -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Category</label>
            <select name="category" class="w-full p-2 border rounded-lg">
                <option value="capstone" {{ $file->category == 'capstone' ? 'selected' : '' }}>Capstone</option>
                <option value="thesis" {{ $file->category == 'thesis' ? 'selected' : '' }}>Thesis</option>
                <option value="accreditation" {{ $file->category == 'accreditation' ? 'selected' : '' }}>Accreditation</option>
                <option value="admin_documents" {{ $file->category == 'admin_documents' ? 'selected' : '' }}>Admin Documents</option>
            </select>
        </div>

        <!-- Year Published -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Year Published</label>
            <input type="number" name="year_published" value="{{ $file->year_published }}" class="w-full p-2 border rounded-lg">
        </div>

        <!-- Published By -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Published By</label>
            <input type="text" name="published_by" value="{{ $file->published_by }}" class="w-full p-2 border rounded-lg">
        </div>

        <!-- Description -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Description</label>
            <textarea name="description" class="w-full p-2 border rounded-lg">{{ $file->description }}</textarea>
        </div>

        <!-- Status Selection -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Status</label>
            <select name="status" class="w-full p-2 border rounded-lg">
                <option value="active" {{ $file->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $file->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ $file->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="deactivated" {{ $file->status == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
            </select>
        </div>

        <!-- File Upload -->
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Upload New File (Optional)</label>
            <input type="file" name="file" class="w-full p-2 border rounded-lg">
            <p class="text-gray-500 italic mt-1">Uploading a new file will replace the existing one.</p>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
            Save Changes
        </button>
    </form>
</div>
@endsection
