@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto p-6 bg-white rounded-xl shadow-lg">
    <h1 class="text-3xl font-semibold mb-4">Activity Log</h1>

    <!-- Search Bar -->
    <div class="mb-4">
        <input type="text" id="search" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search logs...">
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
            <thead>
                <tr class="bg-gray-200 text-gray">
                    <th class="py-2 px-4">File ID</th>
                    <th class="py-2 px-4">Accessed By</th>
                    <th class="py-2 px-4">Action</th>
                    <th class="py-2 px-4">Access Time</th>
                </tr>
            </thead>
            <tbody id="logsTable">
                @foreach($accessLogs as $log)
                    <tr class="border-b border-gray-300 text-center">
                        <td class="py-2 px-4">{{ ucfirst($log->file_id) }}</td>
                        <td class="py-2 px-4">{{ ucfirst($log->user->role) }}</td> <!-- Display role based on accessed_by -->
                        <td class="py-2 px-4">{{ ucfirst($log->action) }}</td>
                        <td class="py-2 px-4 text-gray-500">
                            {{ \Carbon\Carbon::parse($log->access_time)->format('F d, Y h:i A') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">
    {{ $accessLogs->links() }}
</div>
</div>

<!-- JavaScript for Search Filtering -->
<script>
    document.getElementById("search").addEventListener("keyup", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#logsTable tr");

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
</script>

@endsection
