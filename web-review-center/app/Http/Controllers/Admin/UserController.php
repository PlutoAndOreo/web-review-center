<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $admin = new Admin();
        $users = $admin->getUsers();
        return view('admin.pages.user-list', compact('users'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);  
        if (!$admin) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $id,
        ]);

        $admin->first_name = $request->input('first_name');
        $admin->last_name = $request->input('last_name');
        $admin->email = $request->input('email');
        $admin->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }
}
