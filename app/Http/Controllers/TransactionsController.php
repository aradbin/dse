<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use App\Models\Broker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class TransactionsController extends Controller
{
    // public function index()
    // {
    //     return Inertia::render('Portfolios/Index', [
    //         'brokers' => Broker::select('id','name')->get(),
    //         'portfolios' => Auth::user()->portfolios()->with('transactions','trades')->get()
    //     ]);
    // }

    public function store(Request $request)
    {
        $portfolio = Auth::user()->portfolios()->find(Request::get('portfolio_id'));

        if($portfolio){
            $transaction = $portfolio->allTransactions()->create(
                Request::validate([
                    'type' => ['required'],
                    'amount' => ['required']
                ])
            );
        }

        $balanceAdjustment = 0;
        if(Request::get('type')==1){
            
        }
        $portfolio->balance = $portfolio->balance + $balanceAdjustment;
        $portfolio->save();

        return Redirect::route('portfolio')->with('success', 'Transaction created');
    }

    // public function update(Portfolio $portfolio)
    // {
    //     $portfolio->update(
    //         Request::validate([
    //             'name' => ['required'],
    //             'bo_account' => ['nullable', Rule::unique('portfolios')],
    //             'broker_user_id' => ['required', Rule::unique('portfolios')],
    //             'commission' => ['required'],
    //         ])
    //     );

    //     return Redirect::back()->with('success', 'Portfolio updated');
    // }

    // public function destroy(Portfolio $portfolio)
    // {
    //     $portfolio->delete();

    //     return Redirect::back()->with('success', 'Portfolio deleted');
    // }

    // public function restore(Portfolio $portfolio)
    // {
    //     $portfolio->restore();

    //     return Redirect::back()->with('success', 'Portfolio restored');
    // }
}