@extends('student.layouts.app')

@section('title', 'Student Info')

@section('content')
<div class="max-w-2xl mx-auto p-6">    
    <div class="bg-white p-6 rounded-lg shadow-xl">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                class="mb-4 p-4 text-green-800 bg-green-100 border border-green-200 rounded-lg ">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('student.updateInfo') }}" method="POST">
            @csrf

            <a href="{{ route('student.dashboard') }}" mb-10 class="inline-flex items-center text-black-600 hover:underline mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M10.72 11.47a.75.75 0 0 0 0 1.06l7.5 7.5a.75.75 0 1 0 1.06-1.06L12.31 12l6.97-6.97a.75.75 0 0 0-1.06-1.06l-7.5 7.5Z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M4.72 11.47a.75.75 0 0 0 0 1.06l7.5 7.5a.75.75 0 1 0 1.06-1.06L6.31 12l6.97-6.97a.75.75 0 0 0-1.06-1.06l-7.5 7.5Z" clip-rule="evenodd" />
                </svg>
            </a>
            
            <div class="mb-4 ">
                <label class="block text-gray-500 text-sm">Enrolled Since</label>
                <p class="text-gray-900 font-medium">{{ optional($student->created_at)->format('M d, Y') }}</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="mb-4 relative">
                    <label for="first_name" class="block text-gray-700 text-sm mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" 
                        value="{{ old('first_name', $student->first_name) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('first_name') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-gray-700 text-sm mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" 
                        value="{{ old('last_name', $student->last_name) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('last_name') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-gray-700 text-sm mb-1">Email</label>
                    <input type="email" name="email" id="email" 
                        value="{{ old('email', $student->email) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('email') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-gray-700 text-sm mb-1">Phone</label>
                    <input type="text" name="phone" id="phone" 
                        value="{{ old('phone', $student->phone) }}"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                    @error('phone') 
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>
            </div>
            <div class="mt-6"></div>

            {{-- Password Update --}}

            <label for="current_password" class="block text-gray-700 mb-1">New Password</label>
            <div class="relative">
                <input
                    id="password"
                    type="password"
                    name="password"
                    placeholder="New password"
                    class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring focus:ring-blue-400"
                />
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
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mt-6"></div>

            <label for="password_confirmation" class="block text-gray-700 mb-1">Confirm Password</label>
            <div class="relative">
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    class="w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring focus:ring-blue-400"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                    onclick="togglePassword('password_confirmation', 'eyeConfirmPassword')"
                >
                
                <svg id="eyeConfirmPassword"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                </button>
                @error('password_confirmation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mt-6"></div>

            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
                <button type="submit" 
                class="w-full bg-black text-white py-2 rounded-lg 
                    hover:bg-gray-300 hover:text-black 
                    transition flex items-center justify-center"                        >
                    Update Information
                </button>

               
            </div>
        </form>

        
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
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
@endsection
