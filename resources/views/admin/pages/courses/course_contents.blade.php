@extends('admin.master_page')

@section('title', '{{ $course->title }}')

@section('main')
    <div class="mb-4">
        <h2 class="fw-bold">{{ $course->title }} - Contents</h2>
    </div>

    <div class="row g-4">
        @foreach ($course->contents as $content)
            <div class="col-md-12 col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="row align-items-lg-center">
                            <div class="col">
                                <h6 class="fw-bold">{{ $content->title }}</h6>
                                <small class="d-block mb-3 text-muted">{{ ucfirst($content->type) }}</small>
                            </div>
                            <div class="col">
                                @if ($content->type === 'video')
                                    <video class="w-100 mb-3 rounded" controls>
                                        <source src="{{ asset('storage/' . $content->file_path) }}">
                                    </video>
                                @elseif ($content->type === 'image')
                                    <img src="{{ asset('storage/' . $content->file_path) }}" class="img-fluid rounded mb-3">
                                @elseif ($content->type === 'pdf')
                                    <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank"
                                        class="btn btn-outline-secondary btn-sm mb-3">
                                        View PDF
                                    </a>
                                @else
                                    <p class="mb-3">{{ $content->content }}</p>
                                @endif
                            </div>
                            <div class="col">
                                <!-- Action buttons in a neat row -->
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.course_contents.show', $course->id) }}"
                                        class="btn btn-sm btn-light">View</a>

                                    <a href="" class="btn btn-sm btn-primary">Update</a>

                                    <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this item?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
