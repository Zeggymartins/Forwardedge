@extends('admin.master_page')

@section('title', $blog->title . ' Dashboard')

@push('styles')
    <style>
        @media (max-width: 575.98px) {
            .content-block .btn-group {
                width: 100%;
                flex-direction: column;
                gap: .5rem;
            }

            .content-block .btn-group .btn {
                width: 100%;
            }
        }

        .list-editor [data-list-item]+[data-list-item] {
            margin-top: .5rem;
        }
    </style>
@endpush


@section('main')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ $blog->title }} <small class="text-muted">Blog Dashboard</small></h1>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        {{-- Navigation Tabs --}}
        <ul class="nav nav-tabs nav-pills flex-wrap mb-4 shadow-sm rounded-3 fe-admin-tabs" id="blogTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active d-flex align-items-center px-4 py-2" id="overview-tab" data-bs-toggle="tab"
                    href="#overview" role="tab">
                    <i class="bi bi-info-circle me-2"></i> Post Overview
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link d-flex align-items-center px-4 py-2" id="details-tab" data-bs-toggle="tab"
                    href="#details" role="tab">
                    <i class="bi bi-columns-gap me-2"></i> Content Details
                </a>
            </li>
        </ul>


        <div class="tab-content">
            <!-- Overview Tab (Main Blog Info) -->
            <div class="tab-pane fade show active" id="overview">
                <div class="card shadow-md border-0 rounded-3">
                    <div
                        class="card-header d-flex justify-content-between align-items-center bg-gradient-cyan text-white mb-4">
                        <h5 class="mb-0">Blog Post Summary</h5>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editBlogModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit Info
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : 'https://via.placeholder.com/300x200?text=Post+Thumbnail' }}"
                                    class="img-fluid rounded shadow-sm mb-3 blog-thumb-lg" alt="Blog Thumbnail">
                                <p><strong>Category:</strong> {{ $blog->category ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-8">
                                <h4 class="fw-bold">{{ $blog->title }}</h4>
                                <p class="text-muted">
                                    <strong>Author:</strong> {{ $blog->author->name ?? 'Unknown' }}
                                </p>

                                <div class="row">
                                    <div class="col-sm-6">
                                        <p><strong>Slug:</strong> {{ $blog->slug }}</p>
                                    </div>
                                    <div class="col-sm-6">
                                        <p><strong>Status:</strong>
                                            <span class="badge bg-{{ $blog->is_published ? 'success' : 'warning' }}">
                                                {{ $blog->is_published ? 'Published' : 'Draft' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Details Tab (BlogDetail) -->
            <div class="tab-pane fade" id="details">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Modular Content Blocks</h5>
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal"
                        data-bs-target="#addDetailModal">
                        <i class="bi bi-plus-lg me-2"></i> Add Content Block
                    </button>
                </div>

                {{-- List Existing Content Blocks --}}
                <div class="list-group">
                    @forelse ($blog->details->sortBy('order') as $detail)
                        <div
                            class="list-group-item content-block p-4 mb-3 d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-secondary me-3 p-2 rounded-pill fw-bold">{{ $detail->order }}</span>
                                <div>
                                    <h6 class="mb-1 text-uppercase small text-primary">{{ $detail->type }}</h6>

                                    {{-- Display content preview based on type --}}
                                    @switch($detail->type)
                                        @case('image')
                                            @php $imageUrl = $detail->imageUrl(); @endphp
                                            @if ($imageUrl)
                                                <p class="small text-muted mb-0">
                                                    <i class="bi bi-image me-1"></i>
                                                    {{ basename($detail->content) }}
                                                </p>
                                                <img src="{{ $imageUrl }}" alt="Content Image" style="max-height: 100px;"
                                                    class="mt-2 rounded shadow-sm">
                                            @else
                                                <p class="text-muted">No image uploaded.</p>
                                            @endif
                                        @break

                                        @case('list')
                                            @php $listItems = $detail->contentArray(); @endphp
                                            <p class="small text-muted mb-0">List with {{ count($listItems) }} items</p>
                                            <ul class="small mt-2 mb-0">
                                                @foreach (array_slice($listItems, 0, 2) as $item)
                                                    <li>{{ \Illuminate\Support\Str::limit($item, 50) }}</li>
                                                @endforeach
                                                @if (count($listItems) > 2)
                                                    <li>... and more.</li>
                                                @endif
                                            </ul>
                                        @break

                                        @case('heading')
                                            <h4 class="mb-0">{{ $detail->contentString() }}</h4>
                                        @break

                                        @case('quote')
                                            <blockquote class="blockquote small mb-0">
                                                {{ \Illuminate\Support\Str::limit($detail->contentString(), 100) }}
                                                <footer class="blockquote-footer mt-1">{{ $detail->quoteAuthor() }}</footer>
                                            </blockquote>
                                        @break

                                        @default
                                            <p class="mb-0">{{ \Illuminate\Support\Str::limit($detail->contentString(), 150) }}
                                            </p>
                                        @break
                                    @endswitch
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Edit -->
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#editDetailModal{{ $detail->id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('admin.blogs.details.destroy', [$blog->id, $detail->id]) }}"
                                    method="POST" onsubmit="return confirm('Delete this content block?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit Detail Modal (Per Detail) --}}
                        <div class="modal fade" id="editDetailModal{{ $detail->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form action="{{ route('admin.blogs.details.update', [$blog->id, $detail->id]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">Edit {{ ucfirst($detail->type) }} Block (Order:
                                                {{ $detail->order }})</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Order</label>
                                                <input type="number" name="order" value="{{ $detail->order }}"
                                                    class="form-control" required min="1">
                                            </div>
                                            <div class="col-md-9">
                                                <label class="form-label">Type (Locked)</label>
                                                <input type="text" class="form-control"
                                                    value="{{ ucfirst($detail->type) }}" disabled>
                                            </div>

                                            @if ($detail->type === 'image')
                                                <div class="col-12">
                                                    <label class="form-label">Replace Image (Current:
                                                        {{ basename($detail->content) }})</label>
                                                    <input type="file" name="content" class="form-control">
                                                    <div class="form-text">Upload a new image to replace the current one.
                                                    </div>
                                                </div>
                                            @elseif ($detail->type === 'list')
                                                @php
                                                    $listItems = $detail->contentArray();
                                                    if (empty($listItems)) {
                                                        $listItems = [''];
                                                    }
                                                @endphp
                                                <div class="col-12">
                                                    <label class="form-label">List Items</label>
                                                    <div class="list-editor" data-list-editor data-name="content[]"
                                                        data-compact="0">
                                                        <div data-list-items>
                                                            @foreach ($listItems as $item)
                                                                <div class="input-group mb-2" data-list-item>
                                                                    <input type="text" name="content[]"
                                                                        class="form-control" value="{{ $item }}"
                                                                        placeholder="List item">
                                                                    <button type="button" class="btn btn-outline-danger"
                                                                        data-action="remove-list-item"><i
                                                                            class="bi bi-dash"></i></button>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button"
                                                            class="btn btn-outline-secondary btn-sm mt-2"
                                                            data-action="add-list-item">+ Add Item</button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-12">
                                                    <label class="form-label">Content</label>
                                                    <textarea name="content" class="form-control" rows="{{ $detail->type === 'heading' ? '1' : '5' }}" required>{{ $detail->contentString() }}</textarea>
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
                            <div class="card p-5 text-center text-muted">
                                <i class="bi bi-layers fs-1 mb-3"></i>
                                <p class="fs-5 mb-0">No content blocks added yet. Start building your post!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Blog Modal (For Overview Tab) --}}
        <div class="modal fade" id="editBlogModal" tabindex="-1" aria-labelledby="editBlogModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-gradient-cyan text-white">
                        <h5 class="modal-title" id="editBlogModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit
                            Post Info</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Title</label>
                                    <input type="text" name="title" value="{{ $blog->title }}" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Category</label>
                                    <input type="text" name="category" value="{{ $blog->category }}"
                                        class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Slug</label>
                                    <input type="text" name="slug" value="{{ $blog->slug }}" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Thumbnail (4MB Max) - Leave blank to keep current</label>
                                    <input type="file" name="thumbnail" class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Publication Status</label>
                                    <div class="form-check form-switch fs-5">
                                        <input class="form-check-input" type="checkbox" id="isPublishedEditSwitch"
                                            name="is_published" value="1" {{ $blog->is_published ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isPublishedEditSwitch">
                                            {{ $blog->is_published ? 'Published' : 'Draft' }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save
                                Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        {{-- Add Detail Modal (Multi-block) --}}
        <div class="modal fade" id="addDetailModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <form action="{{ route('admin.blogs.details.store', $blog->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Add Blog Content Blocks</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div id="contentBlocksContainer">
                                <!-- Dynamic blocks will appear here -->
                                <div class="content-block border p-3 rounded mb-3 bg-light position-relative">
                                    <button type="button"
                                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-block-btn"
                                        style="display: none;">
                                        <i class="bi bi-x"></i>
                                    </button>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Block Type</label>
                                            <select name="blocks[0][type]" class="form-select block-type" required>
                                                <option value="" disabled selected>Select type...</option>
                                                <option value="heading">Heading</option>
                                                <option value="paragraph">Paragraph</option>
                                                <option value="quote">Quote</option>
                                                <option value="image">Image</option>
                                                <option value="list">List</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="blocks[0][order]" class="form-control"
                                                min="1" value="{{ $blog->details->max('order') + 1 }}">
                                        </div>
                                        <div class="col-12 dynamic-content-area">
                                            <p class="text-muted small">Select a type to load its input field...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" id="addNewBlock" class="btn btn-outline-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Add Content Block
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save All Blocks</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                let blockIndex = 1;

                const container = document.getElementById('contentBlocksContainer');
                const addBtn = document.getElementById('addNewBlock');

                function listInputTemplate(name, value = '', compact = false) {
                    const sizeClass = compact ? 'form-control form-control-sm' : 'form-control';
                    const groupClass = compact ? 'input-group input-group-sm mb-2' : 'input-group mb-2';
                    return `
                        <div class="${groupClass}" data-list-item>
                            <input type="text" class="${sizeClass}" name="${name}" value="${(value ?? '').replace(/"/g, '&quot;')}" placeholder="List item">
                            <button type="button" class="btn btn-outline-danger ${compact ? 'btn-sm' : ''}" data-action="remove-list-item"><i class="bi bi-dash"></i></button>
                        </div>
                    `;
                }

                function initListEditors(scope = document) {
                    scope.querySelectorAll('[data-list-editor]').forEach(editor => {
                        if (!editor.querySelector('[data-list-items]')) {
                            const wrapper = document.createElement('div');
                            wrapper.setAttribute('data-list-items', '1');
                            wrapper.innerHTML = listInputTemplate(editor.dataset.name, '', editor.dataset
                                .compact === '1');
                            editor.insertBefore(wrapper, editor.querySelector('[data-action="add-list-item"]'));
                        }
                    });
                }

                function notifyWarning(message) {
                    if (window.iziToast) {
                        iziToast.warning({
                            title: 'Notice',
                            message,
                            position: 'topRight'
                        });
                    } else {
                        alert(message);
                    }
                }

                document.addEventListener('click', (event) => {
                    const addBtn = event.target.closest('[data-action="add-list-item"]');
                    if (addBtn) {
                        const editor = addBtn.closest('[data-list-editor]');
                        const itemsWrapper = editor.querySelector('[data-list-items]');
                        const name = editor.dataset.name;
                        const compact = editor.dataset.compact === '1';
                        itemsWrapper.insertAdjacentHTML('beforeend', listInputTemplate(name, '', compact));
                    }

                    const removeBtn = event.target.closest('[data-action="remove-list-item"]');
                    if (removeBtn) {
                        const editor = removeBtn.closest('[data-list-editor]');
                        const itemsWrapper = editor.querySelector('[data-list-items]');
                        const items = itemsWrapper.querySelectorAll('[data-list-item]');
                        if (items.length <= 1) {
                            notifyWarning('At least one list item is required.');
                            return;
                        }
                        removeBtn.closest('[data-list-item]').remove();
                    }
                });

                const blockTemplate = () => `
                    <div class="content-block border p-3 rounded mb-3 bg-light position-relative">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-block-btn">
                            <i class="bi bi-x"></i>
                        </button>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Block Type</label>
                                <select name="blocks[${blockIndex}][type]" class="form-select block-type" required>
                                    <option value="" disabled selected>Select type...</option>
                                    <option value="heading">Heading</option>
                                    <option value="paragraph">Paragraph</option>
                                    <option value="quote">Quote</option>
                                    <option value="image">Image</option>
                                    <option value="list">List</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Order</label>
                                <input type="number" name="blocks[${blockIndex}][order]" class="form-control" min="1" value="${blockIndex + 1}">
                            </div>
                            <div class="col-12 dynamic-content-area">
                                <p class="text-muted small">Select a type to load its input field...</p>
                            </div>
                        </div>
                    </div>
                `;

                addBtn.addEventListener('click', () => {
                    container.insertAdjacentHTML('beforeend', blockTemplate());
                    blockIndex++;
                });

                container.addEventListener('click', (event) => {
                    const removeBtn = event.target.closest('.remove-block-btn');
                    if (removeBtn) {
                        removeBtn.closest('.content-block').remove();
                    }
                });

                function handleBlockTypeChange(select) {
                    const block = select.closest('.content-block');
                    if (!block) return;
                    const area = block.querySelector('.dynamic-content-area');
                    if (!area) return;

                    const indexMatch = select.name.match(/\d+/);
                    if (!indexMatch) return;
                    const index = indexMatch[0];

                    let inputHtml = '';
                    switch (select.value) {
                        case 'heading':
                            inputHtml =
                                `<label class="form-label">Heading Text</label><input type="text" name="blocks[${index}][content]" class="form-control" required>`;
                            break;
                        case 'paragraph':
                            inputHtml =
                                `<label class="form-label">Paragraph</label><textarea name="blocks[${index}][content]" class="form-control" rows="5" required></textarea>`;
                            break;
                        case 'quote':
                            inputHtml =
                                `<label class="form-label">Quote</label><textarea name="blocks[${index}][content]" class="form-control" rows="3" required></textarea>`;
                            break;
                        case 'image':
                            inputHtml =
                                `<label class="form-label">Upload Image</label><input type="file" name="blocks[${index}][content]" class="form-control" accept="image/*" required>`;
                            break;
                        case 'list':
                            inputHtml = `
                    <label class="form-label">List Items</label>
                    <div class="list-editor" data-list-editor data-name="blocks[${index}][content][]" data-compact="1">
                        <div data-list-items>
                            ${listInputTemplate(\`blocks[\${index}][content][]\`, '', true)}
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" data-action="add-list-item">+ Add item</button>
                    </div>`;
                            break;
                        default:
                            inputHtml = `<div class="alert alert-warning">Unsupported block type.</div>`;
                    }

                    area.innerHTML = inputHtml;
                    initListEditors(area);
                }

                document.addEventListener('change', (event) => {
                    if (event.target.matches('.block-type')) {
                        handleBlockTypeChange(event.target);
                    }
                });

                initListEditors(document);
            });
        </script>
    @endsection
