@extends('admin.master_page')

@section('title', $service->title . ' - Service Details')

@push('styles')
    <style>
        .service-header {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 32px;
            margin-bottom: 30px;
            border: 1px solid #eaeaea;
        }

        .service-header h1 {
            font-weight: 700;
            color: #2c3e50;
        }

        .content-card {
            border: 1px solid #eee;
            border-radius: 14px;
            transition: all 0.2s ease;
            background: white;
        }

        .content-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        .content-type-badge {
            font-size: 0.7rem;
            padding: 4px 10px;
            border-radius: 50px;
        }

        .modal-content {
            border-radius: 14px;
            border: 1px solid #eee;
        }

        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            border-radius: 14px 14px 0 0 !important;
        }

        .btn-outline-primary,
        .btn-outline-danger {
            border-radius: 50px !important;
            font-weight: 500;
        }
    </style>
@endpush

@section('main')
    <div class="container py-5">
        {{-- Responsive Header: Title and Back Button --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <h1 class="fw-bold text-dark mb-3 mb-md-0"><i class="bi bi-gear-wide-connected me-2 text-primary"></i> {{ $service->title }}
                Details</h1>
            <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary rounded-pill px-4  w-sm-auto">
                <i class="bi bi-arrow-left"></i> Back to Services
            </a>
        </div>

        {{-- Responsive Service Header: Info and Add Button --}}
        <div class="service-header row align-items-center mb-3">
            <div class="col-12 col-md-9">
                <h1 class="mb-1">{{ $service->title }}</h1>
                <p class="text-muted mb-0">Manage the modular content blocks for this service page.</p>
            </div>
            <div class="col-12 col-md-3 text-start text-md-end mt-3 mt-md-0">
                <button class="btn btn-primary rounded-pill px-4 py-2 shadow-sm  w-sm-auto" data-bs-toggle="modal"
                    data-bs-target="#addContentModal">
                    <i class="bi bi-plus-lg me-2"></i> Add Content Block
                </button>
            </div>
        </div>

        {{-- Content List --}}
        <div class="row g-4">
            @forelse($service->contents->sortBy('position') as $content)
                <div class="col-12">
                    <div class="card content-card p-4 shadow-sm">
                        {{-- Added flex-wrap to ensure buttons stack on mobile --}}
                        <div class="d-flex flex-wrap justify-content-between align-items-start">
                            <div class="d-flex align-items-start flex-grow-1 me-0 me-sm-3 mb-2 mb-sm-0">
                                <span class="badge bg-primary me-3 p-2 fw-bold rounded-pill"
                                    style="min-width: 40px;">{{ $content->position }}</span>
                                <div class="w-100">
                                    <span class="content-type-badge bg-light text-secondary border">
                                        {{ ucfirst($content->type) }}
                                    </span>
                                    <h5 class="mt-1 mb-2 fw-semibold">
                                        {{-- 💡 FIX: Use asString() for safe display in title/preview --}}
                                        {{ \Illuminate\Support\Str::limit($content->asString(), 80) }}
                                    </h5>

                                    @if ($content->type === 'image')
                                        <img src="{{ asset('storage/' . $content->content) }}"
                                            alt="Content Image" style="max-height: 80px;" class="rounded shadow-sm">
                                    @elseif ($content->type === 'list')
                                        {{-- 💡 FIX: Use asArray() for iterating lists --}}
                                        <ul class="list-unstyled small text-muted mb-0">
                                            @foreach ($content->asArray() as $item)
                                                <li><i class="bi bi-check-circle-fill text-success me-1"></i>
                                                    {{ \Illuminate\Support\Str::limit($item, 50) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted small mb-0">
                                            {{ \Illuminate\Support\Str::limit($content->asString(), 150) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="btn-group mt-2 mt-sm-0 flex-shrink-0 ms-auto">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editContentModal{{ $content->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('admin.services.contents.destroy', [$service->id, $content->id]) }}"
                                    method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this content block?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit Content Modal (Responsive column classes are already good) --}}
                <div class="modal fade" id="editContentModal{{ $content->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form
                                action="{{ route('admin.services.contents.update', [$service->id, $content->id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit {{ ucfirst($content->type) }} Block (Pos:
                                        {{ $content->position }})</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body row g-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label">Position</label>
                                        <input type="number" name="position" value="{{ $content->position }}"
                                            class="form-control" required min="1">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <label class="form-label">Type (Locked)</label>
                                        <input type="text" class="form-control"
                                            value="{{ ucfirst($content->type) }}" disabled>
                                    </div>

                                    @if ($content->type === 'image' || $content->type === 'video')
                                        <div class="col-12">
                                            <label class="form-label">Replace File</label>
                                            {{-- Content is a file path (string) here, no need for helper --}}
                                            <input type="file" name="content" class="form-control"
                                                accept="{{ $content->type === 'image' ? 'image/*' : 'video/*' }}">
                                            <div class="form-text">Current: {{ basename($content->content) }}</div>
                                        </div>
                                    @elseif ($content->type === 'list' || $content->type === 'features')
                                        <div class="col-12">
                                            <label class="form-label">{{ ucfirst($content->type) }} Items (One per
                                                line)</label>
                                            {{-- 💡 FIX: Use asArray() to get the list items and implode them for the textarea --}}
                                            <textarea name="content" class="form-control" rows="6" required>{{ implode("\n", $content->asArray()) }}</textarea>
                                            <div class="form-text">Each line will be saved as a separate item.</div>
                                        </div>
                                    @else
                                        <div class="col-12">
                                            <label class="form-label">Content</label>
                                            {{-- 💡 FIX: Use asString() to safely display the string content in the textarea --}}
                                            <textarea name="content" class="form-control" rows="5" required>{{ $content->asString() }}</textarea>
                                        </div>
                                    @endif

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card p-5 text-center text-muted border-0 shadow-sm">
                        <i class="bi bi-bricks fs-1 mb-3 text-secondary"></i>
                        <p class="fs-5 mb-0">No content blocks defined for this service yet.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>


    {{-- Add Content Modal (Kept as is for structure reference) --}}
    <div class="modal fade" id="addContentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.services.contents.store', $service->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Content Block</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-12 col-md-8">
                            <label class="form-label">Block Type</label>
                            <select name="type" id="addBlockType" class="form-select" required>
                                <option value="" disabled selected>Select content type...</option>
                                <option value="heading">Heading</option>
                                <option value="paragraph">Paragraph/Text</option>
                                <option value="list">List</option>
                                <option value="features">Features/Benefits List</option>
                                <option value="image">Image</option>
                                <option value="video">Video URL</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Position</label>
                            <input type="number" name="position" class="form-control" min="1"
                                value="{{ $service->contents->max('position') + 1 }}">
                        </div>

                        <!-- Dynamic Fields -->
                        <div class="col-12" id="addDynamicContent">
                            <p class="text-muted small">Select a block type above.</p>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Add Block</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // --- Dynamic Content Input for ADD Modal ---
                const typeSelect = document.getElementById('addBlockType');
                const dynamicFieldContainer = document.getElementById('addDynamicContent');

                const updateDynamicFields = (type) => {
                    dynamicFieldContainer.innerHTML = ''; // Clear previous content

                    let contentHtml = '';

                    switch (type) {
                        case 'heading':
                        case 'paragraph':
                            contentHtml = `
                                <label class="form-label">${type === 'heading' ? 'Heading Text' : 'Paragraph Text'}</label>
                                <textarea name="content" class="form-control" rows="${type === 'heading' ? '1' : '5'}" required placeholder="Enter the text content"></textarea>`;
                            break;
                        case 'list':
                        case 'features':
                            contentHtml = `
                                <label class="form-label">${type === 'list' ? 'List Items' : 'Features (one per line)'}</label>
                                <textarea name="content" class="form-control" rows="6" placeholder="Item 1\nItem 2\nItem 3" required></textarea>
                                <div class="form-text">Each line will be saved as a separate item.</div>`;
                            break;
                        case 'image':
                            contentHtml = `
                                <label class="form-label">Upload Image File</label>
                                <input type="file" name="content" class="form-control" accept="image/*" required>`;
                            break;
                        case 'video':
                            contentHtml = `
                                <label class="form-label">Video URL (e.g., YouTube or Vimeo link)</label>
                                <input type="url" name="content" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>`;
                            break;
                        default:
                            contentHtml = `<p class="text-danger small">Select a block type to see the corresponding input fields.</p>`;
                            break;
                    }

                    dynamicFieldContainer.innerHTML = contentHtml;
                };

                if (typeSelect) {
                    typeSelect.addEventListener('change', function() {
                        updateDynamicFields(this.value);
                    });
                }


                // --- List Item Management (for modals containing list/feature content) ---
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.addEventListener('click', function(e) {
                        if (e.target.closest('.addListItem')) {
                            const container = e.target.closest('.list-group');
                            let newItem = document.createElement("div");
                            newItem.classList.add("input-group", "mb-2");
                            newItem.innerHTML =
                                `
                <input type="text" name="content[]" class="form-control" placeholder="List item" required>
                <button type="button" class="btn btn-outline-danger removeListItem"><i class="bi bi-dash"></i></button>`;
                            container.appendChild(newItem);
                        }
                        if (e.target.closest('.removeListItem')) {
                            e.target.closest('.input-group').remove();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
