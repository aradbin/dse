<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::user()){
            return Inertia::render('Dashboard/Index');
        }

        if(Request::ajax()){
            return Redirect::route('login');
        }
        
        return Redirect::route('organizations');
    }
}
