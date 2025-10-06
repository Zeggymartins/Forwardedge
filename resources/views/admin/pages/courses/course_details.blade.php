@extends('admin.master_page')

@section('title', $course->title . ' Dashboard')
<style>
    /* Retaining and enhancing the original, clean tab style */
    #courseTabs .nav-link {
        border: none;
        border-radius: 30px;
        margin: 0.25rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    #courseTabs .nav-link:hover {
        background-color: #e9ecef;
        color: #4e73df;
    }

    #courseTabs .nav-link.active {
        /* Using the same vibrant gradient for a consistent style */
        background: linear-gradient(90deg, #4e73df, #1cc88a);
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
    }
</style>

@section('main')
    <div class="container py-4">
        <h1>{{ $course->title }} <small class="text-muted">Dashboard</small></h1>
        
        {{-- Updated ID to courseTabs and adjusted links for Course management --}}
        <ul class="nav nav-tabs nav-pills flex-wrap mb-4 shadow-sm rounded-3" id="courseTabs" role="tablist"
            style="background: #f8f9fc;">
            <li class="nav-item" role="presentation">
                <a class="nav-link active d-flex align-items-center px-4 py-2" id="overview-tab" data-bs-toggle="tab"
                    href="#overview" role="tab">
                    <i class="bi bi-info-circle me-2"></i> Overview
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="contents-tab" data-bs-toggle="tab"
                    href="#contents" role="tab">
                    <i class="bi bi-file-earmark-text me-2"></i> Contents
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="phases-tab" data-bs-toggle="tab"
                    href="#phases" role="tab">
                    <i class="bi bi-layers me-2"></i> Phases & Topics
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="schedules-tab" data-bs-toggle="tab"
                    href="#schedules" role="tab">
                    <i class="bi bi-calendar-event me-2"></i> Schedules
                </a>
            </li>
            {{-- Removed event-specific tabs (Tickets, Speakers, Sponsors) --}}
        </ul>


        <div class="tab-content">
            <div class="tab-pane fade show active" id="overview">
                <div class="card shadow-md border-0 rounded-3">
                    <div class="card-header d-flex justify-content-between align-items-center bg-gradient text-white"
                        style="background: linear-gradient(90deg,#4e73df,#1cc88a);">
                        <h5 class="mb-0">Course Overview</h5>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editCourseModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit Info
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/300x200?text=No+Thumbnail' }}"
                                    class="img-fluid rounded shadow-sm mb-3" alt="Course Thumbnail">
                            </div>
                            <div class="col-md-8">
                                <h4 class="fw-bold">{{ $course->title }}</h4>
                                <p class="text-muted">{{ $course->description ?? 'No description available.' }}</p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><strong>Slug:</strong> {{ $course->slug }}</p>
                                        <p><strong>Status:</strong>
                                            <span
                                                class="badge bg-{{ $course->status === 'published' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="contents">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Course Details</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContentModal">
                        <i class="bi bi-plus-lg"></i> Add Content
                    </button>
                </div>

                <div class="row">
                    @forelse ($course->details as $content)
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body p-4">
                                    <h6 class="text-uppercase small mb-3">
                                        <span class="badge bg-info me-2">{{ $content->sort_order }}</span>
                                        {{ $course->title }}
                                    </h6>
                                    <p class="text-muted small mb-2">Type: {{ ucfirst($content->type) }}</p>

                                    {{-- Render content by type --}}
                                    @if ($content->type === 'text' && $content->content)
                                        <p class="mb-3 text-truncate">{{ $content->content }}</p>
                                    @elseif (in_array($content->type, ['video', 'pdf', 'quiz', 'assignment']))
                                        @if ($content->file_path)
                                            <p class="mb-1 small">File: <i class="bi bi-file-earmark"></i>
                                                {{ basename($content->image) }}</p>
                                        @else
                                            <p class="mb-1 small text-danger">No file uploaded.</p>
                                        @endif
                                        @if ($content->content)
                                            <p class="mb-3 small text-truncate">Notes: {{ $content->content }}</p>
                                        @endif
                                    @endif

                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editContentModal{{ $content->id }}">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>

                                        <form
                                            action="{{ route('admin.courses.contents.destroy', [$course->id, $content->id]) }}"
                                            method="POST" onsubmit="return confirm('Delete this content?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="editContentModal{{ $content->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form
                                        action="{{ route('admin.courses.contents.update', [$course->id, $content->id]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Content: {{ $content->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-8">
                                                <label class="form-label">Title</label>
                                                <input type="text" name="title" value="{{ $content->title }}"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Order</label>
                                                <input type="number" name="order" value="{{ $content->order }}"
                                                    class="form-control" required min="1">
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">Text Content/Notes</label>
                                                <textarea name="content" class="form-control" rows="4">{{ $content->content }}</textarea>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">File (Only replace if needed. Type:
                                                    {{ ucfirst($content->type) }})</label>
                                                @if ($content->file_path)
                                                    <p class="small text-muted mb-1">Current File:
                                                        {{ basename($content->file_path) }}</p>
                                                @endif
                                                <input type="file" name="file_path" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><p class="text-muted">No course contents added yet.</p></div>
                    @endforelse
                </div>
            </div>


            <div class="tab-pane fade" id="phases">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Course Phases</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPhaseModal">
                        <i class="bi bi-plus-lg"></i> Add Phase
                    </button>
                </div>

                @forelse ($course->phases as $phase)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <span class="badge bg-primary me-2">{{ $phase->order }}</span>
                                {{ $phase->title }}
                            </h5>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal"
                                    data-bs-target="#addTopicModal{{ $phase->id }}">
                                    <i class="bi bi-plus"></i> Topic
                                </button>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editPhaseModal{{ $phase->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form
                                    action="{{ route('admin.courses.phases.destroy', [$course->id, $phase->id]) }}"
                                    method="POST" onsubmit="return confirm('Delete this phase and all its topics?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-2">Duration:
                                {{ $phase->duration ? $phase->duration . ' days' : 'N/A' }}</p>
                            @if ($phase->content)
                                <p>{{ $phase->content }}</p>
                            @endif

                            <h6 class="mt-3">Topics:</h6>
                            <ul class="list-group list-group-flush">
                                @forelse ($phase->topics as $topic)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span class="badge bg-secondary me-2">{{ $topic->order }}</span>
                                            {{ $topic->title }}
                                            <i class="bi bi-info-circle-fill text-muted ms-2" data-bs-toggle="tooltip"
                                                title="{{ $topic->content ?? 'No detailed content.' }}"></i>
                                        </span>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editTopicModal{{ $topic->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form
                                                action="{{ route('admin.courses.topics.destroy', [$course->id, $phase->id, $topic->id]) }}"
                                                method="POST" onsubmit="return confirm('Delete this topic?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </li>

                                    <div class="modal fade" id="editTopicModal{{ $topic->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form
                                                    action="{{ route('admin.courses.topics.update', [$course->id, $phase->id, $topic->id]) }}"
                                                    method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Topic</h5>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body row g-3">
                                                        <div class="col-md-9">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" name="title"
                                                                value="{{ $topic->title }}" class="form-control"
                                                                required>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label">Order</label>
                                                            <input type="number" name="order"
                                                                value="{{ $topic->order }}" class="form-control"
                                                                min="1">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">Content</label>
                                                            <textarea name="content" class="form-control" rows="3">{{ $topic->content }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Save
                                                            Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <li class="list-group-item text-muted small">No topics added to this phase.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <div class="modal fade" id="editPhaseModal{{ $phase->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <form
                                    action="{{ route('admin.courses.phases.update', [$course->id, $phase->id]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Phase</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" value="{{ $phase->title }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Duration (Days)</label>
                                            <input type="number" name="duration"
                                                value="{{ $phase->duration }}" class="form-control" min="0">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="order" value="{{ $phase->order }}"
                                                class="form-control" min="1">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea name="content" class="form-control" rows="3">{{ $phase->content }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Phase Image (Optional)</label>
                                            <input type="file" name="image" class="form-control">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="addTopicModal{{ $phase->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form
                                    action="{{ route('admin.courses.topics.store', [$course->id, $phase->id]) }}"
                                    method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Topic to {{ $phase->title }}</h5>
                                        <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body row g-3">
                                        <div class="col-md-9">
                                            <label class="form-label">Title</label>
                                            <input type="text" name="title" class="form-control" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="order" class="form-control" min="1" value="{{ $phase->topics->max('order') + 1 }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Content</label>
                                            <textarea name="content" class="form-control" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Add Topic</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12"><p class="text-muted">No course phases added yet.</p></div>
                @endforelse
            </div>


            <div class="tab-pane fade" id="schedules">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Course Schedules (e.g., Bootcamps, Classes)</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                        <i class="bi bi-plus-lg"></i> Add Schedule
                    </button>
                </div>

                <div class="row">
                    @forelse ($course->schedules as $sch)
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <h6 class="card-title mb-1">{{ ucfirst($sch->type) }}</h6>
                                    <small class="text-muted d-block mb-2">
                                        Starts: {{ $sch->start_date ? \Carbon\Carbon::parse($sch->start_date)->format('M d, Y') : 'N/A' }} |
                                        Ends: {{ $sch->end_date ? \Carbon\Carbon::parse($sch->end_date)->format('M d, Y') : 'N/A' }}
                                    </small>
                                    <p class="text-truncate mb-2">
                                        Location: {{ $sch->location ?? 'Online' }} | Price:
                                        {{ $sch->price ? '$' . number_format($sch->price, 2) : 'Free' }}
                                    </p>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#viewScheduleModal{{ $sch->id }}">
                                            View/Edit
                                        </button>
                                        <form action="{{ route('admin.courses.schedules.destroy', [$course->id, $sch->id]) }}"
                                            method="POST" onsubmit="return confirm('Delete this schedule?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="viewScheduleModal{{ $sch->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.courses.schedules.update', [$course->id, $sch->id]) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Schedule</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Start Date</label>
                                                <input type="date" name="start_date" class="form-control"
                                                    value="{{ $sch->start_date }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">End Date</label>
                                                <input type="date" name="end_date" class="form-control"
                                                    value="{{ $sch->end_date }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Location</label>
                                                <input type="text" name="location" class="form-control"
                                                    value="{{ $sch->location }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Type</label>
                                                <input type="text" name="type" class="form-control"
                                                    value="{{ $sch->type ?? 'bootcamp' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Price</label>
                                                <input type="number" step="0.01" name="price" class="form-control"
                                                    value="{{ $sch->price }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12"><p class="text-muted">No course schedules added yet.</p></div>
                    @endforelse
                </div>
            </div>

            {{-- Removed Tickets, Speakers, Sponsors tabs --}}

        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Tooltip initialization (used for topic content)
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            // Content Type Selector Logic (Add Content Modal)
            const typeSelect = document.getElementById('contentType');
            const dynamicField = document.getElementById('dynamicContentField'); // Changed ID to avoid conflict with existing logic

            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    dynamicField.innerHTML = '';
                    let type = this.value;

                    // Text fields (title is separate)
                    if (type === 'text') {
                        dynamicField.innerHTML = `
                            <label class="form-label">Content Text</label>
                            <textarea name="content" class="form-control" rows="4" placeholder="Enter text content" required></textarea>`;
                    }
                    // File fields (video, pdf, quiz, assignment)
                    else if (['video', 'pdf', 'quiz', 'assignment'].includes(type)) {
                        dynamicField.innerHTML = `
                            <label class="form-label">Upload File</label>
                            <input type="file" name="file_path" class="form-control mb-3" required>
                            <label class="form-label">Additional Notes (Optional)</label>
                            <textarea name="content" class="form-control" rows="2" placeholder="e.g. video source, quiz instructions"></textarea>`;
                    }
                });
            }
        });
    </script>
@endsection

{{-- All Modals are moved outside of the main section to the bottom of the blade file as per convention --}}

<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-gradient text-white"
                style="background: linear-gradient(90deg,#1cc88a,#36b9cc);">
                <h5 class="modal-title" id="editCourseModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit
                    Course Info</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.courses.update', $course->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Title</label>
                            <input type="text" name="title" value="{{ $course->title }}" class="form-control"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Slug</label>
                            <input type="text" name="slug" value="{{ $course->slug }}" class="form-control"
                                required>
                        </div>
                        {{-- Note: The controller's update method validates 'description' --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Brief Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $course->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select">
                                @foreach (['draft', 'published'] as $status)
                                    <option value="{{ $status }}"
                                        {{ $course->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Thumbnail (4MB Max)</label>
                            <input type="file" name="thumbnail" class="form-control">
                        </div>
                        {{-- Removed all other event-specific fields (dates, location, organizer, etc.) --}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addContentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.courses.details.store', $course->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Course Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-12">
                        <label class="form-label">Content Type</label>
                        <select name="type" id="contentType" class="form-select" required>
                            <option value="">Select type...</option>
                            <option value="text">heading</option>
                            <option value="video">paragraph</option>
                            <option value="pdf">Image</option>
                            <option value="quiz">Feature</option>
                            <option value="assignment">Assignment</option>
                        </select>
                    </div>

                    <div class="col-12" id="dynamicContentField">
                        <p class="text-muted small">Select a content type to show fields.</p>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Content</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addPhaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.courses.phases.store', $course->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Course Phase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Duration (Days)</label>
                        <input type="number" name="duration" class="form-control" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Order</label>
                        <input type="number" name="order" class="form-control" min="1" value="{{ $course->phases->max('order') + 1 }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description/Content</label>
                        <textarea name="content" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Image (4MB Max)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Phase</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="addScheduleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.courses.schedules.store', $course->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type (e.g., bootcamp, live class)</label>
                        <input type="text" name="type" class="form-control" value="bootcamp">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>