@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
@vite('resources/css/app.css')
@endsection

@section('content_header')
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
@stop

@section('content')
@include('admin.components.logout')

<main class="flex-1 p-6 bg-gray-50 min-h-screen">
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-sky-500">
            <p class="text-gray-600 font-semibold">Total Users</p>
            <h2 class="text-3xl font-bold text-sky-700 mt-2">{{ $totalAdminUser }}</h2>
            <p class="text-xs text-gray-500 mt-1">Compared to last week</p>
        </div>
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-rose-500">
            <p class="text-gray-600 font-semibold">Videos</p>
            <h2 class="text-3xl font-bold text-rose-700 mt-2">{{ $totalVideos }}</h2>
            <p class="text-xs text-gray-500 mt-1">Uploaded in total</p>
        </div>
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-emerald-500">
            <p class="text-gray-600 font-semibold">Students</p>
            <h2 class="text-3xl font-bold text-emerald-700 mt-2">{{ $totalStudents }}</h2>
            <p class="text-xs text-gray-500 mt-1">Registered users</p>
        </div>
        <div class="bg-white shadow rounded-xl p-5 border-l-4 border-indigo-500">
            <p class="text-gray-600 font-semibold">Subjects</p>
            <h2 class="text-3xl font-bold text-indigo-700 mt-2">{{ $totalSubjects ?? 0 }}</h2>
            <p class="text-xs text-gray-500 mt-1">Active subjects</p>
        </div>
    </div>

</main>
@stop

@section('scripts')
@include('admin.components.admin-scripts')

@if(auth()->check())
    @include('admin.components.idle-logout')
@endif
@append
