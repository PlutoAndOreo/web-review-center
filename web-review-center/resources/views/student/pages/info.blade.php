@extends('student.layouts.app')

@section('title', 'Student Info')

@section('content')
<div class="max-w-2xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Your Information</h1>
    <div class="bg-white shadow rounded-lg p-5">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
            <div>
                <dt class="text-gray-500 text-sm">First Name</dt>
                <dd class="text-gray-900 font-medium">{{ $student->first_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-sm">Last Name</dt>
                <dd class="text-gray-900 font-medium">{{ $student->last_name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-sm">Email</dt>
                <dd class="text-gray-900 font-medium">{{ $student->email }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 text-sm">Phone</dt>
                <dd class="text-gray-900 font-medium">{{ $student->phone }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-gray-500 text-sm">Member Since</dt>
                <dd class="text-gray-900 font-medium">{{ optional($student->created_at)->format('M d, Y') }}</dd>
            </div>
        </dl>

        <div class="mt-5">
            <a href="{{ route('student.dashboard') }}" class="inline-block px-4 py-2 rounded bg-blue-600 text-white">Back to Dashboard</a>
        </div>
    </div>
</div>
@endsection


