@php($firstName = ucfirst(strtolower($name ?? 'friend')))
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome to Forward Edge</title>
</head>
<body style="margin:0;padding:0;background-color:#0b1224;color:#f8fafc;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#0b1224;padding:32px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#101a35;border-radius:24px;padding:40px 50px;box-shadow:0 20px 60px rgba(0,0,0,0.35);">
                <tr>
                    <td style="text-align:center;padding-bottom:24px;">
                        <img src="{{ asset('frontend/assets/images/fav.png') }}" alt="Forward Edge" width="56" height="56" style="display:block;margin:0 auto 16px;">
                        <h1 style="margin:0;font-size:26px;color:#f8fafc;">Welcome to Forward Edge</h1>
                    </td>
                </tr>
                <tr>
                    <td style="color:#e2e8f0;font-size:16px;line-height:1.7;">
                        <p style="margin:0 0 18px;">Hi {{ $firstName }},</p>
                        <p style="margin:0 0 18px;">Thanks for raising your hand 🙌 – you're officially on the Forward Edge newsletter and will be the first to hear about new cohorts, scholarships, and behind-the-scenes drops from Bootcamp 5.0.</p>
                        <p style="margin:0 0 18px;">Over the next few days we’ll send you the stories, strategies, and openings that help our community break into cybersecurity. Keep an eye on your inbox so you don't miss the next update.</p>
                        <p style="margin:0 0 32px;">Stay ready.<br>– Forward Edge Team</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <a href="{{ url('/') }}" style="display:inline-block;padding:14px 28px;border-radius:999px;background:linear-gradient(135deg,#0891b2,#6366f1);color:#fff;text-decoration:none;font-weight:600;">Explore Forward Edge</a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
