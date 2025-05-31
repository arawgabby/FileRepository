<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Lato', sans-serif;
            zoom: 90%;
            background: url("{{ asset('storage/uploads/bodybackground.png') }}") no-repeat center center fixed;
            background-size: contain;
        }
    </style>
</head>

<body class="bg-gray-50 bg-cover bg-center">

    <div class="flex h-screen">

        <div id="sidebar" class="bg-gray-900 text-white w-66 space-y-6 py-8 px-6 transform -translate-x-full md:translate-x-0
     transition-transform duration-300 fixed top-0 bottom-0 z-40 overflow-y-auto">

            <div class="text-2xl font-bold flex justify-center">
                <img src="{{ asset('storage/uploads/csitlogo.jpg') }}" alt="CSIT Logo" class="w-24 h-24 rounded-full">
            </div>

            <nav class="space-y-8">
                <a href="{{ route('staff.page.dashboard') }}" class="flex items-center text-white hover:text-white relative top-4">
                    <i class="fas fa-thumbtack mr-5"></i> <span class='text-sm'> Dashboard </span>
                </a>

                <p class=" -m-6 mb-6 border-b border-white text-gray-200 pb-2"></p>

                <p class="text-white text-1xl font-bold">
                    <i class="fas fa-folder mr-5"></i> <span class='text-sm'> FILES </span>
                </p>

                <a href="{{ route('staff.upload') }}" class="flex items-center text-white hover:text-white ">
                    <i class="fas fa-upload mr-5"></i> <span class='text-sm'> Upload </span>
                </a>
                <a href="{{ route('staff.active.files') }}" class="flex items-center text-white hover:text-white ">
                    <i class="fas fa-folder-open mr-4"></i> <span class='text-sm'>All Files </span>


                    <a href="{{ route('request.file.access') }}" class="flex items-center text-white hover:text-white ">
                        <i class="fas fa-share-from-square mr-2"></i>
                        <span class='text-sm ml-2'> File Upload Request </span>
                    </a>

                    <a href="{{ route('file-request.outgoing') }}" class="flex items-center text-white hover:text-white ">
                        <i class="fas fa-share-from-square mr-2"></i>
                        <span class='text-sm ml-2'> My Request Uploads </span>
                    </a>
                    <a href="{{ route('show.incoming.requests') }}" class="flex items-center text-white hover:text-white ">
                        <i class="fas fa-share-from-square mr-2"></i>
                        <span class='text-sm ml-2'> Upload Requests </span>
                    </a>

                    <a href="{{ route('staff.archived.files') }}" class="flex items-center text-white hover:text-white ">
                        <i class="fas fa-bookmark mr-5"></i> <span class='text-sm'> Archived Files </span>
                    </a>

                    <p class="-m-6 mb-6 border-b border-white text-gray-200 pb-2"></p>

                    <p class="text-white text-1xl font-bold mt-8">
                        <i class="fas fa-file-text mr-5"></i> <span class='text-sm'> ACTIVITY </span>
                    </p>

                    <!-- <a href="{{ route ('timestamps.index')}}" class="flex items-center text-white hover:text-white ">
                        <i class="fas fa-list mr-5"></i> <span class='text-sm'> File Timestamps </span>
                    </a> -->

                    <a href="{{ route('staff.logs.view') }}" class="flex items-center text-white hover:text-white ">
                        <i class="fas fa-inbox mr-5"></i> <span class='text-sm'> Activity Logs </span>
                    </a>

                    <p class="-m-6 mb-6 border-b border-white text-gray-200 pb-2"></p>

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
                <h1 class="text-2xl font-bold text-gray-800"></h1>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center bg-white p-2 space-x-3 shadow-md overflow-hidden">
                        <div class="w-12 h-12 bg-gray-600 flex items-center justify-center rounded-full">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div class="flex flex-col">
                            <p class="text-lg font-semibold text-black">{{ auth()->user()->name }}</p>
                            <p class="text-lg font-semibold text-black">
                                <span class="text-lg font-bold text-green-600">ONLINE: </span> {{ ucfirst(auth()->user()->role->name ?? '-') }}
                            </p>
                        </div>
                        <!-- Notification Bell and Modal are commented out -->
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
            notification.style.right = "20px";
            notification.style.opacity = "1";
        }

        function closeNotification() {
            let notification = document.getElementById("fileRequestNotification");
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
        document.addEventListener("DOMContentLoaded", function() {
            // Only add event listeners if elements exist
            const bellButton = document.getElementById("bellButton");
            const modal = document.getElementById("notificationModal");
            const closeModal = document.getElementById("closeModal");

            if (bellButton && modal && closeModal) {
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
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
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
                link.addEventListener("click", function() {
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