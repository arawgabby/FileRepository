<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Fetch all users
        return view('admin.pages.UsersView', compact('users'));
    }

    public function AddUserViewBlade()
    {
        return view('admin.pages.AddUser'); // Ensure this blade file exists
    }



    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'status' => 'required|in:active,inactive,pending,deactivated',
        ]);

        try {
            // Insert into the database
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Encrypt password
                'role' => 'user', // Default role
                'status' => $request->status,
            ]);

            // Success message
            return redirect()->route('admin.users')->with('success', 'User added successfully!');
        } catch (\Exception $e) {
            // Error message
            return redirect()->back()->with('error', 'Failed to add user. Please try again.');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id); // Find user by ID or return 404
        return view('admin.pages.EditUser', compact('user')); // Pass user data to view
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'status' => 'required|in:active,inactive,pending,deactivated',
            'role' => 'required|in:admin,staff,faculty,user', // Validate role input
        ]);
    
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->status = $request->status;
        $user->role = $request->role; // Save the role update
    
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        $user->save();
    
        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }
    



}
