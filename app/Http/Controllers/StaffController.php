<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AccessLog;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;
use App\Models\FileVersions;
use App\Models\FileTimeStamp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\FileRequest;
use App\Models\Folder;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\FolderAccess;
use Illuminate\Support\Facades\Response;



class StaffController extends Controller
{

    public function showFolders(Request $request)
    {
        $basePath = $request->get('path', 'uploads'); // Default to 'uploads'

        $directories = Storage::disk('public')->directories($basePath);

        $folderNames = array_map(function ($dir) use ($basePath) {
            return Str::after($dir, $basePath . '/');
        }, $directories);

        // Determine parent path for "Back" navigation
        $parentPath = dirname($basePath);
        if ($parentPath === '.' || $basePath === '') {
            $parentPath = null;
        }

        return view('staff.pages.Folders', compact('folderNames', 'basePath', 'parentPath'));
    }

    public function submitFolderAccess(Request $request)
    {
        $request->validate([
            'folder_id' => 'required|integer|exists:folders,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $user_id = auth()->id();

        $existingRequest = FolderAccess::where('folder_id', $request->folder_id)
            ->where('user_id', $user_id)
            ->first();

        if ($existingRequest) {
            return redirect()->route('request.folder.access')
                ->with('duplicate', true);
        }

        FolderAccess::create([
            'folder_id' => $request->folder_id,
            'user_id' => $user_id,
            'status' => 'Waiting Approval',
            'note' => $request->note,
        ]);

        return redirect()->route('request.folder.access')->with('success', 'Folder access request submitted successfully.');
    }

    public function showRequestFolder()
    {
        $user = auth()->user();

        $folders = Folder::all();

        $folderAccesses = FolderAccess::with('folder')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('staff.pages.StaffRequestView', compact('folders', 'folderAccesses'));
    }


    public function showRequestFile()
    {
        $userId = auth()->id();

        // Get all private folder paths
        $privateFolderPaths = \App\Models\Folder::where('status', 'private')->pluck('path')->toArray();

        // Only show files that are in folders with status 'private'
        $files = \App\Models\Files::where(function ($query) use ($privateFolderPaths) {
            foreach ($privateFolderPaths as $path) {
                $query->orWhere('file_path', 'like', $path . '/%');
            }
        })->get();

        $requests = \App\Models\FileRequest::with('file')
            ->where('requested_by', $userId)
            ->latest()
            ->get();

        $myFileRequests = \App\Models\FileRequest::with('file', 'requester')
            ->whereHas('file', function ($q) use ($userId) {
                $q->where('uploaded_by', $userId);
            })
            ->latest()
            ->get();

        return view('staff.pages.RequestFile', compact('files', 'requests', 'myFileRequests'));
    }

    public function updateFileRequestStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approved,rejected',
        ]);

        $fileRequest = FileRequest::findOrFail($id);
        $fileRequest->request_status = $request->action;
        $fileRequest->save();

