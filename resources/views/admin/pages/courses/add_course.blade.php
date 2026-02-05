@extends('admin.master_page')

@section('main')
    <div class="container py-4">
        <h3 class="mb-4 fw-bold">Create Training </h3>

        <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
            @csrf

            <div class="card shadow-sm mb-4 p-4">
                <h5 class="fw-semibold mb-3">Training Details</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" required>
                    </div>
                    <input type="hidden" name="slug" id="slug">
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" selected>Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                {{-- External Platform Fields --}}
                <h5 class="fw-semibold mb-2">External Platform (optional)</h5>
                <p class="text-muted small mb-3">
                    Check this if the course is hosted on an external platform like Udemy, Teachable, etc.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_external" id="is_external" value="1"
                                {{ old('is_external') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_external">
                                This course is hosted on an external platform
                            </label>
                        </div>
                    </div>

                    <div id="external-fields" style="display: {{ old('is_external') ? 'block' : 'none' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">External Platform</label>
                                <select name="external_platform_name" class="form-select">
                                    <option value="">Select Platform</option>
                                    @foreach(['Udemy', 'Teachable', 'Coursera', 'Skillshare', 'LinkedIn Learning', 'Other'] as $platform)
                                        <option value="{{ $platform }}" {{ old('external_platform_name') == $platform ? 'selected' : '' }}>
                                            {{ $platform }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Course URL on Platform</label>
                                <input type="url" name="external_course_url" class="form-control"
                                    value="{{ old('external_course_url') }}"
                                    placeholder="https://www.udemy.com/course/your-course/">
                                <small class="text-muted">Full URL where users can purchase this course</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h5 class="fw-semibold mb-2">Schedule (optional)</h5>
                <p class="text-muted small mb-4">
                    Each course can carry one schedule. You can always edit it later from the course dashboard.
                </p>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="schedule[start_date]" class="form-control"
                            value="{{ old('schedule.start_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="schedule[end_date]" class="form-control"
                            value="{{ old('schedule.end_date') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="schedule[location]" class="form-control"
                            placeholder="e.g. Lagos / Remote" value="{{ old('schedule.location') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Delivery Type</label>
                        <select name="schedule[type]" class="form-select">
                            <option value="">Select type</option>
                            @foreach (['virtual', 'hybrid', 'physical'] as $type)
                                <option value="{{ $type }}" @selected(old('schedule.type') === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-end mb-5">
                <button type="submit" class="btn btn-success px-4">Create Course</button>
            </div>
        </form>
    </div>


@push('scripts')

<script>
const slugInput = document.getElementById('slug');
const titleInput = document.getElementById('title');
if (titleInput && slugInput) {
  titleInput.addEventListener('input', function () {
    slugInput.value = this.value
      .trim()
      .toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/\s+/g, '-');
  });
}

// Toggle external platform fields
const externalCheckbox = document.getElementById('is_external');
const externalFields = document.getElementById('external-fields');

if (externalCheckbox && externalFields) {
  externalCheckbox.addEventListener('change', function() {
    externalFields.style.display = this.checked ? 'block' : 'none';
  });
}
</script>
@endpush

@endsection
