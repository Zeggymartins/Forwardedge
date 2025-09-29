@extends('admin.master_page')
@section('title', 'Services')
@section('main')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">🛠 Services</h1>
        <a href="{{ route('services.create') }}" class="btn btn-primary rounded-pill px-4 py-2">
            <i class="bi bi-plus-circle me-2"></i> Add Service
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm rounded py-3 px-4">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="bg-gradient" style="background: linear-gradient(90deg, #20c997, #0dcaf0); color: #fff;">
                    <tr>
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">Title</th>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4">Slug</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-4 px-4">{{ $service->id }}</td>
                            <td class="py-4 px-4 fw-bold">{{ $service->title }}</td>
                            <td class="py-4 px-4">{{ Str::limit($service->brief_description, 50) }}</td>
                            <td class="py-4 px-4 text-muted">{{ $service->slug }}</td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('services.show', $service->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-2">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('services.destroy', $service->id) }}" method="POST" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2"
                                            onclick="return confirm('Delete this service?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted fs-5">🚀 No services yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection