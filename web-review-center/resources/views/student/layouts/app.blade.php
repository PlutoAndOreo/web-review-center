<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Student' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body >
    
<div class="flex">
        @include('student.pages.sidebar')
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>
    
    
    @stack('js')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</body>

</html>
