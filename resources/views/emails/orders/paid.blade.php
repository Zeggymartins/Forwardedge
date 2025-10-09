<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f3f8;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #ffc107;
            color: #333;
            text-align: center;
            padding: 30px 20px;
        }

        .header img {
            max-width: 120px;
            margin-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 26px;
        }

        .content {
            padding: 25px 20px;
            color: #333;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background-color: #ffc107;
            color: #333;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #e0a800;
        }

        .order-items {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }

        .order-items li {
            margin-bottom: 8px;
        }

        .footer {
            background-color: #f4f4f4;
            text-align: center;
            font-size: 13px;
            color: #777;
            padding: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('frontend/assets/images/logos/logo.png') }}" alt="Company Logo">
            <h1>Order Confirmed!</h1>
        </div>
        <div class="content">
            <p>Hello {{ $order->user->name }},</p>
            <p>Thank you for your order! Here’s a summary:</p>

            <ul class="order-items">
                @foreach ($order->orderItems as $item)
                    <li>{{ $item->course->name }} - ${{ number_format($item->price, 2) }}</li>
                @endforeach
            </ul>

            <p><strong>Total Paid:</strong> ${{ number_format($order->total_amount, 2) }}</p>
            <p>Your course contents are attached as a ZIP file for easy access.</p>
            @foreach($order->orderItems as $item)
                @foreach ($item->course->contents as $content)
                    <li>{{ $content->title }}.{{ $content->type === 'text' ? 'docx' : pathinfo($content->file_path, PATHINFO_EXTENSION) }}
                    </li>
                @endforeach
            @endforeach
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} ForwardEdge. All rights reserved.
        </div>
    </div>
</body>

</html>
