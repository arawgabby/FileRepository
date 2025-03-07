<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Lato', sans-serif;
            zoom: 80%;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        
        <div id="sidebar" class="bg-gray-800 text-white w-64 space-y-6 py-7 px-4 transform -translate-x-full md:translate-x-0 transition-transform duration-300 fixed top-0 bottom-0 z-40">
            <!-- @if(session()->has('user'))
                <p>Welcome, {{ session('user')->name }}!</p>
            @endif -->
            <div class="text-2xl font-bold">
            <!-- <img src="{{ asset('product-images/efvlogo.png') }}" alt="EFV Logo" class="w-25 h-25"> -->
            <p style="margin-top: 8px; text-align: center"><a href="#" class="text-white">Admin</a></p>
        </div>
            <nav class="space-y-6">

            <p class="text-white text-1xl font-bold">
                <i class="fas fa-folder-open"></i> Manage Files
            </p>

            <a href="{{ route('admin.upload') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-upload mr-4"></i> Upload New File
            </a>

            <a href="{{ route('admin.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-file-alt mr-4"></i> View Primary Files
            </a>

            <a href="{{ route('admin.update') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-history mr-4"></i> File Versions
            </a>

            <a href="{{ route('admin.archived.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-archive mr-4"></i> Archived Files
            </a>

            
            <a href="{{ route('admin.trash.bins') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-trash-alt mr-4"></i> Trash Files
            </a>

            <p class="text-white text-1xl font-bold mt-8">
                <i class="fas fa-folder-open"></i> User Management 
            </p>

            <a href="{{ route('admin.users') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-user mr-4"></i> Users
            </a>

            <p class="text-white text-1xl font-bold mt-8">
                <i class="fas fa-folder-open"></i> Activity Log
            </p>

            <a href="#" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-file mr-4"></i> Logs
            </a>




                <a href="{{ url('/admin-logout') }}" class="flex items-center text-white hover:text-white" style="font-weight: bold">Logout</a>

            </nav>
        </div>

        <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-50 hidden md:hidden" onclick="toggleSidebar()"></div>

        <div class="flex-1 flex flex-col ml-0 md:ml-64">
           

            <main class="p-8 sm: pt-7">
                @yield('content')
            </main>
        </div>
    </div>


    @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const navLinks = document.querySelectorAll("#sidebar nav a");

            // Function to update active link state
            function setActiveLink(clickedLink) {
                navLinks.forEach(link => {
                    link.classList.remove("text-black", "bg-white", "shadow-md", "scale-105", "font-bold", "p-4");
                    link.classList.add("text-gray-300", "hover:text-white"); // Add hover effect back to non-active links
                });

                clickedLink.classList.add("text-black", "bg-white", "shadow-md", "scale-105", "font-bold", "p-4");
                clickedLink.classList.remove("text-gray-300", "hover:text-white"); // Remove hover effect from active link

                // Store the active link in localStorage to persist highlight
                localStorage.setItem("activeNav", clickedLink.getAttribute("href"));
            }

            // Check if there is a stored active link in localStorage
            const storedActiveLink = localStorage.getItem("activeNav");
            if (storedActiveLink) {
                const activeElement = [...navLinks].find(link => link.getAttribute("href") === storedActiveLink);
                if (activeElement) {
                    setActiveLink(activeElement);
                }
            }

            // Add click event listener to each nav link
            navLinks.forEach(link => {
                link.addEventListener("click", function () {
                    setActiveLink(this);
                });
            });
        });
    </script>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            if (dropdown.classList.contains('hidden')) {
                dropdown.classList.remove('hidden', 'opacity-0', 'scale-95');
                dropdown.classList.add('opacity-100', 'scale-100');
            } else {
                dropdown.classList.add('opacity-0', 'scale-95');
                dropdown.classList.remove('opacity-100', 'scale-100');
                setTimeout(() => dropdown.classList.add('hidden'), 200);
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>

</body>
</html>

