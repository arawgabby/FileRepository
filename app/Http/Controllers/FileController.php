<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\FileVersions;
use App\Models\Files;
use App\Models\FolderAccess;
use App\Models\AccessLog;
use App\Models\FileTimeStamp;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\FileRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\Folder;
use App\Models\User;


class FileController extends Controller
{

    public function AdminchangeStatusFile(Request $request, $file_id)
    {
        $request->validate([
            'new_status' => 'required|in:active,private',
        ]);

        $file = Files::find($file_id);

        if (!$file) {
            return back()->with('error', 'File not found.');
        }

        $file->status = $request->new_status;
        $file->save();

        return back()->with('success', 'File status updated successfully.');
    }



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

    public function AdminshowFolders(Request $request)
    {
        $basePath = $request->get('path', 'uploads');

        // Get folder paths from disk
        $directories = Storage::disk('public')->directories($basePath);

        // Map to folder names only
        $folderNames = array_map(function ($dir) use ($basePath) {
            return Str::after($dir, $basePath . '/');
        }, $directories);

        // Get folders from DB with status
        $dbFolders = Folder::where('path', 'like', $basePath . '/%')->get(['name', 'status']);

        // Map db folders into a key-value array: name => status
        $folderStatusMap = $dbFolders->pluck('status', 'name')->toArray();

        // Build final list of folders with status
        $folders = collect($folderNames)->map(function ($folderName) use ($folderStatusMap) {
            return (object)[
                'name' => $folderName,
                'status' => $folderStatusMap[$folderName] ?? 'unknown',
            ];
        });

        $parentPath = dirname($basePath);
        if ($parentPath === '.' || $basePath === '') {
            $parentPath = null;
        }

        return view('admin.pages.AdminFolders', compact('folders', 'basePath', 'parentPath'));
    }

    public function AdminViewRequests(Request $request)
    {
        // Paginate the requests, 6 per page, ordered by created_at in descending order
        $requests = FolderAccess::with(['folder', 'user'])
            ->orderBy('created_at', 'desc') // Order by created_at in descending order
            ->paginate(6);

        return view('admin.pages.AdminViewRequests', compact('requests'));
    }


    public function AdminViewRequestsFile(Request $request)
    {
        // Paginate the requests, 10 per page (you can adjust the number as needed)
        $requests = FileRequest::with(['user', 'file'])->orderBy('created_at', 'desc')->paginate(6);

        return view('admin.pages.AdminViewRequestsFiles', compact('requests'));
    }


