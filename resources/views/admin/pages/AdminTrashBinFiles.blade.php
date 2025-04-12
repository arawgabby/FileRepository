@extends('admin.dashboard.adminDashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
</style>

<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-[30px] font-bold mb-3 flex items-center border-b border-gray pb-2">
        <i class="fas fa-trash w-[30px] h-[30px] mr-2"></i>
        Trash Bin
    </h1>

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4 mt-2">
        <input type="text" id="searchInput" placeholder="Search files..." class="border rounded p-2 w-1/3">
        
        <select id="fileTypeFilter" class="border rounded p-4">
            <option value="">All Types</option>
            <option value="pdf">PDF</option>
            <option value="docx">DOCX</option>
            <option value="pptx">PPTX</option>
        </select>
    </div>

    <!-- Files Table -->
    <table class="w-full -collapse  -gray-300">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-4">File ID</th>
                <th class="p-4">Filename</th>
                <th class="p-4">File Type</th>
                <th class="p-4">Uploaded By</th>
                <th class="p-4">Updated_at</th>
                <th class="p-4">Actions</th>
            </tr>
        </thead>
        <tbody id="fileTableBody">
            @foreach($fileVersions as $fileVersion)
                <tr class="file-row">
                    <td class="p-4 border-b border-gray">{{ $fileVersion->file_id }}</td>
                    <td class="p-4 border-b border-gray filename">{{ $fileVersion->filename }}</td>
                    <td class="p-4 border-b border-gray file-type">
                        @php
                            $fileType = strtolower($fileVersion->file_type);
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
                    <td class="p-4 border-b border-gray">{{ optional($fileVersion->user)->name ?? 'Unknown' }}</td>
                    <td class="p-4 border-b border-gray">{{ $fileVersion->updated_at }}</td>
                    <td class="p-4 border-b border-gray text-center">
                        <div class="flex justify-center space-x-4">
                            <a href="{{ route('admin.restore', $fileVersion->file_id) }}" 
                                class="bg-blue-500 hover:bg-blue-700 text-white rounded-lg p-2 transition duration-300 " 
                                title="Restore File"
                                onclick="confirmRestore(event, {{ $fileVersion->file_id }})">
                                <i class="fas fa-arrow-up"></i>
                            </a>

                            <form id="archive-form-{{ $fileVersion->file_id }}" 
                                action="{{ route('admin.restore', $fileVersion->file_id) }}" 
                                method="POST" 
                                style="display: none;">
                                @csrf
                                @method('PUT')
                            </form>

                            <!-- Delete Button (No route yet) -->
                            <a href="#" 
                                class="bg-red-500 hover:bg-red-700 text-white rounded-lg p-2 transition duration-300" 
                                title="Delete this file permanently">
                                <i class="fas fa-trash"></i>
                            </a>

                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $fileVersions->links() }}
    </div>

</div>

<script>
    function confirmRestore(event, fileId) {
        event.preventDefault();
        if (confirm("Are you sure you want to restore this file?")) {
            document.getElementById('archive-form-' + fileId).submit();
        }
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const fileTypeFilter = document.getElementById("fileTypeFilter");
        const rows = document.querySelectorAll(".file-row");

        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const selectedFileType = fileTypeFilter.value.toLowerCase();

            rows.forEach(row => {
                const filename = row.querySelector(".filename").textContent.toLowerCase();
                const fileType = row.querySelector(".file-type").textContent.toLowerCase();

                const matchesSearch = filename.includes(searchText);
                const matchesFileType = selectedFileType === "" || fileType.includes(selectedFileType);

                row.style.display = matchesSearch && matchesFileType ? "" : "none";
            });
        }

        searchInput.addEventListener("input", filterTable);
        fileTypeFilter.addEventListener("change", filterTable);
    });
</script>

@endsection
