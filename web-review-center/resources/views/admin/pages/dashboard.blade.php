@extends('adminlte::page')

@section('title', 'Dashboard')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content_header')
    <h1>Dashboard</h1>
@stop
    @section('content')

        <main class="flex-1 p-6">
            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex flex-row">
                <div class="bg-teal shadow rounded p-4">
                    <h2 class="text-white font-semibold">Total Users</h2>
                    <p class="text-2xl font-bold mt-2">{{ $totalAdminUser }}</p>
                </div>
                <div class="bg-maroon shadow rounded p-4">
                    <h2 class="text-white font-semibold">Videos</h2>
                    <p class="text-2xl font-bold mt-2">{{ $totalVideos }}</p>
                </div>
                <div class="bg-gray shadow rounded p-4">
                    <h2 class="text-gray-600 font-semibold">Google Forms</h2>
                    <p class="text-2xl font-bold mt-2">Todo...</p>
                </div>
            </div>
            <div class="flex flex-row ...">



        </main>
    @stop
