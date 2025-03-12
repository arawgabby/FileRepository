@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    td {
        text-align: center;
    }
</style>


<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <p class="mb-6"><a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Back</a></p>

    <h1 style="font-size: 36px; font-weight: bold; margin-bottom: 12px"><i class="fas fa-file text-gray-400 mr-4"></i>File Time Stamps Details</h1>

    <div class="max-w-full mx-auto rounded-lg p-6 mt-4">
        <h2 class="text-2xl font-semibold mb-4">Timestamps for File ID: 00{{ $file_id }}</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3">Timestamp ID</th>
                        <th class="p-3">Version</th>
                        <th class="p-3">Event Type</th>
                        <th class="p-3">Recorded At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($timestamps as $timestamp)
                        <tr class="">
                            <td class="p-3">00{{ $timestamp->timestamp_id }}</td>
                            <td class="p-3">00{{ $timestamp->fileVersion->version_number ?? 'N/A' }}</td>
                            <td class="p-3">{{ $timestamp->event_type }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($timestamp->recorded_at)->diffForHumans() }}</td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
