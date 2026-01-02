<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Video;
use App\Models\Student;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAdminUser = Admin::count();
        $totalVideos = Video::count();
        $totalStudents = Student::count();
        $totalSubjects = Subject::where('is_active',1)->count();
        
        return view('admin.pages.dashboard', 
            compact(
                'totalAdminUser',
                'totalVideos',
                'totalStudents',
                'totalSubjects'
            )
        );
    }
}
