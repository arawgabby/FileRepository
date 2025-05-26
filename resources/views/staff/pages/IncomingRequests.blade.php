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
                <th class="px-4 py-2 text-left">Assigned File</th>
                <th class="px-4 py-2 text-left">Action</th>
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
                <td class="px-4 py-2">
                    <div id="assigned-file-{{ $request->request_id }}">
                        @if ($request->file_id)
                        <span class="text-gray-800 font-medium">
                            {{ $files->firstWhere('file_id', $request->file_id)->filename ?? 'File Not Found' }}
                        </span>
                        @else
                        <form action="{{ route('file-request.assign-file') }}" method="POST">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ $request->request_id }}">
                            @php
                            // Collect all assigned file_ids except the current request's file_id
                            $assignedFileIds = $incomingRequests->filter(function($r) use ($request) {
                            return $r->file_id && $r->request_id !== $request->request_id;
                            })->pluck('file_id')->toArray();
                            @endphp

                            <select name="file_id" class="border border-gray-300 rounded p-1" required>
                                <option value="">-- Select File --</option>
                                @foreach ($files as $file)
                                @if (!in_array($file->file_id, $assignedFileIds) || $file->file_id == $request->file_id)
                                <option value="{{ $file->file_id }}"
                                    {{ $request->file_id == $file->file_id ? 'selected' : '' }}>
                                    {{ $file->filename }}
                                </option>
                                @endif
                                @endforeach
                            </select>

                            <button type="submit"
                                class="ml-2 bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-xs">
                                Assign
                            </button>
                        </form>
                        @endif
                    </div>

                    @php
                    // Collect all assigned file_ids except the current request's file_id
                    $assignedFileIds = $incomingRequests->filter(function($r) use ($request) {
                    return $r->file_id && $r->request_id !== $request->request_id;
                    })->pluck('file_id')->toArray();
                    @endphp

                    <div id="update-form-{{ $request->request_id }}" class="hidden mt-2">
                        <form action="{{ route('file-request.assign-file') }}" method="POST">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ $request->request_id }}">
                            <select name="file_id" class="border border-gray-300 rounded p-1" required>
                                <option value="">-- Select File --</option>
                                @foreach ($files as $file)
                                @if (!in_array($file->file_id, $assignedFileIds) || $file->file_id == $request->file_id)
                                <option value="{{ $file->file_id }}"
                                    {{ $request->file_id == $file->file_id ? 'selected' : '' }}>
                                    {{ $file->filename }}
                                </option>
                                @endif
                                @endforeach
                            </select>
                            <button type="submit"
                                class="ml-2 bg-yellow-500 text-white px-2 py-1 rounded hover:bg-yellow-600 text-xs">
                                Update
                            </button>
                            <button type="button" onclick="cancelUpdate('{{ $request->request_id }}')"
                                class="ml-1 text-xs text-red-500">Cancel</button>
                        </form>
                    </div>

                </td>
                {{-- <td class="px-4 py-2">
                    @if ($request->file_id)
                    <button onclick="enableUpdate('{{ $request->request_id }}')"
                        class="bg-indigo-500 text-white px-2 py-1 rounded hover:bg-indigo-600 text-xs">
                        Edit
                    </button>
                    @endif
                </td> --}}
                <td class="px-4 py-2">
                    @if(strtolower($request->request_status) !== 'approved')
                    <form action="{{ route('newFile-request.update-status', $request->request_id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action" value="approved">
                        <button type="submit"
                            class="bg-blue-500 text-white text-xs px-3 py-1 rounded hover:bg-blue-600">
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
        document.getElementById('noteModal').classList.remove('flex');
    }

    function enableUpdate(id) {
        document.getElementById('assigned-file-' + id).style.display = 'none';
        document.getElementById('update-form-' + id).classList.remove('hidden');
    }

    function cancelUpdate(id) {
        document.getElementById('assigned-file-' + id).style.display = '';
        document.getElementById('update-form-' + id).classList.add('hidden');
    }
</script>
