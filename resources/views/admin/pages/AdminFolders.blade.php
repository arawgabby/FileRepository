@extends('admin.dashboard.adminDashboard')

@section('content')

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-[30px] font-bold mb-3 flex items-center border-b border-gray pb-2 -mx-4 px-4">
        <i class="fas fa-folder w-[30px] h-[30px] mr-2"></i>
        Root Folder (Admin side)
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Column: Folder Tree -->
        <div class="relative pl-6">

            <!-- Root Label -->
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-4 h-4 bg-blue-700 rounded-full"></div>
                <h2 class="font-semibold text-lg">Root: /{{ $basePath }}</h2>
            </div>

            <!-- Back Button -->
            @if ($parentPath)
                <div class="mb-4">
                    <a href="{{ route('admin.folders', ['path' => $parentPath]) }}" 
                    class="text-blue-700 hover:underline text-sm">
                        ← Back to {{ $parentPath }}
                    </a>
                </div>
            @endif

            <!-- Folder List -->
            <div class="relative border-l-2 border-gray-300 ml-2 pl-6">
                @forelse ($folderNames as $folder)
                    <div class="flex items-center space-x-2 mb-4 relative">
                        <div class="w-6 h-px bg-gray-400 absolute -left-6 top-1/2 transform -translate-y-1/2"></div>
                        <i class="fas fa-folder text-blue-700 text-xl"></i>
                        <a href="{{ route('admin.folders', ['path' => $basePath . '/' . $folder]) }}" class="hover:underline">
                            {{ $folder }}
                        </a>
                    </div>
                @empty
                    <p class="text-gray-700">No folders found in {{ $basePath }}.</p>
                @endforelse
            </div>

        </div>

        <!-- Right Column: Action Button -->
        <div class="flex items-start justify-end gap-2">

        <button
            onclick="createSubfolder()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition duration-200"
        >
            + Add Subfolder
        </button>

        <button
            onclick="deleteSubfolder()"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition duration-200"
        >
            🗑️ Delete Subfolder
        </button>

        </div>

<script>
    function createSubfolder() {
        const folderName = prompt("Enter subfolder name:");
        if (folderName) {
            fetch("{{ route('admin.folders.create') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    folderName: folderName,
                    basePath: "{{ $basePath }}"
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Folder created successfully!");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                alert("An error occurred.");
                console.error(error);
            });
        }
    }

    function deleteSubfolder() {
        const folderName = prompt("Enter the exact name of the subfolder to delete:");
        if (!folderName) return;

        const confirmDelete = confirm(`Are you sure you want to delete the folder "${folderName}"? This cannot be undone.`);
        if (!confirmDelete) return;

        fetch("{{ route('admin.folders.delete') }}", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                folderName: folderName,
                basePath: "{{ $basePath }}"
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Folder deleted successfully.");
                location.reload();
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            alert("An error occurred.");
            console.error(error);
        });
    }
</script>

<script>
    function createSubfolder() {
        const folderName = prompt("Enter subfolder name:");

        if (folderName) {
            fetch("{{ route('admin.folders.create') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    folderName: folderName,
                    basePath: "{{ $basePath }}"
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Folder created successfully!");
                    location.reload();
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => {
                alert("An error occurred.");
                console.error(error);
            });
        }
    }
</script>

        
    </div>

</div>

@endsection
