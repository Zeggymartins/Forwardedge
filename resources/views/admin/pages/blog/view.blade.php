@extends('admin.master_page')

@section('title', $blog->title . ' Dashboard')
<style>
    /* Modern Tab Style for Dashboard */
    #blogTabs .nav-link {
        border: none;
        border-radius: 30px;
        margin: 0.25rem;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    #blogTabs .nav-link:hover {
        background-color: #e9ecef;
        color: #0d6efd; /* Use primary color */
    }

    #blogTabs .nav-link.active {
        /* Vibrant gradient for active state */
        background: linear-gradient(90deg, #0d6efd, #36b9cc);
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .content-block {
        border-left: 5px solid #0d6efd;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .content-block:hover {
        border-left-color: #36b9cc;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
</style>

@section('main')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>{{ $blog->title }} <small class="text-muted">Blog Dashboard</small></h1>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
        </div>

        {{-- Navigation Tabs --}}
        <ul class="nav nav-tabs nav-pills flex-wrap mb-4 shadow-sm rounded-3" id="blogTabs" role="tablist"
            style="background: #f8f9fc;">
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
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white"
                        style="background: linear-gradient(90deg,#0d6efd,#36b9cc);">
                        <h5 class="mb-0">Blog Post Summary</h5>
                        <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editBlogModal">
                            <i class="bi bi-pencil-square me-1"></i> Edit Info
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4 text-center">
                                <img src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : 'https://via.placeholder.com/300x200?text=Post+Thumbnail' }}"
                                    class="img-fluid rounded shadow-sm mb-3" alt="Blog Thumbnail">
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
                                            <span
                                                class="badge bg-{{ $blog->is_published ? 'success' : 'warning' }}">
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
                    <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addDetailModal">
                        <i class="bi bi-plus-lg me-2"></i> Add Content Block
                    </button>
                </div>

                {{-- List Existing Content Blocks --}}
                <div class="list-group">
                    @forelse ($blog->details->sortBy('order') as $detail)
                        <div class="list-group-item content-block p-4 mb-3 d-flex justify-content-between align-items-start">
                            <div class="d-flex align-items-start">
                                <span class="badge bg-secondary me-3 p-2 rounded-pill fw-bold">{{ $detail->order }}</span>
                                <div>
                                    <h6 class="mb-1 text-uppercase small text-primary">{{ $detail->type }}</h6>

                                    {{-- Display content preview based on type --}}
                                    @switch($detail->type)
                                        @case('image')
                                            <p class="small text-muted mb-0">
                                                <i class="bi bi-image me-1"></i>
                                                {{ basename($detail->content) }}
                                            </p>
                                            <img src="{{ asset('storage/' . $detail->content) }}" alt="Content Image" style="max-height: 100px;" class="mt-2 rounded shadow-sm">
                                            @break
                                        @case('list')
                                            @php $listItems = json_decode($detail->content, true); @endphp
                                            <p class="small text-muted mb-0">List with {{ count($listItems ?? []) }} items</p>
                                            <ul class="small mt-2 mb-0">
                                                @if(is_array($listItems))
                                                    @foreach(array_slice($listItems, 0, 2) as $item)
                                                        <li>{{ \Illuminate\Support\Str::limit($item, 50) }}</li>
                                                    @endforeach
                                                @endif
                                                @if(count($listItems ?? []) > 2)
                                                    <li>... and more.</li>
                                                @endif
                                            </ul>
                                            @break
                                        @case('heading')
                                            <h4 class="mb-0">{{ $detail->content }}</h4>
                                            @break
                                        @case('quote')
                                            <blockquote class="blockquote small mb-0">{{ \Illuminate\Support\Str::limit($detail->content, 100) }}</blockquote>
                                            @break
                                        @default
                                            <p class="mb-0">{{ \Illuminate\Support\Str::limit($detail->content, 150) }}</p>
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
                                <form
                                    action="{{ route('admin.blogs.details.destroy', [$blog->id, $detail->id]) }}"
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
                                    <form
                                        action="{{ route('admin.blogs.details.update', [$blog->id, $detail->id]) }}"
                                        method="POST" enctype="multipart/form-data">
                                        @csrf @method('PUT')
                                        <div class="modal-header bg-light">
                                            <h5 class="modal-title">Edit {{ ucfirst($detail->type) }} Block (Order: {{ $detail->order }})</h5>
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
                                                <input type="text" class="form-control" value="{{ ucfirst($detail->type) }}" disabled>
                                            </div>

                                            @if ($detail->type === 'image')
                                                <div class="col-12">
                                                    <label class="form-label">Replace Image (Current: {{ basename($detail->content) }})</label>
                                                    <input type="file" name="content" class="form-control">
                                                    <div class="form-text">Upload a new image to replace the current one.</div>
                                                </div>
                                            @elseif ($detail->type === 'list')
                                                <div class="col-12">
                                                    <label class="form-label">List Items (One per line)</label>
                                                    @php $listItems = json_decode($detail->content, true) ?? []; @endphp
                                                    <textarea name="content" class="form-control" rows="6" placeholder="Item 1\nItem 2\nItem 3">{{ implode("\n", $listItems) }}</textarea>
                                                    <div class="form-text">Each line will be treated as a separate list item.</div>
                                                </div>
                                            @else
                                                <div class="col-12">
                                                    <label class="form-label">Content</label>
                                                    <textarea name="content" class="form-control" rows="{{ $detail->type === 'heading' ? '1' : '5' }}" required>{{ $detail->content }}</textarea>
                                                </div>
                                            @endif

                                            {{-- Optional: Extras field for things like image captions or heading size --}}
                                            {{-- NOTE: This field is handled generally but specific UI is omitted for simplicity --}}
                                            <div class="col-12">
                                                <label class="form-label">Extras (JSON format, optional)</label>
                                                <textarea name="extras" class="form-control" rows="2" placeholder='{"caption": "A description for the image."}'>{{ $detail->extras ? json_encode($detail->extras) : '' }}</textarea>
                                            </div>
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
                <div class="modal-header bg-gradient text-white"
                    style="background: linear-gradient(90deg,#0d6efd,#36b9cc);">
                    <h5 class="modal-title" id="editBlogModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit
                        Post Info</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
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
                                <input type="text" name="category" value="{{ $blog->category }}" class="form-control">
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
                                    <input class="form-check-input" type="checkbox" id="isPublishedEditSwitch" name="is_published" value="1" {{ $blog->is_published ? 'checked' : '' }}>
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


    {{-- Add Detail Modal (For Content Details Tab) --}}
    <div class="modal fade" id="addDetailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.blogs.details.store', $blog->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Add New Content Block</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Block Type</label>
                            <select name="type" id="blockType" class="form-select" required>
                                <option value="" disabled selected>Select content type...</option>
                                <option value="heading">Heading (H2/H3)</option>
                                <option value="paragraph">Paragraph/Text</option>
                                <option value="quote">Quote Block</option>
                                <option value="image">Image</option>
                                <option value="list">List (Ordered/Unordered)</option>
                                <option value="code">Code Block</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Position/Order</label>
                            <input type="number" name="order" class="form-control" min="1" value="{{ $blog->details->max('order') + 1 }}">
                        </div>

                        <!-- Dynamic Fields -->
                        <div class="col-12" id="dynamicBlockContent">
                            <p class="text-muted small">Select a block type to see the corresponding input fields.</p>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Extras (JSON format, optional)</label>
                            <textarea name="extras" class="form-control" rows="2" placeholder='{"size": "h2"}'></textarea>
                            <div class="form-text">For storing extra configuration (e.g., heading size, list style).</div>
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Script to handle dynamic fields in the Add Content Block modal
            const typeSelect = document.getElementById('blockType');
            const dynamicFieldContainer = document.getElementById('dynamicBlockContent');

            const updateDynamicFields = (type) => {
                dynamicFieldContainer.innerHTML = ''; // Clear previous content

                let contentHtml = '';

                switch (type) {
                    case 'heading':
                        contentHtml = `
                            <label class="form-label">Heading Text</label>
                            <input type="text" name="content" class="form-control" placeholder="Enter your section heading" required>`;
                        break;
                    case 'paragraph':
                    case 'quote':
                    case 'code':
                        contentHtml = `
                            <label class="form-label">${type === 'paragraph' ? 'Paragraph Text' : (type === 'quote' ? 'Quote Text' : 'Code Block Content')}</label>
                            <textarea name="content" class="form-control" rows="6" required placeholder="Enter the main text content or code here"></textarea>`;
                        break;
                    case 'image':
                        contentHtml = `
                            <label class="form-label">Upload Image File</label>
                            <input type="file" name="content" class="form-control" accept="image/*" required>`;
                        break;
                    case 'list':
                        contentHtml = `
                            <label class="form-label">List Items (One per line)</label>
                            <textarea name="content" class="form-control" rows="6" placeholder="Item 1\nItem 2\nItem 3" required></textarea>
                            <div class="form-text">Each line will be treated as a separate list item and stored as JSON.</div>`;
                        break;
                    default:
                        contentHtml = `<p class="text-danger small">Unknown block type selected.</p>`;
                        break;
                }

                dynamicFieldContainer.innerHTML = contentHtml;
            };

            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    updateDynamicFields(this.value);
                });
            }

            // Publication status switch label update (for edit modal)
            const isPublishedEditSwitch = document.getElementById('isPublishedEditSwitch');
            if (isPublishedEditSwitch) {
                isPublishedEditSwitch.addEventListener('change', function() {
                    this.nextElementSibling.textContent = this.checked ? 'Published (Live)' : 'Draft (Hidden)';
                });
            }
        });
    </script>
@endsection
