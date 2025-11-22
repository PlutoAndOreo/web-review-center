@extends('student.layouts.app')

@section('title', 'Student Registration')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-xl p-10">
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
            <div>
                <label for="password" class="block text-gray-700 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                @error('password') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="password_confirmation"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring focus:border-blue-400">
                @error('password_confirmation') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition flex justify-center items-center">
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

@push('javascript')
<script>
document.getElementById('registerForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    document.getElementById('btnText').textContent = 'Registering...';
    document.getElementById('spinner').classList.remove('hidden');
});
</script>
@endpush
