<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
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

    public function edit($id)
    {
        $admin = Admin::find($id);  

        return view('admin.pages.edit.user-edit', compact('admin'));
    }

    public function update (StudentRequest $req, $id) {
        $admin = Admin::find($id);

        $admin->first_name = $req->first_name;
        $admin->last_name = $req->last_name;
        $admin->email = $req->email;
        if ($req->password) {
            $admin->password = bcrypt($req->password);  
        }
        $admin->save();

        return redirect()->route('admin.users.list')->with('success', 'User updated successfully.');
    }
}
