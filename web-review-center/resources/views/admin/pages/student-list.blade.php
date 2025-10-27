@extends('adminlte::page')

@section('title', 'Student List')

@section('css')
    @vite('resources/css/app.css')
@endsection

@section('content')

@include('admin.components.logout')
    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Name</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600 border-b">Email</th>

                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $student->first_name .' '.$student->last_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $student->email}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@stop