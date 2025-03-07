@extends('admin.dashboard.adminDashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
</style>


<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <h1 style="font-size: 30px; font-weight: 500; margin-bottom: 12px">Files Overview</h1>

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4">
        <input type="text" id="searchInput" placeholder="Search files..."
            class="border rounded p-2 w-1/3">
        
        <select id="fileTypeFilter" class="border rounded p-2">
            <option value="">All Types</option>
            <option value="pdf">PDF</option>
            <option value="docx">DOCX</option>
            <option value="pptx">PPTX</option>
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

    <!-- Files Table -->
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border p-2"></th>
                <th class="border p-2">Filename</th>
                <th class="border p-2">File Type</th>
                <th class="border p-2">Category</th>
                <th class="border p-2">Uploaded By</th>
                <th class="border p-2">Actions</th>
            </tr>
        </thead>
        <tbody id="fileTableBody">
            @foreach($files as $file)
            <tr class="file-row">
                <td class="border p-2 filename">{{ $file->file_id }}</td>
                <td class="border p-2 filename">{{ $file->filename }}</td>
                <td class="border p-2 file-type">
                    @php
                        $fileType = strtolower($file->file_type);
                    @endphp

                    @if($fileType == 'pdf')
                        <i class="fa-solid fa-file-pdf text-red-500"></i>
                    @elseif($fileType == 'docx' || $fileType == 'doc')
                        <i class="fa-solid fa-file-word text-blue-500"></i>
                    @elseif($fileType == 'pptx' || $fileType == 'ppt')
                        <i class="fa-solid fa-file-powerpoint text-orange-500"></i>
                    @else
                        <i class="fa-solid fa-file text-gray-500"></i>
                    @endif
                    {{ strtoupper($fileType) }}
                </td>         
                <td class="border p-2 category">{{ $file->category }}</td>
                <td class="border p-2">
                    {{ $file->user ? $file->user->name : 'Unknown' }}
                </td>
                <td class="border p-2">
                    <a href="{{ route('files.download', $file->filename) }}" class="text-blue-500">Download</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

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
