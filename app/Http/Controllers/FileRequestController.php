<?php

namespace App\Http\Controllers;

use App\Models\FileRequest;
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

}
