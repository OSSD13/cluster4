{{--
    @title : Layout Map
    @author : Pakkapon Chomchoey 66160080
    @create date : 21/02/2568
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>My Map</title>
    <link rel="icon" type="image/png" href="./public/assets/image/logo-mymap.png">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- โหลดฟอนต์จาก Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai+Looped:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Noto Sans Thai Looped', sans-serif;
            font-style: normal;
        }
    </style>

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

    {{-- Turf.js --}}
    <script src="https://cdn.jsdelivr.net/npm/@turf/turf@6.5.0/turf.min.js"></script>

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

    <link rel="stylesheet" href="./public/css/style.css">


    {{-- ถ้าไม่ได้อีกลองใช้แบบนี้แม้ง --}}
    {{-- <script src="/livewire/livewire.js" data-turbo-eval="false" data-csrf="{{ csrf_token() }}"></script> --}}


    <title>{{ $title ?? 'Page Title' }}</title>
</head>

<body>


    {{ $slot }}

    @livewireScripts
    @stack('scripts')
    {{-- <script>
        Livewire.onPageExpired((response, message) => {})
    </script> --}}


</body>

</html>
