<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Video;
use App\Models\Subject;


class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $student = auth()->guard('student')->user();

        $subjects = Subject::where('is_active', true)->get();
        $selectedSubject = $request->get('subject');
        
        $query = Video::where('status','=','Published')->with('subject');

        $histories = DB::table('rc_student_histories')
            ->where('student_id', $student->id)
            ->join('rc_videos', 'rc_student_histories.video_id', '=', 'rc_videos.id')
            ->leftJoin('rc_subjects', 'rc_videos.subject_id', '=', 'rc_subjects.id')
            ->select(
                'rc_student_histories.*',
                'rc_videos.id as video_id',
                'rc_videos.title as video_title',
                'rc_videos.description as video_description',
                'rc_subjects.name as subject_name'
            )
            ->orderBy('rc_videos.created_at', 'desc')
            ->get();
        $totalWatched = $histories->where('watched', 1)->count();
        $totalCompletedForms = $histories->where('form_complete', 1)->count();

        
        if ($selectedSubject && $selectedSubject !== 'all') {
            $query->where('subject_id', $selectedSubject);
        }
        
        $videos = $query->orderByDesc('created_at')->paginate(9);
        
        return view('student.pages.dashboard', compact('videos', 'subjects', 'selectedSubject','totalWatched','totalCompletedForms'));
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
