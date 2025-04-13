@extends('admin.dashboard.adminDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
</style>


<div class="container mx-auto p-6 bg-white " style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <p class="mb-6">
        <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg flex items-center gap-2 w-max">
            <i class="fa-solid fa-arrow-left"></i> 
        </a>
    </p>

    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px" class="-m-6 mb-6 border-b border-gray-300">
        <i class="fas fa-file text-gray-400 mr-6 ml-6 mt-6"></i>File Time Stamps Details</h1>

    <div class="max-w-full mx-auto rounded-lg p-2 mt-2">
        <h2 class="text-2xl font-semibold mb-4">Timestamps for File ID: 00{{ $file_id }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <!-- <th class="p-4">Timestamp ID</th> -->
                        <!-- <th class="p-4">Version</th> -->
                        <th class="p-4">Activity</th>
                        <th class="p-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timestamps as $timestamp)
                        <tr class="file-row border-b border-gray-300 {{ $loop->odd ? 'bg-gray-100' : '' }}">
                            <!-- <td class="p-4">00{{ $timestamp->timestamp_id }}</td> -->
                            <!-- <td class="p-4">00{{ $timestamp->fileVersion->version_number ?? 'N/A' }}</td> -->
                            <td class="p-4">{{ $timestamp->event_type }}</td>
                            <td class="p-4">{{ \Carbon\Carbon::parse($timestamp->recorded_at)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
