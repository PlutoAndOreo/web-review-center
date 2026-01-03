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

                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('first_name') is-invalid @enderror" 
                        name="first_name" 
                        id="first_name" 
                        value="{{ old('first_name') }}" 
                        required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control @error('last_name') is-invalid @enderror" 
                        name="last_name" 
                        id="last_name" 
                        value="{{ old('last_name') }}" 
                        required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input 
                        type="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
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
                        <input 
                            type="password" 
                            class="form-control @error('password') is-invalid @enderror" 
                            name="password" 
                            id="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input 
                            type="password" 
                            class="form-control" 
                            name="password_confirmation" 
                            id="password_confirmation">
                    </div>
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create Student</button>
                <a href="{{ route('admin.students.list') }}" class="btn btn-default">Cancel</a>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
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

