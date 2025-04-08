@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    @if(session('user')->role === 'faculty')
        <img src="{{ asset('storage/uploads/dashboardbackgroundfaculty.png') }}" 
            alt="Dashboard Background" 
            class="h-74 w-auto rounded-[12px] object-cover">
    @elseif(session('user')->role === 'staff')
        <img src="{{ asset('storage/uploads/dashboardbackgroundstaff.png') }}"  
            alt="Dashboard Background" 
            class="h-74 w-auto rounded-[12px] object-cover">
    @endif


    <br>
    <!-- Cards Container -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        
        <!-- Active Files Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Active Files</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">{{ $activeFilesCount }}</p> <!-- Dynamic Count -->
            </div>
            <i class="fas fa-file-alt text-3xl text-blue-500"></i>
        </div>


        <!-- Pending Files Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Pending Files</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">{{ $pendingFilesCount }}</p> <!-- Dynamic Count -->
            </div>
            <i class="fas fa-clock text-3xl text-yellow-500"></i>
        </div>

        <!-- Archived Files Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Total Storage Used</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">{{ $formattedStorage }}</p> <!-- Dynamic Storage -->
            </div>
            <i class="fas fa-archive text-3xl text-gray-500"></i>
        </div>


        <div class="bg-white rounded-md shadow-md p-2 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Recent Uploads</h2>
                <br>

                <div class="flex space-x-2 pb-2">
                    <a href="{{ route('staff.page.dashboard', ['filter' => 'daily']) }}" class="text-sm bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 px-4 py-2 rounded-md">Daily</a>
                    <a href="{{ route('staff.page.dashboard', ['filter' => 'monthly']) }}" class="text-sm bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 px-4 py-2 rounded-md">Monthly</a>
                    <a href="{{ route('staff.page.dashboard', ['filter' => 'yearly']) }}" class="text-sm bg-yellow-100 text-yellow-600 hover:bg-yellow-200 hover:text-yellow-700 px-4 py-2 rounded-md">Yearly</a>
                    <a href="{{ route('staff.page.dashboard', ['filter' => 'all']) }}" class="text-sm bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-700 px-4 py-2 rounded-md">All</a>
                </div>

                <p class="text-6xl font-bold text-gray-900">{{ $recentUploadsCount }}</p> <!-- Dynamic Recent Uploads -->
            </div>
            <i class="fas fa-upload text-3xl text-green-500"></i>
        </div>



    </div>
    
    <!-- Recent Activities Section -->
    <div class="mt-6 bg-white p-4">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">📌 Recent Activities</h2>

    <!-- Scrollable Table Container -->
    <div class="max-h-80 overflow-y-auto border rounded-md">
        <table class="w-full border-collapse">
            <thead class="top-0 bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">File Name</th>
                    <th class="border px-4 py-2 text-left">Accessed By</th>
                    <th class="border px-4 py-2 text-left">Action</th>
                    <th class="border px-4 py-2 text-left">Access Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentFiles as $file)
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">{{ $file->filename }}</td>
                        <td class="border border-gray-200 px-4 py-2">{{ $file->uploaded_by }}</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">
                            @if ($file->status == 'active') Edited
                            @elseif ($file->status == 'pending') Uploaded
                            @else Deleted
                            @endif
                        </td>
                        <td class="border border-gray-200 px-4 py-2">{{ $file->updated_at->format('F j, Y - g:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



</div>
@endsection
