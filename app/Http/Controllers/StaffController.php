<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Storage;
use App\Models\File;
use App\Models\FileVersions;
use App\Models\FileTimeStamp;
use Illuminate\Support\Facades\Auth;
use App\Models\FileRequest;
use Carbon\Carbon;


class StaffController extends Controller
{

    public function dashboard()
    {
        $userId = Auth::id(); // Get logged-in user ID

        // Count pending file requests for the user
        $pendingRequestCount = FileRequest::where('requested_by', $userId)
                                        ->where('request_status', 'pending')
                                        ->count();

        return view('staff.dashboard.staffDashboard', compact('pendingRequestCount'));
    }


    public function StaffuploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:502400', 
            'category' => 'required|in:capstone,thesis,faculty_request,accreditation,admin_docs',
            'published_by' => 'required|string|max:255',
            'year_published' => 'required|string|regex:/^\d{4}$/', // ✅ Ensure it’s a 4-digit year
            'description' => 'nullable|string|max:1000',
        ]);

        if (!session()->has('user')) {
            return response()->json(['message' => 'Unauthorized: Please log in.'], 403);
        }

        $user = session('user');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $filename, 'public');

            $fileEntry = File::create([
                'filename' => $filename,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => $user->id,
                'category' => $request->category,
                'published_by' => $request->published_by,
                'year_published' => (string) $request->year_published, // ✅ Ensure it's stored as a string
                'description' => $request->description ?? null,
                'status' => 'pending',
            ]);

            if ($fileEntry) {
                AccessLog::create([
                    'file_id' => $fileEntry->id ?? 0,
                    'accessed_by' => $user->id,
                    'action' => 'Uploaded file - Pending approval',
                    'access_time' => now(),
                ]);

                return response()->json(['message' => 'File uploaded successfully!'], 200);
            }

            return response()->json(['message' => 'File upload failed.'], 500);
        }

        return response()->json(['message' => 'No file detected.'], 400);
    }

    public function ActiveFileArchived($file_id)
    {
        // Find the file
        $file = File::find($file_id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found');
        }

        // Update the status to archived
        $file->status = 'archived';
        $file->save();

        return redirect()->back()->with('success', 'File successfully archived');
    }



    public function StaffviewLogs()
    {
        // Get logged-in user from session
        $user = session('user');
    
        // Fetch logs where accessed_by matches the logged-in user's ID
        $accessLogs = AccessLog::where('accessed_by', $user->id)
                        ->with(['user', 'file']) // Load related user and file
                        ->latest()
                        ->get();
    
        return view('staff.pages.StaffLogsView', compact('accessLogs', 'user'));
    }
    


    public function StaffviewFiles(Request $request)
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get logged-in user from session
    
        // Fetch primary files uploaded by the logged-in user
        $files = File::where('uploaded_by', $user->id); // Use session user ID
    
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $files->where('filename', 'LIKE', '%' . $request->search . '%');
        }
    
        // Apply file type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $files->where('file_type', $request->file_type);
        }
    
        // Apply category filter
        if ($request->has('category') && !empty($request->category)) {
            $files->where('category', $request->category);
        }
    
        $files = $files->paginate(10); // Paginate results
    
        // Fetch file versions separately and link to files
        $fileVersions = FileVersions::whereIn('file_id', $files->pluck('file_id'))->get();
    
        return view('staff.pages.StaffViewAllFiles', compact('fileVersions', 'files'));
    }
    
    public function MyUploads(Request $request)
    {
        // Fetch primary files
        $files = File::query();

        // Apply filters to primary files
        if ($request->has('search') && !empty($request->search)) {
            $files->where('filename', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('file_type') && !empty($request->file_type)) {
            $files->where('file_type', $request->file_type);
        }

        if ($request->has('category') && !empty($request->category)) {
            $files->where('category', $request->category);
        }

        $files = $files->paginate(10); // Paginate results

        // Fetch file versions separately and link to files
        $fileVersions = FileVersions::whereIn('file_id', $files->pluck('file_id'))->get();

        return view('staff.pages.MyUploads', compact('fileVersions', 'files'));
    }

    public function StaffdownloadFile($filePath)
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

    public function StaffmoveToTrash(Request $request, $id)
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->back()->with('error', 'Unauthorized: Please log in.');
        }

        $user = session('user'); // Get logged-in user from session

        // Find the file by file_id
        $file = File::where('file_id', $id)->first();

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Update status to 'deleted'
        $file->update(['status' => 'deleted']);

        // ✅ Log the action in access_logs
        AccessLog::create([
            'file_id' => $file->file_id, // Ensure valid file_id
            'accessed_by' => $user->id, // Get user ID from session
            'action' => 'File moved to trash (File ID: ' . $file->file_id . ')',
            'access_time' => now(),
        ]);

        return redirect()->back()->with('success', 'File moved to trash successfully.');
    }

    
    public function StaffOverviewTrashFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);
    
        // Ensure the related file exists
        if (!$fileVersion->file_id) {
            return redirect()->back()->with('error', 'Invalid file version.');
        }
    
        // Update status to 'deleted'
        $fileVersion->update(['status' => 'deleted']);
    
        // ✅ Log the action in access_logs
        AccessLog::create([
            'file_id' => $fileVersion->file_id, // Ensure valid file_id
            'accessed_by' => auth()->id(), // Get the authenticated user's ID
            'action' => 'File moved to trash',
            'access_time' => now(),
        ]);
    
        return redirect()->back()->with('success', 'File version placed on trash successfully!');
    }

    public function requestFile(Request $request, $file_id)
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get logged-in user
    
        // Check if the file exists before proceeding
        $file = File::find($file_id);
        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }
    
        // Check if the request already exists to avoid duplicates
        $existingRequest = FileRequest::where('file_id', $file_id)
            ->where('requested_by', $user->id)
            ->where('request_status', 'pending')
            ->first();
    
        if ($existingRequest) {
            return redirect()->back()->with('error', 'You have already requested this file.');
        }
    
        // ✅ Insert new request
        $fileRequest = FileRequest::create([
            'file_id' => $file_id,
            'requested_by' => $user->id,
            'request_status' => 'pending', // Default status
        ]);
    
        // ✅ Log the action only if the request is successfully created
        if ($fileRequest) {
            AccessLog::create([
                'file_id' => $file_id, // Ensure valid file_id
                'accessed_by' => $user->id,
                'action' => 'Requested file access - Pending approval',
                'access_time' => now(),
            ]);
        }
    
        return redirect()->back()->with('success', 'File request submitted successfully.');
    }

    public function pendingAndDeniedFileRequests()
    {
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user');
    
        $fileRequests = FileRequest::where('requested_by', $user->id)
            ->whereIn('request_status', ['pending', 'denied'])
            ->with('file')
            ->get();
    
        return view('staff.pages.PendingFiles', compact('fileRequests'));
    }

    public function retryFileRequest($id)
    {
        $fileRequest = FileRequest::findOrFail($id);
    
        if ($fileRequest->request_status === 'denied') {
            $fileRequest->request_status = 'pending';
            $fileRequest->save();
            return redirect()->back()->with('success', 'Request successfully resubmitted.');
        }
    
        return redirect()->back()->with('error', 'Invalid action.');
    }
    
        

    public function activeFiles(Request $request)
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

        $user = session('user'); // Get logged-in user from session

        // Fetch primary files uploaded by the logged-in user
        $files = File::where('uploaded_by', $user->id); // Use session user ID
        
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $files->where('filename', 'LIKE', '%' . $request->search . '%');
        }

        // Apply file type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $files->where('file_type', $request->file_type);
        }

        // Apply category filter
        if ($request->has('category') && !empty($request->category)) {
            $files->where('category', $request->category);
        }

        // Paginate results
        $files = $files->paginate(10);

        // Fetch file versions linked to the filtered files
        $fileVersions = FileVersions::whereIn('file_id', $files->pluck('file_id'))->get();

        return view('staff.pages.StaffViewAllFilesActive', compact('fileVersions', 'files'));
    }

    public function StaffeditPrimaryFile($file_id)
    {
        // Fetch the file using the provided ID
        $file = File::findOrFail($file_id);

        return view('staff.pages.StaffEditPrimaryFile', compact('file'));
    }

    public function StaffupdatePrimaryFile(Request $request, $file_id)
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
    
        return redirect()->route('staff.active.files', $file_id)->with('success', 'File updated successfully!');
    }

    public function StaffeditFile($file_id)
    {
        // Ensure $file_id is correctly received and cast to integer
        $file_id = (int) $file_id;

        // Check if the file exists
        $file = File::findOrFail($file_id);

        // Ensure auth user is logged in before logging
        if (auth()->check()) {

            \Log::info('File ID:', ['file_id' => $file_id]);
            \Log::info('Auth User:', ['user_id' => auth()->id()]);


            AccessLog::create([
                'file_id' => $file->id,
                'accessed_by' => auth()->id(),
                'action' => 'Edited File',
                'access_time' => now()
            ]);
        }

        return view('staff.pages.StaffEditFilesView', compact('file'));
    }

    public function StaffupdateFile(Request $request, $file_id)
    {
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get user from session
        $file = File::findOrFail($file_id);
    
        // Validate input
        $request->validate([
            'filename' => 'required|string|max:255',
            'file_type' => 'required|string|max:10',
            'category' => 'required|string|max:50',
            'file' => 'nullable|file|max:5120', // Optional file upload, max 5MB
        ]);
    
        // Get the latest version number and increment it
        $latestVersion = FileVersions::where('file_id', $file->file_id)->max('version_number') ?? 0;
        $newVersion = $latestVersion + 1;
    
        // Handle file upload
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
    
            // Generate a unique filename with the same name
            $newFileName = pathinfo($file->filename, PATHINFO_FILENAME) . '.' . $uploadedFile->getClientOriginalExtension();
            $filePath = $uploadedFile->storeAs('uploads/files', $newFileName, 'public'); // Store with new name
            $fileSize = $uploadedFile->getSize();
            $fileType = $uploadedFile->getClientOriginalExtension();
        } else {
            $filePath = $file->file_path;
            $fileSize = $file->file_size;
            $fileType = $file->file_type;
        }
    
        // Store the new version in `file_versions`
        FileVersions::create([
            'file_id' => $file->file_id,
            'version_number' => $newVersion,
            'filename' => $request->filename, // Use the updated filename from input
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'file_type' => $fileType,
            'uploaded_by' => $user->id ?? null, // Use session user ID
        ]);
    
        // Log the file update in `access_logs`
        AccessLog::create([
            'file_id' => $file->file_id,
            'accessed_by' => $user->id ?? null, // Ensure user is logged in
            'action' => 'Added File - Version ' . $newVersion,
            'access_time' => now()
        ]);
    
        return redirect()->route('staff.editFile', $file_id)->with('success', 'New file version saved!');
    }

    public function StaffViewFilesVersions(Request $request) 
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

        $user = session('user'); // Get the logged-in user

        // Only fetch file versions uploaded by the logged-in user
        $query = FileVersions::where('uploaded_by', $user->id);

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where('filename', 'LIKE', '%' . $request->search . '%');
        }

        // Apply file type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $query->where('file_type', $request->file_type);
        }

        // Apply category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Get filtered results
        $fileVersions = $query->paginate(10); // Paginate results

        return view('staff.pages.StaffEditFilesOverview', compact('fileVersions')); // Pass data to view
    }

    public function StaffarchiveFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);
    
        // Update status to 'archived'
        $fileVersion->update(['status' => 'archived']);
    
        // Insert into file_time_stamps with file_id in event_type
        FileTimeStamp::create([
            'file_id' => $fileVersion->file_id,
            'version_id' => $fileVersion->version_id,
            'event_type' => 'File ID ' . $fileVersion->file_id . ' Archived', // Include file_id in the message
            'timestamp' => now(),
        ]);
    
        return redirect()->back()->with('success', 'File version archived successfully!');
    }

    public function StaffeditFileVersion($version_id)
    {
        $fileVersion = FileVersions::where('version_id', $version_id)->firstOrFail(); // Fetch file version by version_id
    
        return view('staff.pages.StaffEditFileVersion', compact('fileVersion'));
    }

    public function StaffTrashFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);

        // Update status to 'deleted'
        $fileVersion->update(['status' => 'deleted']);

        // Insert into file_time_stamps with file_id in event_type
        FileTimeStamp::create([
            'file_id' => $fileVersion->file_id,
            'version_id' => $fileVersion->version_id,
            'event_type' => 'File ID ' . $fileVersion->file_id . ' Moved to Trash', // Log trash event
            'timestamp' => now(),
        ]);

        return redirect()->back()->with('success', 'File version placed in trash successfully!');
    }


    public function StaffArchivedViewFilesVersions(Request $request) 
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get the logged-in user
    
        // Fetch archived file versions uploaded by the user
        $fileVersionsQuery = FileVersions::where('uploaded_by', $user->id)
            ->where('status', 'archived');
    
        // Fetch archived files uploaded by the user
        $filesQuery = File::where('uploaded_by', $user->id)
            ->where('status', 'archived');
    
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $fileVersionsQuery->where('filename', 'LIKE', '%' . $request->search . '%');
            $filesQuery->where('filename', 'LIKE', '%' . $request->search . '%');
        }
    
        // Apply file type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $fileVersionsQuery->where('file_type', $request->file_type);
            $filesQuery->where('file_type', $request->file_type);
        }
    
        // Apply category filter
        if ($request->has('category') && !empty($request->category)) {
            $fileVersionsQuery->where('category', $request->category);
            $filesQuery->where('category', $request->category);
        }
    
        // Merge results and paginate
        $archivedFiles = $filesQuery->get();
        $archivedFileVersions = $fileVersionsQuery->get();
        $mergedResults = $archivedFiles->merge($archivedFileVersions)->sortByDesc('updated_at');
    
        // Paginate manually
        $perPage = 10;
        $currentPage = request()->input('page', 1);
        $paginatedResults = $mergedResults->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $fileVersions = new \Illuminate\Pagination\LengthAwarePaginator($paginatedResults, $mergedResults->count(), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    
        return view('staff.pages.StaffArchivedFiles', compact('fileVersions'));
    }
    

    
    public function StaffunarchiveFile($id)
    {
        // Check if the ID exists in file_versions first
        $fileVersion = FileVersions::where('version_id', $id)->first();
    
        if ($fileVersion) {
            // Update status in file_versions
            $fileVersion->update(['status' => 'active']);
    
            // Log unarchive event
            FileTimeStamp::create([
                'file_id' => $fileVersion->file_id,
                'version_id' => $fileVersion->version_id,
                'event_type' => 'File Version ID ' . $fileVersion->version_id . ' Unarchived',
                'timestamp' => now(),
            ]);
    
            return redirect()->back()->with('success', 'File version unarchived successfully!');
        }
    
        // If not found in file_versions, check in files (for original files)
        $originalFile = File::where('file_id', $id)->first() ?? 0;
    
        if ($originalFile) {
            // Update status in files (original file)
            $originalFile->update(['status' => 'active']);
    
            // Log unarchive event
            FileTimeStamp::create([
                'file_id' => $originalFile->file_id,
                'version_id' => null,
                'event_type' => 'File ID ' . $originalFile->id . ' Unarchived',
                'timestamp' => now(),
            ]);
    
            return redirect()->back()->with('success', 'Original file unarchived successfully!');
        }
    
        return redirect()->back()->with('error', 'File not found!');
    }
    

    public function CountActiveFiles()
    {
        $user = session('user'); // Get logged-in user from session
    
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }
    
        // Count active files uploaded by this user
        $activeFilesCount = File::where('uploaded_by', $user->id)
                                ->where('status', 'active')
                                ->count();
    
        // Count pending files uploaded by this user
        $pendingFilesCount = File::where('uploaded_by', $user->id)
                                 ->where('status', 'pending')
                                 ->count();
    
        // Calculate total storage used
        $uploadPath = storage_path('app/public/uploads'); // Absolute path
        $totalStorageUsed = $this->getFolderSize($uploadPath); // Get folder size
        $formattedStorage = $this->formatSizeUnits($totalStorageUsed); // Format size
    
        // Count recent uploads (within the last 24 hours)
        $recentUploadsCount = $this->countRecentUploads($uploadPath);
    
        return view('staff.pages.StaffDashboardPage', compact(
            'activeFilesCount', 
            'pendingFilesCount', 
            'formattedStorage',
            'recentUploadsCount'
        ));
    }
    
    /**
     * Get folder size in bytes.
     */
    private function getFolderSize($folder)
    {
        $size = 0;
        foreach (glob(rtrim($folder, '/') . '/*', GLOB_NOSORT) as $file) {
            $size += is_file($file) ? filesize($file) : $this->getFolderSize($file);
        }
        return $size;
    }
    
    /**
     * Convert bytes to human-readable format.
     */
    private function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' Bytes';
        }
    }
    
    /**
     * Count recent uploads based on file timestamps (last 24 hours).
     */
    private function countRecentUploads($folder)
    {
        $recentUploads = 0;
        $cutoffTime = Carbon::now()->subHours(24); // Get the time 24 hours ago
    
        foreach (glob(rtrim($folder, '/') . '/*') as $file) {
            if (is_file($file)) {
                $fileTime = Carbon::createFromTimestamp(filemtime($file)); // Get file modification time
                if ($fileTime->greaterThanOrEqualTo($cutoffTime)) {
                    $recentUploads++;
                }
            }
        }
    
        return $recentUploads;
    }
        
            
    public function StaffTrashViewFilesVersions(Request $request) 
    {
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get the logged-in user
    
        // Fetch only trashed file versions uploaded by the logged-in user
        $query = FileVersions::where('uploaded_by', $user->id);
    
        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where('filename', 'LIKE', '%' . $request->search . '%');
        }
    
        // Apply file type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $query->where('file_type', $request->file_type);
        }
    
        // Apply category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }
    
        // Get filtered results with pagination
        $fileVersions = $query->paginate(10);
    
        return view('staff.pages.StaffTrashBinFiles', compact('fileVersions')); // Pass data to view
    }

    public function StafRestoreFile($version_id)
    {
        // Find the file version
        $fileVersion = FileVersions::findOrFail($version_id);
    
        // Update status to 'active'
        $fileVersion->update(['status' => 'active']);
    
        // Insert into file_time_stamps with file_id in event_type
        FileTimeStamp::create([
            'file_id' => $fileVersion->file_id,
            'version_id' => $fileVersion->version_id,
            'event_type' => 'File ID ' . $fileVersion->file_id . ' Restored from Trash', // Log restore event
            'timestamp' => now(),
        ]);
    
        return redirect()->back()->with('success', 'File version restored successfully!');
    }
    

    public function StaffupdateFileVersion(Request $request, $version_id)
    {
        // Fetch file version by version_id
        $fileVersion = FileVersions::where('version_id', $version_id)->firstOrFail();
    
        // Validate input
        $request->validate([
            'filename' => 'required|string|max:255',
            'file_type' => 'required|string|max:10',
            'file' => 'nullable|file|max:5120', // Optional file upload, max 5MB
        ]);
    
        // Track changes
        $changesMade = false;
    
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
            $changesMade = true;
        }
    
        // Update other details
        if ($fileVersion->filename !== $request->filename) {
            $fileVersion->filename = $request->filename;
            $changesMade = true;
        }
    
        if ($changesMade) {
            $fileVersion->save();
    
            // Log the update in file_time_stamps
            FileTimeStamp::create([
                'file_id' => $fileVersion->file_id,
                'version_id' => $fileVersion->version_id,
                'event_type' => 'File ID ' . $fileVersion->file_id . ' Updated',
                'timestamp' => now(),
            ]);
        }
    
        return redirect()->route('staff.update')->with('success', 'File version updated successfully!');
    }
    

    public function checkFileRequests(Request $request)
    {
        $user = session('user'); // Get logged-in user from session

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get last processed file request timestamp from session
        $lastProcessedTime = session('last_file_request_time');

        // Fetch the latest approved file request that is newer than the last processed one
        $approvedRequest = FileRequest::where('requested_by', $user->id)
            ->where('request_status', 'approved')
            ->when($lastProcessedTime, function ($query) use ($lastProcessedTime) {
                return $query->where('updated_at', '>', $lastProcessedTime);
            })
            ->orderBy('updated_at', 'desc')
            ->first();

        // If no new approved request, stop polling
        if (!$approvedRequest) {
            return response()->json(['status' => 'pending']);
        }

        // Store the latest updated_at timestamp in session
        session(['last_file_request_time' => $approvedRequest->updated_at]);

        return response()->json([
            'status' => 'approved',
            'message' => "File ID {$approvedRequest->file_id} successfully accepted to storage. Please check your Active Files Section.",
        ]);
    }




}
