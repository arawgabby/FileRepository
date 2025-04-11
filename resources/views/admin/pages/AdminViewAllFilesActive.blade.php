@extends('admin.dashboard.admindashboard')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td { text-align: center; }
    .card-view { display: none; } /* Hide card view by default */
</style>

@if (session('success'))
    <script>alert("{{ session('success') }}");</script>
@endif

@if (session('error'))
    <script>alert("{{ session('error') }}");</script>
@endif

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <div class="flex justify-between items-center p-2">
        <h1 class="-m-6 mb-6 pb-2 text-4xl font-bold border-b border-gray-300 p-6">Active Files</h1>
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
        </select>


        <label for="subfolderFilter" class="block text-sm font-medium text-gray-700 mt-2">Subfolder</label>
        <form method="GET" action="{{ route('admin.active.files') }}">
            <select id="subfolderFilter" name="subfolder" class="border rounded p-1 text-sm mt-2" onchange="this.form.submit()">
                <option value="">All files outside root folder</option>
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

    <!-- <div class="mt-2 flex items-center text-red-600 text-sm mb-2">
        <i class="fas fa-info-circle mr-2"></i>
        <span>File versions on this section cannot be downloaded. Go to file versions section to download your selected file version. Thank you.</span>
        </div>
        <div class="mt-2 flex items-center text-red-600 text-sm mb-2">
            <i class="fas fa-info-circle mr-2"></i>
            <span>This section is read-only.</span>
        </div> -->

        <!-- Table View -->
        <!-- <div class="overflow-x-aut mb-12">
        <table class="min-w-full table-auto bg-white rounded-lg shadow-lg border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Filename</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Year Published</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Size</th>
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
                        <tr class="border-t">
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $file->filename }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $file->year_published }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                @if($file->file_size >= 1024 * 1024)
                                    {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
                                @else
                                    {{ number_format($file->file_size / 1024, 2) }} KB
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $file->published_by }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
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
                                        <i class="fas fa-download"></i>
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
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div> -->


  <!-- Card View -->
    <div id="cardView" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6 mb-12">
        @foreach($files as $file)
            @if($file->status == 'active')
                @php
                    $folderName = explode('/', $file->file_path)[1] ?? 'unknown';
                @endphp

                <div class="bg-white rounded-lg shadow-lg p-6" data-subfolder="{{ $folderName }}">
                    <div class="flex justify-between">
                        <span class="text-sm font-semibold break-words w-full block">{{ $file->filename }}</span>
                        <span class="font-semibold text-sm">{{ $file->year_published }}</span>
                    </div>

                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold">
                            Size: 
                            @if($file->file_size >= 1024 * 1024)
                                {{ number_format($file->file_size / (1024 * 1024), 2) }} MB
                            @else
                                {{ number_format($file->file_size / 1024, 2) }} KB
                            @endif
                        </span>
                    </div>

                    <div class="mt-2 text-sm text-gray-600">
                        <span class="font-semibold">Publisher: {{ $file->published_by }}</span>
                    </div>  

                    <div class="flex items-center mt-2">
                        @php
                            $fileType = strtolower($file->file_type);
                        @endphp

                        @if($fileType == 'pdf')
                            <i class="fa-solid fa-file-pdf text-red-500 text-1xl"></i>
                        @elseif($fileType == 'docx' || $fileType == 'doc')
                            <i class="fa-solid fa-file-word text-blue-500 text-1xl"></i>
                        @elseif($fileType == 'pptx' || $fileType == 'ppt')
                            <i class="fa-solid fa-file-powerpoint text-orange-500 text-1xl"></i>
                        @else
                            <i class="fa-solid fa-file text-gray-500 text-1xl"></i>
                        @endif
                        <span class="ml-2">{{ strtoupper($fileType) }}</span>
                    </div>

                    <div class="flex justify-between items-center mt-4">
                        <span class="text-sm text-gray-500">{{ $file->created_at->diffForHumans() }}</span>
                        
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.files.download', basename($file->file_path)) }}" class="text-blue-500" title="Download">
                                <i class="fas fa-download text-sm"></i>
                            </a>
                            <a href="{{ route('admin.files.editPrimary', ['file_id' => $file->file_id]) }}" class="text-blue-500" title="Edit Primary File">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.files.archive.active', ['file_id' => $file->file_id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to archive this file?')">
                                @csrf
                                <button type="submit" class="text-red-500" title="Archive this file">
                                    <i class="fas fa-archive text-sm"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.trash.files', ['file_id' => $file->file_id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to move this file to trash?');">
                                @csrf
                                <button type="submit" class="text-black" title="Move File to Trash">
                                    <i class="fas fa-trash-alt text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $files->links() }}
    </div>

</div>