    public function updateStatusFile(Request $request)
    {
        // Validate the input
        $validated = $request->validate([
            'request_id' => 'required|exists:file_requests,request_id',
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            // Get the session user
            $user = auth()->user();

            // Find the request by ID
            $fileRequest = FileRequest::findOrFail($validated['request_id']);

            // Only allow the file owner to update the status
            if ($fileRequest->file->uploaded_by !== $user->id) {
                return back()->with('error', 'You are not authorized to update this request.');
            }

            // Update status and processed_by
            $fileRequest->request_status = $validated['status'];
            $fileRequest->processed_by = $user->id ?? null; // Use ID from session user

            $fileRequest->save();

            return back()->with('success', 'Request status updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while updating the request status.');
        }
    }


    public function updateFolderAccessStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Restricted',
        ]);

        $access = FolderAccess::find($id);

        if (!$access) {
            return redirect()->back()->with('error', 'Request not found.')->withInput();
        }

        $access->status = $request->status;
        $access->assigned_by = auth()->id();
        $access->save();

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

    public function AdminuploadFile(Request $request)
    {
        Log::info('Admin upload file request', [
            'user_id' => auth()->id(),
            'selected_folder' => $request->input('folder'),
            'request_data' => $request->all()
        ]);
        $request->validate([
            'file' => 'required|file|max:502400',
            'category' => 'required|in:capstone,thesis,faculty_request,accreditation,admin_docs,custom_location',
            'published_by' => 'required|string|max:255',
            'year_published' => 'required|string|regex:/^\d{4}$/',
            'description' => 'nullable|string|max:1000',
            'folder' => 'nullable|string|max:255',
            // Accreditation fields validation
            'level' => 'required_if:category,accreditation|max:255',
            'area' => 'required_if:category,accreditation|max:255',
            'parameter' => 'required_if:category,accreditation|max:255',
            'subparam' => 'required_if:category,accreditation|max:255',
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
                        'status' => 'private',
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
                if ($folder && $folder !== $category && !\App\Models\Folder::where('path', $uploadPath)->where('name', $folder)->exists()) {
                    \App\Models\Folder::create([
                        'name' => $folder,
                        'path' => $uploadPath,
                        'status' => 'private', // or your default
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
                'category' => $category, // This is either selected_folder or category
                'published_by' => $request->published_by,
                'year_published' => (string) $request->year_published,
                'description' => $request->description ?? null,
                'status' => 'active',
            ];

            if (in_array($request->input('category'), ['capstone', 'thesis'])) {
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
            if ($request->filled('subparam')) {
                $fileData['subparam'] = $request->input('subparam');
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

    public function AdmindeleteFolder(Request $request)
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


            // Access log database entry
            AccessLog::create([
                'file_id' => 0, // No file ID since it's a folder
                'accessed_by' => $user->id,
                'action' => "Deleted subfolder '{$request->folderName}' under '{$request->basePath}' - Successful",
                'access_time' => now(),
            ]);

            // Laravel log
            Log::info("User ID {$user->id} successfully deleted folder: {$fullPath}");

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Failed to delete folder: {$fullPath} - Error: " . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to delete folder.']);
        }
    }

    public function AdmincreateFolder(Request $request)
    {
        $request->validate([
            'folderName' => 'required|string',
            'basePath' => 'nullable|string',
            'status' => 'nullable|in:public,private',
            'password' => 'nullable|string'
        ]);

        $basePath = $request->input('basePath', 'uploads');
        $folderName = $request->input('folderName');
        $newPath = $basePath . '/' . $folderName;
        $status = $request->input('status', 'private');
        $password = $request->input('password');

        if (Storage::disk('public')->exists($newPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Folder already exists.'
            ]);
        }

        try {
            // Create the new folder
            Storage::disk('public')->makeDirectory($newPath);

            // Retrieve user from session
            $user = auth()->user();


            // Insert into folders table
            Folder::create([
                'name' => $folderName,
                'path' => $newPath,
                'status' => $status,
                'password' => $password ? bcrypt($password) : null,
                'user_id' => $user->id
            ]);

            // Log to access logs
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

    public function setFolderStatus(Request $request)
    {
        $request->validate([
            'folderName' => 'required|string',
            'basePath' => 'required|string',
            'action' => 'required|string|in:public,private',
        ]);

        Log::info('Setting folder status', [
            'folderName' => $request->folderName,
            'basePath' => $request->basePath,
            'action' => $request->action
        ]);

        try {
            $folder = Folder::where('path', $request->basePath . '/' . $request->folderName)->first();

            if (!$folder) {
                Log::warning('Folder not found', [
                    'folderName' => $request->folderName,
                    'basePath' => $request->basePath
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Folder not found.'
                ]);
            }

            $folder->status = $request->action;
            $folder->save();

            Log::info('Folder status updated', [
                'folderName' => $request->folderName,
                'basePath' => $request->basePath,
                'status' => $folder->status
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error setting folder status', [
                'error' => $e->getMessage(),
                'folderName' => $request->folderName,
                'basePath' => $request->basePath
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function AdminactiveFiles(Request $request)
    {
        $files = Files::query();

        if ($request->has('search') && !empty($request->search)) {
            $files->where('filename', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('file_type') && !empty($request->file_type)) {
            $files->where('file_type', $request->file_type);
        }

        if ($request->has('subfolder') && !empty($request->subfolder)) {
            $files->where('file_path', 'LIKE', 'uploads/' . $request->subfolder . '/%');
        }

        $files = $files->paginate(20)->appends(['subfolder' => $request->subfolder])->sortByDesc('created_at');

        $fileVersions = FileVersions::whereIn('file_id', $files->pluck('file_id'))->get();

        // 🔥 Get subfolders from uploads directory
        $uploadPath = public_path('storage/uploads');
        $subfolders = [];

        if (File::exists($uploadPath)) {
            $subfolders = collect(File::directories($uploadPath))->map(function ($path) {
                return basename($path); // Just get the folder name
            });
        }

        return view('admin.pages.AdminViewAllFilesActive', compact('fileVersions', 'files', 'subfolders'));
    }

    public function AdmindownloadFile($filePath)
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

    public function AdmineditPrimaryFile($file_id)
    {
        // Fetch the file using the provided ID
        $file = Files::findOrFail($file_id);

        return view('admin.pages.AdminEditPrimaryFile', compact('file'));
    }

    public function AdminActiveFileArchived($file_id)
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

    public function AdminupdatePrimaryFile(Request $request, $file_id)
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

        // Check if a new file is uploaded
        if ($request->hasFile('file')) {
            $uploadedFile = $request->file('file');
            $newFileName = $uploadedFile->getClientOriginalName();
            $filePath = $uploadedFile->storeAs('uploads/primaryFiles', $newFileName, 'public');

            // Delete old file if it exists
            if ($file->file_path) {
                Storage::disk('public')->delete($file->file_path);
            }

            $file->file_path = $filePath;
            $file->file_size = $uploadedFile->getSize();
        } else {
            // Rename the existing file
            $oldFilePath = $file->file_path;

            if ($oldFilePath && str_starts_with($oldFilePath, 'uploads/')) {
                $directory = dirname($oldFilePath);
                $oldExtension = pathinfo($oldFilePath, PATHINFO_EXTENSION);
                $newFileName = pathinfo($request->filename, PATHINFO_FILENAME) . '.' . $oldExtension;
                $newFilePath = $directory . '/' . $newFileName;

                Storage::disk('public')->move($oldFilePath, $newFilePath);
                $file->file_path = $newFilePath;
            }
        }

        // Update file details
        $file->filename = pathinfo($request->filename, PATHINFO_FILENAME);
        $file->category = $request->category;
        $file->year_published = $request->year_published;
        $file->published_by = $request->published_by;
        $file->description = $request->description;
        $file->status = $request->status;
        $file->save();

        return redirect()->route('admin.active.files', $file_id)->with('success', 'File updated successfully!');
    }

    public function TrashActiveFile(Request $request, $file_id)
    {
        $file = Files::find($file_id);

        if (!$file) {
            return redirect()->back()->with('error', 'File not found.');
        }

        try {
            $file->status = 'deleted';
            $file->save();

            return redirect()->back()->with('success', 'File successfully marked as trashed.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while deleting the file.');
        }
    }


    public function AdminArchivedViewFilesVersions(Request $request)
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

        return view('admin.pages.adminArchivedFiles', compact('fileVersions'));
    }

    public function AdminunarchiveFile($id)
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

    public function AdminCountActiveFiles(Request $request)
    {
        // ========== FILE COUNTS ==========
        $activeFilesCount = Files::where('status', 'active')->count();
        $pendingFilesCount = Files::where('status', 'pending')->count();

        $filter = $request->get('filter', 'all');
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
            default:
                $recentUploadsCount = Files::count();
                break;
        }

        $uploadPath = storage_path('app/public/uploads');
        $totalStorageUsed = $this->getFolderSize($uploadPath);
        $formattedStorage = $this->formatSizeUnits($totalStorageUsed);
        $recentFiles = Files::orderBy('updated_at', 'desc')->limit(10)->get();

        // ========== USER COUNTS ==========
        $totalUsers = User::count();

        $usersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $usersToday = User::whereDate('created_at', today())->count();

        $activeUsers = User::where('status', 'active')->count();

        return view('admin.pages.adminDashboardPage', compact(
            'activeFilesCount',
            'pendingFilesCount',
            'recentUploadsCount',
            'formattedStorage',
            'recentFiles',
            'filter',

            // Pass user stats to the view
            'totalUsers',
            'usersThisMonth',
            'usersToday',
            'activeUsers'
        ));
    }

    private function getFolderSize($folder)
    {
        $size = 0;
        foreach (glob(rtrim($folder, '/') . '/*', GLOB_NOSORT) as $file) {
            $size += is_file($file) ? filesize($file) : $this->getFolderSize($file);
        }
        return $size;
    }

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

    public function AdminTrashViewFilesVersions(Request $request)
    {
        $query = Files::where('status', 'deleted'); // Only get deleted files

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

        // Fetch filtered results
        $fileVersions = $query->with('user')->paginate(10); // Include user relationship

        return view('admin.pages.AdminTrashBinFiles', compact('fileVersions'));
    }


    public function AdminRestoreFile($file_id)
    {
        // Find the file from the files table
        $file = Files::findOrFail($file_id);

        // Update status to 'active'
        $file->update(['status' => 'active']);

        // Log event to file_time_stamps
        FileTimeStamp::create([
            'file_id' => $file->file_id,
            'version_id' => null, // or remove this if version_id is nullable
            'event_type' => 'File ID ' . $file->file_id . ' Restored from Trash',
            'timestamp' => now(),
        ]);

        return redirect()->back()->with('success', 'File restored successfully!');
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
        $file = Files::where('file_id', $id)->first();

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
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('admin.upload')->with('error', 'Unauthorized: Please log in.');
        }

        $file = Files::findOrFail($file_id);

        if ($file->status === 'archived') {
            return redirect()->back()->with('error', 'This file is already archived.');
        }

        if (!$user->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized: You do not have permission.');
        }

        $file->update(['status' => 'archived']);

        return redirect()->back()->with('success', 'File archived successfully!');
    }

    public function editPrimaryFile($file_id)
    {
        // Fetch the file using the provided ID
        $file = Files::findOrFail($file_id);

        return view('admin.pages.EditPrimaryFile', compact('file'));
    }


    public function updatePrimaryFile(Request $request, $file_id)
    {
        $file = Files::findOrFail($file_id);

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
