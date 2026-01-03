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

                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="first_name" 
                        id="first_name" 
                        value="{{ old('first_name', $student->first_name) }}" 
                        required>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="last_name" 
                        id="last_name" 
                        value="{{ old('last_name', $student->last_name) }}" 
                        required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $student->email) }}" 
                        required>
                </div>

                <!-- Phone -->
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone', $student->phone) }}">
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea 
                        class="form-control" 
                        name="address" 
                        id="address" 
                        rows="3">{{ old('address', $student->address) }}</textarea>
                </div>

                <!-- School Graduated -->
                <div class="form-group">
                    <label for="school_graduated">School Graduated</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="school_graduated" 
                        id="school_graduated" 
                        value="{{ old('school_graduated', $student->school_graduated) }}">
                </div>

                <!-- Graduation Year -->
                <div class="form-group">
                    <label for="graduation_year">Graduation Year</label>
                    <input 
                        type="number" 
                        class="form-control" 
                        name="graduation_year" 
                        id="graduation_year" 
                        value="{{ old('graduation_year', $student->graduation_year) }}"
                        min="1950" 
                        max="{{ date('Y') + 5 }}">
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
                        <label for="new_password">New Password</label>
                        <div class="form-group">
                            <input 
                                type="password" 
                                class="form-control" 
                                name="new_password" 
                                id="new_password">
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation">Confirm New Password</label>
                            <input 
                                type="password" 
                                class="form-control" 
                                name="new_password_confirmation" 
                                id="new_password_confirmation">
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

