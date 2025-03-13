@extends('staff.dashboard.staffDashboard')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto p-6 bg-white rounded-xl" style="box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);">

    <h1 class="text-6xl font-bold mb-6">CSIT Repository System</h1>

    <!-- Cards Container -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        
        <!-- Active Files Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Active Files</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">13</p>
            </div>
            <i class="fas fa-file-alt text-3xl text-blue-500"></i>
        </div>

        <!-- Pending Files Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Pending Files</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">8</p>
            </div>
            <i class="fas fa-clock text-3xl text-yellow-500"></i>
        </div>

        <!-- Archived Files Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Total Storage Used</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">25GB</p>
            </div>
            <i class="fas fa-archive text-3xl text-gray-500"></i>
        </div>

        <!-- Recent Uploads Card -->
        <div class="bg-white rounded-md shadow-md p-4 flex items-center justify-between border">
            <div>
                <h2 class="text-xl font-semibold text-gray-500">Recent Uploads</h2>
                <br>
                <p class="text-6xl font-bold text-gray-900">5</p>
            </div>
            <i class="fas fa-upload text-3xl text-green-500"></i>
        </div>

    </div>
    
    <!-- Recent Activities Section -->
    <div class="mt-6 bg-white p-4">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">📌 Recent Activities </h2>

        <!-- Scrollable Table Container -->
        <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-md">
            <table class="w-full border-collapse">
                <thead class="sticky top-0 bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-4 py-2 text-left">File Name</th>
                        <th class="border border-gray-200 px-4 py-2 text-left">Accessed By</th>
                        <th class="border border-gray-200 px-4 py-2 text-left">Action</th>
                        <th class="border border-gray-200 px-4 py-2 text-left">Access Time</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Static Data Example -->
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Report_2024.pdf</td>
                        <td class="border border-gray-200 px-4 py-2">John Doe</td>
                        <td class="border border-gray-200 px-4 py-2 text-blue-600">Edited</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 10:15 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Design_Docs.zip</td>
                        <td class="border border-gray-200 px-4 py-2">Maria Smith</td>
                        <td class="border border-gray-200 px-4 py-2 text-green-600">Uploaded</td>
                        <td class="border border-gray-200 px-4 py-2">March 13, 2025 - 9:45 AM</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-200 px-4 py-2">Meeting_Notes.docx</td>
                        <td class="border border-gray-200 px-4 py-2">David Johnson</td>
                        <td class="border border-gray-200 px-4 py-2 text-red-600">Deleted</td>
                        <td class="border border-gray-200 px-4 py-2">March 12, 2025 - 4:30 PM</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>


</div>
@endsection
