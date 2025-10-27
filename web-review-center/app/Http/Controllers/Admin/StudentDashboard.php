<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentDashboard extends Controller
{
    public function index()
    {
        $students = Student::all();
        return view('admin.pages.student-list', compact('students'));
    }
}
