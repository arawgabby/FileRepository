@extends('admin.dashboard.adminDashboard')
@section('title', 'Folders')
@section('content')


<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <!-- <h1 class="text-[30px] font-bold mb-3 flex items-center border-b border-gray pb-2 -mx-4 px-4">
        <i class="fas fa-folder w-[30px] h-[30px] mr-2"></i>
        Root Folder
    </h1> -->

    <div class="container mx-auto bg-white">
        <!-- <h1 class="text-[30px] font-bold mb-3 flex items-center border-b border-gray pb-2 -mx-4 px-4">
        <i class="fas fa-folder w-[30px] h-[30px] mr-2"></i>
        Root Folder (Admin side)
    </h1> -->

        <div class="flex items-start justify-between gap-2 mt-4 mb-4">

            <h1 class="text-lg font-bold text-gray-800 mt-3 border-b border-gray pb-2">
                Root Folder <span class="text-blue-500">/uploads</span>
            </h1>

            <div class="flex items-start justify-end gap-2">

                <button
                    onclick="createSubfolder()"
                    title="Add Subfolder"
                    class="bg-blue-800 hover:bg-blue-700 text-white font-semibold px-6 py-3  shadow-md transition duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-plus"></i>
                </button>


                <button
                    onclick="deleteSubfolder()"
                    title="Delete Subfolder"
                    class="bg-red-800 hover:bg-red-700 text-white font-semibold px-6 py-3  shadow-md transition duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-trash-alt"></i>
                </button>

            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full table-auto border-separate border-spacing-0">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-100">
                        <th class="text-left px-4 py-2 font-semibold text-lg">Folder Name</th>
                        <th class="text-left px-4 py-2 font-semibold text-lg">Actions</th>
                        <th class="text-left px-4 py-2 font-semibold text-lg">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($folders as $folder)
                    <tr class="border-b border-gray-100">
                        <td class="px-4 py-2 border-b border-gray">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-folder text-gray-800 text-xl"></i>
                                <a href="{{ route('admin.folders', ['path' => $basePath . '/' . $folder->name]) }}" class="hover:underline">
                                    {{ $folder->name }}
                                </a>
                            </div>
                        </td>

                        <td class="px-4 py-2 border-b border-gray">
                            <select onchange="setFolderStatus('{{ $folder->name }}', this.value)" class="bg-gray-800 text-white font-semibold px-4 py-2  shadow-md transition duration-200">
                                <option value=""></option>
                                <option value="private" {{ $folder->status === 'private' ? 'selected' : '' }}>Set as Private</option>
                                <option value="public" {{ $folder->status === 'public' ? 'selected' : '' }}>Set as Public</option>
                            </select>
                        </td>

                        <td class="px-4 py-2 border-b border-gray">
                            <span class="font-semibold px-3 py-1 rounded-full text-white
                            {{ 
                                $folder->status === 'public' ? 'bg-red-800' : 
                                ($folder->status === 'private' ? 'bg-green-800' : 'bg-gray-400') 
                            }}">
                                {{ ucfirst($folder->status ?? 'unknown') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-gray-700 py-4">No folders found in {{ $basePath }}.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


    </div>



    <script>
        function setFolderStatus(folderName, action) {
            console.log("setFolderStatus triggered for folder:", folderName, "Action:", action);

            if (action === "private" || action === "public") {
                const confirmAction = confirm(`Are you sure you want to set this folder as ${action}?`);
                if (confirmAction) {
                    fetch("{{ route('admin.folders.setStatus') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                folderName: folderName,
                                basePath: "{{ $basePath }}",
                                action: action
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(`Folder set to ${action} successfully!`);
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
            } else {
                console.log("Invalid action:", action);
            }
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