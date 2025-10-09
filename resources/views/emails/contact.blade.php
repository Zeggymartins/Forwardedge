<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>A new message</title>
</head>
<body>
<h2>New Contact Message</h2>
<p><strong>Name:</strong> {{ $messageData->name }}</p>
<p><strong>Email:</strong> {{ $messageData->email }}</p>
<p><strong>Phone:</strong> {{ $messageData->phone }}</p>
<p><strong>Service:</strong> {{ $messageData->service->title }}</p>
<p><strong>Message:</strong><br>{{ $messageData->message }}</p>
</body>
</html>