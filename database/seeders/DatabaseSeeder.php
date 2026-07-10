<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Game;
use App\Models\Post;
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

        // 2. Seed Admin & Moderator
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

        $moderator = User::updateOrCreate(
            ['email' => 'mod@questhive.com'],
            [
                'name' => 'Tavern Moderator',
                'username' => 'moderator',
                'password' => Hash::make('password'),
                'role' => 'moderator',
                'email_verified_at' => now(),
            ]
        );

        // 3. Create ~25 fake members
        $users = User::factory()->count(25)->create();
        $allUsers = collect([$admin, $moderator])->concat($users);

        // 4. Seed Games
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
        ];

        $games = collect();
        foreach ($gamesData as $gd) {
            $game = Game::updateOrCreate(
                ['slug' => $gd['slug']],
                array_merge($gd, ['created_by' => $admin->id])
            );
            $games->push($game);

            // 5. Seed categories per game
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
        }

        // 6. Create posts & comments
        foreach ($games as $game) {
            $gameCategories = $game->categories;

            // Generate ~15 posts per game
            Post::factory()->count(15)->create([
                'game_id' => $game->id,
            ])->each(function (Post $post) use ($gameCategories, $allUsers) {
                // Assign a random category and owner user from our list
                $post->category_id = $gameCategories->random()->id;
                $post->user_id = $allUsers->random()->id;
                $post->save();

                // Create 3-5 top-level comments
                Comment::factory()->count(fake()->numberBetween(3, 5))->create([
                    'post_id' => $post->id,
                    'user_id' => fn() => $allUsers->random()->id,
                ])->each(function (Comment $comment) use ($post, $allUsers) {
                    // Create 1-2 replies (nested level 2) for some top-level comments
                    if (fake()->boolean(60)) {
                        Comment::factory()->count(fake()->numberBetween(1, 2))->create([
                            'post_id' => $post->id,
                            'user_id' => fn() => $allUsers->random()->id,
                            'parent_id' => $comment->id,
                        ]);
                    }
                });

                // If post is a help post and is solved, let's mark a comment accepted
                if ($post->type === 'help' && fake()->boolean(50)) {
                    $comments = $post->comments;
                    if ($comments->isNotEmpty()) {
                        $acceptedComment = $comments->random();
                        $acceptedComment->is_accepted = true;
                        $acceptedComment->save();

                        $post->is_solved = true;
                        $post->save();
                    }
                }
            });
        }
    }
}
