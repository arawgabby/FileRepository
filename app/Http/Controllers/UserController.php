<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        // Paginate users (10 per page) and order by created_at in descending order
        $users = User::orderBy('created_at', 'desc')->paginate(8);

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
            'contact_number' => 'required|string|max:15', // Validate contact number
            'role' => 'required|in:staff,faculty', // Validate role
        ]);

        try {
            // Get the role_id from the roles table
            $role = \App\Models\Role::where('name', $request->role)->firstOrFail();

            // Insert into the database
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Encrypt password
                'role_id' => $role->id, // Use role_id from roles table
                'status' => $request->status,
                'contact_number' => $request->contact_number, // Store contact number
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

        // Get the role_id from the roles table
        $role = \App\Models\Role::where('name', $request->role)->firstOrFail();
        $user->role_id = $role->id; // Save the role_id update

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }
}
