<?php

namespace App\Http\Controllers;

use App\Models\FileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{

    public function submitFileRequests(Request $request)
    {
        $request->validate([
            'requested_to' => 'required|integer|exists:users,id',
            'note' => 'nullable|string',
        ]);
        $admin = Auth::user();

        $exists = FileRequest::where('requested_by', $admin->id)
            ->where('requested_to', $request->requested_to)
            ->whereNull('file_id')
            ->exists();

        if ($exists) {
            return redirect()->back()->with('duplicate', 'You have already submitted a request to this user and it has not yet been assigned a file.');
        }

        try {
            FileRequest::create([
                'file_id' => null,
                'requested_by' => $admin->id,
                'requested_to' => $request->requested_to,
                'note' => $request->note,
                'request_status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Request submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit request.');
        }
    }
    public function viewMyOutgoingRequests()
    {
        $userId = auth()->id(); // Get the logged-in admin's ID

        // Get only the requests created by the logged-in admin
        $outgoingRequests = \App\Models\FileRequest::where('requested_by', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get only the "requested_to" users
        $userIds = $outgoingRequests->pluck('requested_to')->unique()->toArray();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

        // Get all files (if file_id is assigned)
        $files = \App\Models\Files::all();

        return view('admin.pages.AdminOutgoingRequests', compact('outgoingRequests', 'users', 'files'));
    }
    public function updateFileRequestStatusAdmin(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approved',
        ]);

        $fileRequest = FileRequest::findOrFail($id);

        if ($fileRequest->file_id === null) {
            return redirect()->back()->with('error', 'Cannot approve request. No file assigned.');
        }

        $fileRequest->request_status = $request->action;
        $fileRequest->save();

        return redirect()->back()->with('success', 'Request status updated!');
    }
    public function viewAllOutgoingRequests()
    {
        // Get all requests submitted by users
        $outgoingRequests = \App\Models\FileRequest::orderBy('created_at', 'desc')->get();

        // Collect unique user IDs involved
        $userIds = $outgoingRequests->pluck('requested_by')
            ->merge($outgoingRequests->pluck('requested_to'))
            ->unique()
            ->toArray();

        // Fetch users keyed by id
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

        // Fetch all files
        $files = \App\Models\Files::all();

        return view('admin.pages.AdminViewAllOutgoingRequests', compact('outgoingRequests', 'users', 'files'));
    }
}
