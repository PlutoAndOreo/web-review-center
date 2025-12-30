@extends('student.layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-xl p-10 m-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Student Registration</h2>

        <form method="POST" action="{{ route('student.register.submit') }}" id="registerForm" class="space-y-5">
            @csrf

            {{-- First Name & Last Name --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('last_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-gray-700 mb-1">Phone Number</label>
                <div class="flex gap-3">
                    <select name="area_code" class="w-32 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                        <option value="">Code</option>
                        @foreach(['+1'=>'US/CA', '+63'=>'PH', '+44'=>'UK', '+61'=>'AU', '+65'=>'SG'] as $code => $country)
                            <option value="{{ $code }}" {{ old('area_code') == $code ? 'selected' : '' }}>{{ $code }} ({{ $country }})</option>
                        @endforeach
                    </select>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone number"
                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                </div>
                @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Address --}}
            <div>
                <label for="address" class="block text-gray-700 mb-1">Address</label>
                <textarea name="address" id="address" rows="3"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- School & Graduation --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="school_graduated" class="block text-gray-700 mb-1">School Graduated</label>
                    <input type="text" name="school_graduated" value="{{ old('school_graduated') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('school_graduated') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="graduation_year" class="block text-gray-700 mb-1">Graduation Year</label>
                    <input type="number" name="graduation_year" min="1950" max="{{ date('Y')+5 }}" value="{{ old('graduation_year') }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('graduation_year') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Password --}}
            <div class="w-full">
                <label for="password" class="block text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Enter your password"
                        class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="button"
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                        onclick="togglePassword('password', 'eyePassword')"
                    >
                        <svg id="eyePassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>
                            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
                        </svg>
                    </button>
                </div>
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

        {{-- Confirm Password --}}
        <div class="w-full mt-4">
            <label for="password_confirmation" class="block text-gray-700 mb-1">Confirm Password</label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm your password"
                    class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                    onclick="togglePassword('password_confirmation', 'eyeConfirm')"
                >
                    <svg id="eyeConfirm" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>
                        <path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>
                    </svg>
                </button>
            </div>
        @error('password_confirmation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
    </div>
                {{-- Submit --}}
                <button type="submit"
                class="w-full bg-black text-white py-2 rounded-lg 
                    hover:bg-gray-300 hover:text-black 
                    transition flex items-center justify-center"    
                >
                    <span id="btnText">Register</span>
                    <svg id="spinner" class="animate-spin h-5 w-5 ml-2 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
            </form>

            <p class="text-center text-gray-500 mt-4">
                Already have an account?
                <a href="{{ route('student.login') }}" class="text-blue-500 hover:underline">Login</a>
            </p>
        </div>
    </div>
@endsection

@push('js')
<script>
    document.getElementById('registerForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        document.getElementById('btnText').textContent = 'Registering...';
        document.getElementById('spinner').classList.remove('hidden');
    });
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            // Eye-off icon
            eyeIcon.innerHTML = `<path fill-rule="evenodd" d="M3.707 3.293a1 1 0 00-1.414 1.414l1.793 1.793C3.144 7.29 2.39 8.433 2 10c.73 2.89 4 7 9 7 1.567-.39 2.71-1.144 3.5-2.086l1.793 1.793a1 1 0 001.414-1.414L3.707 3.293zM10 15a5 5 0 005-5c0-.465-.073-.91-.21-1.33l-7.12-7.12C5.09 3.073 4.545 3 4 3a5 5 0 000 10c.465 0 .91-.073 1.33-.21l7.12 7.12C14.91 14.927 15 14.465 15 14a5 5 0 00-5 1z" clip-rule="evenodd"/>`;
        } else {
            input.type = 'password';
            // Eye icon
            eyeIcon.innerHTML = `<path d="M10 3C5 3 1.73 7.11 1 10c.73 2.89 4 7 9 7s8.27-4.11 9-7c-.73-2.89-4-7-9-7zM10 15a5 5 0 110-10 5 5 0 010 10z"/>
            <path d="M10 7a3 3 0 100 6 3 3 0 000-6z"/>`;
        }
    }
</script>
@endpush
