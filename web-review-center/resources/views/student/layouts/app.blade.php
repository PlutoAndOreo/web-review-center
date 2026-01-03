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
    
    <style>
        /* Make sidebar always visible on mobile */
        @media (max-width: 991.98px) {
            /* Force sidebar to be visible */
            .main-sidebar {
                transform: translateX(0) !important;
                margin-left: 0 !important;
                display: block !important;
            }
            
            /* Make sidebar narrower on mobile (icon-only mode) */
            .main-sidebar {
                width: 70px !important;
            }
            
            /* Adjust content wrapper to account for sidebar */
            .content-wrapper {
                margin-left: 70px !important;
            }
            
            /* Show only icons on mobile sidebar */
            .sidebar .nav-link p {
                display: none !important;
            }
            
            .sidebar .nav-link {
                justify-content: center !important;
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
            
            /* Hide user panel text on mobile */
            .sidebar .user-panel .info {
                display: none !important;
            }
            
            .sidebar .user-panel {
                padding: 0.5rem !important;
                justify-content: center !important;
            }
            
            /* Remove overlay since sidebar is always visible */
            .sidebar-overlay {
                display: none !important;
            }
            
            /* Ensure sidebar doesn't collapse on mobile */
            body.sidebar-collapse .main-sidebar {
                transform: translateX(0) !important;
            }
        }
        
        /* Ensure sidebar is visible on all screen sizes */
        .main-sidebar {
            display: block !important;
        }
        
        /* Better mobile touch targets */
        @media (max-width: 991.98px) {
            .sidebar .nav-link {
                min-height: 48px;
                display: flex;
                align-items: center;
            }
        }
    </style>

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
        });
    </script>
    
    <script src="{{ asset('js/password-icon.js') }}"></script>
    
    @stack('js')
</body>

</html>
