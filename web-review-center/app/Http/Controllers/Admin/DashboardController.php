<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Video;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAdminUser = Admin::count();
        $totalVideos = Video::count();


        return view('admin.pages.dashboard', compact('totalAdminUser', 'totalVideos'));
    }
}
