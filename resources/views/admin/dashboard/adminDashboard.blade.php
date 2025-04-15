<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Lato', sans-serif;
            zoom: 80%;
        }
    </style>
</head>
<body class="bg-gray-50 bg-cover bg-center" 
      style="background: url('{{ asset('storage/uploads/bodybackground.png') }}') no-repeat center center fixed; 
             background-size: contain;">

    <div class="flex h-screen">
        
    <div id="sidebar" class="bg-gray-900 text-white w-66 space-y-6 py-8 px-6 transform -translate-x-full md:translate-x-0
    transition-transform duration-300 fixed top-0 bottom-0 z-40 overflow-y-auto">

    <div class="text-2xl font-bold flex justify-center">
            <img src="{{ asset('storage/csitlogo.jpg') }}" alt="CSIT Logo" class="w-25 h-25">
            <!-- <p><a href="#" class="text-white">Staff Panel</a></p>  -->
        </div>

        
            <!-- @if(session()->has('user'))
                <p>Welcome, {{ session('user')->name }}!</p>
            @endif -->

            <div class="text-2xl font-bold">
                <!-- <img src="{{ asset('product-images/efvlogo.png') }}" alt="EFV Logo" class="w-25 h-25"> -->
                <p style="margin-top: 4px; text-align: center"><a href="#" class="text-white">Admin</a></p>
            </div>

            <nav class="space-y-6">

            <p class="text-white text-sm font-bold">
                </i> Main
            </p>

            <a href="{{ route('admin.page.dashboard') }}" class="flex items-center text-white hover:text-white relative text-sm">
                <i class="fas fa-thumbtack mr-4"></i> Dashboard
            </a>


            <p class=" -m-5 mb-6 border-b border-white text-gray-200 pb-2">
            </p>

            


            <p class="text-white text-sm font-bold">
                 Files
            </p>

            
            <a href="{{ route('admin.folders') }}" class="flex items-center text-white hover:text-white text-sm ">
                <i class="fas fa-folder-open mr-4 "></i> Folders
            </a>

            <a href="{{ route('admin.uploadFiles') }}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-upload mr-4"></i> Upload
            </a>

            <a href="{{ route('admin.view.requests.file') }}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-share-from-square mr-4"></i> File Requests
            </a>

            <a href="{{ route('admin.view.requests') }}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-share-from-square mr-4"></i> Folder Requests
            </a>

            <a href="{{ route('admin.active.files') }}" class="flex items-center text-white hover:text-white mr-2 text-sm">
                <i class="fas fa-file-alt mr-5"></i> Files
            </a>

            <!-- <a href="#" class="flex items-center text-white hover:text-white ">
                <i class="fas fa-info mr-4"></i> View File Request
            </a> -->


            <!-- <a href="{{ route('admin.update') }}" class="flex items-center text-white hover:text-white ">
                <i class="fas fa-history mr-4"></i> File Versions
            </a> -->

            <a href="{{ route('admin.archived.files') }}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-archive mr-4"></i> Archived Files
            </a>

            
            <a href="{{ route('admin.trash.bins') }}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-trash-alt mr-4"></i> Trash 
            </a>

               <!-- <a href="{{ route('admin.trash.bins') }}" class="flex items-center text-white hover:text-white ">
                <i class="fas fa-trash-alt mr-4"></i> Trash Files
            </a> -->

            <p class=" -m-5 mb-6 border-b border-white text-gray-200 pb-2">
            </p>

            <p class="text-white text-sm font-bold mt-8 text-sm">
                 Accounts
            </p>
            

            <a href="{{ route('admin.users') }}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-user mr-4"></i> Users
            </a>

            
            <p class=" -m-5 mb-6 border-b border-white text-gray-200 pb-2">
            </p>

            <p class="text-white text-sm font-bold mt-8">
                Activity 
            </p>

            <a href="{{ route ('admin.logs.view')}}" class="flex items-center text-white hover:text-white text-sm">
                <i class="fas fa-file mr-4"></i> Activity Logs
            </a>

            <a href="{{ route ('admin.timestamps.index')}}" class="flex items-center text-white hover:text-white ">
                <i class="fas fa-list mr-3"></i> File Timestamps
            </a>

            <p class=" -m-5 mb-6 border-b border-white text-gray-200 pb-2">
            </p>

            <a href="{{ url('/admin-logout') }}" class="flex items-center text-white hover:text-white text-sm" 
            style="font-weight: bold" onclick="return confirmLogout();">
            <i class="fas fa-bookmark mr-4"></i>  Logout
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

            <div class="flex justify-between items-center">
                <!-- Page Title -->
                <h1 class="text-2xl font-bold text-gray-800"></h1>

                <div class="flex items-center space-x-6">
            
                    <!-- User Profile (Right End) -->
                    <div class="flex items-center bg-white p-2 space-x-3 shadow-md overflow-hidden">
                        <div class="w-12 h-12 bg-gray-600 flex items-center justify-center rounded-full">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-sm font-semibold text-black">{{ session('user')->name }}</p>
                            <p class="text-sm font-semibold text-black">
                                <span class="text-sm font-bold text-green-600">ONLINE: </span> {{ session('user')->role }}
                            </p>
                        </div>

                    <!-- Notification Bell with Modal Trigger -->
                        <!-- <button id="bellButton" class="text-gray-600 text-4xl focus:outline-none relative">
                            <i class="fas fa-bell"></i>
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs font-bold rounded-full px-1.5 py-0.5">
                                3
                            </span>
                        </button> -->

                        <!-- Notification Modal (Hidden Initially) -->
                        <!-- <div id="notificationModal" 
                            class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden transition-all duration-300 ease-in-out">
                            
                            <div class="bg-white w-166 p-6 rounded-lg shadow-lg transform scale-95 opacity-0 transition-all duration-300 ease-in-out">
                                <div class="flex justify-between items-center border-b pb-2">
                                    <h2 class="text-xl font-bold text-gray-800">Notifications</h2>
                                    <button id="closeModal" class="text-gray-600 text-2xl focus:outline-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <div class="mt-4">
                                    <p class="text-gray-600">🔔 You have new notifications!</p>
                                    <ul class="mt-2 space-y-2">
                                        <li class="p-2 bg-gray-100 rounded">📢 System Update: New features added!</li>
                                        <li class="p-2 bg-gray-100 rounded">📌 Reminder: Meeting at 3 PM.</li>
                                        <li class="p-2 bg-gray-100 rounded">✉️ Message from Admin: Check your inbox.</li>
                                    </ul>
                                </div>
                            </div>
                        </div> -->

                    </div>
                </div>
            </div>
           

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

            // Function to update active link state with smooth animation
            function setActiveLink(clickedLink) {
                navLinks.forEach(link => {
                    link.classList.remove(
                        "text-black", "bg-white", "shadow-md", "scale-105", 
                        "font-bold", "p-4", "rounded-lg"
                    );
                    link.classList.add("text-white", "hover:text-white", "transition-all", "duration-300", "ease-in-out");
                });

                clickedLink.classList.add(
                    "text-black", "bg-white", "shadow-md", "scale-105", 
                    "font-bold", "p-4", "rounded-lg", "transition-all", "duration-300", "ease-in-out"
                );
                clickedLink.classList.remove("text-white", "hover:text-white"); 

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

