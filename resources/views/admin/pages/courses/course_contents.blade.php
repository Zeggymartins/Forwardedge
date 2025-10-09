@extends('admin.master_page')

@section('title', $course->title . ' - Contents')

@section('main')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">{{ $course->title }} - Course Contents</h2>
        <a href="{{ route('admin.course_contents.index') }}" class="btn btn-outline-secondary rounded-pill">← Back to Courses</a>
    </div>

    <div class="list-group shadow-sm rounded-4">
        @forelse ($course->contents as $content)
            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                <div class="d-flex flex-column">
                    <h6 class="fw-semibold mb-1">{{ $content->title }}</h6>
                    <small class="text-muted">{{ ucfirst($content->type) }}</small>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#viewContentModal{{ $content->id }}">
                        View
                    </button>

                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                        data-bs-target="#updateContentModal{{ $content->id }}">
                        Change File
                    </button>

                    <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST"
                        onsubmit="return confirm('Delete this content?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </div>
            </div>

            <!-- View Modal -->
            <div class="modal fade" id="viewContentModal{{ $content->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">{{ $content->title }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if ($content->type === 'text')
                                <p>{{ $content->content }}</p>

                            @elseif ($content->type === 'video')
                                <video class="w-100 rounded" controls>
                                    <source src="{{ asset('storage/' . $content->file_path) }}">
                                </video>

                            @elseif ($content->type === 'pdf')
                                <iframe src="{{ asset('storage/' . $content->file_path) }}" class="w-100"
                                    style="height:500px;" frameborder="0"></iframe>

                            @elseif ($content->type === 'image')
                                <img src="{{ asset('storage/' . $content->file_path) }}"
                                    class="img-fluid rounded shadow-sm">

                            @elseif ($content->type === 'quiz')
                                <pre>{{ $content->quiz }}</pre>

                            @elseif ($content->type === 'assignment')
                                <pre>{{ $content->assignment }}</pre>

                            @else
                                <p>No content available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Modal -->
            <div class="modal fade" id="updateContentModal{{ $content->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="{{ route('admin.course_contents.update', $content->id) }}" method="POST"
                        enctype="multipart/form-data" class="modal-content">
                        @csrf @method('PUT')
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">Update File - {{ $content->title }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Current file: <strong>{{ basename($content->file_path ?? 'None') }}</strong></p>
                            <input type="file" name="file" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update File</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">No content available for this course yet.</div>
        @endforelse
    </div>
</div>
@endsection
