<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\File;


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

    public function logout()
    {
        session()->forget('user'); // Clear the user session
        return redirect('/admin-login')->with('success', 'You have been logged out successfully.');
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
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads', $filename, 'public');
    
            File::create([
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
        $query = File::query();

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
        $files = $query->paginate(10); // Pagination for better UI

        return view('admin.pages.ViewAllFiles', compact('files'));
    }

    






}
