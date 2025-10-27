@extends('adminlte::page')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content')
@include('admin.components.logout')
<br>
<div class="max-w-lg mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('users.list') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Users
        </a>
        <h2 class="text-2xl font-bold">Edit Admin User</h2>
        <div></div>
    </div>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 text-red-600">{{ session('error') }}</div>
    @endif
    <form method="POST" action="{{ route('users.update', $admin->id) }}">
        @csrf
        <div class="mb-4">
            <label for="first_name" class="block text-gray-700 mb-2">First Name</label>
            <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $admin->first_name) }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('first_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="last_name" class="block text-gray-700 mb-2">Last Name</label>
            <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $admin->last_name) }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('last_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 mb-2">New Password</label>
            <input type="password" name="password" id="password"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                autocomplete="new-password">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 mb-2">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                autocomplete="new-password">
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Update</button>
    </form>
</div>
<br>
@stop