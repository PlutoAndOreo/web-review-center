@extends('layouts.guest')

@section('title', 'Login')

@section('content')
  <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
          <img src="{{ asset('image/logo.png') }}" alt="Logo" class="h-16 w-auto">
      </div>


      <form action="{{ route('admin.store') }}" method="POST" class="space-y-4">
          @csrf
          <div>
              <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
              <input type="email" id="email" name="email" placeholder="you@example.com"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <span class="tmt-1 text-sm text-red-500">{{ $message }}</span>
                @enderror
          </div>

          <div>
              <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
              <input type="password" id="password" name="password" placeholder="••••••••"
                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('password')
                    <span class="mt-1 text-sm text-red-500">{{ $message }}</span>
                @enderror
          </div>

          <button type="submit"
              class="w-full bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors font-semibold">
              Sign In
          </button>
      </form>

  </div>
@endsection
