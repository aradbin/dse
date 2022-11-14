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
                'organization_id' => [Rule::when(Request::get('type') == 3 || Request::get('type') == 4, ['required'])],
                'amount' => ['required', 'numeric', 'min:1', Rule::when(Request::get('type') == 2, ["max:$portfolio->balance"])],
                'quantity' => ['required', 'numeric', 'min:1'],
            ],[
                'amount.max' => 'Insufficient balance'
            ]);
            
            $balanceAdjustment = 0;
            $gainAdjustment = 0;
            $commission = 0;
            $charge = 0;
            $dividend = 0;
            $tax = 0;
            $cost = Request::get('amount') * Request::get('quantity');

            if(Request::get('type')==1){ // Deposit
                $balanceAdjustment = Request::get('amount');
            }else if(Request::get('type')==2){ // Withdraw
                $balanceAdjustment = 0 - Request::get('amount');
            }else if(Request::get('type')==3){ // Buy
                $commission = $cost * ($portfolio->commission / 100);
                Request::validate([
                    'amount' => ['numeric', Rule::when($portfolio->balance < ($cost + $commission), ['max:0'])],
                ],[
                    'amount.max' => 'Insufficient balance'
                ]);
                $balanceAdjustment = 0 - ($cost + $commission);
                $organization = $portfolio->organizations()->where('organization_id',Request::get('organization_id'))->first();
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
                $organization = $portfolio->organizations()->where('organization_id',Request::get('organization_id'))->first();
                Request::validate([
                    'organization' => ['numeric', Rule::when(!$organization, ['max:0'])],
                    'quantity' => ['numeric', Rule::when($organization, ["max:$organization->quantity"])]
                ],[
                    'organization.max' => "Organization doesn't exist on your portfolio",
                    'quantity.max' => "Insufficient quantity to sell",
                ]);
                $commission = $cost * ($portfolio->commission / 100);
                $balanceAdjustment = $cost - $commission;
                $gainAdjustment = $cost - $commission - ($organization->amount * $organization->quantity);
                $organization->quantity = $organization->quantity - Request::get('quantity');
                if($organization->quantity==0){
                    $organization->delete();
                }else{
                    $organization->save();
                }
            }else if(Request::get('type')==5){ // BO Charge
                $balanceAdjustment = 0 - Request::get('amount');
                $charge = Request::get('amount');
            }else if(Request::get('type')==6){ // IPO Charge
                $balanceAdjustment = 0 - Request::get('amount');
                $charge = Request::get('amount');
            }else if(Request::get('type')==7){ // Cash Dividend
                $dividend = $cost;
                $tax = Request::get('tax');
            }else if(Request::get('type')==8){ // Stock Dividend
                
            }

            $transaction = $portfolio->transactions()->create([
                'name' => Request::get('name'),
                'type' => Request::get('type'),
                'organization_id' => Request::get('organization_id'),
                'amount' => Request::get('amount'),
                'quantity' => Request::get('quantity'),
                'commission' => $commission,
                'tax' => $tax,
            ]);

            $portfolio->balance = $portfolio->balance + $balanceAdjustment;
            $portfolio->realized_gain = $portfolio->realized_gain + $gainAdjustment;
            $portfolio->paid_commission = $portfolio->paid_commission + $commission;
            $portfolio->paid_charge = $portfolio->paid_charge + $charge;
            $portfolio->cash_dividend = $portfolio->cash_dividend + $dividend;
            $portfolio->paid_tax = $portfolio->paid_tax + $tax;
            $portfolio->save();

            return Redirect::back()->with('success', 'Transaction created');
        }

        return Redirect::route('portfolio')->with('error', 'Portfolio not found');
    }
}