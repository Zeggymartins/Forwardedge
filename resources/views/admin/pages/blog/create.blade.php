@extends('admin.master_page')

@section('title', 'Create New Blog Post')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark"><i class="bi bi-feather me-2 text-primary"></i> Create New Blog Post</h1>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Back to Blogs
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm rounded-3">
            <h6 class="mb-2"><i class="bi bi-exclamation-circle"></i> Please fix the following errors:</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form for Blog Creation --}}
    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-md p-5 rounded-4">
        @csrf

        <!-- Section: Basic Details -->
        <div class="section-header mb-4 border-bottom pb-2">
            <h5 class="fw-semibold text-primary">Post Details</h5>
        </div>
        <div class="row g-4">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Blog Title</label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-control form-control-lg" placeholder="A compelling title for your blog post" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Category</label>
                <input type="text" name="category" value="{{ old('category') }}" class="form-control form-control-lg" placeholder="e.g., Technology, Lifestyle, Food">
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="Optional: will auto-generate from title">
                <div class="form-text">A URL-friendly version of the title.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">Thumbnail Image (Max 4MB)</label>
                <input type="file" name="thumbnail" class="form-control" accept="image/*">
                <div class="form-text">The main image for your blog card/listing.</div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Publication Status</label>
                <div class="form-check form-switch fs-5">
                    <input class="form-check-input" type="checkbox" id="isPublishedSwitch" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }}>
                    <label class="form-check-label" for="isPublishedSwitch">
                        {{ old('is_published') ? 'Published (Live)' : 'Draft (Hidden)' }}
                    </label>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="text-end mt-5">
            <button class="btn btn-primary btn-lg px-5 shadow-lg rounded-pill">
                <i class="bi bi-save me-2"></i> Save Post & Edit Content
            </button>
        </div>
    </form>
</div>
@endsection
