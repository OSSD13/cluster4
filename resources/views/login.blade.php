<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Map</title>
    <link rel="icon" type="image/png" href="public/assets/image/logo-mymap.png">

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
</head>

<body class="sm:flex items-center justify-center sm:min-h-screen bg-gray-100 sm:px-4">
    <div class="w-full md:h-[586px] h-screen sm:max-w-sm bg-white p-6 sm:p-8 sm:rounded-2xl shadow-lg">
        <!-- โลโก้ -->
        <div class="flex justify-center mb-6 md:mt-36 mt-36">
            <img src="public/assets/image/logo-mymap.png" alt="Login Image" class="w-32 h-32">
        </div>

        <!-- ปุ่ม Google Login -->
        <a id="google-login" href="{{ route('redirect.google') }}"
            class="w-full flex items-center border border-gray-300 justify-center py-3 rounded-2xl bg-white hover:bg-gray-100">
            <img src="public/assets/image/icon-google.png" class="w-5 h-5 mr-3"> Log in with Google
        </a>
    </div>
</body>

</html>
