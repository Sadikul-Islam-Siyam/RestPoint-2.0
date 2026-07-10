<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GameLookupService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function fetchFromRawg(string $query): ?array
    {
        $apiKey = env('RAWG_API_KEY');
        if (!$apiKey) {
            Log::warning('RAWG API key is not configured.');
            return null;
        }

        try {
            $response = $this->client->get('https://api.rawg.io/api/games', [
                'query' => [
                    'key' => $apiKey,
                    'search' => $query,
                    'page_size' => 1,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (empty($data['results'])) {
                return null;
            }

            $game = $data['results'][0];

            return [
                'name' => $game['name'],
                'slug' => $game['slug'] ?? \Illuminate\Support\Str::slug($game['name']),
                'cover_image' => $game['background_image'] ?? null,
                'banner_image' => $game['background_image'] ?? null,
                'release_date' => $game['released'] ?? null,
                'genre' => collect($game['genres'] ?? [])->pluck('name')->join(', '),
                'platform' => collect($game['platforms'] ?? [])->pluck('platform.name')->join(', '),
                'external_api_id' => $game['id'],
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching game from RAWG: ' . $e->getMessage());
            return null;
        }
    }
}
