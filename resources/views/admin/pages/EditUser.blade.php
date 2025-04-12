@extends('admin.dashboard.adminDashboard')

@section('content')

<div class="container mx-auto p-6 bg-white  shadow-md">
    <a href="{{ route('admin.users') }}" class="text-gray-600 hover:text-gray-800 flex items-center mb-4">
       <i class="fas fa-arrow-left mr-2" style="font-size: 34px; font-weight: bold"></i>
    </a>

    <h1 class="text-3xl font-bold mb-4">Edit User</h1>

    @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif

    @if(session('error'))
        <script>
            alert("{{ session('error') }}");
        </script>
    @endif

    <form id="editUserForm" action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Name</label>
            <input type="text" name="name" value="{{ $user->name }}" class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" value="{{ $user->email }}" class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">New Password (Leave blank to keep current)</label>
            <input type="password" name="password" class="w-full p-2 border rounded-lg">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Role</label>
            <select name="role" class="w-full p-2 border rounded-lg">
                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ $user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="faculty" {{ $user->role == 'faculty' ? 'selected' : '' }}>Faculty</option>
                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
            </select>
        </div>


        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Status</label>
            <select name="status" class="w-full p-2 border rounded-lg">
                <option value="active" {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ $user->status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="deactivated" {{ $user->status == 'deactivated' ? 'selected' : '' }}>Deactivate</option>
            </select>
        </div>

        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
            Update
        </button>
    </form>
</div>

<script>
    document.getElementById("editUserForm").addEventListener("submit", function(event) {
        event.preventDefault();

        if (confirm("Are you sure you want to update this user?")) {
            this.submit();
        }
    });
</script>

@endsection
