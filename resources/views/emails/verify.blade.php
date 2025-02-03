<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #333333;
        }
        p {
            color: #666666;
            font-size: 16px;
        }
        .verify-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #ffffff;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            text-decoration: none;
        }
        .verify-button:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>Email Verification</h1>
        <p>Thank you for registering! Please click the button below to verify your email address and complete your registration.</p>
        <a href="{{ $verification_url }}" class="verify-button">Verify Email</a>
        <p class="footer">If you did not create an account, please ignore this email.</p>
    </div>
</body>
</html>