<!--For search filter ni siya-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const searchInput = document.getElementById("searchInput");
        const fileTypeFilter = document.getElementById("fileTypeFilter");
        const subfolderFilter = document.getElementById("subfolderFilter");
        const yearFilter = document.getElementById("yearFilter");
        const dateFilter = document.getElementById("dateFilter");
        const cards = document.querySelectorAll("#cardView > div");

        function filterCards() {
            const searchText = searchInput.value.toLowerCase();
            const selectedFileType = fileTypeFilter.value.toLowerCase();
            const selectedSubfolder = subfolderFilter.value.toLowerCase();
            const selectedYear = yearFilter.value;
            const selectedDate = dateFilter.value;

            cards.forEach(card => {
                const fileName = card.querySelector("span.font-semibold").textContent.toLowerCase();
                const fileType = card.querySelector("span.ml-2").textContent.toLowerCase();
                const folder = card.dataset.subfolder ? card.dataset.subfolder.toLowerCase() : "";
                const year = card.querySelector("span.font-semibold:last-child").textContent;
                const createdDate = card.querySelector(".text-sm.text-gray-500").textContent.trim();

                const matchesSearch = fileName.includes(searchText);
                const matchesFileType = selectedFileType === "" || fileType.includes(selectedFileType);
                const matchesSubfolder = selectedSubfolder === "" || folder.includes(selectedSubfolder);
                const matchesYear = selectedYear === "all" || year === selectedYear;
                const matchesDate = selectedDate === "" || createdDate.includes(selectedDate);

                if (matchesSearch && matchesFileType && matchesSubfolder && matchesYear && matchesDate) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }

        searchInput.addEventListener("input", filterCards);
        fileTypeFilter.addEventListener("change", filterCards);
        subfolderFilter.addEventListener("change", filterCards);
        yearFilter.addEventListener("change", filterCards);
        dateFilter.addEventListener("change", filterCards);
    });
</script>

<!--For year filter-->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const yearFilter = document.getElementById("yearFilter");
        const cards = document.querySelectorAll("#cardView > div");

        yearFilter.addEventListener("change", function () {
            const selectedYear = this.value;

            cards.forEach(card => {
                const yearElement = card.querySelector("div.flex.justify-between span:nth-child(2)");
                const fileYear = yearElement ? yearElement.textContent.trim() : "";

                if (selectedYear === "all" || fileYear === selectedYear) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    });
</script>

<script>
    const subfolderFilter = document.getElementById("subfolderFilter");

    subfolderFilter.addEventListener("change", function () {
        const selectedSubfolder = this.value.toLowerCase();

        document.querySelectorAll("#cardView > div").forEach(card => {
            const subfolder = card.dataset.subfolder ? card.dataset.subfolder.toLowerCase() : "";

            if (selectedSubfolder === "" || subfolder === selectedSubfolder) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });

        updateFileCount && updateFileCount();
    });
</script>

<script>
    function toggleView() {
        document.getElementById("tableView").classList.toggle("hidden");
        document.getElementById("cardView").classList.toggle("hidden");
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Count the visible file rows
        function updateFileCount() {
            const visibleRows = document.querySelectorAll(".file-row");
            const count = visibleRows.length;
            document.getElementById("activeFileCount").textContent = count;
        }

        // Run count function on load
        updateFileCount();
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    function updateFileCount() {
        const tableRows = document.querySelectorAll(".file-row:not([style*='display: none'])").length;
        const cardItems = document.querySelectorAll("#cardView > div:not([style*='display: none'])").length;
        const totalCount = tableRows + cardItems; 
        document.getElementById("activeFileCount").textContent = totalCount;
    }

    // Update file count after filtering
    function setupFilters() {
        const searchInput = document.getElementById("searchInput");
        const fileTypeFilter = document.getElementById("fileTypeFilter");
        const categoryFilter = document.getElementById("categoryFilter");

        function filterFiles() {
            const searchText = searchInput.value.toLowerCase();
            const selectedFileType = fileTypeFilter.value.toLowerCase();
            const selectedCategory = categoryFilter.value.toLowerCase();

            // Filter Table Rows
            document.querySelectorAll(".file-row").forEach(row => {
                const filename = row.querySelector(".filename").textContent.toLowerCase();
                const fileType = row.querySelector(".file-type").textContent.toLowerCase();
                const category = row.querySelector(".category").textContent.toLowerCase();

                const matchesSearch = filename.includes(searchText);
                const matchesFileType = selectedFileType === "" || fileType.includes(selectedFileType);
                const matchesCategory = selectedCategory === "" || category.includes(selectedCategory);

                row.style.display = matchesSearch && matchesFileType && matchesCategory ? "" : "none";
            });

            // Filter Cards
            document.querySelectorAll("#cardView > div").forEach(card => {
                const fileName = card.querySelector("span.font-semibold").textContent.toLowerCase();
                const fileType = card.querySelector("span.ml-2").textContent.toLowerCase();
                const category = card.dataset.category ? card.dataset.category.toLowerCase() : "";

                const matchesSearch = fileName.includes(searchText);
                const matchesFileType = selectedFileType === "" || fileType.includes(selectedFileType);
                const matchesCategory = selectedCategory === "" || category.includes(selectedCategory);

                card.style.display = matchesSearch && matchesFileType && matchesCategory ? "block" : "none";
            });

            updateFileCount();
        }

        searchInput.addEventListener("input", filterFiles);
        fileTypeFilter.addEventListener("change", filterFiles);
        categoryFilter.addEventListener("change", filterFiles);
    }

    setupFilters();
    updateFileCount(); // Run count on load
    });

</script>
@endsection
