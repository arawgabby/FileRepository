@extends('staff.dashboard.staffDashboard')
@section('title', 'View/Edit Word File')
@section('content')
<div class="container mx-auto p-6 bg-white shadow-md">
    <h1 class="text-2xl font-bold mb-4">{{ $file->filename }}</h1>
    <h2 class="text-lg font-semibold mb-2">File Content:</h2>
    <div
        class="bg-gray-100 p-4 rounded w-full max-h-[70vh] overflow-auto"
        style="word-break: break-word;">
        <style>
            /* Ensure Word styles are not overridden */
            .phpword {
                font-family: inherit;
                font-size: inherit;
            }

            .phpword table {
                border-collapse: collapse;
                width: 100%;
            }

            .phpword td,
            .phpword th {
                border: 1px solid #ccc;
                padding: 4px;
            }

            .phpword p {
                margin: 0 0 8px 0;
            }
        </style>
        {!! $html !!}
    </div>
</div>
@endsection