<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff</title>
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


            <!-- <div class="flex items-center bg-white text-white rounded-lg p-4 space-x-3 w-full">
                <div class="w-12 h-12 bg-gray-600 flex items-center justify-center rounded-full">
                    <i class="fas fa-user text-gray-300 text-2xl"></i>
                </div>
                <div class="flex flex-col">
                    <p class="text-black text-sm">ID: {{ session('user')->id }}</p>
                    <p class="text-lg font-semibold text-black">{{ session('user')->role }}</p>
                    <p class="text-lg font-semibold text-black">{{ session('user')->role }}</p>
                </div>
            </div> -->


            <nav class="space-y-6">

            <!-- <p class="text-white text-1xl font-bold">
                <i class="fas fa-folder-open"></i> MAIN
            </p> -->

            <a href="{{ route('staff.page.dashboard') }}" class="flex items-center text-gray-300 hover:text-white relative top-4">
                <i class="fas fa-thumbtack mr-5"></i> Dashboard
            </a>


            <p class=" -m-6 mb-6 border-b border-white text-gray-200 pb-2">
            </p>

            <p class="text-white text-1xl font-bold">
                <i class="fas fa-folder mr-5"></i> FILES
            </p>

            <!-- <a href="{{ route('staff.folders') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-folder-open mr-5"></i> Folders
            </a> -->

            <a href="{{ route('staff.upload') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-upload mr-5"></i> Upload 
            </a>

            <a href="{{ route('request.folder.access') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-upload mr-5"></i> Request Access
            </a>

            <!-- <a href="{{ route('staff.files') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-file-alt mr-5"></i>  My Uploads
            </a>

            <a href="{{ route('staff.pending.files') }}" class="flex items-center text-gray-300 hover:text-white  relative">
                <i class="fas fa-file-alt mr-5"></i> Pending File Request

                @if($pendingRequestCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full absolute -top-2 -right-1">
                        {{ $pendingRequestCount }}
                    </span>
                @endif
            </a> -->

            <!-- <p class="border-b border-white text-gray-200 pb-2">
            </p> -->

            <!-- <p class="text-white text-1xl font-bold">
                <i class="fas fa-folder-open"></i> Manage Files
            </p> -->

            <a href="{{ route('staff.active.files') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-file-alt mr-6"></i> Files
            </a>

            <!-- <a href="{{ route('staff.update') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-history mr-5"></i> File Versions
            </a> -->

            <a href="{{ route('staff.archived.files') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-bookmark mr-5"></i> Archived Files
            </a>

            
            <!-- <a href="{{ route('staff.trash.bins') }}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-trash-alt mr-5"></i> Trash Files
            </a> -->

            <p class="-m-6 mb-6 border-b border-white text-gray-200 pb-2">
            </p>


            <p class="text-white text-1xl font-bold mt-8">
                <i class="fas fa-file-text mr-5"></i> ACTIVITY
            </p>

            <a href="{{ route ('timestamps.index')}}" class="flex items-center text-gray-300 hover:text-white ">
                <i class="fas fa-list mr-5"></i> File Timestamps
            </a>

            <!-- @if (session('user')->role === 'staff') -->
                <a href="{{ route('staff.logs.view') }}" class="flex items-center text-gray-300 hover:text-white ">
                    <i class="fas fa-inbox mr-5"></i> Activity Logs
                </a>
            <!-- @elseif (session('user')->role === 'faculty') -->
                <!-- <a href="{{ route('staff.logs.view') }}" class="flex items-center text-gray-300 hover:text-white ">
                    <i class="fas fa-file mr-5"></i> Faculty Logs
                </a> -->
            <!-- @endif -->

            <p class="-m-6 mb-6 border-b border-white text-gray-200 pb-2">
            </p>


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
                        <p class="text-lg font-semibold text-black">{{ session('user')->name }}</p>
                        <p class="text-lg font-semibold text-black">
                            <span class="text-lg font-bold text-green-600">ONLINE: </span> {{ session('user')->role }}
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


            <main class="p-4 sm: pt-7">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Notification Pop-up -->
    <div id="fileRequestNotification" class="fixed bottom-5 right-[-300px] bg-blue-500 text-white px-4 py-3 rounded-md shadow-md opacity-0 transition-all duration-500 ease-in-out">
        <p id="notificationMessage"></p>
        <button onclick="closeNotification()" class="bg-white text-blue-500 px-3 py-1 rounded-md mt-2">OK</button>
    </div>

    <script>
        let lastCheckedTime = null;

        function checkFileRequests() {
            fetch("{{ route('staff.check.file.requests') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'approved') {
                        showNotification(data.message);
                        lastCheckedTime = Date.now();
                    }
                })
                .catch(error => console.error("Error fetching file requests:", error));
        }

        function showNotification(message) {
            document.getElementById("notificationMessage").innerText = message;
            let notification = document.getElementById("fileRequestNotification");
            
            // Show with slide-in animation
            notification.style.right = "20px";
            notification.style.opacity = "1";
        }

        function closeNotification() {
            let notification = document.getElementById("fileRequestNotification");
            
            // Hide with slide-out animation
            notification.style.right = "-300px";
            notification.style.opacity = "0";
        }

        // Poll every 5 seconds
        setInterval(checkFileRequests, 5000);
    </script>

    @if(session('success'))
        <script>
            alert("{{ session('success') }}");
        </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", function () {
        const bellButton = document.getElementById("bellButton");
        const modal = document.getElementById("notificationModal");
        const closeModal = document.getElementById("closeModal");

        // Open Modal with Animation
        bellButton.addEventListener("click", () => {
            modal.classList.remove("hidden");
            setTimeout(() => {
                modal.children[0].classList.remove("scale-95", "opacity-0");
                modal.children[0].classList.add("scale-100", "opacity-100");
            }, 50);
        });

        // Close Modal with Animation
        closeModal.addEventListener("click", () => {
            modal.children[0].classList.remove("scale-100", "opacity-100");
            modal.children[0].classList.add("scale-95", "opacity-0");
            setTimeout(() => {
                modal.classList.add("hidden");
            }, 200);
        });

        // Close Modal when clicking outside the box
        modal.addEventListener("click", (event) => {
            if (event.target === modal) {
                closeModal.click();
            }
        });
        });
    </script>
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
                    link.classList.add("text-gray-300", "hover:text-white", "transition-all", "duration-300", "ease-in-out");
                });

                clickedLink.classList.add(
                    "text-black", "bg-white", "shadow-md", "scale-105", 
                    "font-bold", "p-4", "rounded-lg", "transition-all", "duration-300", "ease-in-out"
                );
                clickedLink.classList.remove("text-gray-300", "hover:text-white"); 

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

