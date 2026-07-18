<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $allNews = [];

        // Fetch articles from multiple news portals
        $ign = $this->fetchRss('https://www.ign.com/rss/articles', 'IGN', 8);
        $gamespot = $this->fetchRss('https://www.gamespot.com/feeds/news/', 'GameSpot', 8);
        $pcgamer = $this->fetchRss('https://www.pcgamer.com/rss/', 'PC Gamer', 8);

        // Merge all source portals into a single array
        $allNews = array_merge($ign, $gamespot, $pcgamer);

        // Sort dynamically by publication date (descending)
        usort($allNews, function ($a, $b) {
            $timeA = strtotime($a['pubDate']);
            $timeB = strtotime($b['pubDate']);
            return $timeB <=> $timeA;
        });

        // Limit results to top 15 most recent news articles
        $allNews = array_slice($allNews, 0, 15);

        // Fallback mock data if offline or all feeds fail to load
        if (empty($allNews)) {
            $allNews = [
                [
                    'title' => 'Elden Ring DLC "Shadow of the Erdtree" Reaches Major Milestone',
                    'link' => 'https://www.ign.com',
                    'description' => 'FromSoftware announces that the highly anticipated expansion has reached massive player counts within its first weeks of release.',
                    'pubDate' => now()->subHours(2)->toDateTimeString(),
                    'source' => 'IGN',
                    'image' => null
                ],
                [
                    'title' => 'Nintendo Switch 2 Rumored Spec Sheets Leaked Online',
                    'link' => 'https://www.gamespot.com',
                    'description' => 'Leaks suggest a major upgrade to screen technology and graphics processor architecture for the next Nintendo console.',
                    'pubDate' => now()->subHours(3)->toDateTimeString(),
                    'source' => 'GameSpot',
                    'image' => null
                ],
                [
                    'title' => 'PC Gaming Hardware Demand Climbs Following Next-Gen Releases',
                    'link' => 'https://www.pcgamer.com',
                    'description' => 'Retailers report a significant lift in CPU and GPU sales as gamers prepare rigs for upcoming fall blockbuster titles.',
                    'pubDate' => now()->subHours(5)->toDateTimeString(),
                    'source' => 'PC Gamer',
                    'image' => null
                ]
            ];
        }

        return view('news.index', compact('allNews'));
    }

    private function fetchRss($url, $source, $limit = 8)
    {
        $articles = [];
        try {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) RestPointApp/1.0\r\n",
                    'timeout' => 3
                ]
            ]);

            $rssData = @file_get_contents($url, false, $context);
            if ($rssData) {
                $xml = @simplexml_load_string($rssData);
                if ($xml && isset($xml->channel->item)) {
                    foreach ($xml->channel->item as $item) {
                        // Extract namespace content if available (e.g. media:content for thumbnails)
                        $namespaces = $xml->getDocNamespaces();
                        $mediaUrl = null;
                        if (isset($namespaces['media'])) {
                            $media = $item->children($namespaces['media']);
                            if (isset($media->content)) {
                                $mediaUrl = (string)$media->content->attributes()->url;
                            }
                        }

                        $articles[] = [
                            'title' => (string) $item->title,
                            'link' => (string) $item->link,
                            'description' => strip_tags((string) $item->description),
                            'pubDate' => (string) $item->pubDate,
                            'source' => $source,
                            'image' => $mediaUrl,
                        ];
                        if (count($articles) >= $limit) {
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Fail silently
        }
        return $articles;
    }
}
