@extends('adminlte::page')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content')
@include('admin.components.logout')
<br>
<div class="max-w-lg mx-auto bg-white rounded-lg shadow p-8 mt-8">
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('admin.users.list') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
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
    <form method="POST" action="{{ route('admin.users.update', $admin->id) }}">
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
            <label for="phone" class="block text-gray-700 mb-2">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $admin->phone) }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300">
            @error('phone')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="role" class="block text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
            <select name="role" id="role" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-300" required>
                <option value="admin" {{ old('role', $admin->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="super_admin" {{ old('role', $admin->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="editor" {{ old('role', $admin->role) === 'editor' ? 'selected' : '' }}>Editor</option>
            </select>
            @error('role')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                    {{ old('is_active', $admin->is_active) ? 'checked' : '' }}
                    class="mr-2">
                <label for="is_active" class="text-gray-700">Active</label>
            </div>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 mb-2">New Password (leave blank to keep current)</label>
            <div class="relative">
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="New password"
                    autocomplete="new-password"
                    class="w-full px-3 py-2 pr-10 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    onclick="togglePassword('password', 'eyePassword')"
                >
                    <svg id="eyePassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 mb-2">Confirm New Password</label>
            <div class="relative">
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    placeholder="Confirm password"
                    autocomplete="new-password"
                    class="w-full px-3 py-2 pr-10 border rounded-lg focus:outline-none focus:ring focus:border-blue-300"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
                    onclick="togglePassword('password_confirmation', 'eyePasswordConfirmation')"
                >
                    <svg id="eyePasswordConfirmation" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </button>
            </div>
            @error('password_confirmation')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">Update</button>
    </form>
</div>
<br>
@stop

@push('js')
@include('admin.components.admin-scripts')
<script src="{{ asset('js/password-icon.js') }}"></script>
@endpush