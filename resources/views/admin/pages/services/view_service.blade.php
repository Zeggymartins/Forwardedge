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

    .service-thumbnail-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-thumbnail-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .modal-thumbnail-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 15px auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-thumbnail-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $index => $service)
                        <tr class="table-row-hover">
                            <td class="fw-semibold py-4 px-4">{{ $index + 1 }}</td>
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
                            <td class="py-4 px-4 text-center">
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3 py-2 me-1"
                                    data-bs-toggle="modal" data-bs-target="#updateServiceModal"
                                    data-id="{{ $service->id }}"
                                    data-title="{{ htmlentities($service->title, ENT_QUOTES) }}"
                                    data-description="{{ htmlentities($service->brief_description, ENT_QUOTES) }}"
                                    data-thumbnail="{{ $service->thumbnail }}">
                                    <i class="bi bi-pencil"></i> Update
                                </button>
                                <a href="{{ route('admin.services.show', $service->id) }}"
                                    class="btn btn-sm btn-outline-info rounded-pill px-3 py-2 me-1">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline">
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
                            <td colspan="5" class="text-center py-5 text-muted fs-5">
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

<!-- Update Modal -->
<div class="modal fade" id="updateServiceModal" tabindex="-1" aria-labelledby="updateServiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="updateServiceForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="serviceId">
                    <div class="mb-3 text-left">
                        <div class="modal-thumbnail-wrapper">
                            <img id="currentThumbnail" src="" alt="Thumbnail"  class="img-fluid rounded-circle shadow-sm border mb-2"
                                                style="width: 250px; height: 250px; object-fit: cover;">
                        </div>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" id="serviceTitle" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Brief Description</label>
                        <textarea name="brief_description" id="serviceDescription" class="form-control" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success rounded-pill px-4">Update Service</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const updateModal = document.getElementById('updateServiceModal');
    updateModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const thumbnail = button.getAttribute('data-thumbnail');

        document.getElementById('serviceId').value = id;
        document.getElementById('serviceTitle').value = title;
        document.getElementById('serviceDescription').value = description;

        const img = document.getElementById('currentThumbnail');
        img.src = thumbnail ? `/storage/${thumbnail}` : '/placeholder-image.png';

        // Optional: update form action dynamically
        document.getElementById('updateServiceForm').action = `/admin/services/${id}`;
    });
</script>
@endsection
