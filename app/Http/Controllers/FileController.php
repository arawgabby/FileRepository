<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FileVersions;
use App\Models\File;


class FileController extends Controller
{

    public function downloadFile($filePath)
    {
        // Ensure that the file path doesn't start with 'uploads/' (because it could break the path)
        $storagePath = 'uploads/' . $filePath;  // Full path to the file inside 'uploads'
    
        // Check if the file exists in the 'uploads' folder
        if (Storage::disk('public')->exists($storagePath)) {
            // Generate the correct path to be used for download
            return response()->download(storage_path("app/public/$storagePath"));
        }
    
        // Check if the file exists in 'uploads/primaryFiles' folder
        $primaryFilePath = 'uploads/primaryFiles/' . $filePath;
        if (Storage::disk('public')->exists($primaryFilePath)) {
            return response()->download(storage_path("app/public/$primaryFilePath"));
        }
    
        // If not found, return an error
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
            $newFileName = $uploadedFile->getClientOriginalName();
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

        return redirect()->back()->with('success', 'File version unarchived successfully!');
    }

    public function RestoreFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);

        // Update status to 'archived'
        $fileVersion->update(['status' => 'active']);

        return redirect()->back()->with('success', 'File version restored successfully!');
    }

    public function unarchiveFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);

        // Update status to 'archived'
        $fileVersion->update(['status' => 'active']);

        return redirect()->back()->with('success', 'File version archived successfully!');
    }

    public function moveToTrash(Request $request, $id)
    {
        // Find the file by file_id
        $file = File::where('file_id', $id)->first();
    
        if ($file) {
            $file->status = 'deleted';
            $file->save();
            return redirect()->back()->with('success', 'File moved to trash successfully.');
        }
    
        return redirect()->back()->with('error', 'File not found.');
    }
    
    



    public function TrashFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);

        // Update status to 'archived'
        $fileVersion->update(['status' => 'deleted']);

        return redirect()->back()->with('success', 'File version placed on trash successfully!');
    }

    public function OverviewTrashFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);

        // Update status to 'archived'
        $fileVersion->update(['status' => 'deleted']);

        return redirect()->back()->with('success', 'File version placed on trash successfully!');
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

    public function editPrimaryFile($file_id)
    {
        // Fetch the file using the provided ID
        $file = File::findOrFail($file_id);

        return view('admin.pages.EditPrimaryFile', compact('file'));
    }


    public function updatePrimaryFile(Request $request, $file_id)
    {
        $file = File::findOrFail($file_id);
    
        // Validate input
        $request->validate([
            'filename' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'status' => 'required|string|in:active,inactive,pending,deactivated',
            'file' => 'nullable|file|max:5120', // Optional file upload, max 5MB
        ]);
    
        // Check if a new file is uploaded
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
    
            // Use the original filename
            $newFileName = $uploadedFile->getClientOriginalName();
            
            // Store the new file in 'uploads/primaryFiles' directory
            $filePath = $uploadedFile->storeAs('uploads/primaryFiles', $newFileName, 'public');
    
            // Delete old file if it exists
            if ($file->file_path) {
                Storage::disk('public')->delete($file->file_path);
            }
    
            // Update file path & size
            $file->file_path = $filePath;
            $file->file_size = $uploadedFile->getSize();
        } else {
            // If no new file is uploaded, rename the existing file
            $oldFilePath = $file->file_path; // Get the existing file path
    
            if ($oldFilePath && str_starts_with($oldFilePath, 'uploads/')) {
                // Extract the directory and get the file extension
                $directory = dirname($oldFilePath);
                $oldExtension = pathinfo($oldFilePath, PATHINFO_EXTENSION);
                
                // Ensure the filename doesn't already contain the extension
                $newFileName = pathinfo($request->filename, PATHINFO_FILENAME) . '.' . $oldExtension;
                $newFilePath = $directory . '/' . $newFileName;
    
                // Rename the file in storage
                Storage::disk('public')->move($oldFilePath, $newFilePath);
    
                // Update the file path in the database
                $file->file_path = $newFilePath;
            }
        }
    
        // Update file details
        $file->filename = pathinfo($request->filename, PATHINFO_FILENAME); // Save filename without extension
        $file->category = $request->category;
        $file->status = $request->status;
        $file->save();
    
        return redirect()->route('admin.files', $file_id)->with('success', 'File updated successfully!');
    }
    







    

    


   


    
    
}
