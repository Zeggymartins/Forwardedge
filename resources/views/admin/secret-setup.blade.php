<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Secret Admin Setup - Forward Edge</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 8px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .current-admin {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-left: 4px solid #667eea;
        }

        .current-admin h3 {
            font-size: 14px;
            color: #667eea;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .current-admin p {
            margin: 4px 0;
            color: #555;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e4e8;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 4px solid #ffc107;
        }

        .lock-icon {
            text-align: center;
            margin-bottom: 20px;
            font-size: 48px;
        }

        .hint {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="lock-icon">🔐</div>

        <div class="header">
            <h1>Secret Admin Setup</h1>
            <p>Secure administrative access configuration</p>
        </div>

        @if(session('success'))
            <div class="success">
                {{ session('success') }}
            </div>
        @endif

        @if($admin)
            <div class="current-admin">
                <h3>Current Admin</h3>
                <p><strong>Name:</strong> {{ $admin->name }}</p>
                <p><strong>Email:</strong> {{ $admin->email }}</p>
                <p class="hint" style="margin-top: 8px;">Only ONE admin can exist. Updating will modify the current admin.</p>
            </div>
        @else
            <div class="warning">
                <strong>⚠️ No admin exists.</strong> Use this form to create the first admin account.
            </div>
        @endif

        <form method="POST" action="{{ route('admin.secret.update') }}">
            @csrf

            <div class="form-group">
                <label for="secret_key">Secret Key *</label>
                <input type="password" id="secret_key" name="secret_key" required autofocus>
                @error('secret_key')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="hint">Enter the secret key from your .env file</div>
            </div>

            <div class="form-group">
                <label for="name">Admin Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $admin->name ?? '') }}" required>
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Admin Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $admin->email ?? '') }}" required>
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">New Password *</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
                <div class="hint">Minimum 8 characters</div>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn">
                {{ $admin ? 'Update Admin Credentials' : 'Create Admin Account' }}
            </button>
        </form>
    </div>
</body>
</html>
