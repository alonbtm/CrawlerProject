<?php

// app/Services/WebCrawler.php

namespace App\Services;

use App\Models\Page;
use GuzzleHttp\Client;

class WebCrawler
{
    protected $visitedUrls = [];

    public function crawlAndSave($url, $depth = 1, $parent = null)
    {

        $baseURL = $this->getBaseURL($url);

        if ($depth < 1 || in_array($url, $this->visitedUrls)) {
            return;  // Base case for recursion
        }

        $client = new Client();
        $response = $client->get($url);

        if ($response->getStatusCode() != 200) {
            return [
                'success' => false,
                'message' => "Failed to fetch URL: $url with status code: " . $response->getStatusCode()
            ];
        }

        $content = (string) $response->getBody();

        // Save to database
        $existingPage = Page::where('url', $url)->first();
        if (!$existingPage) {
            Page::create(['url' => $url, 'content' => $content, 'depth' => $depth, 'parent' => $parent]);
        } else {
            $existingPage->content = $content;
            $existingPage->parent = $parent;
            $existingPage->save();
        }

        $this->visitedUrls[] = $url; // Mark this URL as visited

        // Extract links and recursively crawl them
        $links = $this->extractLinks($content);
        foreach ($links as $link) {
            $this->crawlAndSave($link, $depth - 1, $baseURL);
        }

        return [
            'success' => true,
            'message' => "URL and content saved/updated: $url"
        ];
    }

    protected function extractLinks($content)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($content); // Suppressing warnings using "@"
        $links = [];
        foreach ($dom->getElementsByTagName('a') as $tag) {
            $href = $tag->getAttribute('href');
            if (!in_array($href, $links) && filter_var($href, FILTER_VALIDATE_URL)) {
                $links[] = $href;
            }
        }
        return $links;
    }
    public function getBaseURL($url) {
        $parsedURL = parse_url($url);
        $scheme = isset($parsedURL['scheme']) ? $parsedURL['scheme'] . '://' : '';
        $host = isset($parsedURL['host']) ? $parsedURL['host'] : '';
        return $scheme . $host;
    }
    
}
