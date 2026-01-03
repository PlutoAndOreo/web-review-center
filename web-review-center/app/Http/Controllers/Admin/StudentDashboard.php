<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Video;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentDashboard extends Controller
{
    public function index()
    {
        $students = Student::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.student-list', compact('students'));
    }

    public function create()
    {
        return view('admin.pages.edit.student-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:rc_students,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'school_graduated' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'is_active' => 'boolean',
            'auto_generate_password' => 'boolean',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Handle password
        $password = null;
        if ($request->has('auto_generate_password') && $request->auto_generate_password) {
            // Auto-generate password
            $password = Str::random(12);
        } elseif ($request->filled('password')) {
            // Use provided password
            $password = $validated['password'];
        } else {
            // Default password if neither is provided
            $password = Str::random(12);
        }

        $student = Student::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'school_graduated' => $validated['school_graduated'] ?? null,
            'graduation_year' => $validated['graduation_year'] ?? null,
            'is_active' => $request->has('is_active') ? true : false,
            'password' => Hash::make($password),
        ]);

        $message = 'Student created successfully.';
        if ($request->has('auto_generate_password') && $request->auto_generate_password) {
            $message .= ' Password: ' . $password;
        } elseif (!$request->filled('password')) {
            $message .= ' Auto-generated password: ' . $password;
        }

        return redirect()->route('admin.students.list')->with('success', $message);
    }

    public function edit($id)
    {
        $student = Student::findOrFail($id);
        return view('admin.pages.edit.student-edit', compact('student'));
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('rc_students', 'email')->ignore($id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'school_graduated' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'is_active' => 'boolean',
            'change_password' => 'boolean',
            'new_password' => 'nullable|string|min:6|confirmed',
            'auto_generate_password' => 'boolean',
        ]);

        $student->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $student->phone,
            'address' => $validated['address'] ?? $student->address,
            'school_graduated' => $validated['school_graduated'] ?? $student->school_graduated,
            'graduation_year' => $validated['graduation_year'] ?? $student->graduation_year,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        // Handle password change
        if ($request->has('change_password') && $request->change_password) {
            if ($request->has('auto_generate_password') && $request->auto_generate_password) {
                // Auto-generate password
                $newPassword = Str::random(12);
                $student->update(['password' => Hash::make($newPassword)]);
                return redirect()->route('admin.students.list')
                    ->with('success', 'Student updated successfully. New password: ' . $newPassword);
            } elseif ($request->filled('new_password')) {
                // Use provided password
                $student->update(['password' => Hash::make($validated['new_password'])]);
            }
        }

        return redirect()->route('admin.students.list')
            ->with('success', 'Student updated successfully');
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('admin.students.list')
            ->with('success', 'Student deleted successfully');
    }

    public function showVideoProgress($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Get all videos with completion status
        $histories = DB::table('rc_student_histories')
            ->where('student_id', $studentId)
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
        
        // Get all videos to show which ones haven't been started
        $allVideos = \App\Models\Video::with('subject')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.pages.student-video-progress', compact('student', 'histories', 'allVideos'));
    }

    public function toggleRetake(Request $request, $studentId, $videoId)
    {
        $request->validate([
            'retake_allowed' => 'required|boolean'
        ]);

        $history = DB::table('rc_student_histories')
            ->where('student_id', $studentId)
            ->where('video_id', $videoId)
            ->first();

        if ($history) {
            DB::table('rc_student_histories')
                ->where('student_id', $studentId)
                ->where('video_id', $videoId)
                ->update([
                    'retake_allowed' => $request->retake_allowed,
                    'updated_at' => now()
                ]);
        } else {
            // Create history record if it doesn't exist
            DB::table('rc_student_histories')->insert([
                'student_id' => $studentId,
                'video_id' => $videoId,
                'watched' => false,
                'form_completed' => false,
                'retake_allowed' => $request->retake_allowed,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $request->retake_allowed ? 'Retake enabled for this student' : 'Retake disabled for this student'
        ]);
    }
}
