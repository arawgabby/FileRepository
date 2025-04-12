@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    td {
        text-align: center;
    }
    .status-pending {
        background-color: red;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .status-denied {
        background-color: gray;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        display: inline-block;
    }
    .file-icon {
        margin-right: 5px;
    }
    .btn-request-again {
        background-color: blue;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>

<!-- Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-2xl font-bold mb-4">File Requests</h1>
    
    <input type="text" id="searchInput" class="border w-full p-2 mb-4 border-gray-300 rounded" 
           placeholder="Search requests...">

    <div class="mt-2 flex items-center text-red-600 text-sm mb-2">
        <i class="fas fa-info-circle mr-2"></i>
        Pending and denied files can be managed here.
    </div>
    
    <table class="w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2">Request ID</th>
                <th class="p-2">File Name</th>
                <th class="p-2">Requested By</th>
                <th class="p-2">Request Status</th>
                <th class="p-2">Requested At</th>
                <th class="p-2">Action</th>
            </tr>
        </thead>
        <tbody id="requestTable">
            @forelse($fileRequests as $request)
                @php
                    $filename = $request->file->filename ?? 'Unknown';
                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    $iconClass = match ($extension) {
                        'doc', 'docx' => 'fa-file-word text-blue-600',
                        'pdf' => 'fa-file-pdf text-red-600',
                        'xls', 'xlsx' => 'fa-file-excel text-green-600',
                        'ppt', 'pptx' => 'fa-file-powerpoint text-orange-600',
                        'zip', 'rar' => 'fa-file-zipper text-yellow-600',
                        default => 'fa-file text-gray-600',
                    };
                @endphp
                <tr>
                    <td class="p-2">REQ00{{ $request->request_id }}</td>
                    <td class="p-2">
                        <i class="fa-solid {{ $iconClass }} file-icon"></i> {{ $filename }}
                    </td>
                    <td class="p-2">{{ $request->user->name ?? 'Unknown' }}</td>
                    <td class="p-2">
                        <span class="{{ $request->request_status === 'denied' ? 'status-denied' : 'status-pending' }}">
                            {{ ucfirst($request->request_status) }}
                        </span>
                    </td>
                    <td class="p-2">{{ $request->created_at->diffForHumans() }}</td>
                    <td class="p-2">
                        @if($request->request_status === 'denied')
                            <form action="{{ route('retry.status', ['id' => $request->request_id]) }}" method="POST" 
                                onsubmit="return confirm('Are you sure you want to request this file again?');">
                                @csrf
                                <button type="submit" class="btn-request-again">Request Again</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-2 text-center text-gray-500">No requests found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
