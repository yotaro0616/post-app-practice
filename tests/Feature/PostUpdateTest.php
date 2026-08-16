<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostUpdateTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create(['name' => 'テストカテゴリ']);
        $this->post = Post::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'title' => '元のタイトル',
            'content' => '元の本文',
        ]);
    }

    public function test_本文が140字ちょうどなら更新できる(): void
    {
        $content = str_repeat('あ', 140);

        $response = $this->actingAs($this->user)
            ->put(route('posts.update', $this->post), [
                'title' => '更新後のタイトル',
                'content' => $content,
                'category_id' => $this->category->id,
            ]);

        $response->assertRedirect(route('posts.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('posts', [
            'id' => $this->post->id,
            'content' => $content,
        ]);
    }

    public function test_本文が141字ならエラーになり保存されない(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('posts.update', $this->post), [
                'title' => '更新後のタイトル',
                'content' => str_repeat('あ', 141),
                'category_id' => $this->category->id,
            ]);

        $response->assertSessionHasErrors([
            'content' => '本文は140文字以内で入力してください。',
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $this->post->id,
            'title' => '元のタイトル',
            'content' => '元の本文',
        ]);
    }
}
