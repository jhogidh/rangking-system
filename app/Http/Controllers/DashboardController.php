<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            return view('layouts.admin.contents.dashboard-admin');
        } else {
            return view('layouts.admin.contents.dashboard-guru');
        }
    }
}
