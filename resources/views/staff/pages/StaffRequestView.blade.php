@extends('staff.dashboard.staffDashboard')

@section('content')

@if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-2xl font-bold mb-4 border-b border-gray pb-2">Submit a Folder Access Request</h1>

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

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700" id="submitBtn">
                Save
            </button>
        </div>
    </form>

</div>

<script>
    // Handle form submission and validation
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

<script>
    // Check if a duplicate folder access request exists and show an alert
    @if(session('duplicate'))
        alert('You have already submitted a request for this folder.');
    @endif
</script>

@endsection

