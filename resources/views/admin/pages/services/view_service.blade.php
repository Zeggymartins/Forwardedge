@extends('admin.master_page')

@section('title', 'Services Management')

@push('styles')
    <style>
        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(32, 201, 151, 0.05), rgba(13, 202, 240, 0.05));
            transition: all 0.3s ease;
        }

        .bg-gradient {
            background: linear-gradient(90deg, #20c997, #0dcaf0);
            color: #fff;
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .btn-primary {
            background: linear-gradient(90deg, #4e73df, #1cc88a);
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #1cc88a, #4e73df);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(78, 115, 223, 0.3);
        }

.service-thumbnail-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    /* ensures child image is clipped */
    display: flex;
    align-items: center;
    justify-content: center;
}

.service-thumbnail-wrapper img {
    /* ✨ CHANGE THESE TWO LINES: */
    width: 100%; 
    height: 100%;
    object-fit: cover;
    /* object-fit: cover ensures the image fills the 50x50 area without stretching, 
       cropping any excess parts. */
}
    </style>
@endpush

@section('main')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-dark">🛠 Services</h1>
            <a href="{{ route('admin.services.add') }}" class="btn btn-primary rounded-pill px-4 py-2">
                <i class="bi bi-plus-circle me-2"></i> Add Service
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="bg-gradient">
                        <tr>
                            <th class="py-3 px-4">#</th>
                            <th class="py-3 px-4">Thumbnail</th>
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
                                <td class="py-4 px-4">
                                    @if ($service->thumbnail)
                                        <div class="service-thumbnail-wrapper shadow-sm">
                                                               <img src="{{ asset('storage/' . $service->thumbnail) }}" 
                         alt="{{ $service->title }}" 
                         class="img-fluid rounded-circle shadow-sm border"
                         style="width: 100px; height: 100px; object-fit: cover;">
                                        </div>
                                    @else
                                        <div class="service-thumbnail-wrapper bg-light shadow-sm">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>


                                <td class="py-4 px-4 fw-bold">{{ $service->title }}</td>
                                <td class="py-4 px-4">{{ Str::limit($service->brief_description, 50) }}</td>
                                <td class="py-4 px-4 text-muted"><code>{{ $service->slug }}</code></td>
                                <td class="py-4 px-4 text-center">
                                    <a href="{{ route('admin.services.show', $service->id) }}"
                                        class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-2">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 py-2"
                                            onclick="return confirm('Delete this service? All contents will be removed.')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted fs-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    🚀 No services yet. Click "Add Service" to get started!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
