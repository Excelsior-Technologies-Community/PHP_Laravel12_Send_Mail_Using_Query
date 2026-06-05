{{-- resources/views/emails/queryMail.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $details['subject'] }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f5f5f5;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        
        .email-header {
            background-color: #4a90e2;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 18px;
            font-weight: 500;
        }
        
        .email-body {
            padding: 30px;
            color: #333333;
            line-height: 1.6;
        }
        
        .email-body h2 {
            color: #333333;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .email-body p {
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .email-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px;
            font-size: 11px;
            color: #888888;
            border-top: 1px solid #e0e0e0;
        }
        
        .button {
            display: inline-block;
            background-color: #4a90e2;
            color: white !important;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
            font-size: 14px;
        }
        
        @media screen and (max-width: 600px) {
            .email-container {
                width: 95% !important;
                margin: 10px auto;
            }
            
            .email-body {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            {{ $details['subject'] }}
        </div>
        <div class="email-body">
            <h2>Hello {{ $details['name'] }},</h2>
            <p>{{ $details['message'] }}</p>
            <p>Thank you for your interest.</p>
            <a href="#" class="button">Learn More</a>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} Your Company. All rights reserved.
        </div>
    </div>
</body>
</html>