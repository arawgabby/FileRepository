@extends('admin.dashboard.adminDashboard')
@section('content')

<div class="container mx-auto p-6 bg-white shadow-md">
    <a href="{{ route('admin.users') }}" class="text-gray-600 hover:text-gray-800 flex items-center mb-4">
        <i class="fas fa-arrow-left mr-2" style="font-size: 34px; font-weight: bold"></i>
    </a>

    <h1 class="text-3xl font-bold mb-4">Add New User</h1>

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

    @if ($errors->any())
    <div class="mb-4 text-red-600">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="addUserForm" action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Name</label>
            <input type="text" name="name" class="w-full p-2 border rounded-lg" required value="{{ old('name') }}">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" class="w-full p-2 border rounded-lg" required value="{{ old('email') }}">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Password</label>
            <input type="password" name="password" class="w-full p-2 border rounded-lg" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Contact Number</label>
            <input type="text" name="contact_number" class="w-full p-2 border rounded-lg" required value="{{ old('contact_number') }}">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Role</label>
            <select name="role" class="w-full p-2 border rounded-lg" required>
                <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Status</label>
            <select name="status" class="w-full p-2 border rounded-lg" required>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="deactivated" {{ old('status') == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
            </select>
        </div>

        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg">
            Submit
        </button>
    </form>
</div>

<script>
    document.getElementById("addUserForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent immediate submission

        if (confirm("Are you sure you want to add this user?")) {
            this.submit(); // Proceed with form submission
        }
    });
</script>

@endsection