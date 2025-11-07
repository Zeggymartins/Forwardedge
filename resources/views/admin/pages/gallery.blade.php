@extends('admin.master_page')

@section('title', 'Gallery Management')
{{-- CSS for hover --}}

@section('main')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold text-dark">📸 Gallery</h1>
        <!-- Add Photos Button -->
        <button class="btn btn-primary rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#addPhotoModal">
            <i class="bi bi-plus-circle me-2"></i> Add Photos
        </button>
    </div>
    {{-- Gallery Grid --}}
    <div class="row g-4">
        @foreach($photos as $photo)
            <div class="col-md-3">
                <div class="card gallery-card">
                    <div class="gallery-image-wrapper">
                        <img src="{{ asset('storage/'.$photo->image) }}" 
                             class="card-img-top fixed-size-img" alt="{{ $photo->title }}">
                        <div class="overlay">
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editPhotoModal{{ $photo->id }}">Edit</button>
                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deletePhotoModal{{ $photo->id }}">Delete</button>
                        </div>
                    </div>
                    <div class="card-body text-center m-4">
                        <h6>{{ $photo->title ?? 'Untitled' }}</h6>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editPhotoModal{{ $photo->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.gallery.update', $photo->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Photo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Title</label>
                                    <input type="text" name="title" value="{{ $photo->title }}" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Replace Image</label>
                                    <input type="file" name="image" class="form-control">
                                </div>
                                <img src="{{ asset('storage/'.$photo->image) }}" class="img-fluid mt-2 rounded">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Update</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Delete Modal --}}
            <div class="modal fade" id="deletePhotoModal{{ $photo->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <form action="{{ route('admin.gallery.destroy', $photo->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Photo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Are you sure you want to delete this photo?
                                <img src="{{ asset('storage/'.$photo->image) }}" class="img-fluid mt-2 rounded">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        @endforeach
    </div>
</div>

{{-- Add Photos Modal --}}
<div class="modal fade" id="addPhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Photos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Title for all photos</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Summer" required>
                    </div>
                    <div class="mb-3">
                        <label>Choose Photos</label>
                        <input type="file" name="images[]" class="form-control" multiple required accept="image/*">
                        <small class="text-muted">You can select multiple images at once</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Upload Photos</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- JS to dynamically add inputs --}}
<script>
function addPhotoInput() {
    let container = document.getElementById('photo-inputs');
    let html = `
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="titles[]" class="form-control">
        </div>
        <div class="mb-3">
            <label>Photo</label>
            <input type="file" name="images[]" class="form-control" required>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
}
</script>


@endsection