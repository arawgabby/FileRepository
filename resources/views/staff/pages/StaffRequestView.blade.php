@extends('staff.dashboard.staffDashboard')
@section('title', 'Request View')
@section('content')

@if(session('success'))
<div class="bg-green-500 text-white p-4 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('duplicate'))
<div class="bg-red-500 text-white p-4 rounded-lg mb-4">
    {{ session('duplicate') }}
</div>
@endif

<div class="grid grid-cols-1 grid-cols-1 gap-8 p-6">
    <!-- Left: Display Folder Access Table -->
    <div class="bg-white p-6  shadow-md">
        <h2 class="text-xl font-semibold mb-4 border-b pb-2">My File Upload Requests</h2>

        @if($folderAccesses->isEmpty())
        <p class="text-gray-600">No requests submitted yet.</p>
        @else
        <table class="min-w-full text-sm">
            <thead>
                <tr class="border-b font-medium text-gray-700">
                    <th class="py-2 text-left text-left">Folder Name</th>
                    <th class="py-2 text-left text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($folderAccesses as $access)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 text-left">{{ $access->folder->name ?? 'N/A' }}</td>
                    <td class="py-2 text-center">
                        <span class="px-2 py-1 rounded-full 
                                    {{
                                        ($access->status === 'Approved') ? 'bg-green-200 text-green-800' :
                                        (($access->status === 'Restricted') ? 'bg-violet-700 text-white' :
                                        (($access->status === 'Rejected') ? 'bg-red-200 text-red-800' :
                                        'bg-yellow-200 text-yellow-800'))
                                    }}">
                            {{ $access->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Right: Submit Form -->
</div>

<script>
    document.getElementById('folderAccessForm').addEventListener('submit', function(event) {
        event.preventDefault();

        let folderId = document.getElementById('folder_id').value;
        if (!folderId) {
            alert('Please select a folder.');
            return;
        }

        if (confirm('Are you sure you want to submit the request?')) {
            this.submit();
        } else {
            alert('Request not submitted.');
        }
    });
</script>

@endsection