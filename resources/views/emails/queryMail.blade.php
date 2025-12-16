<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $details['subject'] }}</title>
    <style>
        /* General reset */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 22px;
        }

        .body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }

        .body h2 {
            color: #333333;
            margin-bottom: 15px;
        }

        .body p {
            margin-bottom: 15px;
        }

        .footer {
            background-color: #f4f4f7;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #888888;
        }

        .button {
            display: inline-block;
            background-color: #4CAF50;
            color: white !important;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        @media screen and (max-width: 600px) {
            .container {
                width: 90% !important;
                margin: 20px auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            {{ $details['subject'] }}
        </div>
        <div class="body">
            <h2>Hello {{ $details['name'] }},</h2>
            <p>{{ $details['message'] }}</p>
            <p>Thank you!</p>
            <a href="#" class="button">Visit Our Website</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </div>
    </div>
</body>
</html>
