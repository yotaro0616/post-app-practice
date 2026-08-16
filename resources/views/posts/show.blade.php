<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; min-height: 100vh; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; }
        .back { display: inline-block; color: #3b82f6; text-decoration: none; font-size: 0.875rem; margin-bottom: 1rem; }
        .back:hover { text-decoration: underline; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); margin-bottom: 1rem; }
        h1 { color: #1f2937; font-size: 1.5rem; margin-bottom: 0.5rem; }
        h2 { color: #1f2937; font-size: 1.25rem; margin-bottom: 1rem; }
        .meta { color: #6b7280; font-size: 0.875rem; margin-bottom: 1rem; }
        .content { color: #374151; margin-bottom: 1.5rem; white-space: pre-wrap; }
        .actions { display: flex; gap: 0.5rem; }
        .actions a, .actions button { padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.875rem; text-decoration: none; cursor: pointer; }
        .btn-edit { background: #3b82f6; color: white; border: none; }
        .btn-edit:hover { background: #2563eb; }
        .btn-delete { background: #ef4444; color: white; border: none; }
        .btn-delete:hover { background: #dc2626; }
        .comment { border-top: 1px solid #e5e7eb; padding: 1rem 0; display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; }
        .comment:first-of-type { border-top: none; padding-top: 0; }
        .comment .body { color: #374151; white-space: pre-wrap; }
        .comment .meta { margin-bottom: 0.25rem; }
        .empty { color: #6b7280; padding: 1rem 0; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; color: #374151; font-size: 0.875rem; margin-bottom: 0.25rem; }
        textarea { width: 100%; min-height: 6rem; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 4px; font-family: inherit; font-size: 1rem; resize: vertical; }
        .error { color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; }
        .btn-submit { background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.875rem; cursor: pointer; }
        .btn-submit:hover { background: #2563eb; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('posts.index') }}" class="back">← 投稿一覧へ戻る</a>

        <div class="card">
            <h1>{{ $post->title }}</h1>
            <p class="meta">投稿者: {{ $post->user->name }}／カテゴリ: {{ $post->category->name }}</p>
            <p class="content">{{ $post->content }}</p>
            <div class="actions">
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="btn-edit">編集</a>
                @endcan
                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">削除</button>
                    </form>
                @endcan
            </div>
        </div>

        <div class="card">
            <h2>コメント</h2>

            @if ($post->comments->isEmpty())
                <p class="empty">まだコメントはありません。</p>
            @else
                @foreach ($post->comments as $comment)
                    <div class="comment">
                        <div>
                            <p class="meta">{{ $comment->user->name }}　{{ $comment->created_at->format('Y-m-d H:i') }}</p>
                            <p class="body">{{ $comment->content }}</p>
                        </div>
                        @can('delete', $comment)
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">削除</button>
                            </form>
                        @endcan
                    </div>
                @endforeach
            @endif
        </div>

        <div class="card">
            <form action="{{ route('comments.store', $post) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="content">コメントを書く</label>
                    {{-- 空のときにサーバー側のエラーを出したいので、HTML の required は付けない --}}
                    <textarea id="content" name="content">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-submit">コメントする</button>
            </form>
        </div>
    </div>
</body>
</html>
