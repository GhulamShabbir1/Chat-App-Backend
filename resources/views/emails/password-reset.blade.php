<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button { display: inline-block; padding: 10px 20px; background-color: #3490dc; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p>Your password reset token is:</p>
        <h3>{{ $token }}</h3>
        <p>Use this token in the password reset form.</p>
        <p>If you did not request a password reset, no further action is required.</p>
    </div>
</body>
</html>
