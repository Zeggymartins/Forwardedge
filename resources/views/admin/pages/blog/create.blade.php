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

    {{-- Form for Blog Creation --}}
 <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data" 
      class="card border-0 shadow-sm p-4 rounded-3">
    @csrf

    <div class="row g-4">
        <!-- Blog Title -->
        <div class="col-md-8">
            <label class="form-label fw-semibold">Blog Title</label>
            <input type="text" name="title" value="{{ old('title') }}" 
                   class="form-control" 
                   placeholder="Enter a compelling title" required>
        </div>

        <!-- Category -->
        <div class="col-md-4">
            <label class="form-label fw-semibold">Category</label>
            <input type="text" name="category" value="{{ old('category') }}" 
                   class="form-control" 
                   placeholder="e.g., Technology, Lifestyle, Food">
        </div>

        <!-- Thumbnail -->
        <div class="col-md-6">
            <label class="form-label fw-semibold">Thumbnail Image</label>
            <input type="file" name="thumbnail" class="form-control" accept="image/*">
            <div class="form-text">Upload a JPG/PNG (Max 4MB).</div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Meta Title (optional)</label>
            <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                   class="form-control"
                   placeholder="SEO title for this post">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold">Meta Description (optional)</label>
            <textarea name="meta_description" class="form-control" rows="2"
                      placeholder="Short SEO description (max ~300 chars)">{{ old('meta_description') }}</textarea>
        </div>
    </div>

    <!-- Submit -->
    <div class="text-end mt-4">
        <button class="btn btn-primary px-4 rounded-pill">
            <i class="bi bi-save me-2"></i> Save Post
        </button>
    </div>
</form>

</div>
@endsection
