@extends('student.layouts.app')

@section('title', 'Student Info')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Your Information</h1>
    
    <div class="bg-white shadow rounded-lg p-6">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" 
                class="mb-4 p-4 text-green-800 bg-green-100 border border-green-200 rounded-lg ">
                {{ session('success') }}
            </div>
        @endif
        <form action="{{ route('student.updateInfo') }}" method="POST">
            @csrf

            <div class="mb-4">
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
            <hr class="my-6">
            <div class="mb-4 relative">
                <label for="current_password" class="block text-gray-700 text-sm mb-1">Current Password</label>
                <input type="password" name="current_password" id="current_password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                <span onclick="togglePassword('current_password')" class="absolute right-3 top-9 cursor-pointer text-sm text-gray-600">👁</span>
                @error('current_password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4 relative">
                <label for="password" class="block text-gray-700 text-sm mb-1">New Password</label>
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                <span onclick="togglePassword('password')" class="absolute right-3 top-9 cursor-pointer text-sm text-gray-600">👁</span>
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4 relative">
                <label for="password_confirmation" class="block text-gray-700 text-sm mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                <span onclick="togglePassword('password_confirmation')" class="absolute right-3 top-9 cursor-pointer text-sm text-gray-600">👁</span>
            </div>
            <div>
                
            </div>
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:space-x-4 space-y-2 sm:space-y-0">
                <button type="submit" 
                        class="w-full sm:w-auto px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Update Information
                </button>

                <a href="{{ route('student.dashboard') }}" 
                    class="w-full sm:w-auto px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition text-center">
                    Dashboard
                </a>
            </div>
        </form>

        
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === "password" ? "text" : "password";
    }
</script>
@endsection
