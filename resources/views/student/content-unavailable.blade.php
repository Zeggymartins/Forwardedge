<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Coming Soon - Forward Edge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .content-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header-section {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            padding: 3rem 2rem;
            text-align: center;
        }
        .icon-circle {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .icon-circle i {
            font-size: 3rem;
            color: #333;
        }
        .body-section {
            padding: 2rem;
        }
        .info-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        .btn-primary-custom {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            border: none;
            color: #333;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 152, 0, 0.3);
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="content-card">
                    <div class="header-section">
                        <div class="icon-circle">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h1 class="h3 mb-2 text-dark">Content Coming Soon</h1>
                        <p class="mb-0 text-dark opacity-75">{{ $content->title }}</p>
                    </div>

                    <div class="body-section">
                        <div class="info-box">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Your purchase is confirmed!</strong>
                            <p class="mb-0 mt-2 small">
                                The course content is being prepared and will be available shortly.
                                You will receive an email notification when your materials are ready to access.
                            </p>
                        </div>

                        <div class="mb-4">
                            <h5>What happens next?</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    Your payment has been processed successfully
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    You'll receive access instructions via email
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-folder-open text-warning me-2"></i>
                                    Course materials will be shared to your account
                                </li>
                            </ul>
                        </div>

                        <div class="text-center">
                            <a href="{{ route('home') }}" class="btn btn-primary-custom me-2">
                                <i class="fas fa-home me-2"></i>Go Home
                            </a>
                            <a href="{{ route('contact') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-headset me-2"></i>Contact Support
                            </a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center text-muted small">
                            <p class="mb-1">Course: <strong>{{ $course->title ?? 'N/A' }}</strong></p>
                            <p class="mb-0">Content ID: {{ $content->id }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