        return redirect()->back()->with('success', 'Request status updated!');
    }

    public function submitFileRequests(Request $request)
    {
        $exists = FileRequest::where('requested_by', auth()->id())
            ->where('file_id', $request->file_id)
            ->first();

        if ($exists) {
            return redirect()->back()->with('duplicate', 'You have already requested access to this file.');
        }

        try {
            FileRequest::create([
                'file_id' => $request->file_id,
                'requested_by' => auth()->id(),
                'note' => $request->note,
                'request_status' => 'Pending',
            ]);

            return redirect()->back()->with('success', 'File access request submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to submit request.');
        }
    }




    public function deleteFolder(Request $request)
    {
        $request->validate([
            'folderName' => 'required|string',
            'basePath' => 'required|string',
        ]);

        $fullPath = $request->basePath . '/' . $request->folderName;

        if (!Storage::disk('public')->exists($fullPath)) {
            Log::warning("Attempted to delete non-existent folder: {$fullPath} by user ID " . Auth::id());

            return response()->json(['success' => false, 'message' => 'Folder does not exist.']);
        }

        try {
            Storage::disk('public')->deleteDirectory($fullPath);

            $user = auth()->user();

            AccessLog::create([
                'file_id' => 0,
                'accessed_by' => $user->id,
                'action' => "Deleted subfolder '{$request->folderName}' under '{$request->basePath}' - Successful",
                'access_time' => now(),
            ]);

            Log::info("User ID {$user->id} successfully deleted folder: {$fullPath}");

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to delete folder: {$fullPath} - Error: " . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete folder.']);
        }
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'folderName' => 'required|string',
            'basePath' => 'nullable|string'
        ]);

        $basePath = $request->input('basePath', 'uploads');
        $folderName = $request->input('folderName');
        $newPath = $basePath . '/' . $folderName;

        if (Storage::disk('public')->exists($newPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Folder already exists.'
            ]);
        }

        try {
            Storage::disk('public')->makeDirectory($newPath);

            $user = auth()->user();

            AccessLog::create([
                'file_id' => 0,
                'accessed_by' => $user->id,
                'action' => "Created folder '{$folderName}' under '{$basePath}' - Successful",
                'access_time' => now(),
            ]);

            Log::info("User ID {$user->id} successfully created folder: {$newPath}");

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to create folder: {$newPath} - Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
            ]);
        }
    }


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
            'category' => 'required|in:capstone,thesis,faculty_request,accreditation,admin_docs,custom_location',
            'published_by' => 'required|string|max:255',
            'year_published' => 'required|string|regex:/^\d{4}$/',
            'description' => 'nullable|string|max:1000',
            'folder' => 'nullable|string|max:255',
            'level' => 'required_if:category,accreditation|max:255',
            'area' => 'required_if:category,accreditation|max:255',
            'parameter' => 'required_if:category,accreditation|max:255',
            'character' => 'required_if:category,accreditation|max:255',
            'authors' => 'nullable|required_if:category,capstone,thesis|string|max:500',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized: Please log in.'], 403);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            // Use selected_folder as custom category if set, else use category
            $selected_folder = trim($request->input('folder'));
            $category = ($selected_folder !== '' && $selected_folder !== null) ? $selected_folder : trim($request->input('category'));

            if ($request->input('category') === 'accreditation') {
                $level = trim($request->input('level'));
                $area = trim($request->input('area'));
                $parameter = trim($request->input('parameter'));
                $character = trim($request->input('character'));

                $mergedLevel = 'Level-' . $level;
                $mergedArea = 'Area-' . $area;
                $mergedParameterChar = $parameter . '-' . $character;

                // Build the full path
                $basePath = 'uploads/' . $category;
                $levelPath = $basePath . '/' . $mergedLevel;
                $areaPath = $levelPath . '/' . $mergedArea;
                $parameterCharPath = $areaPath . '/' . $mergedParameterChar;

                // Create directories if not exist
                foreach ([$basePath, $levelPath, $areaPath, $parameterCharPath] as $path) {
                    if (!Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path, 0775, true);
                    }
                }

                // Register each folder level if not exists
                $folderPaths = [
                    ['name' => $category, 'path' => $basePath],
                    ['name' => $mergedLevel, 'path' => $levelPath],
                    ['name' => $mergedArea, 'path' => $areaPath],
                    ['name' => $mergedParameterChar, 'path' => $parameterCharPath],
                ];

                foreach ($folderPaths as $folderInfo) {
                    if (!\App\Models\Folder::where('path', $folderInfo['path'])->where('name', $folderInfo['name'])->exists()) {
                        \App\Models\Folder::create([
                            'name' => $folderInfo['name'],
                            'path' => $folderInfo['path'],
                            'status' => 'public',
                            'user_id' => $user->id,
                        ]);
                    }
                }

                // $uploadPath is always the deepest
                $uploadPath = $parameterCharPath;
            } else {
                $folder = $selected_folder;
                $basePath = 'uploads/' . $category;

                // Always register the category as a folder if not exists
                if (!\App\Models\Folder::where('path', $basePath)->where('name', $category)->exists()) {
                    \App\Models\Folder::create([
                        'name' => $category,
                        'path' => $basePath,
                        'status' => 'public',
                        'user_id' => $user->id,
                    ]);
                }

                // Only append folder if it's set and different from category
                if ($folder && $folder !== $category) {
                    $uploadPath = $basePath . '/' . $folder;
                } else {
                    $uploadPath = $basePath;
                }

                if (!Storage::disk('public')->exists($uploadPath)) {
                    Storage::disk('public')->makeDirectory($uploadPath, 0775, true);
                }
                // Register in folders table if not exists
                if ($folder && !\App\Models\Folder::where('path', $uploadPath)->where('name', $folder)->exists()) {
                    \App\Models\Folder::create([
                        'name' => $folder,
                        'path' => $uploadPath,
                        'status' => 'public', // or your default
                        'user_id' => $user->id,
                    ]);
                }
            }

            if (!Storage::disk('public')->exists($uploadPath)) {
                Storage::disk('public')->makeDirectory($uploadPath, 0775, true);
            }

            $filePath = $file->storeAs($uploadPath, $filename, 'public');

            // Prepare data for file entry
            $fileData = [
                'filename' => $filename,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => $user->id,
                'category' => $category,
                'published_by' => $request->published_by,
                'year_published' => (string) $request->year_published,
                'description' => $request->description ?? null,
                'status' => 'active',
            ];

            if (in_array($category, ['capstone', 'thesis'])) {
                $fileData['authors'] = $request->input('authors');
            }

            if ($request->filled('level')) {
                $fileData['level'] = $request->input('level');
            }
            if ($request->filled('area')) {
                $fileData['area'] = $request->input('area');
            }
            if ($request->filled('parameter')) {
                $fileData['parameter'] = $request->input('parameter');
            }
            if ($request->filled('character')) {
                $fileData['character'] = $request->input('character');
            }

            $fileEntry = Files::create($fileData);

            if ($fileEntry) {
                AccessLog::create([
                    'file_id' => $fileEntry->id ?? 0,
                    'accessed_by' => $user->id,
                    'action' => 'Uploaded file - Successful',
                    'access_time' => now(),
                ]);

                return response()->json(['message' => 'File uploaded successfully and marked as active!'], 200);
            }

            return response()->json(['message' => 'File upload failed.'], 500);
        }

        return response()->json(['message' => 'No file detected.'], 400);
    }


    public function ActiveFileArchived($file_id)
    {
        // Find the file
        $file = Files::find($file_id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found');
        }

        // Update the status to archived
        $file->status = 'archived';
        $file->save();

        // Insert into file_time_stamps to log the event
        FileTimeStamp::create([
            'file_id' => $file->file_id,
            'event_type' => 'File ID ' . $file->id . ' Archived', // Log archive event
            'timestamp' => now(),
        ]);

        return redirect()->back()->with('success', 'File successfully archived');
    }




    public function StaffviewLogs()
    {
        // Fetch all access logs with pagination
        $accessLogs = AccessLog::with(['user', 'file']) // Load related user and file
            ->latest()
            ->paginate(12); // Set pagination to 15 per page

        return view('staff.pages.StaffLogsView', compact('accessLogs'));
    }



    public function StaffviewFiles(Request $request)
    {
        // Use Laravel Auth, no session check
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

        // Fetch primary files uploaded by the logged-in user
        $files = Files::where('uploaded_by', $user->id);

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

        $files = $files->paginate(20); // Paginate results

        // Fetch file versions separately and link to files
        $fileVersions = FileVersions::whereIn('file_id', $files->pluck('file_id'))->get();

        return view('staff.pages.StaffViewAllFiles', compact('fileVersions', 'files'));
    }

    public function MyUploads(Request $request)
    {
        // Fetch primary files
        $files = Files::query();

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
        // Define the root folder where files are stored
        $basePath = storage_path('app/public/uploads');

        // Recursively get all files under 'uploads'
        $allFiles = File::allFiles($basePath);

        // Search for the file by name
        foreach ($allFiles as $file) {
            if ($file->getFilename() === $filePath) {
                return response()->download($file->getRealPath());
            }
        }

        // If not found, return back with error
        return back()->with('error', 'File not found.');
    }

    public function StaffmoveToTrash(Request $request, $id)
    {
        // Use Laravel Auth, no session check
        $user = auth()->user();
        if (!$user) {
            return redirect()->back()->with('error', 'Unauthorized: Please log in.');
        }

        // Find the file by file_id
        $file = Files::where('file_id', $id)->first();

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Update status to 'deleted'
        $file->update(['status' => 'deleted']);

        // ✅ Log the action in access_logs
        AccessLog::create([
            'file_id' => $file->file_id, // Ensure valid file_id
            'accessed_by' => $user->id,
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
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

        // Check if the file exists before proceeding
        $file = Files::find($file_id);
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
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

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
        $files = Files::query();
        $user = auth()->user();
        $userId = $user->id ?? null;
        $role = $user->role ?? null;

        // Get accreditation filters from request
        $category = $request->get('category');
        $level = $request->get('level');
        $area = $request->get('area');
        $parameter = $request->get('parameter');
        $subfolder = $request->get('subfolder');

        // Accreditation path filter
        if (
            $category === 'accreditation' &&
            !empty($level) &&
            !empty($area) &&
            !empty($parameter)
        ) {
            $accreditationPath = "uploads/accreditation/{$level}/{$area}/{$parameter}/%";
            $files->where('file_path', 'like', $accreditationPath);
        } elseif (!empty($subfolder)) {
            $files->where('file_path', 'LIKE', 'uploads/' . $subfolder . '/%');
        }

        // Only restrict if not admin
        if ($role !== 'admin') {
            // Get all folders with their status
            $allFolders = Folder::all(['name', 'status']);
            $publicFolders = $allFolders->where('status', 'public')->pluck('name')->toArray();
            $privateFolders = $allFolders->where('status', 'private')->pluck('name')->toArray();

            // Show all files in public folders
            $files->where(function ($query) use ($publicFolders, $userId, $privateFolders) {
                // Files in public folders
                if (!empty($publicFolders)) {
                    foreach ($publicFolders as $folder) {
                        $query->orWhere('file_path', 'like', 'uploads/' . $folder . '/%');
                    }
                }
                // Files in private folders: only show if user has approved file request or is uploader
                if (!empty($privateFolders)) {
                    foreach ($privateFolders as $folder) {
                        $query->orWhere(function ($subQ) use ($folder, $userId) {
                            $subQ->where('file_path', 'like', 'uploads/' . $folder . '/%')
                                ->where(function ($fileQ) use ($userId) {
                                    $fileQ->where('uploaded_by', $userId)
                                        ->orWhereIn('file_id', function ($subQ2) use ($userId) {
                                            $subQ2->select('file_id')
                                                ->from('file_requests')
                                                ->where('requested_by', $userId)
                                                ->where('request_status', 'approved');
                                        });
                                });
                        });
                    }
                }
            });
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $files->where('filename', 'LIKE', '%' . $request->search . '%');
        }

        // Apply file type filter
        if ($request->has('file_type') && !empty($request->file_type)) {
            $files->where('file_type', $request->file_type);
        }

        $files = $files->paginate(20)->appends($request->all());

        // Fetch all approved requests for the current user, eager load the file relation
        $approvedRequests = FileRequest::with('file')
            ->where('requested_by', $userId)
            ->where('request_status', 'approved')
            ->get();

        // Extract the files (filter out nulls in case of missing files)
        $grantedFiles = $approvedRequests->pluck('file')->filter();

        // Logging each approved request
        Log::info('Approved File Requests Summary', [
            'user_id' => $userId,
            'total_approved' => $approvedRequests->count(),
        ]);

        foreach ($approvedRequests as $requestObj) {
            Log::info('Approved Request Detail', [
                'request_id' => $requestObj->request_id ?? $requestObj->id,
                'file_id' => $requestObj->file_id,
                'requested_by' => $requestObj->requested_by,
                'request_status' => $requestObj->request_status,
                'created_at' => optional($requestObj->created_at)->toDateTimeString(),
            ]);
        }

        // Extract file IDs for use in the view
        $approvedFileRequests = $approvedRequests->pluck('file_id')->toArray();

        // Fix for paginator: get file_ids as array
        $fileIds = $files->getCollection()->pluck('file_id')->toArray();
        $fileVersions = FileVersions::whereIn('file_id', $fileIds)->get();

        // Subfolders logic (unchanged)
        $uploadPath = public_path('storage/uploads');
        $subfolders = [];
        $approvedFolderAccessIds = FolderAccess::where('user_id', $userId)
            ->where('status', 'approved')
            ->pluck('folder_id')
            ->toArray();

        if (\Illuminate\Support\Facades\File::exists($uploadPath)) {
            $allSubfolders = collect(\Illuminate\Support\Facades\File::directories($uploadPath))->map(function ($path) {
                return basename($path);
            })->toArray();

            $userFolders = Folder::where('user_id', $userId)->pluck('name')->toArray();
            $approvedFolders = Folder::whereIn('id', $approvedFolderAccessIds)->pluck('name')->toArray();
            $publicFolders = Folder::where('status', 'public')->pluck('name')->toArray();
            $privateFolders = Folder::where('status', 'private')->pluck('name')->toArray();
            $allowedFolders = array_unique(array_merge($userFolders, $approvedFolders, $publicFolders));
            $allowedFolders = array_diff($allowedFolders, $privateFolders);
            $subfolders = array_values(array_intersect($allSubfolders, $allowedFolders));
        }

        return view('staff.pages.StaffViewAllFilesActive', compact(
            'fileVersions',
            'files',
            'subfolders',
            'approvedFileRequests',
            'role',
            'grantedFiles'
        ));
    }

    public function StaffeditPrimaryFile(Request $request, $file_id)
    {
        // Fetch the file
        $file = Files::findOrFail($file_id);

        // Get subfolder path from query string
        $subfolder = $request->query('subfolder');

        // Get the corresponding folder from DB
        $folder = Folder::where('path', 'uploads/' . $subfolder)->first();

        if (!$folder) {
            return redirect()->back()->withErrors(['Folder not found.']);
        }

        return view('staff.pages.StaffEditPrimaryFile', compact('file', 'folder'));
    }

    public function StaffupdatePrimaryFile(Request $request, $file_id)
    {
        $file = Files::findOrFail($file_id);

        // Validate input
        $request->validate([
            'filename' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'year_published' => 'nullable|integer|min:1900|max:' . date('Y'),
            'published_by' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,inactive,pending,deactivated',
            'file' => 'nullable|file|max:5120', // Optional file upload, max 5MB
        ]);

        // Fetch the folder path from the database
        $folderPath = $request->input('folder_path');

        // Double-check that a folder record exists (optional but good practice)
        $folder = Folder::where('path', $folderPath)->first();
        if (!$folder) {
            return redirect()->back()->with('error', 'Subfolder not found!');
        }


        $folderPath = $folder->path; // e.g., 'uploads/wow'

        // Check if a new file is uploaded
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $newFileName = $uploadedFile->getClientOriginalName();

            // Define the new file path
            $newFilePath = $folderPath . '/' . $newFileName;

            // Delete the old file if it's a different file
            if ($file->file_path !== $newFilePath && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }

            // Save the new file (overwrite if same name)
            Storage::disk('public')->putFileAs($folderPath, $uploadedFile, $newFileName);

            // Update the file record
            $file->file_path = $newFilePath;
            $file->file_size = $uploadedFile->getSize();
        } else {
            // Rename the existing file while keeping it in the same folder
            $oldFilePath = $file->file_path;

            if ($oldFilePath && str_starts_with($oldFilePath, 'uploads/')) {
                $directory = dirname($oldFilePath); // e.g., 'uploads/wow'
                $oldExtension = pathinfo($oldFilePath, PATHINFO_EXTENSION);
                $newFileName = pathinfo($request->filename, PATHINFO_FILENAME) . '.' . $oldExtension;

                // Ensure the file stays in the same directory (subfolder)
                $newFilePath = $directory . '/' . $newFileName;

                Storage::disk('public')->move($oldFilePath, $newFilePath);
                $file->file_path = $newFilePath;
            }
        }

        // Update other file details
        $file->filename = pathinfo($request->filename, PATHINFO_FILENAME);
        $file->category = $request->category;
        $file->year_published = $request->year_published;
        $file->published_by = $request->published_by;
        $file->description = $request->description;
        $file->status = $request->status;
        $file->save();

        // Pass subfolder info in redirect
        return redirect()->route('staff.active.files', ['subfolder' => request('subfolder')])
            ->with('success', 'File updated successfully!');
    }


    public function StaffeditFile($file_id)
    {
        // Ensure $file_id is correctly received and cast to integer
        $file_id = (int) $file_id;

        // Check if the file exists
        $file = Files::findOrFail($file_id);

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
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

        $file = Files::findOrFail($file_id);

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
            'uploaded_by' => $user->id,
        ]);

        // Log the file update in `access_logs`
        AccessLog::create([
            'file_id' => $file->file_id,
            'accessed_by' => $user->id,
            'action' => 'Added File - Version ' . $newVersion,
            'access_time' => now()
        ]);

        return redirect()->route('staff.editFile', $file_id)->with('success', 'New file version saved!');
    }

    public function StaffViewFilesVersions(Request $request)
    {
        // Fetch all file versions
        $query = FileVersions::query();

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
        // Fetch all archived file versions
        $fileVersionsQuery = FileVersions::where('status', 'archived');

        // Fetch all archived files
        $filesQuery = Files::where('status', 'archived');

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
        $perPage = 6;
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
        $originalFile = Files::where('file_id', $id)->first() ?? 0;

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


    public function CountActiveFiles(Request $request)
    {
        // ✅ Count all active files
        $activeFilesCount = Files::where('status', 'active')->count();

        // ✅ Count all pending files
        $pendingFilesCount = Files::where('status', 'pending')->count();

        // ✅ Get filter type from request, default to 'all' (count all uploads)
        $filter = $request->get('filter', 'all');

        // ✅ Apply the filter for recent uploads count
        switch ($filter) {
            case 'daily':
                $recentUploadsCount = Files::whereDate('created_at', today())->count();
                break;
            case 'monthly':
                $recentUploadsCount = Files::whereMonth('created_at', now()->month)->count();
                break;
            case 'yearly':
                $recentUploadsCount = Files::whereYear('created_at', now()->year)->count();
                break;
            default: // 'all' or no filter
                $recentUploadsCount = Files::count();  // Count all uploads
                break;
        }

        // ✅ Get total storage used
        $uploadPath = storage_path('app/public/uploads'); // Absolute path
        $totalStorageUsed = $this->getFolderSize($uploadPath); // Get folder size
        $formattedStorage = $this->formatSizeUnits($totalStorageUsed); // Format size

        // ✅ Fetch recent file activities (latest updated files)
        $recentFiles = Files::orderBy('updated_at', 'desc')->limit(10)->get();

        // ✅ Return all necessary data to the view
        return view('staff.pages.StaffDashboardPage', compact(
            'activeFilesCount',
            'pendingFilesCount',
            'recentUploadsCount',
            'formattedStorage',
            'recentFiles',
            'filter' // Pass the filter to the view
        ));
    }

    public function AdminCountActiveFiles(Request $request)
    {
        // ✅ Count all active files
        $activeFilesCount = Files::where('status', 'active')->count();

        // ✅ Count all pending files
        $pendingFilesCount = Files::where('status', 'pending')->count();

        // ✅ Get filter type from request, default to 'all' (count all uploads)
        $filter = $request->get('filter', 'all');

        // ✅ Apply the filter for recent uploads count
        switch ($filter) {
            case 'daily':
                $recentUploadsCount = Files::whereDate('created_at', today())->count();
                break;
            case 'monthly':
                $recentUploadsCount = Files::whereMonth('created_at', now()->month)->count();
                break;
            case 'yearly':
                $recentUploadsCount = Files::whereYear('created_at', now()->year)->count();
                break;
            default: // 'all' or no filter
                $recentUploadsCount = Files::count();  // Count all uploads
                break;
        }

        // ✅ Get total storage used
        $uploadPath = storage_path('app/public/uploads'); // Absolute path
        $totalStorageUsed = $this->getFolderSize($uploadPath); // Get folder size
        $formattedStorage = $this->formatSizeUnits($totalStorageUsed); // Format size

        // ✅ Fetch recent file activities (latest updated files)
        $recentFiles = Files::orderBy('updated_at', 'desc')->limit(10)->get();

        // ✅ Return all necessary data to the view
        return view('admin.pages.adminDashboardPage', compact(
            'activeFilesCount',
            'pendingFilesCount',
            'recentUploadsCount',
            'formattedStorage',
            'recentFiles',
            'filter' // Pass the filter to the view
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
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('staff.upload')->with('error', 'Unauthorized: Please log in.');
        }

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
        $user = auth()->user();
        // Get logged-in user from session

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
