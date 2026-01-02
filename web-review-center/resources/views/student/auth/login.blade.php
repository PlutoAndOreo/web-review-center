@extends('student.layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 text-center">Student Login</h2>
    @if($errors->any())
        <div class="mb-4 text-red-600">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('student.login.submit') }}" id="loginForm">
        @csrf
        <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-2">Email</label>
            <input type="email" name="email" id="email" required autofocus
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
        </div>
        <label for="password" class="block text-gray-700 mb-2">Password</label>
        <div class="mb-6 relative align-items">
            <input type="password" name="password" id="password" required placeholder="******"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
                <button
                    type="button"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                    onclick="togglePassword('password', 'eyeNewPassword')"
                >
                    <svg id="eyeNewPassword"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>            
                </button>
        </div>
        <button type="submit" id="loginBtn" 
            class="w-full bg-black text-white py-2 rounded-lg 
                    hover:bg-gray-300 hover:text-black 
                    transition flex items-center justify-center">
            <span id="loginText">Login</span>
            <svg id="loginSpinner" class="animate-spin h-5 w-5 ml-2 text-white hidden"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </button>
    </form>
    <p class="text-center text-gray-500 text-sm mt-4">
        Don't have an account? <a href="{{ route('student.register') }}"
            class="text-blue-500 hover:underline">Sign up</a>
    </p>
</div>

@endsection

@push('js')
    <script>
        document.getElementById('loginForm').addEventListener('submit', function () {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            document.getElementById('loginText').textContent = 'Logging in...';
            document.getElementById('loginSpinner').classList.remove('hidden');
            document.body.style.pointerEvents = 'none';
        });

        function togglePassword(inputId, iconId) {
            console.log('sadasd');
            const input = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                // Eye-off icon
                eyeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                `;
            } else {
                input.type = 'password';
                // Eye icon
                eyeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                `;
            }
        }
    </script>
@endpush
