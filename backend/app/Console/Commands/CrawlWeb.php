<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Page;
use GuzzleHttp\Client;
use App\Services\WebCrawler;


class CrawlWeb extends Command
{
    protected $crawler;

    public function __construct(WebCrawler $crawler)
    {
        parent::__construct();
        $this->crawler = $crawler;
    }

    public function handle()
    {
        $url = $this->argument('url');
        $depth = $this->argument('depth');
        
        $result = $this->crawler->crawlAndSave($url, $depth);
        $this->info($result['message']);
        
        // Add logic here for depth-based crawling...
    }
}
