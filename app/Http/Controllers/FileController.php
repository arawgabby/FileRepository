<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FileVersions;
use App\Models\File;


class FileController extends Controller
{
    public function downloadFile($filename)
    {
        $filePath = 'public/uploads/' . $filename;

        if (Storage::exists($filePath)) {
            return Storage::download($filePath);
        }

        return back()->with('error', 'File not found.');
    }



    public function downloadFileUpdated($filename)
    {
        $filePath = 'uploads/files/' . $filename; 
    
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath);
        }
    
        return back()->with('error', 'File not found.');
    }

    public function editFileVersion($version_id)
    {
        $fileVersion = FileVersions::where('version_id', $version_id)->firstOrFail(); // Fetch file version by version_id
    
        return view('admin.pages.EditFileVersion', compact('fileVersion'));
    }


    public function updateFileVersion(Request $request, $version_id)
    {
        // Fetch file version by version_id
        $fileVersion = FileVersions::where('version_id', $version_id)->firstOrFail();
    
        // Validate input
        $request->validate([
            'filename' => 'required|string|max:255',
            'file_type' => 'required|string|max:10',
            'file' => 'nullable|file|max:5120', // Optional file upload, max 5MB
        ]);
    
        // Handle file upload
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $newFileName = time() . '_' . $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->storeAs('uploads/files', $newFileName, 'public'); // Store with new name
    
            // Update file details
            $fileVersion->file_path = 'uploads/files/' . $newFileName;
            $fileVersion->file_size = $uploadedFile->getSize();
            $fileVersion->file_type = $uploadedFile->getClientOriginalExtension();
            $fileVersion->updated_at = now();
            $fileVersion->save();

        }
    
        // Update other details
        $fileVersion->filename = $request->filename;
        $fileVersion->save();
    
        return redirect()->route('admin.update')->with('success', 'File version updated successfully!');
    }


    public function archiveFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);

        // Update status to 'archived'
        $fileVersion->update(['status' => 'archived']);

        return redirect()->back()->with('success', 'File version archived successfully!');
    }


    public function archiveFileAdmin($file_id)
    {
        if (!session()->has('user')) {
            return redirect()->route('admin.upload')->with('error', 'Unauthorized: Please log in.');
        }

        $file = File::findOrFail($file_id);

        if ($file->status === 'archived') {
            return redirect()->back()->with('error', 'This file is already archived.');
        }

        $user = session('user');
        if (!$user || !$user->isAdmin()) { 
            return redirect()->back()->with('error', 'Unauthorized: You do not have permission.');
        }

        $file->update(['status' => 'archived']);

        return redirect()->back()->with('success', 'File archived successfully!');
    }



    

    


   


    
    
}
