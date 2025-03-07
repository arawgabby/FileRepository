<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            zoom: 90%;
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
            <nav class="space-y-4">

            <p class="text-white text-1xl font-bold">Manage Files</p>

                <a href="{{ route('admin.upload') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                    Upload New File
                </a>

                <a href="{{ route('admin.files') }}" class="flex items-center text-gray-300 hover:text-white ml-4">
                    View All Files
                </a>

                <a href="#" class="flex items-center text-gray-300 hover:text-white ml-4">
                    Edit/Updated Files
                </a>

                <a href="#" class="flex items-center text-gray-300 hover:text-white ml-4">
                    Delete and Archive Files
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

