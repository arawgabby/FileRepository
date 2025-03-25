<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Open-Sans', sans-serif;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100 bg-contain bg-no-repeat bg-center"
      style="background-image: url('{{ asset('storage/uploads/loginbackground.png') }}');">

    <!-- Success/Error Alerts -->
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

    <div class="bg-white p-10 rounded-xl shadow-md backdrop-blur-md" style="width: 360px">
        <h2 class="text-2xl font-bold text-center mb-6">CSIT LOGIN</h2>

        <form action="{{ url('/staff-login') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="w-full bg-indigo-800 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition">
                    Login
                </button>
            </div>
        </form>

        <p class="mt-4 text-center text-sm text-gray-600">
            Don't have an account?
            <a href="{{ url('/staff-signup') }}" class="text-indigo-600 hover:underline">Sign up</a>
        </p>
        <p class="mt-4 text-center text-sm text-gray-600">
            Forgot Password?
            <a href="#" id="forgot-password" class="text-indigo-600 hover:underline">Click Here</a>
        </p>

        <script>
            document.getElementById("forgot-password").addEventListener("click", function (event) {
                event.preventDefault(); // Prevent default link action

                Swal.fire({
                    title: "Forgot Password?",
                    text: "Please contact your administrator at +639123456789 to change your password.",
                    icon: "info",
                    confirmButtonText: "OK"
                });
            });
        </script>


    </div>

</body>
</html>
