@extends('student.layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="w-full max-w-md mx-auto bg-white rounded-lg shadow-md p-8 mt-8">
    <h2 class="text-2xl font-bold mb-6 text-center">Student Registration</h2>
    <form method="POST" action="{{ route('student.register.submit') }}" id="registerForm">
        @csrf
        <div class="mb-4">
            <label for="first_name" class="block text-gray-700 mb-2">First Name</label>
            <input type="text" name="first_name" id="first_name"  value="{{ old('first_name') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('first_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror    
        </div>
        
        <div class="mb-4">
            <label for="last_name" class="block text-gray-700 mb-2">Last Name</label>
            <input type="text" name="last_name" id="last_name"  value="{{ old('last_name') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('last_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-2">Email</label>
            <input type="email" name="email" id="email"  value="{{ old('email') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror    
        </div>
        
        <div class="mb-4">
            <label for="phone" class="block text-gray-700 mb-2">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('phone')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror    
        </div>

        <div class="mb-4">
            <label for="address" class="block text-gray-700 mb-2">Address</label>
            <textarea name="address" id="address" rows="3" value="{{ old('address') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">{{ old('address') }}</textarea>
            @error('address')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror    
        </div>

        <div class="mb-4">
            <label for="school_graduated" class="block text-gray-700 mb-2">School Graduated</label>
            <input type="text" name="school_graduated" id="school_graduated" value="{{ old('school_graduated') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('school_graduated')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror    
        </div>

        <div class="mb-4">
            <label for="graduation_year" class="block text-gray-700 mb-2">Graduation Year</label>
            <input type="number" name="graduation_year" id="graduation_year" value="{{ old('graduation_year') }}"
                min="1950" max="{{ date('Y') + 5 }}" 
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('graduation_year')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror    
        </div>
        
        <div class="mb-6">
            <label for="password" class="block text-gray-700 mb-2">Password</label>
            <input type="password" name="password" id="password" 
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <button type="submit" id="submitBtn"
            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition flex items-center justify-center">
            <span id="btnText">Register</span>
            <svg id="spinner" class="animate-spin h-5 w-5 ml-2 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </button>
    </form>
    <p class="text-center text-gray-500 text-sm mt-4">
        Already have an account? <a href="{{ route('student.login') }}" class="text-blue-500 hover:underline">Login</a>
    </p>
</div>
@endsection

@push('javascript')
<script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        document.getElementById('btnText').textContent = 'Registering...';
        document.getElementById('spinner').classList.remove('hidden');
        document.body.style.pointerEvents = 'none';
    });
</script>
@endpush