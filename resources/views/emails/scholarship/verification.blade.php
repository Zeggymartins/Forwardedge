@php
    $isLink = $type === 'link';
    $isApproved = $type === 'verified';
    $isRejected = $type === 'resubmit';
    $recipient = ucfirst(strtolower($user->name ?? 'Applicant'));
    $verificationUrl = $isLink ? route('verify.show', $user->verification_token) : null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forward Edge Identity Verification</title>
</head>
<body style="margin:0;padding:0;background-color:#050b1f;color:#e2e8f0;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#050b1f;padding:32px 0;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#0c1530;border-radius:28px;padding:40px 48px;box-shadow:0 25px 70px rgba(0,0,0,0.35);">
                <tr>
                    <td style="text-align:center;padding-bottom:24px;">
                        <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Forward Edge" width="140" style="display:block;margin:0 auto 16px;">
                        <h1 style="margin:0;font-size:26px;color:#f8fafc;">
                            @if($isLink)
                                Complete Your Verification
                            @elseif($isApproved)
                                Verification Approved!
                            @elseif($isRejected)
                                Verification Update
                            @endif
                        </h1>
                        <p style="margin:8px 0 0;color:#cbd5f5;font-size:16px;">
                            Forward Edge Academy
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="color:#d6ddff;font-size:16px;line-height:1.7;">
                        <p style="margin:0 0 18px;">Hi {{ $recipient }},</p>

                        @if($isLink)
                            <p style="margin:0 0 18px;">
                                Thanks for completing your payment. To protect your account and course access,
                                we need to verify your identity. This is a quick, one-time step.
                            </p>
                            <p style="margin:0 0 18px;">
                                Please click the button below to complete your identity verification:
                            </p>
                            <p style="margin:0 0 24px;text-align:center;">
                                <a href="{{ $verificationUrl }}"
                                   style="display:inline-block;padding:14px 28px;border-radius:999px;background:linear-gradient(135deg,#0891b2,#6366f1);color:#fff;text-decoration:none;font-weight:600;">
                                    Verify My Identity
                                </a>
                            </p>
                            <p style="margin:0 0 18px;font-size:14px;color:#94a3b8;">
                                This link will expire in 7 days. If you have any issues, please contact our support team.
                            </p>
                        @elseif($isApproved)
                            <p style="margin:0 0 18px;">
                                Great news! Your identity verification is <strong style="color:#22c55e;">complete</strong>.
                                You now have full access to your course materials.
                            </p>
                            @if($user->enrollment_id)
                                <div style="background:linear-gradient(135deg,#0891b2,#6366f1);border-radius:12px;padding:20px;margin:0 0 24px;text-align:center;">
                                    <p style="margin:0 0 8px;font-size:14px;color:#e2e8f0;">Your Enrollment ID</p>
                                    <p style="margin:0;font-size:28px;font-weight:bold;color:#fff;letter-spacing:2px;">
                                        {{ $user->enrollment_id }}
                                    </p>
                                </div>
                                <p style="margin:0 0 18px;font-size:14px;color:#94a3b8;">
                                    Please keep this Enrollment ID safe. You may need it for identification purposes during the program.
                                </p>
                            @endif
                        @elseif($isRejected)
                            <p style="margin:0 0 18px;">
                                We need a quick update to complete your verification.
                            </p>
                            @if($user->verification_notes)
                                <div style="background:rgba(239,68,68,0.1);border-left:4px solid #ef4444;padding:16px;margin:0 0 18px;border-radius:0 8px 8px 0;">
                                    <p style="margin:0;color:#fca5a5;"><strong>What needs attention:</strong></p>
                                    <p style="margin:8px 0 0;color:#d6ddff;">{{ $user->verification_notes }}</p>
                                </div>
                            @endif
                            <p style="margin:0 0 18px;">
                                Please use your verification link to resubmit your documents. If you need help,
                                reply to this email and our team will assist you.
                            </p>
                        @endif

                        <p style="margin:0 0 32px;">Stay sharp,<br>Forward Edge Team</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
