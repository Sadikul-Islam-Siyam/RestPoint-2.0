<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Game;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RestPointIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database roles, games, categories, badges, etc.
        $this->seed();
    }

    /**
     * 1. Test auto-categorization based on keywords.
     */
    public function test_post_auto_categorization(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $game = Game::first(); // seeded game

        $post = Post::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'type' => 'discussion',
            'title' => 'How to beat the final boss easily',
            'body' => 'Use this strategy to kill the phase 2 boss.',
        ]);

        // Category should be automatically set to "Boss Strategy"
        $this->assertNotNull($post->category_id);
        $category = Category::find($post->category_id);
        $this->assertEquals('Boss Strategy', $category->name);
    }

    /**
     * 2. Test AJAX follow toggles for games and users.
     */
    public function test_ajax_follow_interactions(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $targetUser = User::factory()->create(['role' => 'member']);
        $game = Game::first();

        // 1. Follow Game
        $response = $this->actingAs($user)->postJson(route('follow.game'), [
            'game_id' => $game->id,
        ]);
        $response->assertJson(['success' => true, 'following' => true]);
        $this->assertTrue($user->followedGames()->where('games.id', $game->id)->exists());

        // 2. Follow User
        $response = $this->actingAs($user)->postJson(route('follow.user'), [
            'user_id' => $targetUser->id,
        ]);
        $response->assertJson(['success' => true, 'following' => true]);
        $this->assertTrue($user->following()->where('users.id', $targetUser->id)->exists());
    }

    /**
     * 3. Test mentions and real-time notifications.
     */
    public function test_mentions_and_notifications(): void
    {
        $author = User::factory()->create(['role' => 'member']);
        $mentionedUser = User::factory()->create(['role' => 'member', 'username' => 'lorekeeper']);
        $game = Game::first();

        $post = Post::create([
            'user_id' => $author->id,
            'game_id' => $game->id,
            'type' => 'discussion',
            'title' => 'A post about lore',
            'body' => 'Talking to @lorekeeper about story.',
        ]);

        $responseComment = $this->actingAs($author)->post(route('comments.store'), [
            'post_id' => $post->id,
            'body' => 'Check this comment @lorekeeper',
        ]);
        $responseComment->assertRedirect();

        // Assert mention was created
        $comment = Comment::latest('id')->first();
        $this->assertNotNull($comment);
        $this->assertDatabaseHas('mentions', [
            'mentioned_user_id' => $mentionedUser->id,
            'comment_id' => $comment->id,
        ]);

        // Assert notification exists
        $this->assertDatabaseHas('notifications', [
            'user_id' => $mentionedUser->id,
            'type' => 'mention',
        ]);

        // Assert notification polling count is correct
        $response = $this->actingAs($mentionedUser)->getJson(route('notifications.ajax'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['count', 'notifications']);
        $this->assertEquals(1, $response->json('count'));
    }

    /**
     * 4. Test anti-spam rate throttling for posts.
     */
    public function test_anti_spam_throttling(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $game = Game::first();

        // First post succeeds
        $response1 = $this->actingAs($user)->post(route('posts.store'), [
            'game_id' => $game->id,
            'type' => 'discussion',
            'title' => 'First Thread',
            'body' => 'Content here',
        ]);
        $response1->assertRedirect();

        // Second post within 60s is throttled
        $response2 = $this->actingAs($user)->post(route('posts.store'), [
            'game_id' => $game->id,
            'type' => 'discussion',
            'title' => 'Second Thread',
            'body' => 'Content here again',
        ]);
        $response2->assertSessionHasErrors(['body']);
    }

    /**
     * 5. Test search filters.
     */
    public function test_search_filtering(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $game = Game::first();

        $post = Post::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'type' => 'help',
            'title' => 'Finding secret talisman item location',
            'body' => 'Talisman location guide.',
        ]);

        // Search with query "talisman"
        $response = $this->get(route('search', ['q' => 'talisman', 'type' => 'help']));
        $response->assertStatus(200);
        $response->assertSee('Finding secret talisman item location');
    }

    /**
     * 6. Test moderator report flagging and pinning.
     */
    public function test_flag_moderation_queue_and_pinning(): void
    {
        $user = User::factory()->create(['role' => 'member']);
        $mod = User::factory()->create(['role' => 'moderator']);
        $game = Game::first();

        $post = Post::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'type' => 'discussion',
            'title' => 'Offending Post',
            'body' => 'Bad content',
        ]);

        // 1. Submit report flag
        $response = $this->actingAs($user)->postJson(route('reports.flag'), [
            'reportable_id' => $post->id,
            'reportable_type' => 'post',
            'reason' => 'Inappropriate behavior',
        ]);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('reports', [
            'reportable_id' => $post->id,
            'reportable_type' => Post::class,
            'reason' => 'Inappropriate behavior',
            'status' => 'pending',
        ]);

        // 2. View queue as moderator
        $queueResponse = $this->actingAs($mod)->get(route('moderation.index'));
        $queueResponse->assertStatus(200);
        $queueResponse->assertSee('Inappropriate behavior');

        // 3. Pin post as moderator
        $pinResponse = $this->actingAs($mod)->from('/posts/' . $post->id)->post(route('posts.pin', $post->id));
        $pinResponse->assertRedirect();
        $this->assertTrue($post->fresh()->is_pinned);

        // 4. Resolve report (deletes the post)
        $report = Report::first();
        $resolveResponse = $this->actingAs($mod)->post(route('moderation.resolve', $report->id));
        $resolveResponse->assertRedirect();
        $this->assertNull(Post::find($post->id)); // Post should be deleted
        $this->assertEquals('resolved', $report->fresh()->status);
    }
}
