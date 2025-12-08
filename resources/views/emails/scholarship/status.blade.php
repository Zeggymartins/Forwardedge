@php
    $application = $application->fresh(['course', 'schedule', 'user']);
    $isApproved = $status === 'approved';
    $isRejected = $status === 'rejected';
    $isPending = !$isApproved && !$isRejected;
    $courseTitle = $application->course->title ?? 'Forward Edge Academy Training';
    $schedule = $application->schedule;
    $startDate = $schedule?->start_date ? $schedule->start_date->format('M j, Y') : null;
    $endDate = $schedule?->end_date ? $schedule->end_date->format('M j, Y') : null;
    $recipient = ucfirst(strtolower($application->user->name ?? 'friend'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forward Edge Scholarship Update</title>
</head>
<body style="margin:0;padding:0;background-color:#050b1f;color:#e2e8f0;font-family:'Helvetica Neue',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#050b1f;padding:32px 0;">
    <tr>
        <td align="center">
            <table width="620" cellpadding="0" cellspacing="0" role="presentation" style="background-color:#0c1530;border-radius:28px;padding:40px 48px;box-shadow:0 25px 70px rgba(0,0,0,0.35);">
                <tr>
                    <td style="text-align:center;padding-bottom:24px;">
                        <img src="{{ asset('frontend/assets/images/logo/logo.png') }}" alt="Forward Edge" width="140" style="display:block;margin:0 auto 16px;">
                        <h1 style="margin:0;font-size:26px;color:#f8fafc;">
                            @if($isApproved)
                                Congratulations!
                            @elseif($isRejected)
                                Update on your application
                            @else
                                Thanks for applying
                            @endif
                        </h1>
                        <p style="margin:8px 0 0;color:#cbd5f5;font-size:16px;">
                            Forward Edge Cyber1000 Scholarship
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="color:#d6ddff;font-size:16px;line-height:1.7;">
                        <p style="margin:0 0 18px;">Hi {{ $recipient }},</p>

                        @if($isApproved)
                            <p style="margin:0 0 18px;">
                                We’re thrilled to let you know that your application for the <strong>{{ $courseTitle }}</strong> cohort
                                has been <strong>approved</strong>. You’ve secured a sponsored seat in our Cyber1000 experience.
                            </p>
                            @if($startDate || $endDate)
                                <p style="margin:0 0 18px;">
                                    <strong>Cohort timeline:</strong> {{ $startDate ?? 'TBA' }} – {{ $endDate ?? 'TBA' }}
                                </p>
                            @endif
                            <p style="margin:0 0 24px;">
                                Our enrollments team will reach out shortly with onboarding instructions, community invites,
                                and the resources you need to hit the ground running.
                            </p>
                        @elseif($isRejected)
                            <p style="margin:0 0 18px;">
                                Thank you for taking the time to apply for the <strong>{{ $courseTitle }}</strong> scholarship.
                                After careful review we’re unable to offer you a seat in this cohort.
                            </p>
                            @if(!empty($notes))
                                <p style="margin:0 0 18px;">
                                    <strong>Notes from the team:</strong> {{ $notes }}
                                </p>
                            @endif
                            <p style="margin:0 0 24px;">
                                We encourage you to stay plugged into Forward Edge — we’ll have more scholarships, pop-up
                                intensives, and events built for Cyber1000 applicants.
                            </p>
                        @else
                            <p style="margin:0 0 18px;">
                                Thanks for submitting your application for the <strong>{{ $courseTitle }}</strong> cohort.
                                Our admissions team is reviewing every submission carefully, and you’ll receive another email as soon
                                as a decision is made.
                            </p>
                            @if($startDate || $endDate)
                                <p style="margin:0 0 18px;">
                                    <strong>Program timeline:</strong> {{ $startDate ?? 'TBA' }} – {{ $endDate ?? 'TBA' }}
                                </p>
                            @endif
                            <p style="margin:0 0 24px;">
                                In the meantime, watch out for invites to community events, cyber career resources, and other
                                Forward Edge opportunities designed for applicants like you.
                            </p>
                        @endif

                        <p style="margin:0 0 32px;">Stay sharp,<br>Forward Edge Team</p>
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <a href="{{ url('/scholarships') }}"
                           style="display:inline-block;padding:14px 28px;border-radius:999px;background:linear-gradient(135deg,#0891b2,#6366f1);color:#fff;text-decoration:none;font-weight:600;">
                            View Scholarships
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
