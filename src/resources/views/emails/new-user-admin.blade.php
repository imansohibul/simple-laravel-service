<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New User Registration</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 40px auto;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #2563eb;
            color: white;
            text-align: center;
            padding: 20px 10px;
        }
        .content {
            padding: 25px 30px;
            color: #333;
        }
        .content p {
            margin: 0 0 12px;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #777;
            padding: 15px 10px;
            border-top: 1px solid #eee;
        }
        .highlight {
            color: #2563eb;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>👋 New User Registered</h2>
    </div>
    <div class="content">
        <p>Hello Admin,</p>
        <p>A new user has just registered on the platform.</p>
        <p><strong>User Details:</strong></p>
        <ul>
            <li><strong>Name:</strong> {{ $user->name }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Registered At:</strong> {{ $registrationTime }}</li>
        </ul>
        <p>You can view this user in your admin dashboard.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
