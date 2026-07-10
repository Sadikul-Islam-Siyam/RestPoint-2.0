<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Game;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function show(Post $post)
    {
        // Eager load comments and nested comments (level 2) with their users
        $post->load(['user', 'game', 'category', 'tags']);
        
        $comments = $post->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->withCount('votes')
            ->get();

        // Increment views
        $viewed = session('viewed_posts', []);
        if (!in_array($post->id, $viewed)) {
            $post->increment('views');
            session()->push('viewed_posts', $post->id);
        }

        return view('posts.show', compact('post', 'comments'));
    }

    public function create(Request $request)
    {
        $selectedGame = null;
        if ($request->filled('game')) {
            $selectedGame = Game::findOrFail($request->game);
        }
        $games = Game::all();
        return view('posts.create', compact('games', 'selectedGame'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'type' => 'required|in:help,discussion',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'tags' => 'nullable|string',
            'is_spoiler' => 'nullable|boolean',
        ]);

        $post = new Post();
        $post->game_id = $validated['game_id'];
        $post->type = $validated['type'];
        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->is_spoiler = $request->boolean('is_spoiler');
        $post->user_id = auth()->id();
        $post->save();

        // Handle Tags (Comma separated list)
        if ($request->filled('tags')) {
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $slug = Str::slug($name);
                $tag = Tag::firstOrCreate(
                    ['game_id' => $post->game_id, 'slug' => $slug],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('posts.show', $post->id)->with('success', 'Post created successfully!');
    }

    public function edit(Post $post)
    {
        $this->authorizeOwner($post);
        $games = Game::all();
        return view('posts.edit', compact('post', 'games'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorizeOwner($post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'tags' => 'nullable|string',
            'is_spoiler' => 'nullable|boolean',
        ]);

        $post->title = $validated['title'];
        $post->body = $validated['body'];
        $post->is_spoiler = $request->boolean('is_spoiler');
        $post->save();

        if ($request->has('tags')) {
            $tagNames = array_filter(array_map('trim', explode(',', $request->tags)));
            $tagIds = [];
            foreach ($tagNames as $name) {
                $slug = Str::slug($name);
                $tag = Tag::firstOrCreate(
                    ['game_id' => $post->game_id, 'slug' => $slug],
                    ['name' => $name]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }

        return redirect()->route('posts.show', $post->id)->with('success', 'Post updated successfully!');
    }

    public function destroy(Post $post)
    {
        $this->authorizeOwner($post);
        $game = $post->game;
        $post->delete();

        return redirect()->route('games.show', $game->slug)->with('success', 'Post deleted successfully!');
    }

    public function markSolved(Request $request, Post $post)
    {
        if (auth()->id() !== $post->user_id) {
            abort(403, 'Only the author can mark this post as solved.');
        }

        $validated = $request->validate([
            'comment_id' => 'required|exists:comments,id',
        ]);

        $comment = $post->comments()->findOrFail($validated['comment_id']);
        
        // Reset previously accepted comments
        $post->comments()->where('is_accepted', true)->update(['is_accepted' => false]);

        $comment->is_accepted = true;
        $comment->save();

        $post->is_solved = true;
        $post->save();

        return redirect()->route('posts.show', $post->id)->with('success', 'Help request solved! Thank you.');
    }

    protected function authorizeOwner(Post $post)
    {
        if (auth()->id() !== $post->user_id && auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
            abort(403, 'Unauthorized action.');
        }
    }
}
