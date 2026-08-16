<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');

        $posts = Post::with(['user', 'category'])
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->latest()
            ->get();

        $categories = Category::orderBy('id')->get();

        return view('posts.index', compact('posts', 'categories', 'categoryId'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        $categories = Category::orderBy('id')->get();

        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:140',
            'category_id' => 'required|exists:categories,id',
        ], [
            'content.max' => '本文は140文字以内で入力してください。',
        ]);

        $post->update($validated);

        return redirect()->route('posts.index');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect()->route('posts.index');
    }
}
