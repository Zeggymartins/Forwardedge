@extends('layouts.app')

@section('title', $course->title . ' - Course Materials')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Course Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('academy') }}">Academy</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $course->title }}</li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-3">{{ $course->title }}</h1>
                    <p class="text-muted mb-0">{{ $course->description }}</p>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-info mb-4">
                <i class="fas fa-shield-alt me-2"></i>
                <strong>Secure Access:</strong> These materials are protected and can only be accessed with your account.
                Sharing links will not work for other users.
            </div>

            @php
                $isGmail = str_ends_with(strtolower(Auth::user()->email), '@gmail.com') ||
                          str_ends_with(strtolower(Auth::user()->email), '@googlemail.com');
            @endphp

            @if(!$isGmail)
                <div class="alert alert-warning mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Non-Gmail Account:</strong> You're using {{ Auth::user()->email }}.
                    Content will be displayed through our platform viewer since you don't have a Gmail account.
                </div>
            @endif

            <!-- Course Contents -->
            @if($contents->isEmpty())
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No content is currently available for this course. Please contact support if you believe this is an error.
                </div>
            @else
                <div class="row">
                    @foreach($contents as $content)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm hover-lift">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $content->title }}</h5>

                                    @if($content->content)
                                        <p class="card-text text-muted small flex-grow-1">
                                            {{ Str::limit($content->content, 120) }}
                                        </p>
                                    @endif

                                    <div class="mt-3">
                                        @if($content->type)
                                            <span class="badge bg-primary mb-2">
                                                <i class="fas fa-tag me-1"></i>{{ ucfirst($content->type) }}
                                            </span>
                                        @endif

                                        @if($content->price)
                                            <div class="text-muted small mb-2">
                                                Value: ₦{{ number_format($content->price, 2) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-auto pt-3">
                                        @if($isGmail && $content->drive_share_link)
                                            <!-- Gmail users get both options -->
                                            <a href="{{ route('student.content.view', $content->id) }}"
                                               class="btn btn-warning w-100 mb-2"
                                               target="_blank">
                                                <i class="fas fa-external-link-alt me-2"></i>Open in Google Drive
                                            </a>
                                            <a href="{{ route('student.content.view', $content->id) }}?embed=1"
                                               class="btn btn-outline-secondary w-100 btn-sm"
                                               target="_blank">
                                                <i class="fas fa-eye me-2"></i>View on Platform
                                            </a>
                                        @else
                                            <!-- Non-Gmail users only get embedded viewer -->
                                            <a href="{{ route('student.content.view', $content->id) }}"
                                               class="btn btn-warning w-100"
                                               target="_blank">
                                                <i class="fas fa-play-circle me-2"></i>Access Content
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection
