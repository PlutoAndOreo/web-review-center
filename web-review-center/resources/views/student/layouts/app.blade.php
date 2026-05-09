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

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        @if(!request()->routeIs('student.register') && !request()->routeIs('student.login'))
            @include('student.parts.sidebar')
        @endif

        @if(request()->routeIs('student.register') || request()->routeIs('student.login'))
            <!-- No content-wrapper for register/login pages -->
            @yield('content')
        @else
            <!-- Content Wrapper -->
            <div class="content-wrapper">
                @include('student.parts.topbar')
                
                <!-- Main content -->
                <div class="content">
                    @yield('content')
                </div>
            </div>
            <!-- /.content-wrapper -->
        @endif
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
    
    <script>
        // Sidebar toggle functionality (AdminLTE handles this automatically)
        // The sidebar will be always visible on mobile, but can be toggled for icon-only view
        $(document).ready(function() {
            // Wait for AdminLTE to be fully loaded
            if (typeof $ !== 'undefined' && $.fn.pushMenu) {
                // Initialize AdminLTE pushmenu - just initialize, don't toggle
                $('[data-widget="pushmenu"]').each(function() {
                    if ($(this).length) {
                        try {
                            $(this).pushMenu();
                        } catch (e) {
                            console.warn('PushMenu initialization failed:', e);
                        }
                    }
                });
            } else {
                // Fallback if AdminLTE is not loaded
                $('[data-widget="pushmenu"]').on('click', function(e) {
                    e.preventDefault();
                    $('body').toggleClass('sidebar-collapse');
                });
            }
            
            // Prevent AdminLTE IFrame widget from initializing on elements that don't exist or aren't meant for it
            // AdminLTE auto-initializes IFrame widgets, so we need to prevent it from running on null elements
            if (typeof $ !== 'undefined' && $.fn.IFrame) {
                // Override AdminLTE's auto-initialization to add null checks
                const originalIFrame = $.fn.IFrame;
                $.fn.IFrame = function(options) {
                    // Only initialize if element exists and is in DOM
                    if (this.length && this[0] && document.body.contains(this[0])) {
                        try {
                            return originalIFrame.call(this, options);
                        } catch (e) {
                            console.warn('IFrame initialization failed:', e);
                            return this;
                        }
                    }
                    return this;
                };
            }
        });
    </script>
    
    <script src="{{ asset('js/password-icon.js') }}"></script>
    
    @stack('js')
</body>

</html>
