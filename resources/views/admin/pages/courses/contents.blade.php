@extends('admin.master_page')

@section('title', 'Course Contents')

@php
    use Illuminate\Support\Str;

    $typeOptions = [
        'text' => 'Text',
        'video' => 'Video',
        'pdf' => 'PDF',
        'image' => 'Image',
        'quiz' => 'Quiz',
        'assignment' => 'Assignment',
    ];
@endphp

<style>
    .course-card {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(15, 23, 42, 0.08);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-card .status-pill {
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .course-card .status-pill.published {
        background: rgba(24, 188, 156, 0.15);
        color: #0f5132;
    }

    .course-card .status-pill.draft {
        background: rgba(255, 193, 7, 0.2);
        color: #856404;
    }

    .content-line {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 12px;
        background: #fff;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .content-line:last-child {
        margin-bottom: 0;
    }

    .content-line h6 {
        margin: 0;
        font-weight: 600;
    }

    .content-line small {
        color: #6c757d;
    }

    .phase-builder {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 14px;
        background: #f8fafc;
    }

    .content-phase-block {
        border: 1px dashed rgba(15, 23, 42, 0.15);
    }

    .content-topic-block {
        border: 1px dashed rgba(15, 23, 42, 0.15);
        background: rgba(15, 23, 42, 0.02);
    }
</style>

@section('main')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Course Content Library</h2>
            <p class="text-muted mb-0">Organize modules inside each course, complete with phases and topics.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addContentModal">
            + Add Course Content
        </button>
    </div>

    @if ($courses->isEmpty())
        <div class="text-center py-5 border rounded-4 bg-white">
            <p class="mb-1 fw-semibold">No course content yet</p>
            <p class="text-muted">Use the button above to create your first learning module.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach ($courses as $course)
                <div class="col-12 col-lg-6 col-xxl-4">
                    <div class="course-card p-4 bg-white">
                        <div class="d-flex gap-3 align-items-start mb-4">
                            <div class="flex-shrink-0" style="width:70px;height:70px;">
                                @if ($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="img-fluid rounded-3"
                                        alt="{{ $course->title }}">
                                @else
                                    <div class="bg-light border rounded-3 h-100 d-flex align-items-center justify-content-center text-muted">
                                        No Image
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                    <h5 class="fw-bold mb-0">{{ $course->title }}</h5>
                                    <span class="status-pill {{ $course->is_published ? 'published' : 'draft' }}">
                                        {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                                <div class="d-flex gap-2 align-items-center mt-2 flex-wrap">
                                    <span class="badge bg-secondary-subtle text-secondary fw-semibold">
                                        {{ $course->contents_count }} {{ Str::plural('Content', $course->contents_count) }}
                                    </span>
                                    @if ($course->price)
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">
                                            ₦{{ number_format($course->discount_price ?? $course->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            @if ($course->contents->isEmpty())
                                <div class="border rounded-3 py-4 text-center text-muted">
                                    No content blocks yet.
                                </div>
                            @else
                                <div class="content-list">
                                    @foreach ($course->contents as $content)
                                        <div class="content-line">
                                            <div>
                                                <h6>{{ $content->title }}</h6>
                                                <small>
                                                    {{ ucfirst($content->type) }} · {{ $content->phases_count }}
                                                    {{ Str::plural('phase', $content->phases_count) }}
                                                </small>
                                            </div>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.course_contents.show', $course->id) }}#content-{{ $content->id }}"
                                                    class="btn btn-sm btn-outline-primary">View</a>

                                                    @php
                                                        $payload = [
                                                            'id'        => $content->id,
                                                            'title'     => $content->title,
                                                            'type'      => $content->type,
                                                            'content'   => $content->content,
                                                            'file_name' => $content->file_path ? basename($content->file_path) : null,
                                                            'has_file'  => (bool) $content->file_path,
                                                        ];
                                                        @endphp
                                                <button class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editContentModal"
                                                    data-action="{{ route('admin.course_contents.update', $content->id) }}"
                                                    data-content='@json($payload)'>
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this content block?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.course_contents.show', $course->id) }}"
                                class="btn btn-outline-secondary btn-sm rounded-pill">Manage Contents</a>
                            <button class="btn btn-link text-decoration-none p-0" data-bs-toggle="modal"
                                data-bs-target="#addContentModal" data-course="{{ $course->id }}">
                                + Add Module
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@include('admin.pages.courses.partials.content_modal_manager', [
    'courseOptions' => $courseOptions,
    'typeOptions' => $typeOptions,
])
