<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;

class ObserverController extends CrawlObserver
{
    private $table;
    
    public function willCrawl(UriInterface $url)
    {

    }

    public function crawled(
        UriInterface $url,
        ResponseInterface $response,
        ?UriInterface $foundOnUrl = null
    ): void {
        $crawler = new Crawler(
            (string) $response->getBody()
        );
        $table = $crawler->filter('table')->filter('tr')->each(function ($tr, $i) {
            return $tr->filter('td')->each(function ($td, $i) {
                return trim($td->text());
            });
        });
        dd($table);
        // $data = $crawler->filter('.box')->each(function ($el, $i){
        //     return $el->filter('.value')->each(function($div, $j){
        //         return $div->text();
        //     });
        // });
        // dd($data);
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
        
    }

    public function finishedCrawling(): void
    {
        
    }
}
