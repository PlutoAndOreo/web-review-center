<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Student' }}</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/js/app.js'])
    @stack('styles')
    

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @if(!request()->routeIs('student.register') && !request()->routeIs('student.login'))
            @include('student.parts.sidebar')
            <!-- Sidebar overlay for mobile -->
            <div class="sidebar-overlay" onclick="document.body.classList.remove('sidebar-open')"></div>
        @endif

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            @if(!request()->routeIs('student.register') && !request()->routeIs('student.login'))
                @include('student.parts.topbar')
            @endif
            
            <!-- Main content -->
            <div class="content">
                @yield('content')
            </div>
        </div>
        <!-- /.content-wrapper -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    
    <script>
        // Mobile sidebar toggle
        $(document).on('click', '[data-widget="pushmenu"]', function() {
            $('body').toggleClass('sidebar-open');
        });
        
        // Close sidebar when clicking overlay
        $(document).on('click', '.sidebar-overlay', function() {
            $('body').removeClass('sidebar-open');
        });
    </script>
    
    <script src="{{ asset('js/password-icon.js') }}"></script>
    
    @stack('js')
</body>

</html>
