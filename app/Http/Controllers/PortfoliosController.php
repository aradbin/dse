<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PortfoliosController extends Controller
{
    public function index()
    {
        return Inertia::render('Portfolios/Index', [
            'portfolios' => Auth::user()->portfolios()->get()
        ]);
    }
}