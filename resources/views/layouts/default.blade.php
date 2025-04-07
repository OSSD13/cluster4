<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>My Map</title>
    <link rel="icon" type="image/png" href="/public/assets/image/logo-mymap.png">

    <!-- Icon FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">

    <!-- Tailwindcss -->
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- JQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Mapbox --}}
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.10.0/mapbox-gl.css' rel='stylesheet' />
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.10.0/mapbox-gl.js'></script>

    {{-- Dependencies AutoFill --}}
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript"
        src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js"></script>
    <script type="text/javascript"
        src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js"></script>

    {{-- jquery.Thailand.js --}}
    <link rel="stylesheet"
        href="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css">
    <script type="text/javascript"
        src="https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js"></script>


    {{-- Import CSS form --}}
    <link rel="stylesheet" href="/resources/css/style.css">

    @yield('styles')
</head>

<body>
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 w-full bg-white shadow-md z-10 flex justify-between items-center px-4 py-2">
        <!-- Hamburger Menu Button -->
        <button id="menuToggle" class="text-gray-700">
            <i class="fa-solid fa-bars fa-xl"></i>
        </button>

        <!-- ชื่อผู้ใช้ -->
        <div class="ml-auto flex items-center space-x-2">
            <span class="text-md font-sm text-gray-500">สวัสดี! {{ Auth::user()->us_fname ?? 'ผู้ใช้' }}</span>

            <!-- ไอคอนโปรไฟล์ -->
            <button class="w-9 h-9 flex items-center justify-center border rounded-full">
                @if (!empty(Auth::user()->us_image))
                    <img src="{{ Auth::user()->us_image }}" alt="Profile" class="w-9 h-9 rounded-full">
                @else
                    <i class="fa-solid fa-user text-gray-600"></i>
                @endif
            </button>
        </div>
    </nav>

    <!-- Sidebar -->
    @include('components.sitebar')

    <!-- Main Content Wrapper -->
    <div class="min-h-screen overflow-y-auto">
        @yield('content')
    </div>

    @yield('scripts')
    @livewireScripts

    <!-- Script เปิด/ปิดเมนู -->
    <script>
        $(document).ready(function() {
            $("#menuToggle").click(function() {
                $("#sidebar").removeClass("-translate-x-full");
                $("#overlay").removeClass("hidden");
            });

            $("#closeMenu, #overlay").click(function() {
                $("#sidebar").addClass("-translate-x-full");
                $("#overlay").addClass("hidden");
            });
        });
    </script>
</body>

</html>
