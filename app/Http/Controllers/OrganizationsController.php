<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

// use Spatie\Crawler\Crawler;
// use App\Http\Controllers\ObserverController;
use Symfony\Component\DomCrawler\Crawler;

class OrganizationsController extends Controller
{
    public function index()
    {
        $per_page = 20;
        if(Request::get('per_page')){
            $per_page = Request::get('per_page');
        }
        return Inertia::render('Organizations/Index', [
            'filters' => Request::all('search', 'se_index', 'per_page'),
            'organizations' => Organization::where('account_id',1)
                ->orderBy('code')
                ->filter(Request::only('search', 'se_index'))
                ->select('id','code')
                ->paginate($per_page)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'code' => $organization->code,
                    'name' => $organization->name,
                    'category' => $organization->category,
                    'price' => null,
                    'eps' => null,
                    'pe' => null,
                    'upe' => null,
                    'pnav' => null,
                    'pepnav' => null,
                    'upepnav' => null,
                    'div' => null,
                    'agm' => null,
                    'listingYear' => null,
                    'longLoan' => null,
                    'shortLoan' => null,
                    'marketCap' => null,
                    'website' => null
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Organizations/Create');
    }

    public function store()
    {
        Auth::user()->account->organizations()->create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
        );

        return Redirect::route('organizations')->with('success', 'Organization created.');
    }

    public function edit(Organization $organization)
    {
        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,
                'deleted_at' => $organization->deleted_at,
                'contacts' => $organization->contacts()->orderByName()->get()->map->only('id', 'name', 'city', 'phone'),
            ],
        ]);
    }

    public function update(Organization $organization)
    {
        $organization->update(
            Request::validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
        );

        return Redirect::back()->with('success', 'Organization updated.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return Redirect::back()->with('success', 'Organization deleted.');
    }

    public function restore(Organization $organization)
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Organization restored.');
    }

    public function sync()
    {
        // DSEX
        $ch = curl_init("https://www.dsebd.org/dseX_share.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        $crawler = new Crawler(
            (string) $content
        );
        $table = $crawler->filter('table.shares-table')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        
        foreach($table as $index => $tr){
            if($index>0){
                Organization::firstOrCreate(
                    [
                        'account_id' => 1,
                        'code' => $tr[1]
                    ]
                );
            }
        }

        // DS30
        $ch = curl_init("https://www.dsebd.org/dse30_share.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        $crawler = new Crawler(
            (string) $content
        );
        $table = $crawler->filter('table.shares-table')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        
        foreach($table as $index => $tr){
            if($index>0){
                Organization::updateOrCreate(
                    [
                        'account_id' => 1,
                        'code' => $tr[1]
                    ],
                    [
                        'se_index' => 'DS30'
                    ]
                );
            }
        }

        return Redirect::route('organizations');
    }

    public function show($name)
    {
        $ch = curl_init("https://www.amarstock.com/data/1258dca00155/".$name);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        
        return $content;
    }
}
