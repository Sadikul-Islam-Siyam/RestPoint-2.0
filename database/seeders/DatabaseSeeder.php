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

        $realisticPosts = [
            'elden-ring' => [
                'boss-strategy' => [
                    [
                        'title' => 'How to beat Malenia, Blade of Miquella - Phase 2 Tips',
                        'body' => 'Malenia is widely considered the hardest boss in Elden Ring. In her second phase, her Scarlet Rot build-up is extreme. Tip: Use a shield with Barricade Shield to absorb her Waterfowl Dance, or learn the roll direction: dodge backward twice, then forward through the third burst. Frost and bleed builds work wonders.',
                    ],
                    [
                        'title' => 'Margit the Fell Omen: Easy Solo Strategy for Beginners',
                        'body' => 'Struggling with Margit? Make sure you buy Margit\'s Shackle from Patches in Murkwater Cave. It allows you to bind him to the ground twice during phase 1, letting you land fully charged heavy attacks. Summoning Spirit Jellyfish also helps draw aggro.',
                    ]
                ],
                'builds-loadouts' => [
                    [
                        'title' => 'The "Rivers of Blood" Bleed Build - Post Nerf',
                        'body' => 'Even after the patches, Bleed remains incredibly strong. Run Rivers of Blood in your main hand, Uchigatana with Seppuku in the off-hand, and wear the White Mask with Lord of Blood\'s Exultation. Focus stats: Vigor 50, Arcane 60, Dexterity 40.',
                    ]
                ],
                'item-locations' => [
                    [
                        'title' => 'Where to find the Moonveil Katana early',
                        'body' => 'The Moonveil Katana is dropped by the Magma Wyrm inside Gael Tunnel, which is located on the border between Limgrave and Caelid. It requires 12 Strength, 18 Dexterity, and 23 Intelligence.',
                    ]
                ],
                'lore-story' => [
                    [
                        'title' => 'The Tragedy of Miquella and Mogh\'s kidnapping',
                        'body' => 'Why did Mogh steal Miquella? Sir Gideon Ofnir suggests Mogh wanted to elevate Miquella to godhood and become his consort, establishing the Mohgwyn Dynasty. However, Miquella remained unresponsive inside the cocoon.',
                    ]
                ],
            ],
            'cyberpunk-2077' => [
                'boss-strategy' => [
                    [
                        'title' => 'Defeating Adam Smasher in Patch 2.0/2.1',
                        'body' => 'Adam Smasher received a massive upgrade in the 2.0 update. He now uses Sandevistan and moves incredibly fast. To beat him, use a high-mitigation build, keep moving, and target his chest missile launcher to disable his rocket barrages.',
                    ]
                ],
                'builds-loadouts' => [
                    [
                        'title' => 'Ultimate Netrunner Build - Infinite RAM & Quickhacks',
                        'body' => 'For patch 2.0+, combine the Netwatch Netdriver MK.5 cyberdeck with quickhacks like Synapse Burnout, Overheat, and Cyberware Malfunction. Trigger Overclock to use health instead of RAM, allowing you to wipe out whole building rooms in seconds.',
                    ]
                ],
                'item-locations' => [
                    [
                        'title' => 'How to get the iconic "Errata" Thermal Katana',
                        'body' => 'The Errata Thermal Katana is hidden inside the Electric Corporation building in Santo Domingo. You can grab it during the "Disasterpiece" mission or return later if you have technical ability 15+ to open the door.',
                    ]
                ],
                'lore-story' => [
                    [
                        'title' => 'Is Mr. Blue Eyes an AI from beyond the Blackwall?',
                        'body' => 'Mr. Blue Eyes appears in several key ending sequences and side missions. Many theorists believe he is a proxy body controlled by a rogue AI from beyond the Blackwall, similar to what Alt Cunningham does.',
                    ]
                ],
            ],
            'the-witcher-3-wild-hunt' => [
                'boss-strategy' => [
                    [
                        'title' => 'Tips for defeating Dettlaff in Blood and Wine',
                        'body' => 'Dettlaff\'s second phase where he spawns bat swarms is notoriously difficult. When you hear the bat swarm sound cue, start running laterally and dodge just as they release to completely avoid the hit. Use Black Blood potion and Vampire oil.',
                    ]
                ],
                'builds-loadouts' => [
                    [
                        'title' => 'OP Alchemy Euphoria Build guide',
                        'body' => 'This is the strongest build in the game. Unlock the Euphoria mutation in Blood and Wine. Equip full Grandmaster Manticore armor, drink 3-4 decoctions (Ekhidna, Ekimmara, Archgriffin), and watch your sword attack power increase by over 200%.',
                    ]
                ],
                'item-locations' => [
                    [
                        'title' => 'Where to find Grandmaster Wolven Gear diagrams',
                        'body' => 'The Grandmaster Wolven Gear diagrams can be found in the Toussaint region. Head to the ruined Termes Palace ruins located east of Gelenser Farm to find the hidden chest containing all Wolven diagrams.',
                    ]
                ],
                'lore-story' => [
                    [
                        'title' => 'Understanding Gaunter O\'Dimm - Who is he really?',
                        'body' => 'Gaunter O\'Dimm, also known as Master Mirror or Evil Incarnate, is one of the most mysterious entities in the Witcher universe. His initials spell G.O.D., and he plays with time, souls, and pacts, hinting that he is a demon or devil.',
                    ]
                ],
            ],
            'hades' => [
                'boss-strategy' => [
                    [
                        'title' => 'How to dodge Hades\' laser attacks in Phase 2',
                        'body' => 'When Hades starts channeling his ring of lasers in phase 2, you can either hide behind the two pillars in the arena, or stand directly on top of Hades (hugging him closely) to avoid the laser beams completely.',
                    ]
                ],
                'builds-loadouts' => [
                    [
                        'title' => 'Coronacht (Bow) Chiron Aspect + Artemis Build',
                        'body' => 'Max out Aspect of Chiron. Take Ares or Dionysus on Attack to apply status effects, and Artemis on Special for high critical damage strikes. Grab the Relentless Volley hammer upgrade for maximum damage.',
                    ]
                ],
                'item-locations' => [
                    [
                        'title' => 'Best ways to farm Titan Blood fast',
                        'body' => 'The fastest way to get Titan Blood is defeating the Furies and Hades on increasing Heat levels for each weapon. You can also trade 15 Ambrosia for 1 Titan Blood with the Wretched Broker in the House of Hades.',
                    ]
                ],
                'lore-story' => [
                    [
                        'title' => 'Zag\'s relationship with Persephone and the Olympians',
                        'body' => 'Zag\'s primary quest is to find his mother, Persephone. However, if the Olympians discover Persephone\'s hidden cottage, it could trigger a war between the Underworld and Olympus due to the breaking of the ancient pact.',
                    ]
                ],
            ],
            'minecraft' => [
                'boss-strategy' => [
                    [
                        'title' => 'Safe way to fight the Wither in underground caves',
                        'body' => 'To trap the Wither, mine down to bedrock level (around Y: -59) and dig a long 1x2 strip tunnel. Spawn the Wither horizontally in the tunnel and back away while firing arrows. The bedrock blocks will restrict its movement and blast radius.',
                    ]
                ],
                'builds-loadouts' => [
                    [
                        'title' => 'Essential tools enchantments list for late game',
                        'body' => 'Always prioritize: Mending, Unbreaking III, and Efficiency V on all tools. For pickaxes, have one Fortune III pickaxe for ore extraction and one Silk Touch pickaxe for stone/glass blocks.',
                    ]
                ],
                'item-locations' => [
                    [
                        'title' => 'How to locate Ancient Debris easily at Y: 15',
                        'body' => 'Ancient Debris spawns most frequently in the Nether between Y levels 8 and 22, peaking at Y: 15. The easiest mining method is using TNT or Beds to cause explosions, clearing large sections of netherrack safely.',
                    ]
                ],
                'lore-story' => [
                    [
                        'title' => 'What is the lore behind the End Cities and Shulkers?',
                        'body' => 'Are Shulkers artificial security shells built by an ancient builder race? The End Cities contain ships with Elytras, suggesting they were once used for flight by the civilization that populated the End dimension before the Ender Dragon took control.',
                    ]
                ],
            ],
            'hollow-knight' => [
                'boss-strategy' => [
                    [
                        'title' => 'Nightmare King Grimm: Charm Setup & Strategy',
                        'body' => 'NKG is incredibly fast. Charm recommendations: Grimmchild (required), Fragile/Unbreakable Strength, Shaman Stone, and Sharp Shadow. Dodge through his fire dash using Sharp Shadow, and only heal during the spike attack.',
                    ],
                    [
                        'title' => 'How to beat Sentinel Hornet in Kingdom\'s Edge',
                        'body' => 'Hornet\'s second fight is tricky due to the spiked balls she places. Use Vengeful Spirit / Shade Soul quick casts to clear the spikes from a distance. Equip Quick Slash to punish her when she lands near you.',
                    ]
                ],
                'builds-loadouts' => [
                    [
                        'title' => 'Best Spell Build for Pantheon of Hallownest',
                        'body' => 'Maximize spell damage with: Shaman Stone, Spell Twister, Soul Catcher, and Quick Slash. This setup generates soul rapidly, allowing you to spam Abyss Shriek to melt aerial bosses.',
                    ]
                ],
                'item-locations' => [
                    [
                        'title' => 'All Pale Ore locations in Hallownest',
                        'body' => 'There are 6 Pale Ores: 1. Ancient Basin (guarded by Lesser Mawleks), 2. Crystal Peak (top peak), 3. Resting Grounds (Seer reward), 4. Deepnest (Nosk lair), 5. Colosseum of Fools (trial 2), 6. Forgotten Crossroads (Grubfather reward).',
                    ]
                ],
                'lore-story' => [
                    [
                        'title' => 'The Void Entity and the true nature of the Knight',
                        'body' => 'The Knight is a vessel created from the Pale King\'s soul and Void. Unlike the Hollow Knight, who was tarnished by an idea instilled (paternal love), the Knight is completely hollow and can unify the Void to destroy the Radiance.',
                    ]
                ],
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

        // 6. Create realistic game posts & comments
        foreach ($games as $game) {
            $gameCategories = $game->categories;
            $slug = $game->slug;
            $templates = $realisticPosts[$slug] ?? [];

            for ($i = 0; $i < 15; $i++) {
                $category = $gameCategories->random();
                $catSlug = $category->slug;

                $title = null;
                $body = null;
                if (isset($templates[$catSlug]) && count($templates[$catSlug]) > 0) {
                    $postTemplate = array_shift($templates[$catSlug]);
                    $title = $postTemplate['title'];
                    $body = $postTemplate['body'];
                }

                if (!$title) {
                    $topics = [
                        'boss-strategy' => [
                            "General tips for defeating early bosses in {$game->name}",
                            "Which level should I be for the main boss of {$game->name}?",
                            "Hardest boss challenge run ideas in {$game->name}",
                        ],
                        'builds-loadouts' => [
                            "What is your favorite loadout in {$game->name}?",
                            "Early game weapons that are actually overpowered in {$game->name}",
                            "Fun stats build to try for a second playthrough",
                        ],
                        'item-locations' => [
                            "Where to find upgrade materials fast in {$game->name}",
                            "Missable hidden chests in {$game->name}",
                            "Secrets guide for beginners",
                        ],
                        'lore-story' => [
                            "Let's discuss the ending of {$game->name}",
                            "The best side quest storyline in {$game->name}",
                            "Hidden details in dialogue you probably missed",
                        ],
                        'technical-bugs' => [
                            "Fps drop issues on PC - potential fixes",
                            "Game crashing on launch since the last update",
                            "Screen tearing and graphics stuttering solutions",
                        ],
                        'general' => [
                            "Is {$game->name} worth playing in 2026?",
                            "Just started playing {$game->name}, any tips?",
                            "What makes {$game->name} a masterpiece?",
                        ],
                    ];

                    $titleTemplates = $topics[$catSlug] ?? ["General Discussion about {$game->name}"];
                    $title = $titleTemplates[array_rand($titleTemplates)];
                    $body = "I've been playing {$game->name} for a while now and I really wanted to discuss this topic. Under the {$category->name} category, let's share tips, help each other out, and write down some thoughts! Let me know what you think in the comments.";
                }

                $post = Post::create([
                    'game_id' => $game->id,
                    'category_id' => $category->id,
                    'user_id' => $allUsers->random()->id,
                    'title' => $title,
                    'body' => $body,
                    'type' => $catSlug === 'technical-bugs' || $catSlug === 'boss-strategy' ? 'help' : 'discussion',
                    'is_spoiler' => fake()->boolean(20),
                    'is_solved' => false,
                ]);

                // Create 3-5 top-level comments
                Comment::factory()->count(fake()->numberBetween(3, 5))->create([
                    'post_id' => $post->id,
                    'user_id' => fn() => $allUsers->random()->id,
                ])->each(function (Comment $comment) use ($post, $allUsers) {
                    if (fake()->boolean(60)) {
                        Comment::factory()->count(fake()->numberBetween(1, 2))->create([
                            'post_id' => $post->id,
                            'user_id' => fn() => $allUsers->random()->id,
                            'parent_id' => $comment->id,
                        ]);
                    }
                });

                if ($post->type === 'help' && fake()->boolean(50)) {
                    $topLevelComments = $post->comments()->whereNull('parent_id')->get();
                    if ($topLevelComments->isNotEmpty()) {
                        $acceptedComment = $topLevelComments->random();
                        $acceptedComment->is_accepted = true;
                        $acceptedComment->save();

                        $post->is_solved = true;
                        $post->save();
                    }
                }
            }
        }
    }
}
