@extends('admin.master_page')

@section('title', $course->title . ' - Contents')

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
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="fw-bold mb-1">{{ $course->title }} · Content & Phases</h2>
                <p class="text-muted mb-0">Manage every module, phase, and topic inside this course.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.course_contents.index') }}" class="btn btn-outline-secondary rounded-pill">← All Academy Training</a>
                <button class="btn btn-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#addContentModal" data-course="{{ $course->id }}">
                    + Add New Content
                </button>
            </div>
        </div>

        @forelse ($course->contents as $content)
            <div class="card shadow-sm border-0 mb-4" id="content-{{ $content->id }}">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <h5 class="fw-bold mb-1">{{ $content->title }}</h5>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <span class="badge bg-secondary-subtle text-secondary fw-semibold">{{ ucfirst($content->type) }}</span>
                                @if ($content->file_path)
                                    <span class="badge bg-primary-subtle text-primary fw-semibold">File Attached</span>
                                @endif
                                <span class="text-muted small">Created {{ $content->created_at->format('d M, Y') }}</span>
                            </div>
                        </div>
                        <div class="content-actions">
                            <button class="btn btn-sm btn-soft-primary" data-bs-toggle="modal" data-bs-target="#viewContentModal{{ $content->id }}">View</button>
                            @php
                                $contentPayload = [
                                    'id'        => $content->id,
                                    'title'     => $content->title,
                                    'type'      => $content->type,
                                    'content'   => $content->content,
                                    'file_name' => $content->file_path ? basename($content->file_path) : null,
                                    'has_file'  => (bool) $content->file_path,
                                ];
                            @endphp
                            <button class="btn btn-sm btn-soft-neutral" data-bs-toggle="modal" data-bs-target="#editContentModal" data-action="{{ route('admin.course_contents.update', $content->id) }}" data-content='@json($contentPayload)'>Edit</button>
                            <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST" class="content-action-form" onsubmit="return confirm('Delete this content block?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-soft-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    @if ($content->type === 'text' && $content->content)
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase small">Overview</h6>
                            <p class="mb-0">{{ Str::limit($content->content, 400) }}</p>
                        </div>
                    @elseif ($content->file_path)
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase small">Resource</h6>
                            <p class="mb-2 text-muted">{{ basename($content->file_path) }}</p>
                            <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">Open File</a>
                        </div>
                    @endif

                    <div class="phase-builder p-3 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-semibold mb-0">Phases & Topics</h6>
                            <span class="badge bg-secondary-subtle text-secondary">{{ $content->phases->count() }} {{ Str::plural('Phase', $content->phases->count()) }}</span>
                        </div>

                        @if ($content->phases->isEmpty())
                            <div class="border rounded-3 py-4 text-center text-muted">No phases yet for this content.</div>
                        @else
                            <div class="accordion" id="phasesAccordion{{ $content->id }}">
                                @foreach ($content->phases as $phase)
                                    <div class="accordion-item mb-2">
                                        <h2 class="accordion-header" id="phaseHeading{{ $phase->id }}">
                                            <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#phaseCollapse{{ $phase->id }}">
                                                <div class="d-flex flex-column me-3">
                                                    <span class="fw-semibold">Phase {{ $loop->iteration }} · {{ $phase->title }}</span>
                                                    <small class="text-muted">Order {{ $phase->order ?? $loop->iteration }} · {{ $phase->duration ? $phase->duration . ' mins' : 'Duration TBD' }}</small>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="phaseCollapse{{ $phase->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#phasesAccordion{{ $content->id }}">
                                            <div class="accordion-body">
                                                <div class="row g-3 mb-3">
                                                    @if ($phase->image)
                                                        <div class="col-md-4">
                                                            <img src="{{ asset('storage/' . $phase->image) }}" class="img-fluid rounded-3" alt="{{ $phase->title }}">
                                                        </div>
                                                    @endif
                                                    <div class="col">
                                                        <p class="mb-0">{{ $phase->content ?? 'No description provided for this phase yet.' }}</p>
                                                    </div>
                                                </div>

                                                <h6 class="fw-semibold">Topics</h6>
                                                @if ($phase->topics->isEmpty())
                                                    <div class="text-muted">No topics added yet.</div>
                                                @else
                                                    <div class="list-group list-group-flush">
                                                        @foreach ($phase->topics as $topic)
                                                            <div class="list-group-item px-0">
                                                                <div class="d-flex justify-content-between gap-3">
                                                                    <div>
                                                                        <div class="fw-semibold">{{ $loop->iteration }}. {{ $topic->title }}</div>
                                                                        <div class="text-muted small">{{ $topic->content ?: 'No notes yet.' }}</div>
                                                                    </div>
                                                                    <span class="badge bg-light text-dark">Order {{ $topic->order ?? $loop->iteration }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
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
                                <iframe src="{{ asset('storage/' . $content->file_path) }}" class="w-100" style="height:500px;" frameborder="0"></iframe>
                            @elseif ($content->type === 'image')
                                <img src="{{ asset('storage/' . $content->file_path) }}" class="img-fluid rounded shadow-sm">
                            @elseif ($content->type === 'quiz' || $content->type === 'assignment')
                                <a href="{{ asset('storage/' . $content->file_path) }}" target="_blank" class="btn btn-outline-primary">Open {{ ucfirst($content->type) }} File</a>
                            @else
                                <p>No content available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="border rounded-4 py-5 text-center text-muted">
                No content has been linked to this course yet.
            </div>
        @endforelse
    </div>
@endsection

@include('admin.pages.courses.partials.content_modal_manager', [
    'courseOptions' => $courseOptions,
    'typeOptions' => $typeOptions,
])
