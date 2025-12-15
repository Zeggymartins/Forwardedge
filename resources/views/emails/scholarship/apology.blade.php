@php
    $name = $recipientName ? ucfirst($recipientName) : 'there';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>We’re still reviewing your scholarship application</title>
</head>
<body style="margin:0;padding:0;background:#f8fafc;font-family:'Helvetica Neue',Arial,sans-serif;color:#111827;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="620" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,0.08);">
                <tr>
                    <td style="padding:28px 32px;background:linear-gradient(135deg,#0f172a,#111c44);color:#e5edff;text-align:center;">
                        <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge" width="140" style="display:block;margin:0 auto 12px;">
                        <h1 style="margin:0;font-size:22px;color:#fff;">We’re still reviewing your application</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;color:#111827;font-size:15px;line-height:1.7;">
                        <p style="margin:0 0 16px;">Hi {{ $name }},</p>
                        <p style="margin:0 0 16px;">
                            You may have received an incorrect “rejected” email from us. Please ignore that message—your scholarship application is still under review.
                        </p>
                        <p style="margin:0 0 16px;">
                            We’re re-running our screening manually to ensure fairness. We’ll email you as soon as a decision is made.
                        </p>
                        <p style="margin:0 0 16px;">
                            If you have any questions, just reply to this email and our team will help.
                        </p>
                        <p style="margin:0;">Thank you for your patience,<br>Forward Edge Team</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
