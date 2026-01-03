@extends('adminlte::page')

@section('title', 'Create Student')

@section('content')
@include('admin.components.logout')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.list') }}" class="btn btn-sm btn-outline-light mr-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h3 class="card-title mb-0">Create New Student</h3>
            </div>
        </div>

        <!-- form start -->
        <form action="{{ route('admin.students.store') }}" method="POST" id="createStudentForm">
            @csrf
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <!-- First Name and Last Name in a row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('first_name') is-invalid @enderror" 
                                name="first_name" 
                                id="first_name" 
                                value="{{ old('first_name') }}" 
                                >
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('last_name') is-invalid @enderror" 
                                name="last_name" 
                                id="last_name" 
                                value="{{ old('last_name') }}" 
                                >
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Email and Phone in a row -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input 
                                type="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}" 
                                >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input 
                                type="text" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                name="phone" 
                                id="phone" 
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea 
                        class="form-control @error('address') is-invalid @enderror" 
                        name="address" 
                        id="address" 
                        rows="3">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- School Graduated -->
                <div class="form-group">
                    <label for="school_graduated">School Graduated</label>
                    <input 
                        type="text" 
                        class="form-control @error('school_graduated') is-invalid @enderror" 
                        name="school_graduated" 
                        id="school_graduated" 
                        value="{{ old('school_graduated') }}">
                    @error('school_graduated')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Graduation Year -->
                <div class="form-group">
                    <label for="graduation_year">Graduation Year</label>
                    <input 
                        type="number" 
                        class="form-control @error('graduation_year') is-invalid @enderror" 
                        name="graduation_year" 
                        id="graduation_year" 
                        value="{{ old('graduation_year') }}"
                        min="1950" 
                        max="{{ date('Y') + 5 }}">
                    @error('graduation_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="form-group">
                    <div class="form-check">
                        <input 
                            type="checkbox" 
                            class="form-check-input" 
                            name="is_active" 
                            id="is_active" 
                            value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <!-- Password Management -->
                <div class="form-group">
                    <div class="form-check">
                        <input 
                            type="checkbox" 
                            class="form-check-input" 
                            name="auto_generate_password" 
                            id="auto_generate_password" 
                            value="1"
                            checked
                            onchange="togglePasswordFields()">
                        <label class="form-check-label" for="auto_generate_password">
                            Auto Generate Password
                        </label>
                    </div>
                </div>

                <div id="manualPasswordFields" style="display: none;">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                class="form-control @error('password') is-invalid @enderror" 
                                name="password" 
                                id="password">
                            <div class="input-group-append">
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('password', 'eyePassword')">
                                    <svg id="eyePassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="input-group">
                            <input 
                                type="password" 
                                class="form-control" 
                                name="password_confirmation" 
                                id="password_confirmation">
                            <div class="input-group-append">
                                <button 
                                    type="button" 
                                    class="btn btn-outline-secondary"
                                    onclick="togglePassword('password_confirmation', 'eyePasswordConfirmation')">
                                    <svg id="eyePasswordConfirmation" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Student</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/password-icon.js') }}"></script>
<script>
    function togglePasswordFields() {
        const autoGenerate = document.getElementById('auto_generate_password').checked;
        const manualFields = document.getElementById('manualPasswordFields');
        manualFields.style.display = autoGenerate ? 'none' : 'block';
        
        if (autoGenerate) {
            document.getElementById('password').value = '';
            document.getElementById('password_confirmation').value = '';
        }
    }

    document.getElementById('createStudentForm').addEventListener('submit', function(e) {
        const autoGenerate = document.getElementById('auto_generate_password').checked;
        
        if (!autoGenerate) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (!password || password.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
        }
    });
</script>
@stop

