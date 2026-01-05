<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $content->title }} - Forward Edge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .viewer-header {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .content-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-top: 2rem;
            padding: 2rem;
        }
        .file-item {
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .file-item:hover {
            background-color: #f8f9fa;
            border-color: #ffc107;
            transform: translateX(5px);
        }
        .file-icon {
            font-size: 2rem;
            margin-right: 1rem;
            color: #ffc107;
        }
        .file-info {
            flex-grow: 1;
        }
        .file-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }
        .file-meta {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .view-btn {
            background-color: #ffc107;
            color: #333;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .view-btn:hover {
            background-color: #ff9800;
            transform: scale(1.05);
        }
        .security-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
        }
        .no-download-notice {
            background: #d1ecf1;
            border-left: 4px solid #0dcaf0;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="viewer-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-0">
                        <i class="fas fa-book-open me-2"></i>{{ $content->title }}
                    </h1>
                </div>
                <a href="{{ route('student.courses.content', $content->course_id) }}" class="btn btn-dark btn-sm">
                    <i class="fas fa-arrow-left me-2"></i>Back to Course
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- Security Notice -->
        <div class="security-notice">
            <i class="fas fa-shield-alt me-2"></i>
            <strong>Protected Content:</strong> This content is secured with your account. Links cannot be shared with others.
        </div>

        <!-- View-Only Notice -->
        <div class="no-download-notice">
            <i class="fas fa-info-circle me-2"></i>
            <strong>View-Only Access:</strong> These materials are for online viewing only. Downloads are disabled to protect course content.
        </div>

        <div class="content-container">
            @if($content->content)
                <div class="mb-4">
                    <h5>Description</h5>
                    <p class="text-muted">{{ $content->content }}</p>
                </div>
                <hr>
            @endif

            <h5 class="mb-3">Course Files</h5>

            @if(count($files) === 0)
                <div class="alert alert-warning">
                    <i class="fas fa-folder-open me-2"></i>
                    No files are currently available in this module. Please contact support if you believe this is an error.
                </div>
            @else
                @foreach($files as $file)
                    <div class="file-item">
                        <div class="d-flex align-items-center flex-grow-1">
                            <div class="file-icon">
                                @php
                                    $mimeType = $file->getMimeType();
                                    $icon = 'fa-file';
                                    if (str_contains($mimeType, 'pdf')) {
                                        $icon = 'fa-file-pdf text-danger';
                                    } elseif (str_contains($mimeType, 'word') || str_contains($mimeType, 'document')) {
                                        $icon = 'fa-file-word text-primary';
                                    } elseif (str_contains($mimeType, 'video')) {
                                        $icon = 'fa-file-video text-warning';
                                    } elseif (str_contains($mimeType, 'image')) {
                                        $icon = 'fa-file-image text-success';
                                    } elseif (str_contains($mimeType, 'presentation') || str_contains($mimeType, 'powerpoint')) {
                                        $icon = 'fa-file-powerpoint text-danger';
                                    } elseif (str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) {
                                        $icon = 'fa-file-excel text-success';
                                    }
                                @endphp
                                <i class="fas {{ $icon }}"></i>
                            </div>
                            <div class="file-info">
                                <div class="file-name">{{ $file->getName() }}</div>
                                <div class="file-meta">
                                    @if($file->getSize())
                                        {{ number_format($file->getSize() / 1024 / 1024, 2) }} MB
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div>
                            @if($file->getWebViewLink())
                                <a href="{{ $file->getWebViewLink() }}" target="_blank" class="view-btn">
                                    <i class="fas fa-eye me-2"></i>View File
                                </a>
                            @else
                                <span class="text-muted small">Preview not available</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Prevent right-click and common keyboard shortcuts -->
    <script>
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            alert('Right-click is disabled to protect course content.');
        });

        // Disable common download shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+S / Cmd+S (Save)
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                alert('Downloading is disabled for course content.');
            }
            // Ctrl+P / Cmd+P (Print)
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                alert('Printing is disabled for course content.');
            }
        });

        // Log access (this could be enhanced with AJAX to track detailed usage)
        console.log('Content accessed:', '{{ $content->title }}', new Date().toISOString());
    </script>
</body>
</html>
