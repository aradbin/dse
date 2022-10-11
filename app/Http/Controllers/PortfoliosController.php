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
            'portfolios' => Auth::user()->portfolios()->with('trades')->get()
        ]);
    }

    public function store(Request $request)
    {
        Auth::user()->portfolios()->create(
            Request::validate([
                'bo_account' => ['required'],
                'broker_id' => ['nullable'],
                'trading_charge' => ['required'],
            ])
        );

        return Redirect::route('portfolios')->with('success', 'Portfolio created');
    }

    public function update(Portfolio $portfolio)
    {
        $portfolio->update(
            Request::validate([
                'bo_account' => ['required'],
                'broker_id' => ['nullable'],
                'trading_charge' => ['required'],
            ])
        );

        return Redirect::back()->with('success', 'Portfolio updated');
    }

    public function destroy(Portfolio $portfolio)
    {
        $portfolio->delete();

        return Redirect::back()->with('success', 'Portfolio deleted');
    }

    public function restore(Portfolio $portfolio)
    {
        $portfolio->restore();

        return Redirect::back()->with('success', 'Portfolio restored');
    }
}