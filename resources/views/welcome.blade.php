<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>認可スターター</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; text-align: center; }
        h1 { color: #1f2937; margin-bottom: 1rem; font-size: 1.5rem; }
        p { color: #6b7280; margin-bottom: 1.5rem; }
        .user-info { background: #eff6ff; border: 1px solid #bfdbfe; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; }
        .user-info p { color: #1e40af; margin-bottom: 0; }
        .links { display: flex; flex-direction: column; gap: 0.75rem; }
        .links a { display: block; padding: 0.75rem 1.5rem; border-radius: 4px; text-decoration: none; font-size: 1rem; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background: #d1d5db; }
        .btn-danger { background: #ef4444; color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 4px; font-size: 1rem; cursor: pointer; width: 100%; }
        .btn-danger:hover { background: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <h1>認可スターター</h1>

        @auth
            <div class="user-info">
                <p><strong>ログイン中:</strong> {{ auth()->user()->name }}</p>
            </div>
            <div class="links">
                <a href="{{ route('posts.index') }}" class="btn-primary">投稿一覧へ</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-danger">ログアウト</button>
                </form>
            </div>
        @else
            <p>認可（Policy）を学ぶためのスターターキットです。</p>
            <div class="links">
                <a href="{{ route('login') }}" class="btn-primary">ログイン</a>
                <a href="{{ route('register') }}" class="btn-secondary">新規登録</a>
            </div>
        @endauth
    </div>
</body>
</html>
