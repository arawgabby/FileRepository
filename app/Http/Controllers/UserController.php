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
            'email' => 'required|email|unique:users,email', // This already checks if email exists
            'password' => 'required|min:6',
            'status' => 'required|in:active,inactive,pending,deactivated',
            'contact_number' => 'required|string|max:15',
            'role' => 'required|in:staff,faculty',
        ], [
            'email.unique' => 'The email address is already registered.', // Custom error message
        ]);

        try {
            // Get the role_id from the roles table
            $role = \App\Models\Role::where('name', $request->role)->firstOrFail();

            // Insert into the database
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $role->id,
                'status' => $request->status,
                'contact_number' => $request->contact_number,
            ]);

            return redirect()->route('admin.users')->with('success', 'User added successfully!');
        } catch (\Exception $e) {
            \Log::error('User creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['email' => 'Failed to add user. ' . ($e->getCode() == 23000 ? 'The email address is already registered.' : 'Please try again.')]);
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
