<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .container { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px; }
        h1 { color: #1f2937; margin-bottom: 1.5rem; font-size: 1.5rem; text-align: center; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; color: #374151; margin-bottom: 0.5rem; font-weight: 500; }
        input[type="email"], input[type="password"] { width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 4px; font-size: 1rem; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        button { width: 100%; padding: 0.75rem; background: #3b82f6; color: white; border: none; border-radius: 4px; font-size: 1rem; cursor: pointer; margin-top: 1rem; }
        button:hover { background: #2563eb; }
        .error { color: #dc2626; font-size: 0.875rem; margin-top: 0.25rem; }
        .links { text-align: center; margin-top: 1rem; }
        .links a { color: #3b82f6; text-decoration: none; }
        .links a:hover { text-decoration: underline; }
        .test-accounts { background: #eff6ff; border: 1px solid #bfdbfe; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; font-size: 0.875rem; }
        .test-accounts h3 { color: #1e40af; margin-bottom: 0.5rem; font-size: 0.875rem; }
        .test-accounts p { color: #1e40af; margin: 0.25rem 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>ログイン</h1>

        <div class="test-accounts">
            <h3>テストアカウント</h3>
            <p><strong>ユーザーA:</strong> usera@example.com / password</p>
            <p><strong>ユーザーB:</strong> userb@example.com / password</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">ログイン</button>
        </form>

        <div class="links">
            <a href="{{ route('register') }}">アカウントを作成</a>
        </div>
    </div>
</body>
</html>
