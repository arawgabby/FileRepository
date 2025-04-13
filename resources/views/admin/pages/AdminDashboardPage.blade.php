@extends('admin.dashboard.adminDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <!-- @if(session('user')->role === 'faculty')
        <img src="{{ asset('storage/uploads/dashboardbackgroundfaculty.png') }}" 
            alt="Dashboard Background" 
            class="h-74 w-auto   object-cover">
    @elseif(session('user')->role === 'staff')
        <img src="{{ asset('storage/uploads/dashboardbackgroundstaff.png') }}"  
            alt="Dashboard Background" 
            class="h-74 w-auto  object-cover">
    @elseif(session('user')->role === 'admin')
        <img src="{{ asset('storage/uploads/dashboardbackgroundadmin.png') }}"  
            alt="Dashboard Background" 
            class="h-74 w-auto  object-cover">
    @endif -->


    <!-- <br> -->

    <!-- Cards Container -->
    <h2 class="text-2xl font-semibold text-gray-700 mb-4 pt-4 border-b border-gray">Dashboard</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4">
        
        <!-- Active Files Card -->
        <div class="bg-white  shadow-md p-4 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Active Files</h2>
                <br>
                <p class="text-2xl font-bold text-gray-900">{{ $activeFilesCount }}</p> <!-- Dynamic Count -->
            </div>
            <i class="fas fa-file-alt text-2xl text-blue-500"></i>
        </div>


        <!-- Pending Files Card -->
        <div class="bg-white  shadow-md p-4 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Pending Files</h2>
                <br>
                <p class="text-2xl font-bold text-gray-900">{{ $pendingFilesCount }}</p> <!-- Dynamic Count -->
            </div>
            <i class="fas fa-clock text-2xl text-yellow-500"></i>
        </div>

        <!-- Archived Files Card -->
        <div class="bg-white  shadow-md p-4 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Total Storage Used</h2>
                <br>
                <p class="text-2xl font-bold text-gray-900">{{ $formattedStorage }}</p> <!-- Dynamic Storage -->
            </div>
            <i class="fas fa-archive text-2xl text-gray-500"></i>
        </div>


        <div class="bg-white  shadow-md p-2 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Recent Uploads</h2>
                <br>

                <div class="flex space-x-2">
                    <a href="{{ route('admin.page.dashboard', ['filter' => 'daily']) }}" class="text-sm bg-blue-100 text-blue-600 hover:bg-blue-200 hover:text-blue-700 px-4 py-2 ">Daily</a>
                    <a href="{{ route('admin.page.dashboard', ['filter' => 'monthly']) }}" class="text-sm bg-green-100 text-green-600 hover:bg-green-200 hover:text-green-700 px-4 py-2 ">Monthly</a>
                    <a href="{{ route('admin.page.dashboard', ['filter' => 'yearly']) }}" class="text-sm bg-yellow-100 text-yellow-600 hover:bg-yellow-200 hover:text-yellow-700 px-4 py-2 ">Yearly</a>
                    <a href="{{ route('admin.page.dashboard', ['filter' => 'all']) }}" class="text-sm bg-gray-100 text-gray-600 hover:bg-gray-200 hover:text-gray-700 px-4 py-2 ">All</a>
                </div>

                <p class="text-2xl font-bold text-gray-900">{{ $recentUploadsCount }}</p> <!-- Dynamic Recent Uploads -->
            </div>
            <i class="fas fa-upload text-2xl text-green-500"></i>
        </div>



    </div>

    <br>

     <!-- Cards Container -->
     <h2 class="text-2xl font-semibold text-gray-700 mb-4 pt-4 border-b border-gray pb-2">Users</h2>

     <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 ">
        
        <!-- Active Files Card -->
        <div class="bg-white  shadow-md p-4 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Total Users</h2>
                <br>
                <p class="text-2xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
            <i class="fas fa-user text-2xl text-blue-500"></i>
        </div>


        <!-- Pending Files Card -->
        <div class="bg-white  shadow-md p-4 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Users This Month</h2>
                <br>
                <p class="text-2xl font-bold text-gray-900">{{ $usersThisMonth }}</p>
                </div>
            <i class="fas fa-users text-2xl text-yellow-500"></i>
        </div>

        <!-- Archived Files Card -->
        <div class="bg-white  shadow-md p-4 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Users Today</h2>
                <br>
                <p class="text-2xl font-bold text-gray-900">{{ $usersToday }}</p>
                </div>
            <i class="fas fa-circle-user text-2xl text-gray-500"></i>
        </div>


        <div class="bg-white  shadow-md p-2 flex items-center justify-between border-left">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Active Users</h2>
                <br>

                <p class="text-2xl font-bold text-gray-900">{{ $activeUsers }}</p>
                </div>
            <i class="fas fa-circle-check text-2xl text-gray-800"></i>
        </div>



    </div>
    
    <!-- Recent Activities Section -->
    <div class="mt-6 bg-white p-4">
    <h2 class="text-2xl font-semibold text-gray-700 mb-4">📌 Recent Activities</h2>

    <!-- Scrollable Table Container -->
    <div class="max-h-80 overflow-y-auto border-left pt-4 ">
        <table class="w-full border-left-collapse">
            <thead class="top-0 bg-gray-100">
                <tr>
                    <th class="border-left px-4 py-2 text-left">File Name</th>
                    <th class="border-left px-4 py-2 text-left">Accessed By</th>
                    <th class="border-left px-4 py-2 text-left">Action</th>
                    <th class="border-left px-4 py-2 text-left">Access Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentFiles as $file)
                    <tr class="hover:bg-gray-50">
                        <td class="border-left border-left-gray-200 px-4 py-2">{{ $file->filename }}</td>
                        <td class="border-left border-left-gray-200 px-4 py-2">
                            {{ optional($file->user)->name ?? 'Unknown' }}
                        </td>
                        <td class="border-left border-left-gray-200 px-4 py-2 text-blue-600">
                            @if ($file->status == 'active') Edited
                            @elseif ($file->status == 'pending') Uploaded
                            @else Deleted
                            @endif
                        </td>
                        <td class="border-left border-left-gray-200 px-4 py-2">{{ $file->updated_at->format('F j, Y - g:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



</div>
@endsection
