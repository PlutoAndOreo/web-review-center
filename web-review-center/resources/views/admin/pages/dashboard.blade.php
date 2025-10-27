@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
@include('admin.components.logout')
    <main class="flex-1 p-6">
        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 flex flex-row">
            <div class="bg-teal shadow rounded p-4">
                <h2 class="text-white font-semibold">Total Users</h2>
                <p class="text-2xl font-bold mt-2">{{ $totalAdminUser }}</p>
            </div>
            <div class="bg-maroon shadow rounded p-4">
                <h2 class="text-white font-semibold">Videos</h2>
                <p class="text-2xl font-bold mt-2">{{ $totalVideos }}</p>
            </div>
            <div class="bg-green shadow rounded p-4">
                <h2 class="text-white font-semibold">Students</h2>
                <p class="text-2xl font-bold mt-2">{{ $totalStudents }}</p>
            </div>
            <div class="bg-blue shadow rounded p-4">
                <h2 class="text-white font-semibold">Subjects</h2>
                <p class="text-2xl font-bold mt-2">{{ $totalSubjects ?? 0 }}</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('videos.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                    Upload Video
                </a>
                <a href="{{ route('subjects.list') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                    Manage Subjects
                </a>
                <a href="{{ route('users.list') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                    Manage Users
                </a>
                <a href="{{ route('students.list') }}" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded text-center">
                    View Students
                </a>
                <a href="{{ route('notifications.list') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-center">
                    Notifications
                </a>
            </div>
        </div>
    </main>
@stop


