@extends('admin.master_page')

@section('title', $course->title . ' • Course Overview')

@section('main')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ $course->title }}</h1>
                <p class="text-muted mb-0">Slug: <code>{{ $course->slug }}</code></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">Back to Academy Training</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editCourseModal">
                    <i class="bi bi-pencil-square me-1"></i>Edit Course
                </button>
            </div>
        </div>

        {{-- Summary --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-4 align-items-center">
                    <div class="col-md-4 text-center">
                        <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/600x350?text=No+Thumbnail' }}"
                            alt="Thumbnail" class="img-fluid rounded shadow-sm">
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                            <h4 class="fw-bold mb-0">{{ $course->title }}</h4>
                            <span class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }} px-3">
                                {{ ucfirst($course->status) }}
                            </span>
                            @if($course->isExternal())
                                <span class="badge bg-info px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>External ({{ $course->external_platform_name ?? 'Platform' }})
                                </span>
                            @elseif($course->contents()->count() > 0)
                                <span class="badge bg-primary px-3">
                                    <i class="bi bi-file-earmark-text me-1"></i>Internal Content
                                </span>
                            @else
                                <span class="badge bg-warning text-dark px-3">
                                    <i class="bi bi-calendar-event me-1"></i>Live Training Only
                                </span>
                            @endif
                        </div>
                        <p class="text-muted mb-3">{{ $course->description ?? 'No description provided yet.' }}</p>
                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <strong>Created:</strong>
                                {{ $course->created_at ? $course->created_at->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Last updated:</strong>
                                {{ $course->updated_at ? $course->updated_at->diffForHumans() : 'N/A' }}
                            </div>
                            <div class="col-12">
                                <strong>Contents linked:</strong> {{ $course->contents()->count() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($course->isExternal())
            {{-- External Course Notice --}}
            <div class="alert alert-info d-flex align-items-start mb-4">
                <i class="bi bi-info-circle me-3 fs-4"></i>
                <div>
                    <strong>External Course</strong>
                    <p class="mb-1">This course is hosted on <strong>{{ $course->external_platform_name }}</strong>.</p>
                    <p class="mb-0 small">Users will be redirected to: <a href="{{ $course->external_course_url }}" target="_blank" class="alert-link">{{ $course->external_course_url }}</a></p>
                </div>
            </div>
        @endif

        {{-- Page Builder handoff --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <h5 class="mb-0">Course Landing Page</h5>
                    @if ($course->page)
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Attached
                        </span>
                    @else
                        <span class="badge bg-secondary">
                            <i class="bi bi-x-circle me-1"></i>Not Attached
                        </span>
                    @endif
                </div>

                @if ($course->page)
                    <div class="small text-muted mb-3">
                        <i class="bi bi-link-45deg me-1"></i>
                        Page URL: <code>{{ route('page.show', $course->page->slug) }}</code>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-outline-primary" href="{{ route('pb.blocks', $course->page) }}">
                            <i class="bi bi-puzzle me-1"></i>Manage Blocks
                        </a>
                        <a class="btn btn-outline-dark" href="{{ route('page.show', $course->page->slug) }}" target="_blank">
                            <i class="bi bi-eye me-1"></i>Preview Page
                        </a>
                    </div>
                @else
                    <p class="text-muted mb-3">
                        No landing page attached. Create a professional marketing page with the page builder.
                    </p>
                    <form method="POST" action="{{ route('pb.pages.store') }}" class="row g-2">
                        @csrf
                        <div class="col-md-4">
                            <input type="text" name="title" class="form-control" placeholder="Page title" required
                                value="{{ $course->title }}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="slug" class="form-control" placeholder="Slug (optional)">
                        </div>
                        <div class="col-md-4 d-grid d-md-block">
                            <input type="hidden" name="owner_type" value="course">
                            <input type="hidden" name="owner_id" value="{{ $course->id }}">
                            <button class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-1"></i>Create & Attach Page
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- Schedule --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">Schedule</h5>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary btn-sm"
                        data-bs-toggle="modal" data-bs-target="#addScheduleModal"
                        @disabled($course->schedules->count() >= 1)>
                        <i class="bi bi-plus-lg me-1"></i>{{ $course->schedules->isEmpty() ? 'Add Schedule' : 'Replace Schedule' }}
                    </button>
                </div>
            </div>
            <div class="card-body">
                @forelse ($course->schedules as $schedule)
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <h6 class="mb-1">{{ ucfirst($schedule->type ?? 'Schedule') }}</h6>
                                <small class="text-muted">ID #{{ $schedule->id }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#editScheduleModal{{ $schedule->id }}">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <form action="{{ route('admin.courses.schedules.destroy', [$course->id, $schedule->id]) }}"
                                    method="POST" onsubmit="return confirm('Remove this schedule?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                        <div class="row g-3 small">
                            <div class="col-md-4">
                                <strong>Start:</strong>
                                {{ $schedule->start_date ? \Carbon\Carbon::parse($schedule->start_date)->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>End:</strong>
                                {{ $schedule->end_date ? \Carbon\Carbon::parse($schedule->end_date)->format('M d, Y') : 'N/A' }}
                            </div>
                            <div class="col-md-4">
                                <strong>Location:</strong> {{ $schedule->location ?? 'Online' }}
                            </div>
                        </div>
                    </div>

                    {{-- Edit Schedule Modal --}}
                    <div class="modal fade" id="editScheduleModal{{ $schedule->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admin.courses.schedules.update', [$course->id, $schedule->id]) }}"
                                    method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Schedule</h5>
                                        <button class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Start date</label>
                                            <input type="date" name="start_date" class="form-control"
                                                value="{{ $schedule->start_date ? $schedule->start_date->format('Y-m-d') : '' }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">End date</label>
                                            <input type="date" name="end_date" class="form-control"
                                                value="{{ $schedule->end_date ? $schedule->end_date->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Location</label>
                                            <input type="text" name="location" class="form-control"
                                                value="{{ $schedule->location }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Delivery type</label>
                                            <select name="type" class="form-select">
                                                <option value="">Keep current ({{ ucfirst($schedule->type ?? 'bootcamp') }})</option>
                                                @foreach (['bootcamp', 'virtual', 'hybrid', 'physical'] as $type)
                                                    <option value="{{ $type }}" @selected($schedule->type === $type)>{{ ucfirst($type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">This course does not have a schedule yet.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ---------------------------- Modals ---------------------------- --}}

    {{-- Edit Course Modal --}}
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-header bg-gradient-teal text-white">
                        <h5 class="modal-title">Edit Course</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6"><label>Title</label><input type="text" name="title" value="{{ $course->title }}" class="form-control" required></div>
                        <div class="col-md-6"><label>Slug</label><input type="text" name="slug" value="{{ $course->slug }}" class="form-control" required></div>
                        <div class="col-12"><label>Description</label><textarea name="description" class="form-control" rows="3">{{ $course->description }}</textarea></div>
                        <div class="col-md-6">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                @foreach (['draft', 'published'] as $s)
                                    <option value="{{ $s }}" {{ $course->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6"><label>Thumbnail (optional)</label><input type="file" name="thumbnail" class="form-control"></div>

                        {{-- External Platform Fields --}}
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_external" id="is_external_edit" value="1"
                                    {{ old('is_external', $course->is_external) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_external_edit">
                                    This course is hosted on an external platform
                                </label>
                            </div>
                        </div>

                        <div id="external-fields-edit" style="display: {{ $course->is_external ? 'block' : 'none' }}">
                            <div class="col-md-6">
                                <label>External Platform</label>
                                <select name="external_platform_name" class="form-select">
                                    <option value="">Select Platform</option>
                                    @foreach(['Udemy', 'Teachable', 'Coursera', 'Skillshare', 'LinkedIn Learning', 'Other'] as $platform)
                                        <option value="{{ $platform }}" {{ old('external_platform_name', $course->external_platform_name) == $platform ? 'selected' : '' }}>
                                            {{ $platform }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label>Course URL on Platform</label>
                                <input type="url" name="external_course_url" class="form-control"
                                    value="{{ old('external_course_url', $course->external_course_url) }}"
                                    placeholder="https://www.udemy.com/course/your-course/">
                                <small class="text-muted">Full URL where users can purchase this course</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-success">Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add Schedule Modal --}}
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.courses.schedules.store', $course->id) }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Add Schedule</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6"><label>Start date</label><input type="date" name="start_date" class="form-control" required></div>
                        <div class="col-md-6"><label>End date</label><input type="date" name="end_date" class="form-control"></div>
                        <div class="col-md-6"><label>Location</label><input type="text" name="location" class="form-control" placeholder="e.g. Lagos / Remote"></div>
                        <div class="col-md-6">
                            <label>Delivery type</label>
                            <select name="type" class="form-select">
                                <option value="">Default (Bootcamp)</option>
                                @foreach (['bootcamp','virtual','hybrid','physical'] as $t)
                                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave blank to use the standard bootcamp format.</small>
                        </div>
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Add Schedule</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- Page JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

            // Toggle external platform fields in edit modal
            const externalCheckbox = document.getElementById('is_external_edit');
            const externalFields = document.getElementById('external-fields-edit');

            if (externalCheckbox && externalFields) {
                externalCheckbox.addEventListener('change', function() {
                    externalFields.style.display = this.checked ? 'block' : 'none';
                });
            }
        });
    </script>
@endsection
