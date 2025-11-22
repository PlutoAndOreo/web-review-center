<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('admin.pages.subject-list', compact('subjects'));
    }

    public function create()
    {
        return view('admin.pages.subject-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:rc_subjects,code',
            'description' => 'nullable|string',
        ]);

        Subject::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.subjects.list')->with('success', 'Subject created successfully!');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.pages.subject-edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10|unique:rc_subjects,code,' . $id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $subject->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.subjects.list')->with('success', 'Subject updated successfully!');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        
        // Check if subject has videos
        if ($subject->videos()->count() > 0) {
            return redirect()->route('admin.subjects.list')->with('error', 'Cannot delete subject with existing videos.');
        }

        $subject->delete();
        return redirect()->route('admin.subjects.list')->with('success', 'Subject deleted successfully!');
    }
}