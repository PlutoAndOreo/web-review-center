<?php

namespace App\Http\Controllers\Student;

use App\Http\Requests\StudentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('student.pages.register');
    }

    public function register(StudentRequest $request)
    {
        $validated = $request->validated();

        // Combine area code and phone number
        $areaCode = $validated['area_code'] ?? '';
        $phoneNumber = $validated['phone'] ?? '';
        $fullPhone = $areaCode . $phoneNumber;

        $student = Student::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'phone'      => $fullPhone, // Store with area code
            'address'    => $validated['address'] ?? null,
            'school_graduated' => $validated['school_graduated'] ?? null,
            'graduation_year' => $validated['graduation_year'] ?? null,
            'password'   => bcrypt($validated['password']),
        ]);

        auth()->guard('student')->login($student);

        return redirect()->route('student.dashboard');
    }

    public function addHistory(Request $request, $id)
    {
        $studentId = auth()->guard('student')->id();
        
        $exists = DB::table('rc_student_histories')
            ->where('student_id', $studentId)
            ->where('video_id', $id)
            ->exists();

        if ($exists) {
            DB::table('rc_student_histories')
                ->where('student_id', $studentId)
                ->where('video_id', $id)
                ->update([
                    'watched' => true,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('rc_student_histories')->insert([
                'student_id' => $studentId,
                'video_id' => $id,
                'watched' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function markComplete(Request $request, $id)
    {
        $studentId = auth()->guard('student')->id();
        
        $exists = DB::table('rc_student_histories')
            ->where('student_id', $studentId)
            ->where('video_id', $id)
            ->first();

        $data = [
            'watched' => true,
            'updated_at' => now(),
        ];

        // Check if form_completed column exists before adding it
        $columns = DB::getSchemaBuilder()->getColumnListing('rc_student_histories');
        if (in_array('form_completed', $columns)) {
            $data['form_completed'] = true;
            $data['form_completed_at'] = now();
            // Reset retake_allowed when marking as complete
            if (in_array('retake_allowed', $columns)) {
                $data['retake_allowed'] = false;
            }
        }

        if ($exists) {
            DB::table('rc_student_histories')
                ->where('student_id', $studentId)
                ->where('video_id', $id)
                ->update($data);
        } else {
            $data['student_id'] = $studentId;
            $data['video_id'] = $id;
            $data['created_at'] = now();
            DB::table('rc_student_histories')->insert($data);
        }

        return response()->json(['success' => true, 'message' => 'Review marked as complete']);
    }

    public function googleForms()
    {
        return view('student.pages.google-forms');
    }
}
