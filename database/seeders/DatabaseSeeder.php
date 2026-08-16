<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // カテゴリ
        $notice = Category::create(['name' => 'お知らせ']);
        $techNote = Category::create(['name' => '技術メモ']);
        Category::create(['name' => '雑記']);

        // ユーザーA
        $userA = User::create([
            'name' => 'ユーザーA',
            'email' => 'usera@example.com',
            'password' => Hash::make('password'),
        ]);

        // ユーザーB
        $userB = User::create([
            'name' => 'ユーザーB',
            'email' => 'userb@example.com',
            'password' => Hash::make('password'),
        ]);

        // ユーザーAの投稿
        $postA1 = Post::create([
            'user_id' => $userA->id,
            'category_id' => $notice->id,
            'title' => 'ユーザーAの投稿1',
            'content' => 'これはユーザーAが作成した投稿です。ユーザーAのみが編集・削除できるはずです。',
        ]);

        Post::create([
            'user_id' => $userA->id,
            'category_id' => $techNote->id,
            'title' => 'ユーザーAの投稿2',
            'content' => 'ユーザーAの2つ目の投稿です。',
        ]);

        // ユーザーBの投稿
        Post::create([
            'user_id' => $userB->id,
            'category_id' => $techNote->id,
            'title' => 'ユーザーBの投稿1',
            'content' => 'これはユーザーBが作成した投稿です。ユーザーBのみが編集・削除できるはずです。',
        ]);

        Post::create([
            'user_id' => $userB->id,
            'category_id' => $notice->id,
            'title' => 'ユーザーBの投稿2',
            'content' => 'ユーザーBの2つ目の投稿です。',
        ]);

        // ユーザーAの投稿1へのコメント
        // 他人のコメントは消せないことを確認できるよう、A・B 双方のコメントを入れる
        Comment::create([
            'post_id' => $postA1->id,
            'user_id' => $userB->id,
            'content' => 'なるほど、参考になりました。',
        ]);

        Comment::create([
            'post_id' => $postA1->id,
            'user_id' => $userA->id,
            'content' => 'ありがとうございます。',
        ]);
    }
}
