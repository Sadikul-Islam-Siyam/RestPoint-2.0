<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Services\GameLookupService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GameManagementController extends Controller
{
    protected GameLookupService $lookupService;

    public function __construct(GameLookupService $lookupService)
    {
        $this->lookupService = $lookupService;
    }

    public function index()
    {
        $games = Game::withCount('posts')->paginate(10);
        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.games.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug',
            'genre' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'trailer_url' => 'nullable|url',
            'cover_image_file' => 'nullable|image|max:5120',
            'banner_image_file' => 'nullable|image|max:5120',
            'cover_image' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'external_api_id' => 'nullable|integer',
        ]);

        $data = $validated;
        
        // Remove file inputs from data
        unset($data['cover_image_file']);
        unset($data['banner_image_file']);

        // Handle uploaded cover image
        if ($request->hasFile('cover_image_file')) {
            $data['cover_image'] = '/storage/' . $request->file('cover_image_file')->store('covers', 'public');
        }

        // Handle uploaded banner image
        if ($request->hasFile('banner_image_file')) {
            $data['banner_image'] = '/storage/' . $request->file('banner_image_file')->store('banners', 'public');
        }

        // Add created_by
        $data['created_by'] = auth()->id();

        $game = Game::create($data);

        // Seed default categories for this game
        $defaultCategories = [
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

        foreach ($defaultCategories as $cat) {
            \App\Models\Category::create([
                'game_id' => $game->id,
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'keywords' => $cat['keywords']
            ]);
        }

        return redirect()->route('admin.games.index')->with('success', 'Game created successfully!');
    }

    public function edit(Game $game)
    {
        return view('admin.games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug,' . $game->id,
            'genre' => 'nullable|string|max:255',
            'platform' => 'nullable|string|max:255',
            'developer' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'trailer_url' => 'nullable|url',
            'cover_image_file' => 'nullable|image|max:5120',
            'banner_image_file' => 'nullable|image|max:5120',
            'cover_image' => 'nullable|string',
            'banner_image' => 'nullable|string',
            'external_api_id' => 'nullable|integer',
        ]);

        $data = $validated;
        unset($data['cover_image_file']);
        unset($data['banner_image_file']);

        if ($request->hasFile('cover_image_file')) {
            if ($game->cover_image && str_starts_with($game->cover_image, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $game->cover_image));
            }
            $data['cover_image'] = '/storage/' . $request->file('cover_image_file')->store('covers', 'public');
        }

        if ($request->hasFile('banner_image_file')) {
            if ($game->banner_image && str_starts_with($game->banner_image, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $game->banner_image));
            }
            $data['banner_image'] = '/storage/' . $request->file('banner_image_file')->store('banners', 'public');
        }

        $game->update($data);

        return redirect()->route('admin.games.index')->with('success', 'Game updated successfully!');
    }

    public function destroy(Game $game)
    {
        if ($game->cover_image && str_starts_with($game->cover_image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $game->cover_image));
        }
        if ($game->banner_image && str_starts_with($game->banner_image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $game->banner_image));
        }
        $game->delete();

        return redirect()->route('admin.games.index')->with('success', 'Game deleted successfully!');
    }

    public function lookup(Request $request)
    {
        $query = $request->query('q');
        if (!$query) {
            return response()->json(['error' => 'Query parameter q is required.'], 400);
        }

        $gameData = $this->lookupService->fetchFromRawg($query);

        if (!$gameData) {
            return response()->json(['error' => 'Game not found on RAWG.'], 404);
        }

        return response()->json($gameData);
    }
}
