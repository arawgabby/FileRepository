Hello World
@extends('staff.dashboard.staffDashboard')
@section('title', 'Request File')
@section('content')


    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 p-6">

        <div class="bg-white p-6 shadow-md mt-6">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">Requests for My Files</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">File Name</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Requested By</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Note</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Requested At</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($myFileRequests as $request)
                            <tr>
                                <td class="px-4 py-2">{{ $request->file->filename ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $request->requester->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">
                                    <button onclick="showNoteModal(`{{ $request->note ?? 'No note provided.' }}`)"
                                        class="text-white text-sm font-bold bg-green-500 rounded-lg p-2">
                                        View Note
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $request->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-4 py-2 capitalize">
                                    <span
                                        class="px-2 py-1 rounded text-white text-sm 
                                        @if ($request->request_status === 'pending') bg-yellow-500 
                                        @elseif($request->request_status === 'approved') bg-green-500 
                                        @elseif($request->request_status === 'rejected') bg-red-500 
                                        @else bg-gray-400 @endif
                                    ">
                                        {{ $request->request_status }}
                                    </span>

                                </td>

                                <td class="px-4 py-2 capitalize">
                                    @if ($request->request_status === 'Pending')
                                        <form
                                            action="{{ route('newFile-request.update-status', $request->id ?? $request->request_id) }}"
                                            method="POST" class="inline">
                                            @csrf
                                            <button name="action" value="approved"
                                                class="bg-green-500 text-white px-2 py-1 rounded ml-2 hover:bg-green-600"
                                                onclick="return confirm('Approve this request?')">Approve</button>
                                            <button name="action" value="rejected"
                                                class="bg-red-500 text-white px-2 py-1 rounded ml-2 hover:bg-red-600"
                                                onclick="return confirm('Reject this request?')">Reject</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-center text-gray-500">No requests for your files.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div> --}}

        {{-- <div class="bg-white p-6  shadow-md">
            <h2 class="text-xl font-semibold mb-4 border-b pb-2">My File Access Requests</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">File Name</th>
                            <th class="px-4 py-2 text-left text-sm font-semibold text-gray-700">Note</th>
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
                                    <button onclick="showNoteModal(`{{ $request->note ?? 'No note provided.' }}`)"
                                        class="text-white text-sm font-bold bg-green-500 rounded-lg p-2">
                                        View Note
                                    </button>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $request->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ $request->file->published_by }}
                                </td>
                                <td class="px-4 py-2 capitalize">
                                    <span
                                        class="px-2 py-1 rounded text-white text-sm
                                        @if ($request->request_status === 'pending') bg-yellow-500 
                                        @elseif($request->request_status === 'approved') bg-green-500 
                                        @elseif($request->request_status === 'rejected') bg-red-500 
                                        @else bg-gray-400 @endif
                                    ">
                                        {{ $request->request_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-gray-500">No file access requests
                                    found.</td>
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
                        <button onclick="closeNoteModal()"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Close</button>
                    </div>
                </div>
            </div>

        </div> --}}

        

        <script>
            function confirmRequest() {
                return confirm("Are you sure you want to submit this file access request?");
            }

            @if (session('success'))
                alert("{{ session('success') }}");
            @endif

            @if (session('error'))
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
