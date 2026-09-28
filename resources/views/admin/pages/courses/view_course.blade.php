@extends('admin.master_page')
@section('title', 'Academy Training')
@section('main')
<div class="container py-4">
    <div class="pagetitle">
        <div class="pagetitle-left">
            <div class="pagetitle-icon"><i class="bi bi-layout-text-window-reverse"></i></div>
            <div>
                <h1>Academy Training</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Training Programs</li>
                    </ol>
                </nav>
            </div>
        </div>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i> Add Training
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $index=>$course)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-4 px-4">{{ $index + 1 }}</td>
                            <td class="py-4 px-4 fw-bold">{{ $course->title }}</td>
                            <td class="py-4 px-4">
                                <span class="badge rounded-pill bg-{{ $course->status == 'published' ? 'success' : 'secondary' }} px-3 py-2">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-muted">{{ $course->slug }}</td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('admin.courses.dashboard', $course->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-2">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2"
                                            onclick="return confirm('Delete this academy training?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted fs-5">🚀 No academy training yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
