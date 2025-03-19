@extends('staff.dashboard.staffDashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
</style>

@if (session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

@if (session('error'))
    <script>
        alert("{{ session('error') }}");
    </script>
@endif

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <h1 style="font-size: 30px; font-weight: bold; margin-bottom: 12px" >My Uploads</h1>

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4">
        <input type="text" id="searchInput" placeholder="Search files..."
            class="border rounded p-2 w-1/3">
        
        <select id="fileTypeFilter" class="border rounded p-2">
            <option value="">All Types</option>
            <option value="pdf">Pdf</option>
            <option value="docx">Docx</option>
            <option value="pptx">Pptx</option>
        </select>

        <select id="categoryFilter" class="border rounded p-2">
            <option value="">All Categories</option>
            <option value="capstone">Capstone</option>
            <option value="thesis">Thesis</option>
            <option value="faculty_request">Faculty Request</option>
            <option value="accreditation">Accreditation</option>
            <option value="admin_docs">Admin Docs</option>
        </select>
    </div>

    <div class="mt-2 flex items-center text-red-600 text-sm mb-2">
        <i class="fas fa-info-circle mr-2"></i>
        <span>File versions on this section are cannot be downloaded, please go to file versions section to download your selected file version. Thank you</span>
    </div>
    <!-- Files Table -->
    <table class="w-full -collapse  -gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class=" p-2">File ID</th>
                <th class=" p-2">Filename</th>
                <th class=" p-2">File Type</th>
                <th class=" p-2">Category</th>
                <th class=" p-2">Uploaded By</th>
                <th class=" p-2">Created At</th>
                <th class=" p-2">Request Status</th>
                <th class=" p-2">Actions</th>
            </tr>
        </thead>
        <tbody id="fileTableBody">
            @foreach($files as $file)
                @if($file->status == 'pending') 
                    <tr class="file-row">
                        <td class="p-2 filename">00{{ $file->file_id }}</td>
                        <td class="p-2 filename">{{ $file->filename }}</td>
                        <td class="p-2 file-type">
                            @php
                                $fileType = strtolower($file->file_type);
                            @endphp
                            @if($fileType == 'pdf')
                                <i class="fa-solid fa-file-pdf text-red-500"></i>
                            @elseif(in_array($fileType, ['docx', 'doc']))
                                <i class="fa-solid fa-file-word text-blue-500"></i>
                            @elseif(in_array($fileType, ['pptx', 'ppt']))
                                <i class="fa-solid fa-file-powerpoint text-orange-500"></i>
                            @else
                                <i class="fa-solid fa-file text-gray-500"></i>
                            @endif
                            {{ strtoupper($fileType) }}
                        </td>
                        <td class="p-2 category">{{ $file->category ?? 'No Category' }}</td>
                        <td class="p-2">{{ $file->user ? $file->user->name : 'Unknown' }}</td>
                        <td class="p-2 filename">{{ $file->created_at->diffForHumans() }}</td>
                        <td class="p-2">
                            <span class="px-3 py-1 text-white text-sm font-semibold rounded-[12px] 
                                @if($file->status == 'pending') bg-red-500 
                                @elseif($file->status == 'approved') bg-green-500 
                                @elseif($file->status == 'denied') bg-gray-500 
                                @endif">
                                {{ ucfirst($file->status) }}
                            </span>
                        </td>
                        <td class="p-2">
                            <div class="flex justify-center space-x-4">
                                @if($file->status == 'active')
                                    <a href="{{ route('files.download', basename($file->file_path)) }}" class="text-blue-500" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('admin.files.editPrimary', ['file_id' => $file->file_id]) }}" class="text-blue-500" title="Edit Primary File">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.editFile', $file->file_id) }}" class="text-red-500" title="Upload New File Based on this version">
                                        <i class="fas fa-upload"></i>
                                    </a>
                                @endif
                                <form action="{{ route('staff.requestFile', ['file_id' => $file->file_id]) }}" method="POST" onsubmit="return confirmRequest();">
                                    @csrf
                                    <button type="submit" class="text-red-500" title="Request File To be Saved">
                                        <i class="fas fa-file"></i>
                                    </button>
                                </form>
                                <form action="{{ route('staff.files.trash', $file->file_id) }}" method="POST" onsubmit="return confirmTrash(event);">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-blue-500 hover:text-blue-700" title="Cancel File Storing">
                                        <i class="fas fa-cancel"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        {{ $files->links() }}
    </div>

</div>

<script>
    function confirmRequest() {
        return confirm("Are you sure you want to request this file to be saved?");
    }
</script>
<script>
    function confirmTrash(fileId) {
        if (confirm("Are you sure you want to put this on trash this file?")) {
            document.getElementById('archive-form-' + fileId).submit();
        }
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const fileTypeFilter = document.getElementById("fileTypeFilter");
        const categoryFilter = document.getElementById("categoryFilter");
        const rows = document.querySelectorAll(".file-row");

        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const selectedFileType = fileTypeFilter.value.toLowerCase();
            const selectedCategory = categoryFilter.value.toLowerCase();

            rows.forEach(row => {
                const filename = row.querySelector(".filename").textContent.toLowerCase();
                const fileType = row.querySelector(".file-type").textContent.toLowerCase();
                const category = row.querySelector(".category").textContent.toLowerCase();

                const matchesSearch = filename.includes(searchText);
                const matchesFileType = selectedFileType === "" || fileType === selectedFileType;
                const matchesCategory = selectedCategory === "" || category === selectedCategory;

                if (matchesSearch && matchesFileType && matchesCategory) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }

        searchInput.addEventListener("input", filterTable);
        fileTypeFilter.addEventListener("change", filterTable);
        categoryFilter.addEventListener("change", filterTable);
    });
</script>

@endsection
