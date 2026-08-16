<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_本文を入れて送るとコメントが保存され詳細画面へ戻る(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $post), ['content' => 'はじめてのコメント']);

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'content' => 'はじめてのコメント',
        ]);
    }

    public function test_送ったコメントが詳細画面の一覧に増える(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post(route('comments.store', $post), ['content' => '一覧に増えるはずのコメント']);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertSee('一覧に増えるはずのコメント');
        $this->assertSame(1, $post->comments()->count());
    }

    public function test_本文が空だとエラーになり保存されない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $post), ['content' => '']);

        $response->assertSessionHasErrors(['content' => 'コメントを入力してください。']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_本文が256文字だとエラーになり保存されない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $post), ['content' => str_repeat('あ', 256)]);

        $response->assertSessionHasErrors(['content' => 'コメントは255文字以内で入力してください。']);
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_本文が255文字なら保存される(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('comments.store', $post), ['content' => str_repeat('あ', 255)]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_未ログインではコメントを送れない(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('comments.store', $post), ['content' => 'ログインしていない']);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_自分のコメントは削除できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertRedirect(route('posts.show', $post));
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_他人のコメントは削除できず403になる(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $other->id]);

        $response = $this->actingAs($user)->delete(route('comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_投稿の書き手でも他人のコメントは削除できない(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($author)->delete(route('comments.destroy', $comment));

        $response->assertForbidden();
        $this->assertDatabaseHas('comments', ['id' => $comment->id]);
    }

    public function test_削除ボタンは自分のコメントにだけ表示される(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create();

        $mine = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $user->id]);
        $theirs = Comment::factory()->create(['post_id' => $post->id, 'user_id' => $other->id]);

        $response = $this->actingAs($user)->get(route('posts.show', $post));

        $response->assertSee(route('comments.destroy', $mine));
        $response->assertDontSee(route('comments.destroy', $theirs));
    }

    public function test_投稿を削除するとコメントも消える(): void
    {
        $author = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $author->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $this->actingAs($author)->delete(route('posts.destroy', $post));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }
}
