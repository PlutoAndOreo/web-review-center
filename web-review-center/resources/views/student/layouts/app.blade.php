<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Student' }}</title>

    @vite(['resources/js/app.js'])

        @stack('styles')
</head>

<body class="bg-gray-500">

    <div class="flex min-h-screen" style="font-family: 'Nunito', sans-serif; background-color: #f3f4f6;">
        @if(!request()->routeIs('student.register') && !request()->routeIs('student.login'))
            @include('student.parts.sidebar')
        @endif

        <!-- Main content -->
        <main class="relative flex flex-1 flex-col p-0">
        @if(!request()->routeIs('student.register') && !request()->routeIs('student.login'))
            @include('student.parts.topbar')
        @endif
            
        @yield('content')
        </main>

    </div>
    @stack('js')
   
</body>

</html>
