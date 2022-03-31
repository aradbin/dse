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
        return Inertia::render('Dashboard/Index');
    }

    public function home()
    {
        // if(Auth::user()){
        //     if(Request::ajax()){
        //         return Inertia::render('Dashboard/Home');
        //     }

        //     return redirect('/users/watchlist');
        // }

        return Inertia::render('Dashboard/Home');
    }
}
