<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
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
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            padding: 30px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .user-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            width: 120px;
        }
        .detail-value {
            color: #333;
            flex: 1;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #667eea;
            margin-bottom: 10px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Profile</h1>

        @if(session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="user-details">
            <div class="profile-header">
                @if ($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="Avatar of {{ $user->name }}" class="avatar">
                @else
                    <div class="avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center; color:white; font-weight:600;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div><strong>{{ $user->name }}</strong></div>
                <div style="color:#666; font-size:14px;">{{ $user->email }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">ID:</div>
                <div class="detail-value">{{ $user->id }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Bio:</div>
                <div class="detail-value">{{ $user->bio ?: 'No bio provided.' }}</div>
            </div>
               <div class="detail-row">
                <div class="detail-label">Phone:</div>
                <div class="detail-value">{{ $user->phone }}</div>
            </div>
         
            <div class="detail-row">
                <div class="detail-label">Created At:</div>
                <div class="detail-value">{{ $user->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Updated At:</div>
                <div class="detail-value">{{ $user->updated_at->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>

        <div class="btn-group">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit User</a>
            <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete User</button>
            </form>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</body>
</html>

