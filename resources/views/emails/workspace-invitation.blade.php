<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Workspace Invitation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Workspace Invitation</h1>
    </div>
    <div class="content">
        <p>Hello {{ $user->name }},</p>

        <p>You have been invited to join the workspace <strong>{{ $workspace->name }}</strong>.</p>

        @if($workspace->description)
        <p><em>{{ $workspace->description }}</em></p>
        @endif

        <p>You can now access this workspace and collaborate with your team members.</p>

        <p>Best regards,<br>{{ config('app.name') }} Team</p>
    </div>
    <div class="footer">
<p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>

