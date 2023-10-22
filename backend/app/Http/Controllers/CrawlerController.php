<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;  // Import the Guzzle client

use App\Models\Page; // Assuming you have a Page model for MongoDB
use App\Services\WebCrawler;

class CrawlerController extends Controller
{

    protected $crawler;

    public function __construct(WebCrawler $crawler)
    {
        $this->crawler = $crawler;
    }

    
    public function startCrawl(Request $request)
    {
        $url = $request->input('url');
        $depth = $request->input('depth');

        // Check if the URL already exists in the database
        $existingPage = Page::where('url', $url)->first();

        // if (!$existingPage) {
            // If the URL does not exist, crawl and save content
            $result = $this->crawler->crawlAndSave($url, $depth);
            return response()->json(['message' => $result['message']]);
        // } else {
        //     return response()->json(['message' => 'URL already exists in MongoDB.']);
        // }
    }


    
    public function getResults()
    {
        $results = Page::all(); // Fetching all the results from MongoDB
        
        return response()->json($results);
    }
}
