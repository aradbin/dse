<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Watchlist;
use App\Models\Dividend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// use Spatie\Crawler\Crawler;
// use App\Http\Controllers\ObserverController;
use Symfony\Component\DomCrawler\Crawler;

class OrganizationsController extends Controller
{
    public function index()
    {
        return Inertia::render('Organizations/Index');
    }

    public function initial($type)
    {
        $organizations = [];

        if($type=='organizations'){
            $query = Organization::where('organizations.account_id',1)->orderBy('organizations.code');
            if(Auth::user()){
                $query->with('dividends','isWatchListed');
            }else{
                $query->with('dividends');
            }
            $organizations = $query->limit(20)->get();
        }

        if($type=='watchlist'){
            $watchlists = Auth::user()->watchlists()->get();
            $watchlist_organizations = [];
            foreach($watchlists as $watchlist){
                $watchlist_organizations[] = $watchlist->organization_id;
            }
            $query = Organization::whereIn('id',$watchlist_organizations)->orderBy('organizations.code');
            if(Auth::user()){
                $query->with('dividends','isWatchListed');
            }else{
                $query->with('dividends');
            }
            $organizations = $query->get();
        }
        
        return [
            'organizations' => $organizations,
            'sectors' => Organization::groupBy('sector')->select('sector')->get()
        ];
    }

    public function all()
    {
        $query = Organization::where('organizations.account_id',1)->orderBy('organizations.code');
        if(Auth::user()){
            $query->with('dividends','isWatchListed');
        }else{
            $query->with('dividends');
        }
        $organizations = $query->get();
        
        return [
            'organizations' => $organizations
        ];
    }

    public static function getAllFromAmarStock()
    {
        set_time_limit(0);
        
        $ch = curl_init("https://www.amarstock.com/LatestPrice/34267d8d73dd");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
        $organizations = json_decode($content);
        foreach($organizations as $index => $item){
            if($item->LTP==0){
                $organizations[$index]->LTP = $item->YCP;
            }
        }
        
        return $organizations;
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

    public function syncFromDse()
    {
        set_time_limit(0);

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
                    Organization::updateOrCreate(
                        [
                            'account_id' => 1,
                            'code' => $tr[1],
                        ],
                        [
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
                Organization::where('account_id',1)->where('code',$tr[1])->update(
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
                        Organization::where('account_id',1)->where('code',$td)->update(
                            [
                                'sector' => $sector['name']
                            ]
                        );
                    }
                }
            }
        }

        return 'Success';
    }

    public function syncFromAmarStock()
    {
        $organizations = $this->getAllFromAmarStock();
        
        foreach($organizations as $item){
            $item = (array) $item;
            Organization::where('account_id',1)->where('code',$item['Scrip'])->update(
                [
                    'name' => $item['FullName'],
                    'category' => $item['MarketCategory'],
                    'instrument_type' => $item['InstrumentType'],
                    'sector' => $item['BusinessSegment'],
                    'market_cap' => $item['MarketCap'],
                    'authorized_cap' => $item['AuthorizedCap'],
                    'paidup_cap' => $item['PaidUpCap'],
                    'shares' => $item['TotalSecurities'],
                    'director' => $item['SponsorDirector'],
                    'govt' => $item['Govt'],
                    'institute' => $item['Institute'],
                    'foreign' => $item['Foreign'],
                    'public' => $item['Public'],
                    'eps' => $item['Eps'],
                    'floor_price' => $item['FloorPrice'],
                    'price' => $item['LTP'],
                    'pe' => $item['AuditedPE'],
                    'upe' => $item['UnAuditedPE'],
                    'nav' => $item['NAV'],
                ]
            );
        }

        return 'success';
    }

    public function syncDividend()
    {
        set_time_limit(0);

        $organizations = Organization::get();
        foreach($organizations as $organization){
            $ch = curl_init("https://www.amarstock.com/company/a4e5-dd034dc69f8a/?symbol=".$organization->code);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
            $content = curl_exec($ch);
            curl_close($ch);

            foreach(json_decode($content) as $dividend){
                Dividend::updateOrCreate([
                    'organization_id' => $organization->id,
                    'year' => $dividend->y,
                ],[
                    'cash' => $dividend->c,
                    'stock' => $dividend->d,
                    'eps' => $dividend->e,
                ]);
            }
        }

        return 'success';
    }
}
