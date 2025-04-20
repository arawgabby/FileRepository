@extends('admin.dashboard.adminDashboard')

@section('content')

<!-- @if(session('success'))
    <script>alert("{{ session('success') }}");</script>
@endif
@if(session('error'))
    <script>alert("{{ session('error') }}");</script>
@endif -->

<style>
    th, td {
        text-align: center;
    }
    td.no-center {
        text-align: left;
    }
</style>


<div class="container mx-auto p-6 bg-white shadow-md">
    <h1 class="text-3xl font-bold mb-4 border-b border-gray-300 pb-2">User's Folder Requests</h1>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 mb-4 space-y-4 sm:space-y-0">

        <select id="filterStatus" class="border px-4 py-2 rounded w-full sm:w-1/4 rounded-lg" onchange="filterTable()">
            <option value="">All Statuses</option>
            <option value="Approved">Approved</option>
            <option value="Waiting Approval">Waiting Approval</option>
            <option value="Rejected">Rejected</option>
        </select>

        <input type="text" id="searchUser" placeholder="Search..." class="border px-4 py-2 rounded w-full sm:w-1/4 rounded-lg" onkeyup="filterTable()">

    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray ">
                <tr>
                    <th class="px-4 py-2 text-left border-b">Folder Wishes to Access</th>
                    <th class="px-4 py-2 text-left border-b  text-center">User</th>
                    <th class="px-4 py-2 text-left border-b  text-center">Note</th>
                    <th class="px-4 py-2 text-left border-b  text-center">Status</th>
                    <th class="px-4 py-2 text-left border-b  text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                @forelse($requests as $index => $request)
                    <tr class="hover:bg-gray-100 table-row">
                        <td class="px-4 py-2 border-b no-center">
                            <i class="fas fa-folder text-gray-800 mr-2"></i>{{ $request->folder->name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-2 border-b">{{ $request->user->name ?? 'N/A' }}</td>

                       <td class="px-4 py-2 border-b text-center">
                            <button 
                                class="text-blue-600 hover:text-blue-800" 
                                onclick="showNoteModal('{{ addslashes($request->note ?? 'N/A') }}')"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>


                        <div id="noteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
                            <div class="bg-white w-96 p-6 rounded-lg shadow-lg relative">
                                <h2 class="text-lg font-semibold mb-4">Note</h2>
                                <p id="modalNoteContent" class="text-gray-700"></p>
                                <button onclick="hideNoteModal()" class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Close</button>
                            </div>
                        </div>

                        <script>
                            function showNoteModal(noteContent) {
                                document.getElementById('modalNoteContent').textContent = noteContent;
                                document.getElementById('noteModal').classList.remove('hidden');
                                document.getElementById('noteModal').classList.add('flex');
                            }

                            function hideNoteModal() {
                                document.getElementById('noteModal').classList.remove('flex');
                                document.getElementById('noteModal').classList.add('hidden');
                            }
                        </script>

                        @php
                            $status = ucfirst($request->status);
                            $bgColor = match($status) {
                                'Approved' => 'bg-green-400 text-white',
                                'Restricted' => 'bg-violet-700 text-white',
                                'Waiting Approval' => 'bg-yellow-400 text-black',
                                default => 'bg-gray-200 text-black',
                            };
                        @endphp
                        <td class="px-4 py-2 border-b">
                            <span class="px-3 py-1 {{ $bgColor }} rounded-full inline-block text-sm font-semibold">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <button onclick="openModal({{ $request->id }}, '{{ $request->status }}')" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500 border-b">
                            No requests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $requests->links() }}
    </div>

</div>

<!-- Status Modal -->
<div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 class="text-xl font-semibold mb-4">Update Status</h2>
        <form id="statusForm" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="requestId">
            <select name="status" id="statusSelect" class="w-full border rounded px-3 py-2 mb-4">
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="Restricted">Restricted</option>
            </select>
            <div class="flex justify-end">
                <button type="button" onclick="closeModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<!-- JS Section -->
<script>
    function openModal(id, currentStatus) {
        document.getElementById('requestId').value = id;
        document.getElementById('statusSelect').value = currentStatus;
        document.getElementById('statusForm').action = `/admin/folder-access/${id}/update-status`;
        document.getElementById('statusModal').classList.remove('hidden');
        document.getElementById('statusModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('statusModal').classList.add('hidden');
        document.getElementById('statusModal').classList.remove('flex');
    }

    function filterTable() {
        const searchInput = document.getElementById('searchUser').value.toLowerCase();
        const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
        const rows = document.querySelectorAll('tbody .table-row');

        rows.forEach(row => {
            const userCell = row.children[1].textContent.toLowerCase();
            const statusCell = row.children[2].textContent.toLowerCase();

            const matchesUser = userCell.includes(searchInput);
            const matchesStatus = !statusFilter || statusCell.includes(statusFilter);

            row.style.display = (matchesUser && matchesStatus) ? '' : 'none';
        });
    }
</script>

@endsection
