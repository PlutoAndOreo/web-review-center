@extends('adminlte::page')

@section('title', 'Create Subject')

@section('css')
@vite('resources/css/app.css')
@endsection

@section('content')
@include('admin.components.logout')

<div class="min-h-screen flex items-center justify-center  py-10">
    <div class="w-full max-w-2xl bg-white p-8 rounded-2xl shadow-lg">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('admin.subjects.list') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Subjects
            </a>
            <h2 class="text-2xl font-bold text-gray-800">Create New Subject</h2>
            <div></div>
        </div>

        <form action="{{ route('admin.subjects.store') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Subject Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
                @error('name')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Code --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Subject Code <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required maxlength="10"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none uppercase"
                    placeholder="e.g., MATH, SCI, ENG">
                @error('code')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-1">Description</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit button --}}
            <div class="mt-10">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded">
                    Create Subject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
