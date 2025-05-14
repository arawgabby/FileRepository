@extends('admin.dashboard.adminDashboard')
@section('title', 'Log View')
@section('content')

<div class="container mx-auto p-6 bg-white shadow-lg">
    <h1 class="text-5xl font-semibold mb-4 border-b border-gray pb-2 -mx-6 px-6">Activity Log</h1>

    <!-- Search Bar and Date Filter -->
    <div class="mb-4 flex justify-between gap-4">
        <input type="text" id="search" class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search logs...">

        <!-- Date filter -->
        <input type="date" id="dateFilter" class="w-40 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Filter by Date">
    </div>

    <!-- Logs Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
            <thead>
                <tr class="bg-gray-200 text-gray">
                    <th class="py-2 px-4">File</th>
                    <th class="py-2 px-4">Accessed By</th>
                    <th class="py-2 px-4">Activity</th>
                    <th class="py-2 px-4">Access Time</th>
                </tr>
            </thead>
            <tbody id="logsTable">
                @foreach($accessLogs as $log)
                <tr class="border-b border-gray-300 text-center">
                    <td class="py-3 px-4 flex items-center gap-2 text-center">
                        @php
                        $fileType = strtolower($log->file->file_type ?? 'unknown');
                        $icons = [
                        'doc' => 'fa-file-word',
                        'docx' => 'fa-file-word',
                        'pdf' => 'fa-file-pdf',
                        'jpg' => 'fa-file-image',
                        'jpeg' => 'fa-file-image',
                        'png' => 'fa-file-image',
                        'svg' => 'fa-file-image',
                        'ppt' => 'fa-file-powerpoint',
                        'pptx' => 'fa-file-powerpoint',
                        'xls' => 'fa-file-excel',
                        'xlsx' => 'fa-file-excel',
                        'txt' => 'fa-file-lines',
                        'zip' => 'fa-file-zipper',
                        'rar' => 'fa-file-zipper',
                        'mp4' => 'fa-file-video',
                        'avi' => 'fa-file-video',
                        ];
                        $iconClass = $icons[$fileType] ?? 'fa-file';
                        @endphp

                        <i class="fa-solid {{ $iconClass }} text-gray-500 text-lg text-center"></i>
                        {{ strtoupper($log->file->file_type ?? 'N/A') }}
                    </td>
                    <td class="py-2 px-4">{{ ucfirst($log->user->name) }}</td> <!-- Display role based on accessed_by -->
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

<!-- JavaScript for Search and Date Filter -->
<script>
    document.getElementById("search").addEventListener("keyup", filterLogs);
    document.getElementById("dateFilter").addEventListener("change", filterLogs);

    function filterLogs() {
        let searchFilter = document.getElementById("search").value.toLowerCase();
        let dateFilter = document.getElementById("dateFilter").value;
        let rows = document.querySelectorAll("#logsTable tr");

        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            let accessTimeText = row.cells[3].textContent.trim(); // Access Time column text

            // Convert the access time into a comparable format: 'April 13, 2025 02:53 AM' -> '2025-04-13'
            let accessTime = convertToDateString(accessTimeText);

            // Compare the access time and the search filter
            let matchesSearch = text.includes(searchFilter);
            let matchesDate = dateFilter ? accessTime === dateFilter : true;

            row.style.display = (matchesSearch && matchesDate) ? "" : "none";
        });
    }

    // Function to convert "April 13, 2025 02:53 AM" to "2025-04-13"
    function convertToDateString(accessTimeText) {
        const months = {
            'January': '01',
            'February': '02',
            'March': '03',
            'April': '04',
            'May': '05',
            'June': '06',
            'July': '07',
            'August': '08',
            'September': '09',
            'October': '10',
            'November': '11',
            'December': '12'
        };

        // Match "April 13, 2025 02:53 AM" format and convert it to Date object
        const regex = /([A-Za-z]+) (\d{1,2}), (\d{4}) (\d{2}):(\d{2}) (AM|PM)/;
        const match = accessTimeText.match(regex);

        if (match) {
            const month = months[match[1]];
            const day = match[2].padStart(2, '0');
            const year = match[3];
            // Get the date in the format 'YYYY-MM-DD'
            return `${year}-${month}-${day}`;
        }

        return ''; // Return empty string if format doesn't match
    }
</script>

@endsection