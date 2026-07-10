<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'body' => 'required|string',
        ]);

        $post = Post::findOrFail($validated['post_id']);

        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->user_id = auth()->id();
        $comment->body = $validated['body'];

        // Enforce 2-level nesting: if parent has a parent, flatten to parent's parent
        if ($validated['parent_id']) {
            $parentComment = Comment::findOrFail($validated['parent_id']);
            if ($parentComment->parent_id) {
                $comment->parent_id = $parentComment->parent_id;
            } else {
                $comment->parent_id = $parentComment->id;
            }
        }

        $comment->save();

        return redirect()->route('posts.show', $post->id)->with('success', 'Comment posted!');
    }

    public function update(Request $request, Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'body' => 'required|string',
        ]);

        $comment->body = $validated['body'];
        $comment->save();

        return redirect()->route('posts.show', $comment->post_id)->with('success', 'Comment updated!');
    }

    public function destroy(Comment $comment)
    {
        if (auth()->id() !== $comment->user_id && auth()->user()->role !== 'admin' && auth()->user()->role !== 'moderator') {
            abort(403, 'Unauthorized action.');
        }

        $postId = $comment->post_id;
        $comment->delete();

        return redirect()->route('posts.show', $postId)->with('success', 'Comment deleted!');
    }
}
