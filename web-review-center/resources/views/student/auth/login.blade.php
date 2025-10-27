@extends('student.layouts.guest')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Student Login</h2>
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                <ul>
                    @foreach ($errors->all() as $error)
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
            <div class="mb-6">
                <label for="password" class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            </div>
            <button type="submit" id="loginBtn"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                <span id="loginText">Login</span>
                <svg id="loginSpinner" class="animate-spin h-5 w-5 ml-2 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>
        </form>
        <p class="text-center text-gray-500 text-sm mt-4">
          Don't have an account? <a href="{{ route('student.register') }}" class="text-blue-500 hover:underline">Sign up</a>
      </p>
    </div>

@endsection

@push('javascript')
<script>
    document.getElementById('loginForm').addEventListener('submit', function(){
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        document.getElementById('loginText').textContent = 'Logging in...';
        document.getElementById('loginSpinner').classList.remove('hidden');
        document.body.style.pointerEvents = 'none';
    });
</script>
@endpush