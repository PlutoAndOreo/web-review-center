<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bg-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('/image/background.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(5px); 
            z-index: -1; 
        }
    </style>

</head>
<body class="flex items-center justify-center min-h-screen relative" >

    <div class="bg-blur"></div>

    @yield('content')

</body>
</html>