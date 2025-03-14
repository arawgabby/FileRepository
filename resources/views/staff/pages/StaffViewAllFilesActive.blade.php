@extends('staff.dashboard.staffDashboard')

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

    <div class="flex justify-between items-center mt-4 p-3 rounded-lg shadow">
        <h1 class="text-4xl font-bold">Active Files</h1>
        <span class="text-blue-500 text-4xl font-semibold" id="activeFileCount">0</span>
    </div>

    <br>

    <!-- Toggle Button -->
    <!-- <div class="mb-4 flex gap-4">
        <button onclick="toggleView()" class="bg-blue-500 text-white px-4 py-2 rounded">
            Toggle View
        </button>
    </div> -->

    <!-- Search & Filters -->
    <div class="mb-4 flex gap-4">
        <input type="text" id="searchInput" placeholder="Search files..." class="border rounded p-2 w-1/3">
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
        <span>File versions on this section cannot be downloaded. Go to file versions section to download your selected file version. Thank you.</span>
    </div>
    <div class="mt-2 flex items-center text-red-600 text-sm mb-2">
        <i class="fas fa-info-circle mr-2"></i>
        <span>This section is read-only.</span>
    </div>

    <!-- Table View
    <table id="tableView" class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">File ID</th>
                <th class="p-2">Filename</th>
                <th class="p-2">File Type</th>
                <th class="p-2">Category</th>
                <th class="p-2">Uploaded By</th>
                <th class="p-2">Created At</th>
                <th class="p-2">Request Status</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody id="fileTableBody">
        @foreach($files as $file)
            @if($file->status == 'active') 
            <tr class="file-row">
                <td class="p-2 filename">00{{ $file->file_id }}</td>
                <td class="p-2 filename">{{ $file->filename }}</td>
                <td class="p-2 file-type">
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
                <td class="p-2 category">{{ $file->category ?? 'No Category' }}</td>
                <td class="p-2">{{ $file->user ? $file->user->name : 'Unknown' }}</td>
                <td class="p-2 filename">{{ $file->created_at->diffForHumans() }}</td>
                <td class="p-2 filename">{{ $file->status }}</td>
                <td class="p-2">
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('staff.files.download', basename($file->file_path)) }}" class="text-blue-500" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('staff.files.editPrimary', ['file_id' => $file->file_id]) }}" class="text-blue-500" title="Edit Primary File">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('staff.editFile', $file->file_id) }}" class="text-red-500" title="Upload New File Based on this version">
                            <i class="fas fa-upload"></i>
                        </a>
                    </div>
                </td>   
            </tr>
            @endif
        @endforeach
        </tbody>
    </table> -->

    <!-- Card View -->
    <div id="cardView" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6 mb-12">
        @foreach($files as $file)
            @if($file->status == 'active') 
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex justify-between">
                    <span class="text-lg font-semibold">{{ $file->filename }}</span>
                </div>
                <div class="flex items-center mt-2">
                    @php
                        $fileType = strtolower($file->file_type);
                    @endphp

                    @if($fileType == 'pdf')
                        <i class="fa-solid fa-file-pdf text-red-500 text-2xl"></i>
                    @elseif($fileType == 'docx' || $fileType == 'doc')
                        <i class="fa-solid fa-file-word text-blue-500 text-2xl"></i>
                    @elseif($fileType == 'pptx' || $fileType == 'ppt')
                        <i class="fa-solid fa-file-powerpoint text-orange-500 text-2xl"></i>
                    @else
                        <i class="fa-solid fa-file text-gray-500 text-2xl"></i>
                    @endif
                    <span class="ml-2">{{ strtoupper($fileType) }}</span>
                </div>
                <div class="flex justify-between items-center mt-4">
                    <span class="text-sm text-gray-500">{{ $file->created_at->diffForHumans() }}</span>
                    
                    <div class="flex space-x-4">
                        <a href="{{ route('staff.files.download', basename($file->file_path)) }}" class="text-blue-500" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="{{ route('staff.files.editPrimary', ['file_id' => $file->file_id]) }}" class="text-blue-500" title="Edit Primary File">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="{{ route('staff.editFile', $file->file_id) }}" class="text-red-500" title="Upload New File Based on this version">
                            <i class="fas fa-upload"></i>
                        </a>
                    </div>
                </div>

            </div>
            @endif
        @endforeach
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const fileTypeFilter = document.getElementById("fileTypeFilter");
    const categoryFilter = document.getElementById("categoryFilter");
    const cards = document.querySelectorAll("#cardView > div");

    function filterCards() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedType = fileTypeFilter.value.toLowerCase();
        const selectedCategory = categoryFilter.value.toLowerCase();

        cards.forEach(card => {
            const fileName = card.querySelector("span.font-semibold").textContent.toLowerCase();
            const fileType = card.querySelector("span.ml-2").textContent.toLowerCase();
            const category = card.dataset.category ? card.dataset.category.toLowerCase() : "";

            // Check conditions for visibility
            const matchesSearch = fileName.includes(searchText);
            const matchesType = selectedType === "" || fileType.includes(selectedType);
            const matchesCategory = selectedCategory === "" || category.includes(selectedCategory);

            if (matchesSearch && matchesType && matchesCategory) {
                card.style.display = "block";
            } else {
                card.style.display = "none";
            }
        });
    }

    searchInput.addEventListener("input", filterCards);
    fileTypeFilter.addEventListener("change", filterCards);
    categoryFilter.addEventListener("change", filterCards);
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

@endsection
