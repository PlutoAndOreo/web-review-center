@extends('adminlte::page')

@section('title', 'User List')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content')
<div class="overflow-x-auto">
    <table class="w-full border border-gray-200 rounded-lg">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">ID</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Name</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Email</th>
                <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Created At</th>
                <!-- <th class="px-4 py-2 text-center text-sm font-semibold text-gray-600 border-b">Action</th> -->
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->id }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">{{ $user->email }}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        {{ \Carbon\Carbon::parse($user->created_at)->format('Y-m-d') }}
                    </td>
                    <!-- <td class="px-4 py-2 text-center flex justify-center gap-3">
                        <a href="{{ route('users.list') }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <a class="text-red-600 hover:text-red-800" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td> -->
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


    
@stop