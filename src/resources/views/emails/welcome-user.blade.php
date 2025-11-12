<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to {{ $appName }}</title>
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            background-color: #f7f9fc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            margin: 40px auto;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #2563eb;
            color: #fff;
            text-align: center;
            padding: 25px 20px;
        }
        .content {
            padding: 30px;
            color: #333;
        }
        .content p {
            margin-bottom: 16px;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
            font-weight: 600;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #777;
            padding: 15px 10px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Welcome to {{ $appName }} 🎉</h2>
    </div>
    <div class="content">
        <p>Hi <strong>{{ $userName }}</strong>,</p>
        <p>We’re thrilled to have you on board! Your account has been successfully created with the email:</p>
        <p><strong>{{ $userEmail }}</strong></p>

        <p>Explore your new account, manage your profile, and start using all the great features of <strong>{{ $appName }}</strong>.</p>

        <a href="{{ config('app.url') }}" class="btn">Go to {{ $appName }}</a>

        <p>Thanks for joining us!
        <br>— The {{ $appName }} Team</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>
