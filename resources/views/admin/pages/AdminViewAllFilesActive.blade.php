@extends('admin.dashboard.admindashboard')
@section('title', 'View All Files')
@section('content')

<style>
    td {
        text-align: center;
    }

    .card-view {
        display: none;
    }

    /* Hide card view by default */
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

<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">



    <div class="flex justify-between items-center p-2">
        <h1 class="-m-6 mb-6 pb-2 text-4xl font-bold border-b border-gray-300 p-6">Files</h1>
        <span class="text-white text-2xl font-semibold bg-blue-400 p-4 rounded-lg" id="activeFileCount">0</span>
    </div>

    <br>

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4 border-b border-gray pb-4">

        <label for="subfolderFilter" class="block text-sm font-medium text-gray-700 mt-2"> Type</label>
        <select id="fileTypeFilter" class="border rounded p-1\ text-sm">
            <option value="">All Types</option>
            <option value="pdf">Pdf</option>
            <option value="docx">Docx</option>
            <option value="pptx">Pptx</option>
            <option value="xlsx">Xlsx</option>
        </select>


        <label for="subfolderFilter" class="block text-sm font-medium text-gray-700 mt-2">Subfolder</label>
        <form method="GET" action="{{ route('admin.active.files') }}">
            <select id="subfolderFilter" name="subfolder" class="border rounded p-1 text-sm mt-2" onchange="this.form.submit()">
                <option value="">All files</option>
                @foreach ($subfolders as $folder)
                <option value="{{ $folder }}" {{ request('subfolder') === $folder ? 'selected' : '' }}>
                    {{ ucfirst($folder) }}
                </option>
                @endforeach
            </select>
        </form>



        <label for="yearFilter" class="font-medium text-gray-700 mt-2 text-sm">Filter by Year:</label>
        <select id="yearFilter" class="border rounded p-1 text-sm">
            <option value="all">All Years</option>
            @foreach($files->unique('year_published') as $file)
            <option value="{{ $file->year_published }}">{{ $file->year_published }}</option>
            @endforeach
        </select>

        <label for="dateFilter" class="font-medium mt-2 text-gray-700 text-sm">Filter by Date Created:</label>
        <input type="date" id="dateFilter" class="border rounded p-1 text-sm">

        <input type="text" id="searchInput" placeholder="Search files..." class="border rounded p-1 w-1/4 text-sm">
    </div>

    <!-- Table View -->
    <div class="overflow-x-aut mb-12">
        <table class="min-w-full table-auto bg-white rounded-lg shadow-lg border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Filename</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">File Category Type</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Level</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Area</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Paramameter</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Category</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Sub-Parameter</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Year Published</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Size</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Authors</th> <!-- Add this line -->
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Publisher</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">File Type</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                @if($file->status == 'active')
                @php
                $folderName = explode('/', $file->file_path)[1] ?? 'unknown';
                $fileType = strtolower($file->file_type);
                @endphp

                <tr class="border-t file-row">
                    <td class="px-4 py-3 text-sm text-gray-800 filename">{{ $file->filename }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->category }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->level }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->area }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->character }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->parameter }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->subparam }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->year_published }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">@if($file->file_size >= 1024 * 1024 * 1024)
        {{ number_format($file->file_size / (1024 * 1024 * 1024), 2) }} GB
    @elseif($file->file_size >= 1024 * 1024)
        {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
    @else
        {{ number_format($file->file_size / 1024, 2) }} KB
    @endif</td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $file->authors }}</td> <!-- Display authors here -->
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $file->published_by }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 file-type">
                        @if($fileType == 'pdf')
                        <i class="fa-solid fa-file-pdf text-red-500 text-xl"></i>
                        @elseif($fileType == 'docx' || $fileType == 'doc')
                        <i class="fa-solid fa-file-word text-blue-500 text-xl"></i>
                        @elseif($fileType == 'pptx' || $fileType == 'ppt')
                        <i class="fa-solid fa-file-powerpoint text-orange-500 text-xl"></i>
                        @elseif($fileType == 'xlsx' || $fileType == 'xls')
                        <i class="fa-solid fa-file-excel text-green-500 text-xl"></i>
                        @else
                        <i class="fa-solid fa-file text-gray-500 text-xl"></i>
                        @endif
                        {{ strtoupper($fileType) }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <div class="flex space-x-4">
                            <a href="{{ route('staff.files.download', basename($file->file_path)) }}" class="text-blue-500" title="Download">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                            <a href="{{ route('staff.files.editPrimary', ['file_id' => $file->file_id]) }}" class="text-blue-500" title="Edit Primary File">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('files.archive.active', ['file_id' => $file->file_id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this file?')">
                                @csrf
                                <button type="submit" class="text-red-500" title="Archive this file">
                                    <i class="fas fa-archive"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    <td class="hidden created-date">{{ \Carbon\Carbon::parse($file->created_at)->format('Y-m-d') }}</td>
                </tr>
                @endif
                @endforeach

                {{-- Show granted files (approved FileRequests) --}}
                @if(isset($grantedFiles) && $grantedFiles->count())
                @foreach($grantedFiles as $file)
                @php
                $fileType = strtolower($file->file_type);
                @endphp
                <tr class="border-t file-row bg-green-50">
                    <td class="px-4 py-3 text-sm text-gray-800 filename">{{ $file->filename }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 year">{{ $file->year_published }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        @if($file->file_size >= 1024 * 1024)
                        {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
                        @else
                        {{ number_format($file->file_size / 1024, 2) }} KB
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">{{ $file->published_by }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 file-type">
                        @if($fileType == 'pdf')
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
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <div class="flex space-x-4">
                            <a href="{{ route('staff.files.download', basename($file->file_path)) }}" class="text-blue-500" title="Download">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                        </div>
                    </td>
                    <td class="hidden created-date">{{ \Carbon\Carbon::parse($file->created_at)->format('Y-m-d') }}</td>
                </tr>
                @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{-- $files->links() --}}
    </div>

</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("searchInput");
        const fileTypeFilter = document.getElementById("fileTypeFilter");
        const subfolderFilter = document.getElementById("subfolderFilter");
        const yearFilter = document.getElementById("yearFilter");
        const dateFilter = document.getElementById("dateFilter");
        const tableRows = document.querySelectorAll(".file-row");
        const cards = document.querySelectorAll('#cardView > div[data-created]');

        function formatToMMDDYYYY(dateStr) {
            if (!dateStr) return "";
            const [year, month, day] = dateStr.split("-");
            return `${month}/${day}/${year}`;
        }

        function filterFiles() {
            const searchText = searchInput ? searchInput.value.toLowerCase() : "";
            const selectedFileType = fileTypeFilter ? fileTypeFilter.value.toLowerCase() : "";
            const selectedSubfolder = subfolderFilter ? subfolderFilter.value.toLowerCase() : "";
            const selectedYear = yearFilter ? yearFilter.value : "all";
            const selectedDate = dateFilter ? dateFilter.value : "";

            // Table rows filter
            tableRows.forEach(row => {
                const filename = row.querySelector(".filename") ? row.querySelector(".filename").textContent.toLowerCase() : "";
                const fileType = row.querySelector(".file-type") ? row.querySelector(".file-type").textContent.toLowerCase() : "";
                const year = row.querySelector(".year") ? row.querySelector(".year").textContent.trim() : "";
                const createdDateCell = row.querySelector(".created-date");
                let rowDate = createdDateCell ? createdDateCell.textContent.trim() : "";
                if (/^\d{4}-\d{2}-\d{2}$/.test(rowDate)) {
                    rowDate = formatToMMDDYYYY(rowDate);
                }

                let matches = true;
                if (searchText && !filename.includes(searchText)) matches = false;
                if (selectedFileType && !fileType.includes(selectedFileType)) matches = false;
                if (selectedYear !== "all" && year !== selectedYear) matches = false;
                if (selectedDate) {
                    const selectedDateMMDDYYYY = formatToMMDDYYYY(selectedDate);
                    if (rowDate !== selectedDateMMDDYYYY) matches = false;
                }
                // Subfolder filter is not applied to table rows

                row.style.display = matches ? "" : "none";
            });

            // Card view filter
            cards.forEach(card => {
                const fileName = card.querySelector("span.font-semibold") ? card.querySelector("span.font-semibold").textContent.toLowerCase() : "";
                const fileType = card.querySelector("span.ml-2") ? card.querySelector("span.ml-2").textContent.toLowerCase() : "";
                const folder = card.dataset.subfolder ? card.dataset.subfolder.toLowerCase() : "";
                const createdDate = card.dataset.created;
                const year = card.querySelector("span.font-semibold:last-child") ? card.querySelector("span.font-semibold:last-child").textContent : "";

                let matches = true;
                if (searchText && !fileName.includes(searchText)) matches = false;
                if (selectedFileType && !fileType.includes(selectedFileType)) matches = false;
                if (selectedSubfolder && folder !== selectedSubfolder) matches = false;
                if (selectedYear !== "all" && year !== selectedYear) matches = false;
                if (selectedDate && createdDate !== selectedDate) matches = false;

                card.style.display = matches ? "block" : "none";
            });

            // Update file count
            const visibleRows = Array.from(tableRows).filter(row => row.style.display !== "none").length;
            const visibleCards = Array.from(cards).filter(card => card.style.display !== "none").length;
            document.getElementById("activeFileCount").textContent = visibleRows + visibleCards;
        }

        // Add event listeners with null checks
        if (searchInput) searchInput.addEventListener("input", filterFiles);
        if (fileTypeFilter) fileTypeFilter.addEventListener("change", filterFiles);
        if (subfolderFilter) subfolderFilter.addEventListener("change", filterFiles);
        if (yearFilter) yearFilter.addEventListener("change", filterFiles);
        if (dateFilter) dateFilter.addEventListener("change", filterFiles);

        // Initial count/filter
        filterFiles();
    });
</script>
@endsection