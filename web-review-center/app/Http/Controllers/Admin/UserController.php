<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = Admin::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.pages.user-list', compact('users'));
    }

    public function create()
    {
        return view('admin.pages.edit.user-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:rc_admins,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:super_admin,admin,editor',
            'is_active' => 'boolean',
        ]);

        Admin::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return redirect()->route('admin.users.list')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.pages.edit.user-edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('rc_admins', 'email')->ignore($id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'role' => 'required|in:super_admin,admin,editor',
            'is_active' => 'boolean',
        ]);

        $admin->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $admin->phone,
            'role' => $validated['role'],
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        if ($request->filled('password')) {
            $admin->password = Hash::make($validated['password']);
            $admin->save();
        }

        return redirect()->route('admin.users.list')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        
        // Prevent deleting yourself
        if ($admin->id === auth()->guard('admin')->id()) {
            return redirect()->route('admin.users.list')
                ->with('error', 'You cannot delete your own account.');
        }

        $admin->delete();

        return redirect()->route('admin.users.list')
            ->with('success', 'User deleted successfully.');
    }
}
