<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * コメント機能を足しても、一覧・編集・削除の動きが変わっていないことを確認するテスト。
 */
class PostTimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_一覧に本文が全文表示される(): void
    {
        $user = User::factory()->create();
        $content = str_repeat('あ', 200);
        Post::factory()->create(['content' => $content]);

        $response = $this->actingAs($user)->get(route('posts.index'));

        $response->assertOk();
        $response->assertSee($content);
    }

    public function test_一覧のカテゴリ絞り込みと件数表示が動く(): void
    {
        $user = User::factory()->create();
        $notice = Category::factory()->create(['name' => 'お知らせ']);
        $other = Category::factory()->create(['name' => '雑記']);

        Post::factory()->create(['category_id' => $notice->id, 'title' => 'お知らせの投稿']);
        Post::factory()->create(['category_id' => $other->id, 'title' => '雑記の投稿']);

        $response = $this->actingAs($user)
            ->get(route('posts.index', ['category_id' => $notice->id]));

        $response->assertOk();
        $response->assertSee('お知らせの投稿');
        $response->assertDontSee('雑記の投稿');
        $response->assertSee('1 件');
    }

    public function test_自分の投稿を編集できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => '編集後のタイトル',
            'content' => '編集後の本文',
            'category_id' => $post->category_id,
        ]);

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => '編集後のタイトル',
        ]);
    }

    public function test_他人の投稿は編集できない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->get(route('posts.edit', $post));

        $response->assertForbidden();
    }

    public function test_自分の投稿を削除できる(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_他人の投稿は削除できない(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}
