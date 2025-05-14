@extends('admin.dashboard.adminDashboard')
@section('title', 'View Timestamps')
@section('content')


<style>
    td {
        text-align: center;
    }

    input,
    select {
        padding: 8px;
        margin: 10px 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
</style>

<div class="container mx-auto p-4 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">
    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px" class="border-b border-gray pb-2 -mx-4 px-4">
        <i class="fas fa-file text-gray-400 mr-4"></i>File Time Stamps
    </h1>

    <div class="max-w-full mx-auto rounded-lg p-2">



        <input type="text" id="searchInput" placeholder="Search..." class="w-1/2 border border-gray-300">
        <input type="date" id="dateFilter" class="border border-gray-300">

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <!-- <th class="p-3">Timestamp ID</th> -->
                        <!-- <th class="p-3">#</th> -->
                        <th class="p-3">File</th>
                        <!-- <th class="p-3">Version</th> -->
                        <th class="p-3">User</th>
                        <th class="p-3">Event Type</th>
                        <th class="p-3">Created At</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody id="fileTableBody">
                    @foreach ($timestamps as $timestamp)
                    <tr class="file-row border-b border-gray-300 {{ $loop->odd ? 'bg-gray-100' : '' }}">
                        <!-- <td class="p-3">00{{ $timestamp->timestamp_id }}</td> -->
                        <!-- <td class="p-3">00{{ $timestamp->fileVersion->file_id ?? 'N/A' }}</td> -->
                        <td class="p-3 text-center">
                            @php
                            $fileType = strtolower($timestamp->file->file_type ?? 'unknown');
                            $icons = [
                            'doc' => ['icon' => 'fa-file-word', 'color' => 'text-blue-500'], // Word Document
                            'docx' => ['icon' => 'fa-file-word', 'color' => 'text-blue-500'],
                            'pdf' => ['icon' => 'fa-file-pdf', 'color' => 'text-red-500'], // PDF File
                            'jpg' => ['icon' => 'fa-file-image', 'color' => 'text-yellow-500'], // Image Files
                            'png' => ['icon' => 'fa-file-image', 'color' => 'text-yellow-500'],
                            'svg' => ['icon' => 'fa-file-image', 'color' => 'text-yellow-500'],
                            'ppt' => ['icon' => 'fa-file-powerpoint', 'color' => 'text-orange-500'], // PowerPoint Files
                            'pptx' => ['icon' => 'fa-file-powerpoint', 'color' => 'text-orange-500'],
                            'xls' => ['icon' => 'fa-file-excel', 'color' => 'text-green-500'], // Excel Files
                            'xlsx' => ['icon' => 'fa-file-excel', 'color' => 'text-green-500'],
                            'txt' => ['icon' => 'fa-file-alt', 'color' => 'text-gray-500'], // Text Files
                            'zip' => ['icon' => 'fa-file-archive', 'color' => 'text-purple-500'], // Zip/Compressed Files
                            'rar' => ['icon' => 'fa-file-archive', 'color' => 'text-purple-500'],
                            'mp4' => ['icon' => 'fa-file-video', 'color' => 'text-indigo-500'], // Video Files
                            'avi' => ['icon' => 'fa-file-video', 'color' => 'text-indigo-500'],
                            ];
                            $iconData = $icons[$fileType] ?? ['icon' => 'fa-file', 'color' => 'text-gray-500']; // Default icon
                            @endphp

                            <div class="flex items-center justify-center gap-2">
                                <i class="fas {{ $iconData['icon'] }} {{ $iconData['color'] }} text-lg"></i>
                                {{ ucfirst($timestamp->file->file_type ?? 'N/A') }}
                            </div>
                        </td>
                        <!-- <td class="p-3">00{{ $timestamp->fileVersion->version_number ?? 'N/A' }}</td> -->
                        <td class="p-3 text-center">
                            @php
                            // Fetch the uploaded_by user ID from the files table
                            $uploadedById = $timestamp->file->uploaded_by ?? null;

                            // Fetch the user's name from the users table
                            $uploadedByName = $uploadedById ? \App\Models\User::find($uploadedById)?->name : 'N/A';
                            @endphp

                            {{ $uploadedByName }}
                        </td>
                        <td class="p-3">{{ $timestamp->event_type }}</td>
                        <td class="p-3 created-at" data-date="{{ \Carbon\Carbon::parse($timestamp->timestamp)->format('Y-m-d') }}">
                            {{ \Carbon\Carbon::parse($timestamp->timestamp)->diffForHumans() }}
                        </td>
                        <td class="p-3">
                            <a href="{{ route('admin.file.timestamps.details', ['file_id' => $timestamp->file_id]) }}"
                                class="bg-gray-800 text-white px-4 py-2 rounded-lg gap-2">
                                <i class="fas fa-eye text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">
        {{ $timestamps->links() }}
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const dateFilter = document.getElementById("dateFilter");
        const tableRows = document.querySelectorAll("#fileTableBody tr");

        function filterTable() {
            const searchValue = searchInput.value.toLowerCase();
            const selectedDate = dateFilter.value; // YYYY-MM-DD

            tableRows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                const rowDate = row.querySelector(".created-at")?.getAttribute("data-date") || "";

                const matchesSearch = textContent.includes(searchValue);
                const matchesDate = !selectedDate || rowDate >= selectedDate;

                row.style.display = matchesSearch && matchesDate ? "" : "none";
            });
        }

        searchInput.addEventListener("input", filterTable);
        dateFilter.addEventListener("change", filterTable);
    });
</script>

@endsection