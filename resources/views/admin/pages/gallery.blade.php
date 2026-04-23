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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Upload failed.</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div id="galleryUploadAlert" class="alert d-none" role="alert"></div>
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
        <form id="galleryUploadForm" action="{{ route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data">
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
                        <input type="file" id="galleryImages" name="images[]" class="form-control" multiple required accept="image/*">
                        <small class="text-muted">Large selections are uploaded in small batches to avoid server upload limits.</small>
                    </div>
                    <div class="progress d-none" id="galleryUploadProgress" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted d-none mt-2" id="galleryUploadStatus"></small>
                    <div class="alert alert-danger d-none mt-3 mb-0" id="galleryUploadError"></div>
                    <div class="alert alert-success d-none mt-3 mb-0" id="galleryUploadSuccess"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="galleryUploadButton">Upload Photos</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- JS to dynamically add inputs --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('galleryUploadForm');
    if (!form) return;

    const batchSize = 3;
    const fileInput = document.getElementById('galleryImages');
    const button = document.getElementById('galleryUploadButton');
    const progress = document.getElementById('galleryUploadProgress');
    const progressBar = progress?.querySelector('.progress-bar');
    const status = document.getElementById('galleryUploadStatus');
    const errorBox = document.getElementById('galleryUploadError');
    const successBox = document.getElementById('galleryUploadSuccess');

    const setMessage = (box, message) => {
        if (!box) return;
        box.textContent = message;
        box.classList.toggle('d-none', !message);
    };

    const setProgress = (done, total) => {
        const percent = total ? Math.round((done / total) * 100) : 0;
        progress?.classList.remove('d-none');
        if (progressBar) {
            progressBar.style.width = `${percent}%`;
            progressBar.setAttribute('aria-valuenow', String(percent));
        }
        if (status) {
            status.textContent = `Uploaded ${done} of ${total} selected file(s)`;
            status.classList.remove('d-none');
        }
    };

    form.addEventListener('submit', async (event) => {
        const files = Array.from(fileInput?.files || []);
        if (files.length <= batchSize) return;

        event.preventDefault();
        setMessage(errorBox, '');
        setMessage(successBox, '');

        button.disabled = true;
        fileInput.disabled = true;

        let uploaded = 0;
        try {
            for (let start = 0; start < files.length; start += batchSize) {
                const formData = new FormData();
                formData.append('_token', form.querySelector('input[name="_token"]').value);
                formData.append('title', form.querySelector('input[name="title"]').value);

                files.slice(start, start + batchSize).forEach((file) => {
                    formData.append('images[]', file);
                });

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.success) {
                    const errors = data.errors ? Object.values(data.errors).flat().join(' ') : '';
                    throw new Error(errors || data.message || 'One upload batch failed.');
                }

                uploaded += Number(data.uploaded || 0);
                setProgress(Math.min(start + batchSize, files.length), files.length);
            }

            setMessage(successBox, `${uploaded} photo(s) uploaded successfully. Refreshing gallery...`);
            window.location.reload();
        } catch (error) {
            setMessage(errorBox, error.message || 'Upload failed. Please try again.');
            button.disabled = false;
            fileInput.disabled = false;
        }
    });
});
</script>


@endsection
