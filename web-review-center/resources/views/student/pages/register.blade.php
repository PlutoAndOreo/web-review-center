@extends('student.layouts.guest')

@section('title', 'Student Registration')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-xl p-10">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
            Student Registration
        </h2>

        <form method="POST" action="{{ route('student.register.submit') }}" id="registerForm" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700 mb-1">First Name</label>
                    <input 
                        type="text" 
                        name="first_name" 
                        value="{{ old('first_name') }}"
                        class="w-full px-4 py-2 border rounded-lg
                            focus:outline-none focus:border-green-500
                            focus:ring-2 focus:ring-green-200">
                    @error('first_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-700 mb-1">Last Name</label>
                    <input 
                        type="text"
                        name="last_name" 
                        value="{{ old('last_name') }}"
                        class="w-full px-4 py-2 border rounded-lg
                            focus:outline-none focus:border-green-500
                            focus:ring-2 focus:ring-green-200">
                    @error('last_name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            {{-- Email --}}
            <div>
                <label class="block text-gray-700 mb-1">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    placeholder="sample@gmail.com"
                    class="w-full px-4 py-2 border rounded-lg
                        focus:outline-none focus:border-green-500
                        focus:ring-2 focus:ring-green-200">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-gray-700 mb-1">Phone</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone') }}"
                    placeholder="00000000000"
                    class="w-full px-4 py-2 border rounded-lg
                        focus:outline-none focus:border-green-500
                        focus:ring-2 focus:ring-green-200">
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            {{-- Phone --}}
            <div>
                <label class="block text-gray-700 mb-1">Phone</label>
                <input 
                    type="text" 
                    name="phone" 
                    value="{{ old('phone') }}"
                    placeholder="00000000000"
                    class="w-full px-4 py-2 border rounded-lg
                        focus:outline-none focus:border-green-500
                        focus:ring-2 focus:ring-green-200">
                @error('phone')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="birthdate" class="block text-gray-700 mb-1">
                    Birthdate
                </label>

                <input
                    type="date"
                    name="birthdate"
                    id="birthdate"
                    value="{{ old('birthdate') }}"
                    max="{{ date('Y-m-d', strtotime('-15 years')) }}"  {{-- minimum age 15 --}}
                    class="w-full px-4 py-2 border rounded-lg
                        focus:outline-none
                        focus:border-green-500
                        focus:ring-2 focus:ring-green-200"
                >

                @error('birthdate')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </form>

        <p class="text-center text-gray-500 mt-4">
            Already have an account?
            <a href="{{ route('student.login') }}" class="text-green-500 hover:underline">Login</a>
        </p>
    </div>
</div>



@endsection

@push('js')
<script>
// document.getElementById('registerForm').addEventListener('submit', function() {
//     const btn = document.getElementById('submitBtn');
//     btn.disabled = true;
//     document.getElementById('btnText').textContent = 'Registering...';
//     document.getElementById('spinner').classList.remove('hidden');
// });
</script>
@endpush
