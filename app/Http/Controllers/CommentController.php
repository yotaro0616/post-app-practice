<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:255',
        ], [
            'content.required' => 'コメントを入力してください。',
            'content.max' => 'コメントは255文字以内で入力してください。',
        ]);

        $post->comments()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return redirect()->route('posts.show', $post);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        $postId = $comment->post_id;

        $comment->delete();

        return redirect()->route('posts.show', $postId);
    }
}
