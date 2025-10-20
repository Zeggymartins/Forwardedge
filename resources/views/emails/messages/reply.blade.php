<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f3f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { background-color: #28a745; color: #fff; text-align: center; padding: 30px 20px; }
        .header img { max-width: 120px; margin-bottom: 10px; }
        .header h2 { margin: 0; font-size: 26px; }
        .content { padding: 25px 20px; color: #333; line-height: 1.6; }
        .btn { display: inline-block; background-color: #28a745; color: #fff; text-decoration: none; padding: 12px 25px; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .btn:hover { background-color: #1e7e34; }
        .highlight { background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0; }
        .footer { background-color: #f4f4f4; text-align: center; font-size: 13px; color: #777; padding: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Company Logo">
            <h2>Hello,</h2>
        </div>
<p>{!! nl2br(e($reply->body)) !!}</p>
<hr>
<p style="color:#6c757d;font-size:12px">This email is in response to your message (ID: {{ $reply->message_id }}).</p>

            <div class="footer">
            &copy; {{ date('Y') }} ForwardEdge. All rights reserved.
        </div>
    </div>
</body>
</html>