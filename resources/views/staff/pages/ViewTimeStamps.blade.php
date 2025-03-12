@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
    input, select {
        padding: 8px;
        margin: 10px 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
</style>

<div class="container mx-auto p-4 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">
    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px">
        <i class="fas fa-file text-gray-400 mr-4"></i>File Time Stamps
    </h1>

    <div class="max-w-full mx-auto rounded-lg p-6">
        
        <input type="text" id="searchInput" placeholder="Search..." class="w-1/2 border border-gray-300">
        <input type="date" id="dateFilter" class="border border-gray-300">


        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3">Timestamp ID</th>
                        <th class="p-3">File ID</th>
                        <th class="p-3">Version</th>
                        <th class="p-3">Event Type</th>
                        <th class="p-3">Created At</th>
                        <th class="p-3">Action</th>
                    </tr>
                </thead>
                <tbody id="fileTableBody">
                    @php
                        $filteredFiles = [];
                    @endphp

                    @foreach ($timestamps as $timestamp)
                        @if (!in_array($timestamp->file_id, $filteredFiles))
                            @php
                                $filteredFiles[] = $timestamp->file_id;
                            @endphp
                            <tr>
                                <td class="p-3">00{{ $timestamp->timestamp_id }}</td>
                                <td class="p-3">00{{ $timestamp->file_id }}</td>
                                <td class="p-3">00{{ $timestamp->fileVersion->version_number ?? 'N/A' }}</td>
                                <td class="p-3">{{ $timestamp->event_type }}</td>
                                <td class="p-3 created-at" data-date="{{ \Carbon\Carbon::parse($timestamp->timestamp)->format('Y-m-d') }}">
                                    {{ \Carbon\Carbon::parse($timestamp->timestamp)->diffForHumans() }}
                                </td>
                                <td class="p-3">
                                    <a href="{{ route('file.timestamps.details', ['file_id' => $timestamp->file_id]) }}" 
                                    class="bg-blue-500 text-white px-4 py-2 rounded-lg">
                                    View
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
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
