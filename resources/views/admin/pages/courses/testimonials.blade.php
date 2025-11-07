@extends('admin.master_page')

@section('title', 'Testimonials • ' . ($course->title ?? 'Academy Training'))



@section('main')
    <div class="container py-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold mb-1">🌟 Testimonials</h1>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Academy Training
                </a>
                <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                    <i class="bi bi-plus-circle me-2"></i> Add Testimonial
                </button>
            </div>
        </div>

        {{-- Cards Grid (replaces the table) --}}
        @if($items->count())
            <div class="row g-4">
                @foreach($items as $index => $t)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card testimonial-card border-0 shadow-soft rounded-12 h-100 p-4">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <img
                                       class="img-fluid rounded-circle shadow-sm border"
                                        src="{{ $t->image ? asset('storage/' . $t->image) : asset('images/avatar-placeholder.png') }}"
                                        alt="{{ $t->name }} avatar" style="width: 100px; height: 100px; object-fit: cover;">
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate">{{ $t->name }}</div>
                                        <div class="text-muted small text-truncate">{{ $t->organization ?: '—' }}</div>
                                        <span class="badge badge-subtle mt-2">#{{ $index + 1 }}</span>
                                    </div>
                                </div>

                                <p class="mb-4 line-clamp-4">
                                    {{ strip_tags($t->body) }}
                                </p>

                                <div class="mt-auto d-flex justify-content-between align-items-center card-actions">
                                    <button class="btn btn-sm btn-outline-warning px-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editTestimonialModal{{ $t->id }}">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>

                                    <form class="d-inline"
                                          action="{{ route('admin.courses.testimonials.destroy', ['course' => $t->course_id, 'testimonial' => $t->id]) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this testimonial?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger px-3">
                                            <i class="bi bi-trash me-1"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Edit Modal (unchanged) --}}
                    <div class="modal fade" id="editTestimonialModal{{ $t->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form
                                    action="{{ route('admin.courses.testimonials.update', ['course' => $t->course_id, 'testimonial' => $t->id]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-header bg-gradient">
                                        <h5 class="modal-title text-white">Edit Testimonial</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Name</label>
                                                <input type="text" name="name" class="form-control" value="{{ $t->name }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Organization (optional)</label>
                                                <input type="text" name="organization" class="form-control" value="{{ $t->organization }}">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Testimonial</label>
                                                <textarea name="body" class="form-control" rows="5" required>{{ $t->body }}</textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Image (square works best)</label>
                                                <input type="file" name="image" class="form-control" accept="image/*"
                                                       onchange="previewEditImg{{ $t->id }}(event)">
                                                <div class="mt-2">
                                                    <img id="editPreview{{ $t->id }}"
                                                         src="{{ $t->image ? asset('storage/' . $t->image) : asset('images/avatar-placeholder.png') }}"
                                                         class="avatar" alt="preview">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-success">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script>
                        function previewEditImg{{ $t->id }}(e) {
                            const el = document.getElementById('editPreview{{ $t->id }}');
                            if (e.target.files && e.target.files[0]) el.src = URL.createObjectURL(e.target.files[0]);
                        }
                    </script>
                @endforeach
            </div>
        @else
            <div class="card border-0 shadow-soft rounded-12">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                    <div class="fs-5 mb-3">No testimonials yet.</div>
                    <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                        <i class="bi bi-plus-circle me-2"></i> Add your first testimonial
                    </button>
                </div>
            </div>
        @endif

        {{-- (Optional) Pagination --}}
        @if (method_exists($items, 'links'))
            <div class="mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    {{-- Add Modal (unchanged) --}}
    <div class="modal fade" id="addTestimonialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.courses.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-gradient">
                        <h5 class="modal-title text-white">Add Testimonial</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <select name="course_id" class="form-select" required>
                                    @foreach ($courses as $c)
                                        <option value="{{ $c->id }}" @selected(old('course_id', $course->id) == $c->id)>
                                            {{ $c->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Organization (optional)</label>
                                <input type="text" name="organization" class="form-control" value="{{ old('organization') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Testimonial</label>
                                <textarea name="body" class="form-control" rows="5" required>{{ old('body') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Image (optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*"
                                       onchange="previewAddImg(event)">
                                <div class="mt-2">
                                    <img id="addPreview" src="{{ asset('images/avatar-placeholder.png') }}" class="avatar" alt="preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Image previews (deduped to a single function) --}}
    <script>
        function previewAddImg(e) {
            const out = document.getElementById('addPreview');
            const [file] = e.target.files || [];
            if (file && out) out.src = URL.createObjectURL(file);
        }
    </script>
@endsection
