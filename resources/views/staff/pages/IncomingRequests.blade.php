@extends('staff.dashboard.staffDashboard')
@section('title', 'Incoming File Requests')
@section('content')

<div class="p-6 bg-white shadow-md">
    <h2 class="text-xl font-bold mb-4 border-b pb-2">Requests Sent To Me</h2>

    @if($incomingRequests->isEmpty())
    <p class="text-gray-600">No incoming file access requests.</p>
    @else
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead>
            <tr>
                <th class="px-4 py-2 text-left">Requested By</th>
                <th class="px-4 py-2 text-left">Note</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">Requested At</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($incomingRequests as $request)
            <tr>
                <td class="px-4 py-2">
                    {{ $users[$request->requested_by]->name ?? 'Unknown User' }}
                </td>
                <td class="px-4 py-2">
                    <button onclick="showNoteModal(`{{ $request->note ?? 'No note provided.' }}`)"
                        class="text-white text-sm font-bold bg-green-500 rounded-lg p-2">
                        View Note
                    </button>
                </td>
                <td class="px-4 py-2 capitalize">
                    {{ $request->request_status }}
                </td>
                <td class="px-4 py-2">
                    {{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y H:i') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    <!-- Note Modal -->
    <div id="noteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">Note</h3>
            <p id="noteContent" class="text-gray-700"></p>
            <div class="text-right mt-4">
                <button onclick="closeNoteModal()"
                    class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
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
