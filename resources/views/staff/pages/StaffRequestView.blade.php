@extends('staff.dashboard.staffDashboard')

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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
    <!-- Left: Display Folder Access Table -->
    <div class="bg-white p-6  shadow-md">
        <h2 class="text-xl font-semibold mb-4 border-b pb-2">My Folder Access Requests</h2>

        @if($folderAccesses->isEmpty())
            <p class="text-gray-600">No requests submitted yet.</p>
        @else
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b font-medium text-gray-700">
                        <th class="py-2 text-left text-center">Folder Name</th>
                        <th class="py-2 text-left text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($folderAccesses as $access)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-2 text-center">{{ $access->folder->name ?? 'N/A' }}</td>
                            <td class="py-2 text-center">
                                <span class="px-2 py-1 rounded-full 
                                    {{ $access->status === 'approved' ? 'bg-green-200 text-green-800' : 
                                       ($access->status === 'rejected' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800') }}">
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
    <div class="bg-white p-6  shadow-md">
        <h1 class="text-xl font-bold mb-4 border-b border-gray pb-2">Submit a Folder Access Request</h1>

        <form action="{{ route('folder.access.submit') }}" method="POST" id="folderAccessForm">
            @csrf
            <div class="mb-4">
                <label for="folder_id" class="block text-sm font-medium text-gray-700">Select a subFolder</label>
                <select name="folder_id" id="folder_id" class="mt-1 block w-full border border-gray-300 rounded-lg p-2" required>
                    <option value="">Select a Folder</option>
                    @foreach($folders as $folder)
                        <option value="{{ $folder->id }}">{{ $folder->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <input type="text" name="status" id="status" value="Waiting Approval" class="mt-1 block w-full border border-gray-300 rounded-lg p-2" readonly>
            </div>

            <div class="mb-4">
                <label for="note" class="block text-sm font-medium text-gray-700">Note (optional)</label>
                <textarea name="note" id="note" rows="3" placeholder="Add a note for the approver..." class="mt-1 block w-full border border-gray-300 rounded-lg p-2"></textarea>
            </div>


            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700" id="submitBtn">
                    Save
                </button>
            </div>
        </form>
    </div>


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
