<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to Google Drive - {{ $content->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .redirect-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            padding: 3rem;
            text-align: center;
        }
        .icon-box {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        .icon-box i {
            font-size: 2.5rem;
            color: white;
        }
        h1 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.75rem;
        }
        .instructions {
            background: #f8f9fa;
            border-left: 4px solid #ffc107;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: left;
            border-radius: 8px;
        }
        .instructions ol {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }
        .instructions li {
            margin-bottom: 0.75rem;
            color: #555;
        }
        .email-badge {
            display: inline-block;
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            margin: 1rem 0;
        }
        .btn-drive {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: none;
            color: #333;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
        }
        .btn-drive:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.6);
            color: #333;
        }
        .security-note {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #856404;
        }
        .countdown {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="redirect-card">
        <div class="icon-box">
            <i class="fab fa-google-drive"></i>
        </div>

        <h1>Opening Your Course Materials</h1>
        <p class="text-muted">{{ $content->title }}</p>

        <div class="email-badge">
            <i class="fas fa-envelope me-2"></i>{{ $userEmail }}
        </div>

        <div class="instructions">
            <strong><i class="fas fa-info-circle me-2"></i>Important Instructions:</strong>
            <ol>
                <li><strong>Make sure you're logged into Google</strong> with this email: <code>{{ $userEmail }}</code></li>
                <li>Click the button below to open your course materials in Google Drive</li>
                <li>If you see "You need access", verify you're using the correct Google account</li>
                <li>The content is <strong>view-only</strong> - downloading is restricted to protect the material</li>
            </ol>
        </div>

        <a href="{{ $driveUrl }}"
           class="btn btn-drive"
           id="driveButton"
           target="_blank">
            <i class="fas fa-external-link-alt me-2"></i>Open in Google Drive
        </a>

        <div class="countdown">
            <small><i class="fas fa-clock me-1"></i>Auto-opening in <span id="timer">5</span> seconds...</small>
        </div>

        <div class="security-note">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Security:</strong> This content is protected and tied to your account.
            Sharing the link won't work for others.
        </div>

        <div class="mt-4">
            <a href="{{ route('student.courses.content', $content->course_id) }}" class="text-muted">
                <i class="fas fa-arrow-left me-2"></i>Back to Course Materials
            </a>
        </div>
    </div>

    <script>
        // Auto-redirect after 5 seconds
        let countdown = 5;
        const timerElement = document.getElementById('timer');
        const driveUrl = "{{ $driveUrl }}";

        const interval = setInterval(() => {
            countdown--;
            timerElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                window.open(driveUrl, '_blank');
                // Optionally redirect back after opening
                setTimeout(() => {
                    window.location.href = "{{ route('student.courses.content', $content->course_id) }}";
                }, 2000);
            }
        }, 1000);

        // Stop countdown if user clicks manually
        document.getElementById('driveButton').addEventListener('click', () => {
            clearInterval(interval);
            timerElement.parentElement.innerHTML = '<small><i class="fas fa-check-circle me-1"></i>Opening Google Drive...</small>';
        });
    </script>
</body>
</html>
