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
    public function store(Request $request)
    {
        $validator = Request::validate([
            'type' => ['required'],
            'organization_id' => [Rule::when(Request::get('type') === 3 || Request::get('type') === 4, ['required'])],
            'amount' => ['required'],
            'quantity' => [Rule::when(Request::get('type') === 3 || Request::get('type') === 4, ['required'])],
        ]);

        $portfolio = Auth::user()->portfolios()->find(Request::get('portfolio_id'));

        if($portfolio){
            $balanceAdjustment = 0;
            $commission = 0;

            if(Request::get('type')==1){
                $balanceAdjustment = Request::get('amount');
            }else if(Request::get('type')==2){
                if($portfolio->balance < Request::get('amount')){
                    return Redirect::back()->with('error', 'Insufficient balance');
                }
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==3){
                $commission = (Request::get('amount') * Request::get('quantity')) * ($portfolio->commission / 100);
                $cost = Request::get('amount') * Request::get('quantity');
                if($portfolio->balance < ($cost + $commission)){
                    return Redirect::back()->with('error', 'Insufficient balance');
                }
                $balanceAdjustment = 0 - ($cost + $commission);
                $organization = $portfolio->organizations()->find(Request::get('organization_id'));
                if($organization){
                    $organization->amount = (($organization->amount * $organization->quantity) + $cost) / ($organization->quantity + Request::get('quantity'));
                    $organization->quantity = $organization->quantity + Request::get('quantity');
                    $organization->save();
                }else{
                    $portfolio->organizations()->create([
                        'organization_id' => Request::get('organization_id'),
                        'amount' => Request::get('amount'),
                        'quantity' => Request::get('quantity')
                    ]);
                }
            }else if(Request::get('type')==4){
                $commission = (Request::get('amount') * Request::get('quantity')) * ($portfolio->commission / 100);
                $cost = Request::get('amount') * Request::get('quantity');
                $balanceAdjustment = $cost - $commission;
                $organization = $portfolio->organizations()->find(Request::get('organization_id'));
                if($organization && $organization->quantity >= Request::get('quantity')){
                    $organization->quantity = $organization->quantity - Request::get('quantity');
                    $organization->save();
                }else{
                    return Redirect::back()->with('error', 'Insufficient quantity');
                }
            }else if(Request::get('type')==5){
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==6){
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==7){
                $balanceAdjustment = Request::get('amount') - Request::get('tax');
            }

            $transaction = $portfolio->transactions()->create([
                'name' => Request::get('name'),
                'type' => Request::get('type'),
                'organization_id' => Request::get('organization_id'),
                'amount' => Request::get('amount'),
                'quantity' => Request::get('quantity'),
                'commission' => $commission,
                'tax' => 0,
            ]);

            $portfolio->balance = $portfolio->balance + $balanceAdjustment;
            $portfolio->save();
        }

        return Redirect::back()->with('success', 'Transaction created');
    }
}