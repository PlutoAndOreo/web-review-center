@extends('student.layouts.app')

@section('title', 'Student Info')

@section('content')
   
<div class="max-w-5xl mx-auto">

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">My Profile</h1>
        <p class="text-sm text-gray-500">Manage your personal information and account settings.</p>
    </div>
</div>

{{-- Flash Message --}}
@if (session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Profile Summary --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-center gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center overflow-hidden">
                @if(!empty($student->avatar_path))
                    <img src="{{ asset($student->avatar_path) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <span class="text-green-700 font-semibold text-lg">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </span>
                @endif
            </div>

            <div>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $student->first_name }} {{ $student->last_name }}
                </p>
                <p class="text-sm text-gray-500">{{ $student->email }}</p>
                <p class="text-xs text-gray-400 mt-1">
                    Member since {{ optional($student->created_at)->format('M d, Y') }}
                </p>
            </div>
        </div>

        <div class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">Phone</span>
                <span class="text-gray-900">{{ $student->area_code }} {{ $student->phone }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Birthdate</span>
                <span class="text-gray-900">{{ $student->birthdate ?? '—' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Graduation Year</span>
                <span class="text-gray-900">{{ $student->graduation_year ?? '—' }}</span>
            </div>
        </div>

        <hr class="my-6">

        {{-- Quick actions --}}
        <div class="space-y-2">
            <a href="{{ route('student.dashboard') }}"
               class="block text-center px-4 py-2 rounded-lg bg-green-900 text-white hover:bg-green-800 transition">
                Back to Dashboard
            </a>
            <a href="{{ route('student.videos.list') }}"
               class="block text-center px-4 py-2 rounded-lg border border-green-200 hover:bg-green-50 transition">
                Browse Videos
            </a>
        </div>
    </div>

    {{-- RIGHT: Profile Form --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
             x-data="{ edit: false }">

            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>

                <button type="button"
                        @click="edit = !edit"
                        class="px-4 py-2 rounded-lg border border-green-200 hover:bg-green-50 transition text-sm">
                    <span x-show="!edit">Edit</span>
                    <span x-show="edit">Cancel</span>
                </button>
            </div>

            <form method="POST" action="{{ route('student.updateInfo') }}"
                  enctype="multipart/form-data"
                  class="space-y-5">
                @csrf
                @method('PUT')

              

                {{-- Name --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}"
                               :disabled="!edit"
                               class="w-full px-4 py-2 border rounded-lg
                                      focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                      disabled:bg-gray-50 disabled:text-gray-500">
                        @error('first_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                               :disabled="!edit"
                               class="w-full px-4 py-2 border rounded-lg
                                      focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                      disabled:bg-gray-50 disabled:text-gray-500">
                        @error('last_name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Email (usually read-only) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" value="{{ $student->email }}" disabled
                           class="w-full px-4 py-2 border rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1">Email is used for login and cannot be changed here.</p>
                </div>

                {{-- Phone + Birthdate --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Area Code</label>
                        <select name="area_code"
                                :disabled="!edit"
                                class="w-full px-4 py-2 border rounded-lg
                                       focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                       disabled:bg-gray-50 disabled:text-gray-500">
                            @php $ac = old('area_code', $student->area_code ?? '+63'); @endphp
                            <option value="+63" {{ $ac==='+63' ? 'selected' : '' }}>+63 (PH)</option>
                            <option value="+1"  {{ $ac==='+1'  ? 'selected' : '' }}>+1 (US/CA)</option>
                            <option value="+44" {{ $ac==='+44' ? 'selected' : '' }}>+44 (UK)</option>
                            <option value="+61" {{ $ac==='+61' ? 'selected' : '' }}>+61 (AU)</option>
                            <option value="+65" {{ $ac==='+65' ? 'selected' : '' }}>+65 (SG)</option>
                        </select>
                        @error('area_code') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $student->phone) }}"
                               placeholder="9XXXXXXXXX"
                               :disabled="!edit"
                               class="w-full px-4 py-2 border rounded-lg
                                      focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                      disabled:bg-gray-50 disabled:text-gray-500">
                        @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Birthdate</label>
                        <input type="date" name="birthdate"
                               value="{{ old('birthdate', $student->birthdate) }}"
                               :disabled="!edit"
                               class="w-full px-4 py-2 border rounded-lg
                                      focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                      disabled:bg-gray-50 disabled:text-gray-500">
                        @error('birthdate') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="3"
                              :disabled="!edit"
                              class="w-full px-4 py-2 border rounded-lg
                                     focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                     disabled:bg-gray-50 disabled:text-gray-500">{{ old('address', $student->address) }}</textarea>
                    @error('address') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- School + Graduation --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">School Graduated</label>
                        <input type="text" name="school_graduated"
                               value="{{ old('school_graduated', $student->school_graduated) }}"
                               :disabled="!edit"
                               class="w-full px-4 py-2 border rounded-lg
                                      focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                      disabled:bg-gray-50 disabled:text-gray-500">
                        @error('school_graduated') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Graduation Year</label>
                        <input type="number" name="graduation_year"
                               min="1950" max="{{ date('Y')+5 }}"
                               value="{{ old('graduation_year', $student->graduation_year) }}"
                               :disabled="!edit"
                               class="w-full px-4 py-2 border rounded-lg
                                      focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-200
                                      disabled:bg-gray-50 disabled:text-gray-500">
                        @error('graduation_year') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="pt-2">
                    <button type="submit"
                            :disabled="!edit"
                            class="w-full md:w-auto px-6 py-2 rounded-lg bg-green-600 text-white font-semibold
                                   hover:bg-green-700 transition
                                   focus:outline-none focus:ring-2 focus:ring-green-300
                                   disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Save Changes
                    </button>
                    <p class="text-xs text-gray-400 mt-2">Tip: Click <b>Edit</b> to enable fields.</p>
                </div>

            </form>
        </div>

        {{-- Account Security (Optional link) --}}
        <div class="mt-6 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-1">Account Security</h2>
            <p class="text-sm text-gray-500 mb-4">Manage your password and account safety.</p>
            <div x-data="{ open: false }"
                class="flex flex-col  gap-4">
                <button 
                    x-on:click="open = !open"
                    class="px-4 py-2 rounded-lg border border-green-200 hover:bg-green-50 transition text-sm w-max">
                    Change Password
                </button>

                <!-- Current Password -->
                <div x-show="open" >
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Current Password
                        </label>

                        <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            class="w-full px-3 py-2 border pr-12 rounded-lg
                                focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400"
                        >
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                text-gray-500 hover:text-gray-700"
                            @click="show = !show"
                        >
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                        </div>
                    </div>
                </div>

                <!-- New Password -->
                <div x-show="open" >
                    <div x-data="{ show: false }">
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password
                        </label>

                        <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="new_password"
                            placeholder="••••••••"
                            class="w-full px-3 py-2 border pr-12 rounded-lg
                                focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400"
                        >
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                text-gray-500 hover:text-gray-700"
                            @click="show = !show"
                        >
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                        </div>
                    </div>
                </div>

                <!-- New Password -->
                <div x-show="open" >
                    <div x-data="{ show: false }">
                        <label for="c_new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm New Password
                        </label>

                        <div class="relative">
                        <input
                            :type="show ? 'text' : 'password'"
                            name="password"
                            id="c_new_password"
                            placeholder="••••••••"
                            class="w-full px-3 py-2 border pr-12 rounded-lg
                                focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400"
                        >
                        <button
                            type="button"
                            class="absolute right-3 top-1/2 -translate-y-1/2
                                text-gray-500 hover:text-gray-700"
                            @click="show = !show"
                        >
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                            <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                        </div>
                    </div>
                </div>

                <div x-show="open" class="pt-2">
                    <button type="submit"
                            :disabled="!edit"
                            class="w-full md:w-auto px-6 py-2 rounded-lg bg-green-600 text-white font-semibold
                                   hover:bg-green-700 transition
                                   focus:outline-none focus:ring-2 focus:ring-green-300
                                   disabled:bg-gray-300 disabled:cursor-not-allowed">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
