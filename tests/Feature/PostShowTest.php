<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_詳細画面に投稿の本文とコメント一覧が表示される(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['content' => 'これは投稿の本文です。']);
        $commenter = User::factory()->create(['name' => 'コメントした人']);
        Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $commenter->id,
            'content' => 'これはコメントです。',
        ]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertOk();
        $response->assertSee($post->title);
        $response->assertSee('これは投稿の本文です。');
        $response->assertSee($post->user->name);
        $response->assertSee($post->category->name);
        $response->assertSee('コメントした人');
        $response->assertSee('これはコメントです。');
    }

    public function test_コメントは古い順に並ぶ(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Comment::factory()->create([
            'post_id' => $post->id,
            'content' => '先に書いたコメント',
            'created_at' => now()->subDay(),
        ]);
        Comment::factory()->create([
            'post_id' => $post->id,
            'content' => '後から書いたコメント',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertSeeInOrder(['先に書いたコメント', '後から書いたコメント']);
    }

    public function test_コメントが0件のときは案内文が表示される(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertSee('まだコメントはありません。');
    }

    public function test_未ログインで詳細画面を開くとログイン画面へリダイレクトされる(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertRedirect(route('login'));
    }

    public function test_一覧画面から詳細画面へのリンクがある(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee(route('posts.show', $post));
    }
}
