@extends('admin.dashboard.adminDashboard')
@section('title', 'All Outgoing Requests')
@section('content')

<div class="p-6 bg-white shadow-md">
    <h2 class="text-xl font-bold mb-4 border-b pb-2">All Outgoing File Requests</h2>

    @if($outgoingRequests->isEmpty())
    <p class="text-gray-600">No outgoing requests found.</p>
    @else
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead>
            <tr>
                <th class="px-4 py-2 text-left">Requested To</th>
                <th class="px-4 py-2 text-left">Note</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">File</th>
                <th class="px-4 py-2 text-left">Requested At</th>
                <th class="px-4 py-2 text-left">Action</th>

            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($outgoingRequests as $request)
            <tr>
                <td class="px-4 py-2"> {{ $users[$request->requested_to]->name ?? 'Unknown User' }} </td>
                <td class="px-4 py-2">
                    <button onclick="showNoteModal(`{{ $request->note ?? 'No note provided.' }}`)"
                        class="text-white text-sm font-bold bg-green-500 rounded-lg p-2">
                        View Note
                    </button>
                </td>
                <td class="px-4 py-2 capitalize">{{ $request->request_status }}</td>
                <td class="px-4 py-2">
                    @php
                    $file = $files->firstWhere('file_id', $request->file_id);
                    @endphp
                    @if($file)
                    {{ $file->filename }}
                    @else
                    <span class="text-gray-500 italic">Not yet assigned</span>
                    @endif
                </td>
                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($request->created_at)->format('M d, Y H:i') }}</td>
                <td class="px-4 py-2">
                    @if(strtolower($request->request_status) !== 'approved')
                    <form action="{{ route('admin.file-request.update-status', $request->request_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="approved">
                        <button type="submit" class="bg-blue-500 text-white text-xs px-3 py-1 rounded hover:bg-blue-600">
                            Approve
                        </button>
                    </form>
                    @else
                    <span class="text-green-600 font-semibold">Approved</span>
                    @endif
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- Note Modal -->

    <div id="noteModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <h3 class="text-lg font-semibold mb-4">Note</h3>
            <p id="noteContent" class="text-gray-700"></p>
            <div class="text-right mt-4">
                <button onclick="closeNoteModal()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Close
                </button>
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
        document.getElementById('noteModal').classList.remove('flex');
    }
</script>
