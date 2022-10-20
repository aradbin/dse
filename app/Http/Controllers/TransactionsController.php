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
        $portfolio = Auth::user()->portfolios()->find(Request::get('portfolio_id'));

        if($portfolio){
            Request::validate([
                'type' => ['required'],
                'organization_id' => [Rule::when(Request::get('type') === 3 || Request::get('type') === 4, ['required'])],
                'amount' => ['required', 'min:0', Rule::when(Request::get('type') === 2 && Request::get('amount') <= $portfolio->balance, ['balance'])],
                'quantity' => ['required', 'min:1'],
            ],[
                'amount.balance' => 'Insufficient balance'
            ]);
            
            $balanceAdjustment = 0;
            $commission = 0;
            $cost = Request::get('amount') * Request::get('quantity');

            if(Request::get('type')==1){ // Deposit
                $balanceAdjustment = Request::get('amount');
            }else if(Request::get('type')==2){ // Withdraw
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==3){ // Buy
                $commission = (Request::get('amount') * Request::get('quantity')) * ($portfolio->commission / 100);
                Request::validate([
                    'amount' => [Rule::when($portfolio->balance >= ($cost + $commission), ['balance'])],
                ],[
                    'amount.balance' => 'Insufficient balance'
                ]);
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
            }else if(Request::get('type')==4){ // Sell
                $organization = $portfolio->organizations()->find(Request::get('organization_id'));
                Request::validate([
                    'organization' => [Rule::when($organization, ['portfolio'])],
                    'quantity' => [Rule::when($organization && $organization->quantity >= Request::get('quantity'), ['portfolio'])]
                ],[
                    'organization.portfolio' => "Organization doesn't exist on your portfolio",
                    'quantity.portfolio' => "Insufficient quantity to sell",
                ]);
                $commission = (Request::get('amount') * Request::get('quantity')) * ($portfolio->commission / 100);
                $balanceAdjustment = $cost - $commission;
                $organization->quantity = $organization->quantity - Request::get('quantity');
                $organization->save();
            }else if(Request::get('type')==5){ // BO Charge
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==6){ // IPO Charge
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==7){ // Cash Dividend
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

            return Redirect::back()->with('success', 'Transaction created');
        }

        return Redirect::route('portfolio')->with('error', 'Portfolio not found');
    }
}