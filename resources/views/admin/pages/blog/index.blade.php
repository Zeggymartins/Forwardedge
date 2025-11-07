@extends('admin.master_page')

@section('title', 'Blog Posts List')

@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark"><i class="bi bi-journal-richtext me-2 text-primary"></i> Blog Posts</h1>
        {{-- Link to the new blog creation route --}}
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
            <i class="bi bi-plus-circle me-2"></i> Create New Post
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="blog-header-gradient">
                    <tr>
                        <th scope="col" class="py-3 px-4">#</th>
                        <th scope="col" class="py-3 px-4">Blog Post</th>
                        <th scope="col" class="py-3 px-4">Status</th>
                        <th scope="col" class="py-3 px-4">Category</th>
                        <th scope="col" class="py-3 px-4">Author</th>
                        <th scope="col" class="py-3 px-4">Created On</th>
                        <th scope="col" class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through the Blog model collection --}}
                    @forelse($blogs as $index=>$blog)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-4 px-4">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">
                                <div>
                                    <span class="fw-bold fs-6">{{ $blog->title }}</span><br>
                                    <small class="text-muted">{{ $blog->slug ?? 'no-slug' }}</small>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $status_class = $blog->is_published ? 'success' : 'warning';
                                    $status_text = $blog->is_published ? 'Published' : 'Draft';
                                @endphp
                                <span class="badge rounded-pill bg-{{ $status_class }} px-3 py-2">
                                    {{ $status_text }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    {{ $blog->category ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <i class="bi bi-person-circle text-secondary me-1"></i>
                                {{ $blog->author->name ?? 'System User' }}
                            </td>
                            <td class="py-4 px-4">
                                {{ $blog->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-4 text-center">
                                {{-- Link to the blog dashboard (show route) --}}
                                <a href="{{ route('admin.blogs.show', $blog->id) }}"
                                   class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-2">
                                   <i class="bi bi-box-arrow-in-right"></i> Dashboard
                                </a>
                                {{-- Form for deleting the blog post --}}
                                <form action="{{ route('admin.blogs.destroy', $blog->id) }}"
                                      method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2"
                                            onclick="return confirm('Are you sure you want to delete this blog post? This action cannot be undone.')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted fs-5">✍️ No blog posts found. Click 'Create New Post' to start writing.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
