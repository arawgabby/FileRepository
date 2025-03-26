@extends('staff.dashboard.staffDashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
</style>

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-[30px] font-bold mb-3 flex items-center border-b border-gray pb-2 -mx-4 px-4">
        <i class="fas fa-archive w-[30px] h-[30px] mr-2"></i>
        Archived File Versions
    </h1>

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4">
        <input type="text" id="searchInput" placeholder="Search files..." class="border rounded p-4 w-1/3">
        
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
            <tr class="bg-gray-200">
                <!-- <th class=" p-4">File ID</th> -->
                <!-- <th class=" p-4">Version #</th> -->
                <th class=" p-5">File Type</th>
                <th class=" p-5">Filename</th>
                <th class=" p-5">File Size</th>
                <th class=" p-5">User</th>
                <th class=" p-5">Created</th>
                <th class=" p-5">Actions</th>
            </tr>
        </thead>
        <tbody id="fileTableBody">
            @foreach($fileVersions as $file)
                <tr class="file-row border-b border-gray-300 {{ $loop->odd ? 'bg-gray-100' : '' }}">
                    <!-- <td class="p-4">{{ $file->file_id ?? 'N/A' }}</td> -->
                    <!-- <td class="p-4">
                        {{ $file->version_id ? 'Version ' . $file->version_id : 'Original' }}
                    </td> -->
                    <td class="p-4 file-type">
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
                    <td class="p-4 filename">{{ $file->filename }}</td>
                    <td class="p-4 filename">
                        @php
                            $sizeInKB = $file->file_size / 1024; // Convert bytes to KB
                            $sizeFormatted = $sizeInKB >= 1024 
                                ? number_format($sizeInKB / 1024, 2) . ' MB'  // Convert KB to MB if >= 1024 KB
                                : number_format($sizeInKB, 2) . ' KB';  // Keep in KB if less than 1024 KB
                        @endphp
                        {{ $sizeFormatted }}
                    </td>
                    <td class="p-4">{{ optional($file->user)->name ?? 'Unknown' }}</td>
                    <td class="p-4 filename">{{ $file->created_at->format('F j, Y g:i A') }}</td>
                    <td class="p-4 text-center">
                        <div class="flex justify-center space-x-4">
                        <a href="{{ route('staff.unarchiveFile', $file->version_id ?: $file->file_id) }}" 
                        class="bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center justify-center hover:bg-blue-800 transition"
                        title="Unarchive"
                        onclick="confirmArchive(event, '{{ $file->version_id ?: $file->file_id }}')">
                        <i class="fas fa-box-open text-white text-lg"></i>
                        </a>

                            <form id="archive-form-{{ $file->version_id ?: $file->file_id }}" 
                                action="{{ route('staff.unarchiveFile', $file->version_id ?: $file->file_id) }}" 
                                method="POST" 
                                style="display: none;">
                                @csrf
                                @method('PUT')
                            </form>
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
    function confirmArchive(event, versionId) {
        event.preventDefault();
        if (confirm("Are you sure you want to undo archive this file version?")) {
            document.getElementById('archive-form-' + versionId).submit();
        }
    }

    function confirmTrash(event, versionId) {
        event.preventDefault();
        if (confirm("Are you sure you want to put this file into trash?")) {
            document.getElementById('trash-form-' + versionId).submit();
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
