@extends('staff.dashboard.staffDashboard')
@section('title', 'Edit Files Overview')
@section('content')


<style>
    td {
        text-align: center;
    }
</style>

<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-[30px] font-bold mb-3 flex items-center">
        <i class="fas fa-file w-[30px] h-[30px] mr-2"></i>
        View File Versions
    </h1>

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4">
        <input type="text" id="searchInput" placeholder="Search files..." class="border rounded p-2 w-1/3">

        <select id="fileTypeFilter" class="border rounded p-2">
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
                <th class=" p-2">File ID</th>
                <th class=" p-2">Version #</th>
                <th class=" p-2">Filename</th>
                <th class=" p-2">File Type</th>
                <th class=" p-2">Uploaded By</th>
                <th class=" p-2">Updated_at</th>
                <th class=" p-2">Actions</th>
            </tr>
        </thead>
        <tbody id="fileTableBody">
            @foreach($fileVersions as $fileVersion)
            @if($fileVersion->status === 'active') {{-- Show only active files --}}
            <tr class="file-row">
                <td class=" p-2">{{ $fileVersion->file_id }}</td>
                <td class=" p-2 filename">00{{ $fileVersion->version_id }}</td>
                <td class=" p-2 filename">{{ $fileVersion->filename }}</td>
                <td class=" p-2 file-type">
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
                <td class=" p-2">
                    {{ optional($fileVersion->user)->name ?? 'Unknown' }}
                </td>
                <td class=" p-2 filename">{{ $fileVersion->updated_at }}</td>
                <td class=" p-2 text-center">
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('admin.downloadFile', basename($fileVersion->file_path)) }}" class="text-blue-500 hover:text-blue-700" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('staff.archiveFile', $fileVersion->version_id) }}"
                            class="text-blue-500 hover:text-blue-700"
                            title="Archive"
                            onclick="confirmArchive(event, {{ $fileVersion->version_id }})">
                            <i class="fas fa-archive"></i>
                        </a>

                        <form id="archive-form-{{ $fileVersion->version_id }}"
                            action="{{ route('staff.archiveFile', $fileVersion->version_id) }}"
                            method="POST"
                            style="display: none;">
                            @csrf
                            @method('PUT')
                        </form>

                        <a href="{{ route('staff.editFileVersion', $fileVersion->version_id) }}" class="text-red-500 hover:text-red-700" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- <a href="{{
                                 route('staff.trash', $fileVersion->version_id) }}" 
                                    class="text-blue-500 hover:text-blue-700" 
                                    title="Add to Trash"
                                    onclick="confirmTrash(event, {{ $fileVersion->version_id }})">
                                    <i class="fas fa-trash"></i>
                                </a>

                                <form id="trash-form-{{ $fileVersion->version_id }}" 
                                    action="{{ route('staff.trash', $fileVersion->version_id) }}" 
                                    method="POST" 
                                    style="display: none;">
                                    @csrf
                                    @method('PUT')
                                </form> -->
                    </div>
                </td>
            </tr>
            @endif
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
        if (confirm("Are you sure you want to archive this file version?")) {
            document.getElementById('archive-form-' + versionId).submit();
        }
    }

    function confirmTrash(event, versionId) {
        event.preventDefault();
        if (confirm("Are you sure you want to put this on trash of this file version?")) {
            document.getElementById('trash-form-' + versionId).submit();
        }
    }
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
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