<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f3f8; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .header { background-color: #28a745; color: #fff; text-align: center; padding: 30px 20px; }
        .header img { max-width: 120px; margin-bottom: 10px; }
        .header h1 { margin: 0; font-size: 26px; }
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
            <h1>Event Registration Confirmed!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $registration->first_name }},</p>
            <p>Your ticket is confirmed! Here are your details:</p>

            <div class="highlight">
                <strong>Event:</strong> {{ $registration->event->name }}<br>
                <strong>Ticket Type:</strong> {{ $registration->ticket->name }}<br>
                <strong>Registration Code:</strong> {{ $registration->registration_code }}<br>
                <strong>Amount Paid:</strong> ${{ number_format($registration->amount_paid, 2) }}
            </div>

            <p>Present your registration code at the event entrance.</p>
            <a href="{{ route('events.show', $registration->event->id) }}" class="btn">View Event</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} ForwardEdge. All rights reserved.
        </div>
    </div>
</body>
</html>
