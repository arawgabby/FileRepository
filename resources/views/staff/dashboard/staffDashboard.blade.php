<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            zoom: 80%;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        
    <div id="sidebar" class="bg-gray-800 text-white w-72 space-y-6 py-7 px-4 transform -translate-x-full md:translate-x-0
     transition-transform duration-300 fixed top-0 bottom-0 z-40 overflow-y-auto">
     
    <div class="text-2xl font-bold">
                <img src="{{ asset('storage/csitlogo.jpg') }}" alt="CSIT Logo" class="w-25 h-25">
                <!-- <p><a href="#" class="text-white">Staff Panel</a></p>  -->
           </div>

            <div class="flex items-center bg-white text-white rounded-lg p-4 space-x-3 w-full">
                <div class="w-12 h-12 bg-gray-600 flex items-center justify-center rounded-full">
                    <i class="fas fa-user text-gray-300 text-2xl"></i>
                </div>
                <div class="flex flex-col">
                    <p class="text-black text-sm">ID: {{ session('user')->id }}</p>
                    <p class="text-lg font-semibold text-black">{{ session('user')->name }}</p>
                </div>
            </div>


            <nav class="space-y-6">

            <p class="text-white text-1xl font-bold">
                <i class="fas fa-dashboard"></i> Dashboard
            </p>

            <a href="#" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-upload mr-4"></i> Overview
            </a>


            <p class="text-white text-1xl font-bold">
                <i class="fas fa-folder"></i> Request File
            </p>

            <a href="{{ route('staff.upload') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-upload mr-4"></i> Upload New File
            </a>

            <a href="{{ route('staff.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-file-alt mr-4"></i>  My Uploads
            </a>

            <a href="{{ route('staff.pending.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-file-alt mr-4"></i> Pending File Request
            </a>

            <p class="text-white text-1xl font-bold">
                <i class="fas fa-folder-open"></i> Manage Files
            </p>

            <a href="{{ route('staff.active.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-file-alt mr-4"></i> My Active Files
            </a>

            <a href="{{ route('staff.update') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-history mr-4"></i> File Versions
            </a>

            <a href="{{ route('staff.archived.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-archive mr-4"></i> Archived Files
            </a>

            
            <a href="{{ route('staff.trash.bins') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-trash-alt mr-4"></i> Trash Files
            </a>

            <p class="text-white text-1xl font-bold mt-8">
                <i class="fas fa-file-text"></i> Activity Log
            </p>

            <a href="#" class="flex items-center text-gray-300 hover:text-white ml-4">
                <i class="fas fa-file mr-4"></i> Staff Logs
            </a>

            <a href="{{ url('/staff-logout') }}" class="flex items-center text-white hover:text-white mr-2" 
            style="font-weight: bold" onclick="return confirmLogout();">
            <i class="fas fa-sign-out mr-2"></i>
            Logout
            </a>

            <script>
                function confirmLogout() {
                    return confirm("Are you sure you want to log out?");
                }
            </script>


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
                    link.classList.remove(
                        "text-black", "bg-white", "shadow-md", "scale-105", 
                        "font-bold", "p-4", "rounded-lg"
                    );
                    link.classList.add("text-gray-300", "hover:text-white"); // Add hover effect back to non-active links
                });

                clickedLink.classList.add(
                    "text-black", "bg-white", "shadow-md", "scale-105", 
                    "font-bold", "p-4", "rounded-lg"
                );
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

