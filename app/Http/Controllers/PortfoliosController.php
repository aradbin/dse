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
        return Inertia::render('Portfolios/Index');
    }

    public function all()
    {
        return [
            'brokers' => Broker::select('id','name')->get(),
            'portfolios' => Auth::user()->portfolios()->with('organizations.organization', 'transactions')->get()
        ];
    }

    public static function getPorfolioOrganizationIds()
    {
        $organizations = [];
        $portfolios = Auth::user()->portfolios()->get();
        foreach($portfolios as $portfolio){
            foreach($portfolio->organizations as $org){
                $organizations[] = $org->organization_id;
            }
        }
        return $organizations;
    }

    public function store(Request $request)
    {
        $portfolio = Auth::user()->portfolios()->create(
            Request::validate([
                'name' => ['required'],
                'bo_account' => ['nullable', Rule::unique('portfolios')],
                'broker_user_id' => ['nullable', Rule::unique('portfolios')],
                'commission' => ['required'],
            ])
        );

        // BO Charge
        $portfolio->transactions()->create([
            'type' => 5,
            'amount' => 450
        ]);

        // Initial deposit
        $initial_deposit = 0;
        if(Request::get('initial_deposit') > 0){
            $initial_deposit = Request::get('initial_deposit');
            $portfolio->transactions()->create([
                'type' => 1,
                'amount' => $initial_deposit
            ]);
        }

        $portfolio->balance = $initial_deposit - 450;
        $portfolio->paid_charge = 450;   
        $portfolio->save();

        return Redirect::route('portfolio.show',$portfolio->id)->with('success', 'Portfolio created');
    }

    public function show($id)
    {
        $portfolio = Auth::user()->portfolios()->with('organizations.organization', 'transactions')->find($id);

        if($portfolio){
            return Inertia::render('Portfolios/Portfolio', [
                'portfolio' => $portfolio
            ]);
        }

        return Redirect::route('portfolio')->with('error', 'Portfolio not found');
    }

    public function update(Portfolio $portfolio)
    {
        $portfolio->update(
            Request::validate([
                'name' => ['required'],
                'bo_account' => ['nullable', Rule::unique('portfolios')],
                'broker_user_id' => ['required', Rule::unique('portfolios')],
                'commission' => ['required'],
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