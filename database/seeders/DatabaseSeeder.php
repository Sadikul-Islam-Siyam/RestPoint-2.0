<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Category;
use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Badges
        $badges = [
            [
                'name' => 'Tavern Regular',
                'description' => 'Updates activity frequently and visits the Gamers Tavern.',
                'condition_key' => 'days_active',
                'icon' => 'tavern-regular.png',
            ],
            [
                'name' => 'Helper',
                'description' => 'Successfully solved another user\'s help question.',
                'condition_key' => 'comment_accepted',
                'icon' => 'helper.png',
            ],
            [
                'name' => 'Lorekeeper',
                'description' => 'Author of deep lore and story discussion posts.',
                'condition_key' => 'post_created',
                'icon' => 'lorekeeper.png',
            ],
            [
                'name' => 'Veteran',
                'description' => 'Active contributor with high game hub presence.',
                'condition_key' => 'veteran',
                'icon' => 'veteran.png',
            ],
            [
                'name' => 'Trending Voice',
                'description' => 'Post reached high upvote milestone.',
                'condition_key' => 'post_upvotes',
                'icon' => 'trending-voice.png',
            ],
            [
                'name' => 'Legend',
                'description' => 'Achieved XP milestone status.',
                'condition_key' => 'legend',
                'icon' => 'legend.png',
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['condition_key' => $badge['condition_key']], $badge);
        }

        // 2. Seed Admin only
        $admin = User::updateOrCreate(
            ['email' => 'admin@questhive.com'],
            [
                'name' => 'Grandmaster Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 3. Seed Games
        $gamesData = [
            [
                'name' => 'Elden Ring',
                'slug' => 'elden-ring',
                'genre' => 'Action RPG, Soulslike',
                'platform' => 'PC, PlayStation 5, Xbox Series X/S',
                'developer' => 'FromSoftware',
                'release_date' => '2022-02-25',
                'external_api_id' => 326243,
            ],
            [
                'name' => 'Cyberpunk 2077',
                'slug' => 'cyberpunk-2077',
                'genre' => 'Action RPG, Sci-Fi',
                'platform' => 'PC, PlayStation 5, Xbox Series X/S',
                'developer' => 'CD Projekt Red',
                'release_date' => '2020-12-10',
                'external_api_id' => 41494,
            ],
            [
                'name' => 'The Witcher 3: Wild Hunt',
                'slug' => 'the-witcher-3-wild-hunt',
                'genre' => 'RPG, Open World',
                'platform' => 'PC, PlayStation 4/5, Xbox',
                'developer' => 'CD Projekt Red',
                'release_date' => '2015-05-19',
                'external_api_id' => 3328,
            ],
            [
                'name' => 'Hades',
                'slug' => 'hades',
                'genre' => 'Roguelike, Action',
                'platform' => 'PC, Nintendo Switch, PlayStation, Xbox',
                'developer' => 'Supergiant Games',
                'release_date' => '2020-09-17',
                'external_api_id' => 274762,
            ],
            [
                'name' => 'Minecraft',
                'slug' => 'minecraft',
                'genre' => 'Sandbox, Survival',
                'platform' => 'PC, Mobile, Nintendo Switch, PlayStation, Xbox',
                'developer' => 'Mojang Studios',
                'release_date' => '2011-11-18',
                'external_api_id' => 22509,
            ],
            [
                'name' => 'Hollow Knight',
                'slug' => 'hollow-knight',
                'genre' => 'Platformer, Indie, Action',
                'platform' => 'PC, Nintendo Switch, PlayStation 4, Xbox One',
                'developer' => 'Team Cherry',
                'release_date' => '2017-02-24',
                'external_api_id' => 9767,
            ],
        ];

        $lookupService = new \App\Services\GameLookupService();
        $games = collect();

        foreach ($gamesData as $gd) {
            $apiData = null;
            try {
                $apiData = $lookupService->fetchFromRawg($gd['name']);
            } catch (\Exception $e) {
                // Keep default hardcoded details
            }

            $finalData = array_merge([
                'name' => $gd['name'],
                'slug' => $gd['slug'],
                'genre' => $gd['genre'],
                'platform' => $gd['platform'],
                'developer' => $gd['developer'],
                'release_date' => $gd['release_date'],
                'external_api_id' => $gd['external_api_id'],
            ], $apiData ?? []);

            $stores = $finalData['stores'] ?? [];
            unset($finalData['stores']);

            $game = Game::updateOrCreate(
                ['slug' => $gd['slug']],
                array_merge($finalData, ['created_by' => $admin->id])
            );
            $games->push($game);

            // Populate game store links
            if (!empty($stores)) {
                foreach ($stores as $store) {
                    \App\Models\GameLink::updateOrCreate(
                        ['game_id' => $game->id, 'store_name' => $store['store_name']],
                        ['url' => $store['url']]
                    );
                }
            }

            // 4. Seed categories per game
            $categoriesData = [
                [
                    'name' => 'Boss Strategy',
                    'slug' => 'boss-strategy',
                    'keywords' => 'boss,strategy,fight,guide,beat,build,phase,malenia,margit,radahn',
                ],
                [
                    'name' => 'Builds & Loadouts',
                    'slug' => 'builds-loadouts',
                    'keywords' => 'build,loadout,gear,stats,armor,weapon,skills,strength,dexterity,mage',
                ],
                [
                    'name' => 'Item Locations',
                    'slug' => 'item-locations',
                    'keywords' => 'location,find,item,chest,secret,map,weapon,talismans,armor,upgrade',
                ],
                [
                    'name' => 'Lore & Story',
                    'slug' => 'lore-story',
                    'keywords' => 'lore,story,ending,theories,character,dialogue,marika,ranni,greater',
                ],
                [
                    'name' => 'Technical / Bugs',
                    'slug' => 'technical-bugs',
                    'keywords' => 'bug,crash,lag,error,graphic,settings,performance,fps,freeze',
                ],
                [
                    'name' => 'General',
                    'slug' => 'general',
                    'keywords' => 'general,question,discussion,play,time,review,thoughts',
                ],
            ];

            foreach ($categoriesData as $cd) {
                Category::updateOrCreate(
                    ['game_id' => $game->id, 'slug' => $cd['slug']],
                    [
                        'name' => $cd['name'],
                        'keywords' => $cd['keywords'],
                    ]
                );
            }

            // Seed default tags per game
            $defaultTags = [
                ['name' => 'Boss Guide', 'slug' => 'boss-guide'],
                ['name' => 'Builds', 'slug' => 'builds'],
                ['name' => 'Lore', 'slug' => 'lore'],
                ['name' => 'Multiplayer', 'slug' => 'multiplayer'],
                ['name' => 'Beginner Tips', 'slug' => 'beginner-tips'],
                ['name' => 'Item Location', 'slug' => 'item-location'],
                ['name' => 'Speedrun', 'slug' => 'speedrun'],
                ['name' => 'Mods', 'slug' => 'mods'],
                ['name' => 'Glitches', 'slug' => 'glitches'],
                ['name' => 'Review', 'slug' => 'review'],
                ['name' => 'News', 'slug' => 'news'],
                ['name' => 'Updates', 'slug' => 'updates'],
            ];

            foreach ($defaultTags as $dt) {
                \App\Models\Tag::updateOrCreate(
                    ['game_id' => $game->id, 'slug' => $dt['slug']],
                    ['name' => $dt['name']]
                );
            }
        }
    }
}
