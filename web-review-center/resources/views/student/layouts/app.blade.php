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
@if(!request()->routeIs('student.register') && !request()->routeIs('student.login'))
        <!-- <nav class="w-full flex items-center justify-between p-4 bg-white shadow">
            <div>
                <button type="button" id="openLogout" class="px-3 py-1 rounded bg-red-500 text-white">Logout</button>
                <form id="studentLogoutForm" method="POST" action="{{ route('student.logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </nav> -->
        @endif
    
    @yield('content')

    <style>
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 10000; }
        .modal-overlay.show { display: flex; }
        .modal-card { width: 100%; max-width: 420px; background: #fff; border-radius: 12px; padding: 18px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    </style>
    <div class="modal-overlay" id="logoutModal" aria-hidden="true">
        <div class="modal-card">
            <h3 class="text-lg font-semibold mb-2">Confirm Logout</h3>
            <p class="text-sm text-gray-600 mb-4">Are you sure you want to logout?</p>
            <div class="flex justify-end gap-2">
                <button id="cancelLogout" class="px-3 py-1 rounded border">Cancel</button>
                <button id="confirmLogout" class="px-3 py-1 rounded bg-red-600 text-white">Logout</button>
            </div>
        </div>
    </div>
    @stack('js')
    <script>
        (function(){
            const btnOpen = document.getElementById('openLogout');
            const modal = document.getElementById('logoutModal');
            const btnCancel = document.getElementById('cancelLogout');
            const btnConfirm = document.getElementById('confirmLogout');
            const form = document.getElementById('studentLogoutForm');
            btnOpen && btnOpen.addEventListener('click', ()=> modal.classList.add('show'));
            btnCancel && btnCancel.addEventListener('click', ()=> modal.classList.remove('show'));
            btnConfirm && btnConfirm.addEventListener('click', ()=> form.submit());
        })();
    </script>

</body>
</html>