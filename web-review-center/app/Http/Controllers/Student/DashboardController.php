<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentHistory;
use App\Models\Video;
use App\Models\Subject;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $student = auth()->guard('student')->user();

        $subjects = Subject::where('is_active', true)->get();
        $videos = Video::where('status', Video::STATUS['PUBLISHED'])->get();
        $totalSubjects = $subjects->count();
        $totalVideos = $videos->count();

        $watchedVideos = Student::with('histories')->where('id', $student->id)
            ->whereHas('histories', function ($query) {
                $query->where('watched', true);
            })->count();
        
        $examSubmitted = Student::with('histories')->where('id', $student->id)
            ->whereHas('histories', function ($query) {
                $query->where('form_completed', true);
            })->count();

            

        // $currentProgres = $totalVideos > 0 ? round(($watchedVideos / $totalVideos) * 100, 2) : 0;
        $progress = $totalVideos > 0 ? round(($watchedVideos / $totalVideos) * 100) : 0;
        $exam_progress = $totalVideos > 0 ? round(($examSubmitted / $totalVideos) * 100) : 0;
                   
        return view('student.pages.dashboard', compact('student','totalSubjects','totalVideos', 'progress', 'exam_progress') );
    }

    public function info()
    {
        $student = auth()->guard('student')->user();
        return view('student.pages.info', compact('student'));
    }

    public function update(Request $request) {

        $student = auth()->guard('student')->user();
        $student_id = $student->id;

        // Update general info
        $student->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if ($request->password) {
            // Check if password confirmation matches
            if ($request->password !== $request->password_confirmation) {
                return back()->withErrors(['password' => 'The new password and confirmation do not match.']);
            }
        
            $student->password = Hash::make($request->password);
            $student->save();

            return back()->with('success', 'Password updated successfully.');
        }

        return back()->with('success', 'Updated successfully.');
    }
    
}
