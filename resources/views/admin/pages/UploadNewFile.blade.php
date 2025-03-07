@extends('admin.dashboard.adminDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 style="font-size: 30px; font-weight: bold; margin-bottom: 12px">Upload New File</h1>

    <form action="{{ route('admin.uploadFile') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label for="file" class="block text-sm font-medium text-gray-700">Select File</label>
            <input type="file" name="file" id="file" class="mt-1 p-2 border rounded w-full" required>
        </div>

        <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700">File Category</label>
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
