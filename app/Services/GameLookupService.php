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
                'verify' => false,
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
            $gameId = $game['id'];

            $details = $this->fetchDetails($gameId) ?? [];

            return [
                'name' => $game['name'],
                'slug' => $game['slug'] ?? \Illuminate\Support\Str::slug($game['name']),
                'cover_image' => $game['background_image'] ?? null,
                'banner_image' => $game['background_image'] ?? null,
                'release_date' => $game['released'] ?? null,
                'genre' => collect($game['genres'] ?? [])->pluck('name')->join(', '),
                'platform' => collect($game['platforms'] ?? [])->pluck('platform.name')->join(', '),
                'external_api_id' => $gameId,
                'developer' => $details['developer'] ?? null,
                'metacritic' => $details['metacritic'] ?? null,
                'rating' => $details['rating'] ?? null,
                'description' => $details['description'] ?? null,
                'stores' => $details['stores'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching game from RAWG: ' . $e->getMessage());
            return null;
        }
    }

    public function fetchDetails(int $id): ?array
    {
        $apiKey = env('RAWG_API_KEY');
        if (!$apiKey) {
            return null;
        }

        try {
            $response = $this->client->get("https://api.rawg.io/api/games/{$id}", [
                'verify' => false,
                'query' => [
                    'key' => $apiKey,
                ]
            ]);

            $game = json_decode($response->getBody()->getContents(), true);

            $stores = [];
            if (!empty($game['stores'])) {
                foreach ($game['stores'] as $storeInfo) {
                    if (!empty($storeInfo['url'])) {
                        $storeName = 'Store';
                        $storeId = $storeInfo['store']['id'] ?? $storeInfo['store_id'] ?? null;
                        $storeMap = [
                            1 => 'Steam',
                            2 => 'Xbox Store',
                            3 => 'PlayStation Store',
                            4 => 'Xbox 360 Store',
                            5 => 'App Store',
                            6 => 'Google Play',
                            7 => 'Nintendo eShop',
                            8 => 'GOG',
                            9 => 'itch.io',
                            11 => 'Epic Games Store'
                        ];
                        if (isset($storeMap[$storeId])) {
                            $storeName = $storeMap[$storeId];
                        }
                        $stores[] = [
                            'store_name' => $storeName,
                            'url' => $storeInfo['url'],
                        ];
                    }
                }
            }

            return [
                'developer' => collect($game['developers'] ?? [])->pluck('name')->join(', '),
                'metacritic' => $game['metacritic'] ?? null,
                'rating' => $game['rating'] ?? null,
                'description' => $game['description_raw'] ?? null,
                'stores' => $stores,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching game details from RAWG: ' . $e->getMessage());
            return null;
        }
    }
}
