<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Video;
use App\Models\Subject;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $subjects = Subject::where('is_active', true)->get();
        $selectedSubject = $request->get('subject');
        
        $query = Video::with('subject');
        
        if ($selectedSubject && $selectedSubject !== 'all') {
            $query->where('subject_id', $selectedSubject);
        }
        
        $videos = $query->orderByDesc('created_at')->paginate(9);
        
        return view('student.pages.dashboard', compact('videos', 'subjects', 'selectedSubject'));
    }

    public function info()
    {
        $student = auth()->guard('student')->user();
        return view('student.pages.info', compact('student'));
    }
}
