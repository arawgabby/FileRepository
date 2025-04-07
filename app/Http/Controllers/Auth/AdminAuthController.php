<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AccessLog;
use App\Models\FileVersions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Files;
use Symfony\Component\HttpFoundation\Response;


class AdminAuthController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,user',
        ]);

        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            return redirect('/admin-signup')->with('success', 'Account created successfully!');
        } catch (\Exception $e) {
            return redirect('/admin-signup')->with('error', 'Something went wrong. Please try again.');
        }
    }   

    public function Staffstore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:staff,faculty',
        ]);
    
        try {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
    
            return redirect('/staff-login')->with('success', 'Account created successfully! You can now log in.');
        } catch (\Exception $e) {
            return redirect('/staff-signup')->with('error', 'Something went wrong. Please try again.');
        }
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find the user
        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Store user in session if you want
            session(['user' => $user]);

            return redirect()->route('admin.dashboard')->with('success', 'Login successful!');
        } else {
            return redirect('/admin-login')->with('error', 'Invalid email or password. Please try again.');
        }
    }

    public function Stafflogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find the user
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($credentials['password'], $user->password)) {

            // Check if the user's role is either 'faculty' or 'staff'
            if ($user->role === 'faculty' || $user->role === 'staff') {
                // Store user in session if you want
                session(['user' => $user]);

                return redirect()->route('staff.dashboard')->with('success', 'Login successful!');
            } else {
                return redirect('/staff-login')->with('error', 'Access denied. Only faculty or staff can log in.');
            }
        } else {
            return redirect('/staff-login')->with('error', 'Invalid email or password. Please try again.');
        }
    }


    public function logout()
    {
        session()->forget('user'); // Clear the user session
        return redirect('/admin-login')->with('success', 'You have been logged out successfully.');
    }

    public function Stafflogout()
    {
        session()->flush(); // Clear all session data
        return redirect('/staff-login')->with('success', 'You have been logged out successfully.');
    }


    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:502400', 
            'category' => 'required|in:capstone,thesis,faculty_request,accreditation,admin_docs',
        ]);
    
        // Ensure the user is logged in via session
        if (!session()->has('user')) {
            return redirect()->route('admin.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get user data from session
    
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $filename, 'public');
    
            Files::create([
                'filename' => $filename,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'file_type' => $file->getClientOriginalExtension(),
                'uploaded_by' => $user->id, // Use session user ID
                'category' => $request->category,
            ]);
    
            return redirect()->route('admin.upload')->with('success', 'File uploaded successfully!');
        }
    
        return redirect()->route('admin.upload')->with('error', 'File upload failed.');
    }

    public function viewFiles(Request $request)
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

        return view('admin.pages.ViewAllFiles', compact('fileVersions', 'files'));
    }

    public function ViewFilesVersions(Request $request) 
    {
        $query = FileVersions::query(); // Make sure you're fetching from FileVersion model

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
        $fileVersions = $query->paginate(10); // Fetch from file_versions table

        return view('admin.pages.EditFilesOverview', compact('fileVersions')); // Pass $fileVersions to view
    }

    public function ArchivedViewFilesVersions(Request $request) 
    {
        $query = FileVersions::query(); // Make sure you're fetching from FileVersion model

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
        $fileVersions = $query->paginate(10); // Fetch from file_versions table

        return view('admin.pages.ArchivedFiles', compact('fileVersions')); // Pass $fileVersions to view
    }


    public function TrashViewFilesVersions(Request $request) 
    {
        $query = FileVersions::query(); // Make sure you're fetching from FileVersion model

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
        $fileVersions = $query->paginate(10); // Fetch from file_versions table

        return view('admin.pages.TrashBinFiles', compact('fileVersions')); // Pass $fileVersions to view
    }

    public function editFile($file_id)
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

        return view('admin.pages.EditFilesView', compact('file'));
    }


    
    public function updateFile(Request $request, $file_id)
    {
        if (!session()->has('user')) {
            return redirect()->route('admin.upload')->with('error', 'Unauthorized: Please log in.');
        }
    
        $user = session('user'); // Get user from session
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
            'uploaded_by' => $user->id ?? null, // Use session user ID
        ]);
    
        // Log the file update in `access_logs`
        AccessLog::create([
            'file_id' => $file->file_id,
            'accessed_by' => $user->id ?? null, // Ensure user is logged in
            'action' => 'Added File - Version ' . $newVersion,
            'access_time' => now()
        ]);
    
        return redirect()->route('admin.editFile', $file_id)->with('success', 'New file version saved!');
    }
    

    
  


}
