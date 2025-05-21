<?php

namespace App\Http\Controllers;

use App\Models\FileRequest;
use App\Models\User;
use Illuminate\Http\Request;

class FileRequestController extends Controller
{
    public function assignFile(Request $request)
{
    $request->validate([
        'request_id' => 'required|integer|exists:file_requests,request_id',
        'file_id' => 'required|integer|exists:files,file_id',
    ]);

    FileRequest::where('request_id', $request->request_id)
        ->update(['file_id' => $request->file_id]);

    return back()->with('success', 'File assigned successfully.');
}
// AdminFileRequestController.php
public function showAssignFileForm(Request $request)
{
    $users = User::where('role_id', '!=', 1)->get(); // Exclude super admin
    $file_id = $request->input('file_id'); // Accept file_id if passed via query

    return view('admin.pages.AdminViewRequestsFiles', compact('users', 'file_id'));
}



}
