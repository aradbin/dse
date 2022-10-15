<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Broker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PortfoliosController extends Controller
{
    public function index()
    {
        return Inertia::render('Portfolios/Index', [
            'brokers' => Broker::select('id','name')->get(),
            'portfolios' => Auth::user()->portfolios()->with('transactions', 'charges', 'trades')->get()
        ]);
    }

    public function all()
    {
        return Auth::user()->portfolios()->with('transactions', 'charges', 'trades')->get();
    }

    public function store(Request $request)
    {
        $portfolio = Auth::user()->portfolios()->create(
            Request::validate([
                'bo_account' => ['nullable', Rule::unique('portfolios')],
                'broker_user_id' => ['required', Rule::unique('portfolios')],
                'trading_charge' => ['required'],
            ])
        );

        $initial_deposit = 0;
        if(Request::get('initial_deposit') > 0){
            $initial_deposit = Request::get('initial_deposit');
            $portfolio->transactions()->create([
                'type' => 1,
                'amount' => $initial_deposit
            ]);
        }

        $portfolio->charges()->create([
            'type' => 1,
            'associate_id' => $portfolio->id,
            'amount' => 450
        ]);

        $portfolio->balance = $initial_deposit - 450;
        $portfolio->save();

        return Redirect::route('portfolio')->with('success', 'Portfolio created');
    }

    public function update(Portfolio $portfolio)
    {
        $portfolio->update(
            Request::validate([
                'broker_user_id' => ['required'],
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