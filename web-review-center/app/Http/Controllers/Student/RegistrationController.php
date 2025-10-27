<?php

namespace App\Http\Controllers\Student;

use App\Http\Requests\StudentRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class RegistrationController extends Controller
{
    public function showRegistrationForm()
    {
        return view('student.pages.register');
    }

    public function register(StudentRequest $request)
    {

        $validated = $request->validated();

        $student = Student::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'address'    => $validated['address'] ?? null,
            'school_graduated' => $validated['school_graduated'] ?? null,
            'graduation_year' => $validated['graduation_year'] ?? null,
            'password'   => bcrypt($validated['password']),
        ]);

        auth()->guard('student')->login($student);

        return redirect()->route('student.dashboard');
    }
}
