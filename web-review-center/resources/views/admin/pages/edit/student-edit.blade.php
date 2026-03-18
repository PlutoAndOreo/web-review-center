@extends('adminlte::page')

@section('title', 'Edit Student')
@section('content')
@include('admin.components.logout')
<div class="container-fluid">
    <div class="card card-primary">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.students.list') }}" class="btn btn-sm btn-outline-light mr-3">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <h3 class="card-title mb-0">Edit Student</h3>
            </div>
        </div>

        <!-- form start -->
        <form action="{{ route('admin.students.update', $student->id) }}" method="POST" id="editStudentForm">
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
                                value="{{ old('first_name', $student->first_name) }}" 
                                required>
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
                                value="{{ old('last_name', $student->last_name) }}" 
                                required>
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
                                value="{{ old('email', $student->email) }}" 
                                required>
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
                                value="{{ old('phone', $student->phone) }}">
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
                        rows="3">{{ old('address', $student->address) }}</textarea>
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
                        value="{{ old('school_graduated', $student->school_graduated) }}">
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
                        value="{{ old('graduation_year', $student->graduation_year) }}"
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
                            {{ old('is_active', $student->is_active ?? true) ? 'checked' : '' }}>
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
                            name="change_password" 
                            id="change_password" 
                            value="1"
                            onchange="togglePasswordFields()">
                        <label class="form-check-label" for="change_password">
                            Change Password
                        </label>
                    </div>
                </div>

                <div id="passwordFields" style="display: none;">
                    <div class="form-group">
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                class="form-check-input" 
                                name="auto_generate_password" 
                                id="auto_generate_password" 
                                value="1"
                                onchange="toggleAutoGenerate()">
                            <label class="form-check-label" for="auto_generate_password">
                                Auto Generate Password
                            </label>
                        </div>
                    </div>

                    <div id="manualPasswordFields">
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control @error('new_password') is-invalid @enderror" 
                                    name="new_password" 
                                    id="new_password"
                                    placeholder="New password">
                                <div class="input-group-append">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary"
                                        onclick="togglePassword('new_password', 'eyeNewPassword')">
                                        <svg id="eyeNewPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                    name="new_password_confirmation" 
                                    id="new_password_confirmation"
                                    placeholder="Confirm password">
                                <div class="input-group-append">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-secondary"
                                        onclick="togglePassword('new_password_confirmation', 'eyePasswordConfirmation')">
                                        <svg id="eyePasswordConfirmation" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </button>
                                </div>
                                @error('new_password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary" id="btn-update">Update Student</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/password-icon.js') }}"></script>
<script>
    function togglePasswordFields() {
        const changePassword = document.getElementById('change_password').checked;
        const passwordFields = document.getElementById('passwordFields');
        passwordFields.style.display = changePassword ? 'block' : 'none';
        
        if (!changePassword) {
            document.getElementById('auto_generate_password').checked = false;
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
        }
    }

    function toggleAutoGenerate() {
        const autoGenerate = document.getElementById('auto_generate_password').checked;
        const manualFields = document.getElementById('manualPasswordFields');
        manualFields.style.display = autoGenerate ? 'none' : 'block';
        
        if (autoGenerate) {
            document.getElementById('new_password').value = '';
            document.getElementById('new_password_confirmation').value = '';
        }
    }

    document.getElementById('editStudentForm').addEventListener('submit', function(e) {
        const changePassword = document.getElementById('change_password').checked;
        const autoGenerate = document.getElementById('auto_generate_password').checked;
        
        if (changePassword && !autoGenerate) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('new_password_confirmation').value;
            
            if (!newPassword || newPassword.length < 6) {
                e.preventDefault();
                alert('Password must be at least 6 characters long');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return false;
            }
        }
    });
</script>
@stop

