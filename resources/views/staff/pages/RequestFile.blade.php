@extends('staff.dashboard.staffDashboard')

@section('content')


<div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">

    <div class="bg-white p-6  shadow-md">
        <h2 class="text-xl font-semibold mb-4 border-b pb-2">My File Access Requests</h2>

        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">File Name</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Note for Admin</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Requested At</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Verified By</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $request)
                    <tr>
                        <td class="px-4 py-2">{{ $request->file->filename ?? 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <button onclick="showNoteModal(`{{ $request->note ?? 'No note provided.' }}`)" class="text-white text-sm font-bold bg-green-500 rounded-lg p-2">
                                View Note
                            </button>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $request->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-600">
                            {{ $request->file->processed_by ?? 'To be checked by the Admin' }}
                        </td>
                        <td class="px-4 py-2 capitalize">
                            <span class="
                                px-2 py-1 rounded text-white text-sm
                                @if($request->request_status === 'pending') bg-yellow-500 
                                @elseif($request->request_status === 'approved') bg-green-500 
                                @elseif($request->request_status === 'rejected') bg-red-500 
                                @else bg-gray-400 
                                @endif
                            ">
                                {{ $request->request_status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">No file access requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
       
    </div>

    <div class="bg-white p-6 shadow-md">
        <h1 class="text-xl font-bold mb-4 border-b border-gray pb-2">Submit a File Access Request</h1>

        <form id="fileRequestForm" action="{{ route('file-request.submit') }}" method="POST" onsubmit="return confirmRequest()">
            @csrf

            <!-- Hidden Input for requested_by -->
            <input type="hidden" name="requested_by" value="{{ session('user')->id }}">

            <!-- File Dropdown -->
            <div class="mb-4">
                <label for="file_id" class="block text-sm font-medium text-gray-700">Select File</label>
                <select name="file_id" id="file_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    <option value="">-- Choose File --</option>
                    @foreach($files as $file)
                        <option value="{{ $file->file_id }}">{{ $file->filename }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Note Field -->
            <div class="mb-4">
                <label for="note" class="block text-sm font-medium text-gray-700">Note (Optional)</label>
                <textarea name="note" id="note" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"></textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Submit Request
            </button>
        </form>
    </div>

    <script>
        function confirmRequest() {
            return confirm("Are you sure you want to submit this file access request?");
        }

        @if(session('success'))
            alert("{{ session('success') }}");
        @endif

        @if(session('error'))
            alert("{{ session('error') }}");
        @endif
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



</div>


@endsection
