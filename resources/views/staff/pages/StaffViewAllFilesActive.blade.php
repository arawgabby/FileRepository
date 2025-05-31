@extends('staff.dashboard.staffDashboard')
@section('title', 'View All Files')
@section('content')
<style>
    th {
        text-align: center;
        vertical-align: middle;
    }

    .file-row td {
        text-align: center;
        vertical-align: middle;
    }

    .file-row td.filename {
        text-align: left !important;
    }

    .file-row td.actions-cell {
        text-align: center !important;
    }

    .card-view {
        display: none;
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

<div class="container mx-auto p-6 bg-white admin." style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">
    <div class="flex justify-between items-center p-2">
        <h1 class="-m-6 mb-6 pb-2 text-4xl font-bold border-b border-gray-300 p-6">Files</h1>
        <span class="text-white text-2xl font-semibold bg-gray-800 p-4 rounded-lg" id="activeFileCount">0</span>
    </div>
    <br>
    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4 border-b border-gray pb-4">
        <label for="fileTypeFilter" class="block text-sm font-medium text-gray-700 mt-3">Type</label>
        <select id="fileTypeFilter" class="border rounded p-1 mt-2 text-sm">
            <option value="">All Types</option>
            @php
            $fileTypes = [];
            foreach ($files as $file) {
            $ext = strtolower($file->file_type);
            if ($ext && !in_array($ext, $fileTypes)) {
            $fileTypes[] = $ext;
            }
            }
            sort($fileTypes);
            @endphp
            @foreach ($fileTypes as $type)
            <option value="{{ $type }}">{{ strtoupper($type) }}</option>
            @endforeach
        </select>
        <label for="subfolderFilter" class="block text-sm font-medium text-gray-700 mt-3">Subfolder</label>
        <form method="GET" action="{{ route('staff.active.files') }}" id="subfolderForm">
            <select id="subfolderFilter" name="subfolder" class="border rounded p-1 text-sm mt-2"
                onchange="document.getElementById('subfolderForm').submit()">
                <option value="">Public files</option>
                @foreach ($subfolders as $folder)
                <option value="{{ $folder }}" {{ request('subfolder') === $folder ? 'selected' : '' }}>
                    {{ ucfirst($folder) }}
                </option>
                @endforeach
            </select>
        </form>
        <label for="yearFilter" class="font-medium text-gray-700 mt-3 text-sm">Filter by Year:</label>
        <select id="yearFilter" class="border rounded text-sm ">
            <option value="all">All Years</option>
            @foreach ($files->unique('year_published') as $file)
            <option value="{{ $file->year_published }}">{{ $file->year_published }}</option>
            @endforeach
        </select>
        <label for="dateFilter" class="font-medium text-gray-700 text-sm mt-2">Filter by Date Created:</label>
        <input type="date" id="dateFilter" class="border rounded p-1 text-sm ">
        <input type="text" id="searchInput" placeholder="Search files..." class="border rounded p-1 w-1/3 text-sm ">
    </div>

    <!-- Table View -->
    <div class="overflow-x-aut mb-12">
        <table class="min-w-full table-auto bg-white rounded-lg shadow-lg border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Filename</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">File Category Type</th>
                    @if (request('subfolder') === 'accreditation')
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Level</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Area</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Parameter</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Category</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Sub-Parameter</th>
                    @endif
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Year Published</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Size</th>
                    @if (request('subfolder') !== 'accreditation')
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Authors</th>
                    @endif
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Publisher</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">File Type</th>
                    <th class="px-4 py-2 text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                @if ($file->status == 'active')
                @php
                $folderName = explode('/', $file->file_path)[1] ?? 'unknown';
                $fileType = strtolower($file->file_type);
                @endphp
                <tr class="border-t file-row">
                    <td class="px-4 py-3 text-sm text-gray-800 filename">{{ $file->filename }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ ucfirst($file->category) }}</td>
                    @if (request('subfolder') === 'accreditation')
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->level }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->area }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->character }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->parameter }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->subparam }}</td>
                    @endif
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->year_published }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">
                        @if ($file->file_size >= 1024 * 1024 * 1024)
                        {{ number_format($file->file_size / (1024 * 1024 * 1024), 2) }} GB
                        @elseif($file->file_size >= 1024 * 1024)
                        {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
                        @else
                        {{ number_format($file->file_size / 1024, 2) }} KB
                        @endif
                    </td>
                    @if (request('subfolder') !== 'accreditation')
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $file->authors }}</td>
                    @endif
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $file->published_by }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 file-type">
                        @if ($fileType == 'pdf')
                        <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                        @elseif($fileType == 'docx' || $fileType == 'doc')
                        <i class="fa-solid fa-file-word text-blue-500 text-xl"></i>
                        @elseif($fileType == 'pptx' || $fileType == 'ppt')
                        <i class="fa-solid fa-file-powerpoint text-orange-500 text-xl"></i>
                        @else
                        <i class="fa-solid fa-file text-gray-500 text-xl"></i>
                        @endif
                        {{ strtoupper($fileType) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 actions-cell">
                        <div class="flex space-x-4 justify-center">
                            <a href="{{ asset('storage/' . $file->file_path) }}" class="text-blue-500"
                                title="Download" target="_blank" rel="noopener">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                            <!-- <a href="{{ route('staff.files.editPrimary', ['file_id' => $file->file_id, 'subfolder' => explode('/', $file->file_path)[1] ?? '']) }}"
                                            class="text-blue-500" title="Edit Primary File">
                                            <i class="fas fa-edit"></i>
                                        </a> -->
                        </div>
                    </td>
                    <td class="hidden created-date">
                        {{ \Carbon\Carbon::parse($file->created_at)->format('Y-m-d') }}
                    </td>
                </tr>
                @endif
                @endforeach

                {{-- Show granted files (approved FileRequests) --}}
                @if (isset($grantedFiles) && $grantedFiles->count())
                @foreach ($grantedFiles as $file)
                @php
                $fileType = strtolower($file->file_type);
                @endphp
                <tr class="border-t file-row bg-green-50">
                    <td class="px-4 py-3 text-sm text-gray-800 filename">{{ $file->filename }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->year_published }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        @if ($file->file_size >= 1024 * 1024)
                        {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
                        @else
                        {{ number_format($file->file_size / 1024, 2) }} KB
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $file->published_by }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 file-type">
                        @if ($fileType == 'pdf')
                        <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                        @elseif($fileType == 'docx' || $fileType == 'doc')
                        <i class="fa-solid fa-file-word text-blue-500 text-xl"></i>
                        @elseif($fileType == 'pptx' || $fileType == 'ppt')
                        <i class="fa-solid fa-file-powerpoint text-orange-500 text-xl"></i>
                        @else
                        <i class="fa-solid fa-file text-gray-500 text-xl"></i>
                        @endif
                        {{ strtoupper($fileType) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 actions-cell">
                        <div class="flex space-x-4 justify-center">
                            <a href="{{ route('staff.files.download', basename($file->file_path)) }}"
                                class="text-blue-500" title="Download">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                        </div>
                    </td>
                    <td class="hidden created-date">
                        {{ \Carbon\Carbon::parse($file->created_at)->format('Y-m-d') }}
                    </td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $files->links() }}
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Table filter logic
        const searchInput = document.getElementById("searchInput");
        const fileTypeFilter = document.getElementById("fileTypeFilter");
        const subfolderFilter = document.getElementById("subfolderFilter");
        const yearFilter = document.getElementById("yearFilter");
        const dateFilter = document.getElementById("dateFilter");
        const rows = document.querySelectorAll(".file-row");

        function filterTable() {
            const searchText = searchInput.value.toLowerCase();
            const selectedFileType = fileTypeFilter.value.toLowerCase();
            const selectedSubfolder = subfolderFilter.value.toLowerCase();
            const selectedYear = yearFilter.value;
            const selectedDate = dateFilter.value;

            rows.forEach(row => {
                const filename = row.querySelector(".filename").textContent.toLowerCase();
                const fileType = row.querySelector(".file-type").textContent.toLowerCase();
                const year = row.querySelector(".year") ? row.querySelector(".year").textContent : "";
                const createdDate = row.querySelector(".created-date") ? row.querySelector(
                    ".created-date").textContent : "";

                const matchesSearch = filename.includes(searchText);
                const matchesFileType = selectedFileType === "" || fileType.includes(selectedFileType);
                // const matchesSubfolder = selectedSubfolder === "" || subfolder === selectedSubfolder;
                const matchesYear = selectedYear === "all" || year === selectedYear;
                const matchesDate = selectedDate === "" || createdDate === selectedDate;

                if (matchesSearch && matchesFileType /* && matchesSubfolder */ && matchesYear &&
                    matchesDate) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });

            updateFileCount();
        }

        function updateFileCount() {
            const visibleRows = Array.from(rows).filter(row => row.style.display !== "none");
            document.getElementById("activeFileCount").textContent = visibleRows.length;
        }

        // Event listeners
        if (searchInput) searchInput.addEventListener("input", filterTable);
        if (fileTypeFilter) fileTypeFilter.addEventListener("change", filterTable);
        if (subfolderFilter) subfolderFilter.addEventListener("change", filterTable);
        if (yearFilter) yearFilter.addEventListener("change", filterTable);
        if (dateFilter) dateFilter.addEventListener("change", filterTable);

        // Initial count
        updateFileCount();
    });
</script>
@endsection