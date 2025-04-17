@extends('admin.dashboard.adminDashboard')

@section('content')





<div class="container mx-auto p-6 bg-white shadow-md">
    <h1 class="text-3xl font-bold mb-4 border-b border-gray-300 pb-2">User's File Requests</h1>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-2 mb-4 space-y-4 sm:space-y-0">

        <label for="statusFilter" class="text-sm font-medium text-gray-700">Filter by Status</label>
        <select id="statusFilter" class="border px-4 py-2 rounded w-full sm:w-1/4 rounded-lg">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>

        <label for="searchFilter" class="text-sm font-medium text-gray-700">Search</label>
        <input type="text" id="searchFilter" class="border px-4 py-2 rounded w-full sm:w-1/4 rounded-lg" placeholder="Search by ID or Name">
        
    </div>


    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <!-- <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Request ID</th> -->
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">File Name</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">User</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Note</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Requested At</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $request)
                    <tr>
                        <!-- <td class="px-4 py-2">{{ $request->request_id }}</td> -->
                        <td class="px-4 py-2">{{ $request->file->filename ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $request->user->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <button onclick="showNoteModal(`{{ $request->note ?? 'No note provided.' }}`)" class="text-white text-sm rounded-lg bg-green-500 p-2 font-bold ">
                                View Note
                            </button>
                        </td>
                        <td class="px-4 py-2">
                            <span class="
                                px-2 py-1 rounded-full font-bold p-2 text-white text-sm
                                @if($request->request_status === 'pending') bg-yellow-500
                                @elseif($request->request_status === 'approved') bg-green-500
                                @elseif($request->request_status === 'rejected') bg-red-500
                                @else bg-gray-400
                                @endif
                            ">
                                {{ $request->request_status }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $request->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-2">
                            <a href="javascript:void(0);" onclick="openEditStatusModal({{ $request->request_id }})" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">No file access requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <!-- Paginate links -->
        {{ $requests->links() }}
    </div>
</div>

<!-- Edit Status Modal -->
<div id="editStatusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Change Request Status</h3>
        
        <!-- Form to update the status -->
        <form id="editStatusForm" action="{{ route('file-request.update-status') }}" method="POST" onsubmit="return confirmAction()">
            @csrf
            <input type="hidden" id="request_id" name="request_id">
            
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            
            <div class="text-right mt-4">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save Changes</button>
                <button type="button" onclick="closeEditStatusModal()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Note Modal -->
<div id="noteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Note</h3>
        <p id="noteContent" class="text-gray-700"></p>
        <div class="text-right mt-4">
            <button onclick="closeNoteModal()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('statusFilter');
        const searchFilter = document.getElementById('searchFilter');
        const tableRows = document.querySelectorAll('tbody tr');

        function applyFilters() {
            const selectedStatus = statusFilter.value.toLowerCase();
            const searchValue = searchFilter.value.toLowerCase();

            tableRows.forEach(row => {
                const requestId = row.querySelector('td:nth-child(1)')?.textContent.toLowerCase() || '';
                const requestedBy = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const statusSpan = row.querySelector('td:nth-child(5) span');
                const statusText = statusSpan?.textContent.trim().toLowerCase() || '';

                const matchesStatus = !selectedStatus || statusText === selectedStatus;
                const matchesSearch = !searchValue || requestId.includes(searchValue) || requestedBy.includes(searchValue);

                if (matchesStatus && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        statusFilter.addEventListener('change', applyFilters);
        searchFilter.addEventListener('input', applyFilters);
    });
</script>



<script>
    // Show the modal and set the request_id
    function openEditStatusModal(request_id) {
        document.getElementById('request_id').value = request_id;
        document.getElementById('editStatusModal').classList.remove('hidden');
        document.getElementById('editStatusModal').classList.add('flex');
    }

    // Close the modal
    function closeEditStatusModal() {
        document.getElementById('editStatusModal').classList.add('hidden');
    }

    // Confirm the action with a browser prompt
    function confirmAction() {
        return confirm('Are you sure you want to change the status of this request?');
    }
</script>


<script>
    function showNoteModal(note) {
        document.getElementById('noteContent').textContent = note;
        document.getElementById('noteModal').classList.remove('hidden');
        document.getElementById('noteModal').classList.add('flex');
    }

    function closeNoteModal() {
        document.getElementById('noteModal').classList.add('hidden');
    }
</script>





@endsection
