@extends('admin.master_page')
@section('title', 'Courses')
@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">📚 Courses</h1>
        <a href="{{ route('courses.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
            <i class="bi bi-plus-circle me-2"></i> Add Course
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded py-3 px-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient" style="background: linear-gradient(90deg, #6610f2, #6f42c1); color: #fff;">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-4 px-4">{{ $course->id }}</td>
                            <td class="py-4 px-4 fw-bold">{{ $course->title }}</td>
                            <td class="py-4 px-4">
                                <span class="badge rounded-pill bg-{{ $course->status == 'published' ? 'success' : 'secondary' }} px-3 py-2">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-muted">{{ $course->slug }}</td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-2">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2"
                                            onclick="return confirm('Delete this course?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted fs-5">🚀 No courses yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection