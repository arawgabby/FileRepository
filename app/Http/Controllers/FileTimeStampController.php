<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileTimeStamp;
use App\Models\FileVersions;
use App\Models\AccessLog;
use App\Models\Files;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class FileTimeStampController extends Controller
{
    public function ViewIndex()
    {
        $timestamps = FileTimeStamp::with(['file', 'fileVersion'])->paginate(10); // Paginate with 10 records per page
        return view('staff.pages.ViewTimeStamps', compact('timestamps'));
    }    

    public function AdminViewIndex()
    {
        $timestamps = FileTimeStamp::with(['file', 'fileVersion'])->paginate(10); // Paginate with 10 records per page
        return view('admin.pages.AdminViewTimeStamps', compact('timestamps'));
    }   

    public function show($file_id)
    {
        // Fetch all timestamps related to the given file_id
        $timestamps = FileTimeStamp::where('file_id', $file_id)->get();

        // Pass data to the ViewTimeStampsDetails.blade.php
        return view('staff.pages.ViewTimeStampsDetails', compact('timestamps', 'file_id'));
    }

    public function Adminshow($file_id)
    {
        // Fetch all timestamps related to the given file_id
        $timestamps = FileTimeStamp::where('file_id', $file_id)->get();

        // Pass data to the ViewTimeStampsDetails.blade.php
        return view('admin.pages.AdminViewTimeStampDetails', compact('timestamps', 'file_id'));
    }

    public function AdminViewLogs()
    {
        // Fetch all access logs with pagination
        $accessLogs = AccessLog::with(['user', 'file']) // Load related user and file
                        ->latest()
                        ->paginate(12); // Set pagination to 15 per page
    
        return view('admin.pages.AdminLogsView', compact('accessLogs'));
    }

    public function AdmindeleteFile($fileId)
    {
        // Retrieve the file record by its ID
        $file = Files::find($fileId);
    
        if (!$file) {
            // If the file does not exist in the database
            Session::flash('error', 'File not found in the database!');
            return redirect()->route('admin.trash.bins');
        }
    
        // Build the full file path using the file_path stored in the database
        $filePath = storage_path('app/public/' . $file->file_path);
    
        // Check if the file exists on the server
        if (!File::exists($filePath)) {
            // If the file does not exist in the file system
            Session::flash('error', 'File does not exist in the file system!');
            return redirect()->route('admin.trash.bins');
        }
    
        // Try to delete the file from the file system
        try {
            // Delete the file from the server
            File::delete($filePath);
    
            // Now delete the file record from the database
            $file->delete();
    
            // Flash success message and redirect back to trash bin page
            Session::flash('success', 'File deleted successfully!');
            return redirect()->route('admin.trash.bins');
        } catch (\Exception $e) {
            // Handle any errors during the deletion process
            Session::flash('error', 'An error occurred while deleting the file: ' . $e->getMessage());
            return redirect()->route('admin.trash.bins');
        }
    }


}
