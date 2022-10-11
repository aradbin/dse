<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Watchlist;
use App\Models\Dividend;
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
            'filters' => Request::all('search', 'se_index', 'category', 'sector', 'per_page'),
            'sectors' => Organization::groupBy('sector')->select('sector')->get(),
            'organizations' => Organization::where('organizations.account_id',1)
                ->with('dividends','isWatchListed')
                ->orderBy('organizations.code')
                ->filter(Request::only('search', 'se_index', 'category', 'sector'))
                ->select('organizations.id','organizations.code','organizations.category','organizations.sector')
                ->paginate($per_page)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'code' => $organization->code,
                    'name' => null,
                    'category' => $organization->category,
                    'sector' => $organization->sector,
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
                    'website' => null,
                    'watchlisted' => $organization->isWatchListed,
                    'dividends' => $organization->dividends,
                    'avg_dividend' => null
                ]),
        ]);
    }

    public function all()
    {
        return Organization::where('organizations.account_id',1)
            ->with('dividends','isWatchListed')
            ->orderBy('organizations.code')
            ->select('organizations.id','organizations.code','organizations.category','organizations.sector')
            ->get();
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

    public function watch($id)
    {
        $watchlist = Watchlist::where('organization_id',$id)->first();
        if($watchlist){
            $watchlist->delete();
        }else{
            Watchlist::create([
                'user_id' => Auth::user()->id,
                'organization_id' => $id
            ]);
        }

        return true;
    }

    public function sync()
    {
        // // DSEX
        // $ch = curl_init("https://www.dsebd.org/dseX_share.php");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        // $content = curl_exec($ch);
        // curl_close($ch);

        // $crawler = new Crawler(
        //     (string) $content
        // );
        // $table = $crawler->filter('table.shares-table')->filter('tr')->each(function ($tr, $i) {
        //     return $tr->filter('td')->each(function ($td, $i) {
        //         return trim($td->text());
        //     });
        // });
        
        // foreach($table as $index => $tr){
        //     if($index>0){
        //         Organization::firstOrCreate(
        //             [
        //                 'account_id' => 1,
        //                 'code' => $tr[1]
        //             ]
        //         );
        //     }
        // }

        // DSE By Category
        $categories = ['A','B','G','N','Z'];
        foreach($categories as $category){
            $ch = curl_init("https://www.dsebd.org/latest_share_price_scroll_group.php?group=".$category);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            $crawler = new Crawler(
                (string) $content
            );
            $table = $crawler->filter('table.shares-table tr')->each(function ($tr, $i) {
                return $tr->filter('td')->each(function ($td, $k) {
                    return trim($td->text());
                });
            });
            
            foreach($table as $index => $tr){
                if($index>0){
                    Organization::firstOrCreate(
                        [
                            'account_id' => 1,
                            'code' => $tr[1],
                            'category' => $category
                        ]
                    );
                }
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

        // DSE By Sector
        $ch = curl_init("https://www.dsebd.org/by_industrylisting.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);

        $crawler = new Crawler(
            (string) $content
        );
        $sectors = $crawler->filter('#RightBody table tr td.text-left a')->each(function ($a, $i) {
            return [
                'url' => $a->attr('href'),
                'name' => trim($a->text())
            ];
        });
        foreach($sectors as $sector){
            $ch = curl_init("https://www.dsebd.org/".$sector['url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            $crawler = new Crawler(
                (string) $content
            );
            $table = $crawler->filter('#RightBody table td')->each(function ($td, $i) {
                return $td->filter('a')->each(function ($a, $j) {
                    return trim($a->text());
                });
            });
            
            foreach($table as $index => $tr){
                if($index==0){
                    foreach($tr as $td){
                        Organization::where('code',$td)->update(
                            [
                                'sector' => $sector['name']
                            ]
                        );
                    }
                }
            }
        }

        // Dividend History
        $organizations = Organization::get();
        foreach($organizations as $organization){
            $ch = curl_init("https://www.amarstock.com/company/a4e5-dd034dc69f8a/?symbol=".$organization->code);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            foreach(json_decode($content) as $dividend){
                Dividend::firstOrCreate([
                    'organization_id' => $organization->id,
                    'cash' => $dividend->c,
                    'stock' => $dividend->d,
                    'eps' => $dividend->e,
                    'year' => $dividend->y,
                ]);
            }
        }

        // return Redirect::route('organizations');
        return 'Success';
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
