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
                                @php
                                    $startingDisplay = $course->contents
                                        ->map(fn($c) => $c->discount_price ?? $c->price)
                                        ->filter(fn($value) => !is_null($value) && $value >= 0)
                                        ->min();
                                @endphp
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
                                    @if (!is_null($startingDisplay))
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">
                                            From ₦{{ number_format($startingDisplay, 2) }}
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
                                        <div class="content-line d-flex flex-wrap justify-content-between align-items-center">
                                            <div class="me-3">
                                                <h6 class="mb-1">{{ $content->title }}</h6>
                                                <small class="text-muted">
                                                    {{ ucfirst($content->type) }} · {{ $content->phases_count }}
                                                    {{ Str::plural('phase', $content->phases_count) }}
                                                </small>
                                            </div>
                                            <div class="text-end ms-auto me-3">
                                                <span class="fw-semibold d-block">
                                                    ₦{{ number_format($content->discount_price ?? $content->price ?? 0, 2) }}
                                                </span>
                                                @if ($content->discount_price && $content->price && $content->discount_price < $content->price)
                                                    <small class="text-muted text-decoration-line-through">
                                                        ₦{{ number_format($content->price, 2) }}
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="action-buttons d-flex flex-wrap gap-2">
                                                <a href="{{ route('admin.course_contents.show', $course->id) }}#content-{{ $content->id }}"
                                                    class="btn btn-sm btn-outline-primary flex-fill">View</a>

                                                @php
                                                    $payload = [
                                                        'id'        => $content->id,
                                                        'title'     => $content->title,
                                                        'type'      => $content->type,
                                                        'content'   => $content->content,
                                                        'file_name' => $content->file_path ? basename($content->file_path) : null,
                                                        'has_file'  => (bool) $content->file_path,
                                                        'price'     => $content->price,
                                                        'discount_price' => $content->discount_price,
                                                        'drive_folder_id' => $content->drive_folder_id,
                                                        'drive_share_link' => $content->drive_share_link,
                                                        'auto_grant_access' => $content->auto_grant_access,
                                                    ];
                                                @endphp
                                                <button class="btn btn-sm btn-outline-secondary flex-fill"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editContentModal"
                                                    data-action="{{ route('admin.course_contents.update', $content->id) }}"
                                                    data-content='@json($payload)'>
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this content block?');" class="flex-fill">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger w-100">Delete</button>
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
                            <button class="btn btn-outline text-decoration-none p-2" data-bs-toggle="modal"
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

@push('styles')
<style>
    .content-line .action-buttons .btn{
        min-width: 110px;
    }
    @media (max-width: 575px){
        .content-line .action-buttons .btn{
            flex:1 1 100%;
        }
    }
</style>
@endpush
