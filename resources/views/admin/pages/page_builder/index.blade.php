@extends('admin.master_page')
@section('title', 'Page Builder - Pages')

@section('main')
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-0">Page Builder</h3>
      <p class="text-muted mb-0">Create and manage dynamic pages for courses, events, and more</p>
    </div>

    <button class="btn btn-primary" id="openCreateModal" type="button">
      <i class="bi bi-plus-lg me-2"></i>Create Page
    </button>
  </div>

  {{-- Flash Messages --}}
  @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Please fix the following errors:</strong>
      <ul class="mb-0 mt-2">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Title</th>
              <th>Type</th>
              <th>Status</th>
              <th>Updated</th>
              <th class="text-end" style="min-width: 360px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($pages as $p)
              @php
                $typeDisplay = 'Standalone';
                $typeBadge   = 'secondary';
                if ($p->pageable_type === \App\Models\Course::class) {
                    $typeDisplay = 'Course';  $typeBadge = 'info';
                } elseif ($p->pageable_type === \App\Models\Event::class) {
                    $typeDisplay = 'Event';   $typeBadge = 'warning';
                }
                $ownerType = $p->pageable_type === \App\Models\Course::class
                    ? 'course'
                    : ($p->pageable_type === \App\Models\Event::class ? 'event' : '');
              @endphp
              <tr>
                <td>
                  <div class="fw-semibold">{{ $p->title }}</div>
                  <small class="text-muted">/{{ $p->slug }}</small>
                </td>
                <td>
                  <span class="badge bg-{{ $typeBadge }}">{{ $typeDisplay }}</span>
                  @if ($p->pageable)
                    <br><small class="text-muted">{{ $p->pageable->title }}</small>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $p->status === 'published' ? 'success' : 'secondary' }}">
                    {{ ucfirst($p->status) }}
                  </span>
                </td>
                <td><small>{{ $p->updated_at?->diffForHumans() }}</small></td>
                <td class="text-end">
                  <div class="d-flex gap-2 justify-content-end">
                    {{-- Manage blocks --}}
                    <a class="btn btn-outline-secondary btn-sm" href="{{ route('pb.blocks', $p) }}">
                      <i class="bi bi-puzzle me-1"></i>Blocks
                    </a>

                    {{-- Edit --}}
                    <button
                      class="btn btn-outline-primary btn-sm"
                      type="button"
                      data-action="edit"
                      data-payload="{{ json_encode([
                        'id' => $p->id,
                        'title' => $p->title,
                        'slug' => $p->slug,
                        'status' => $p->status,
                        'owner_type' => $ownerType,
                        'owner_id' => $p->pageable_id,
                        'meta' => $p->meta,
                      ]) }}"
                    >
                      <i class="bi bi-pencil me-1"></i>Edit
                    </button>

                    {{-- Preview --}}
                    <a class="btn btn-outline-dark btn-sm" href="{{ route('page.show', $p->slug) }}" target="_blank">
                      <i class="bi bi-eye me-1"></i>Preview
                    </a>

                    {{-- Delete --}}
                    <form class="d-inline" method="post" action="{{ route('pb.pages.destroy', $p) }}"
                          onsubmit="return confirm('Delete this page and all its blocks?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" type="submit">
                        <i class="bi bi-trash me-1"></i>Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  <i class="bi bi-file-earmark-text display-4 d-block mb-3"></i>
                  <p class="mb-0">No pages yet. Click "Create Page" to get started.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if ($pages->hasPages())
      <div class="card-footer">
        {{ $pages->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Create/Edit Modal --}}
<div class="modal fade" id="pageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="pageForm" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="pageModalTitle">Create Page</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-7">
              <label class="form-label">Title*</label>
              <input class="form-control" name="title" id="pageTitle" required>
            </div>
            <div class="col-md-5">
              <label class="form-label">Slug</label>
              <input class="form-control" name="slug" id="pageSlug">
              <small class="form-text text-muted">Leave blank to auto-generate</small>
            </div>

            <div class="col-md-4">
              <label class="form-label">Status*</label>
              <select class="form-select" name="status" id="pageStatus" required>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
              </select>
            </div>

            <div class="col-12">
              <hr>
              <label class="form-label fw-semibold">Attach To (Optional)</label>
              <div class="d-flex gap-3 flex-wrap mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="owner_type" value="" id="type_standalone" checked>
                  <label class="form-check-label" for="type_standalone">Standalone Page</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="owner_type" value="course" id="type_course">
                  <label class="form-check-label" for="type_course">Course</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="owner_type" value="event" id="type_event">
                  <label class="form-check-label" for="type_event">Event</label>
                </div>
              </div>
            </div>

            <div class="col-12 owner-select owner-course" style="display:none;">
              <label class="form-label">Select Course</label>
              <select class="form-select" name="owner_id_course" id="ownerCourse">
                <option value="">Choose course…</option>
                @foreach ($courses as $c)
                  <option value="{{ $c->id }}">{{ $c->title }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12 owner-select owner-event" style="display:none;">
              <label class="form-label">Select Event</label>
              <select class="form-select" name="owner_id_event" id="ownerEvent">
                <option value="">Choose event…</option>
                @foreach ($events as $e)
                  <option value="{{ $e->id }}">{{ $e->title }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <hr>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-semibold mb-0">SEO Metadata</label>
                <small class="text-muted">Optional but recommended for organic search</small>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Meta Title</label>
              <input class="form-control" name="meta_title" id="metaTitle" maxlength="255" placeholder="Displayed in search results">
            </div>

            <div class="col-md-6">
              <label class="form-label">Keywords</label>
              <input class="form-control" name="meta_keywords" id="metaKeywords" maxlength="255" placeholder="Comma-separated focus keywords">
            </div>

            <div class="col-12">
              <label class="form-label">Meta Description</label>
              <textarea class="form-control" name="meta_description" id="metaDescription" rows="3" maxlength="320" placeholder="Short summary shown on Google & social previews"></textarea>
              <small class="text-muted">Aim for 150-160 characters.</small>
            </div>

            <div class="col-md-6">
              <label class="form-label">Canonical URL</label>
              <input class="form-control" name="meta_canonical" id="metaCanonical" maxlength="500" placeholder="https://example.com/landing-page">
            </div>

            <div class="col-md-6">
              <label class="form-label">Social Preview Image</label>
              <div class="input-group">
                <input class="form-control" name="meta_image" id="metaImage" maxlength="500" placeholder="{{ asset('frontend/assets/images/logos/logo.png') }}">
                <button class="btn btn-outline-secondary" type="button" id="metaImageSelectBtn">
                  <i class="bi bi-cloud-upload"></i>
                </button>
              </div>
              <input type="file" class="d-none" name="meta_image_file" id="metaImageFile" accept="image/*">
              <div class="mt-2">
                <img id="metaImagePreview" src="" alt="Meta preview" style="max-width:100%;height:auto;display:none;border-radius:12px;">
              </div>
              <small class="text-muted d-block">Paste a URL or choose an image from your computer (1200×630px recommended).</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-primary" type="submit" id="submitBtn">Save Page</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
{{-- Page JS - Wrapped in DOMContentLoaded to ensure Bootstrap is loaded --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  console.log('Page Builder Index: Initializing...');
  
  // Check if Bootstrap is available
  if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap is not loaded! Make sure bootstrap.bundle.js is included in your layout.');
    return;
  }

  const modalEl = document.getElementById('pageModal');
  const form = document.getElementById('pageForm');
  const titleEl = document.getElementById('pageModalTitle');

  // Initialize Bootstrap modal
  let bsModal;
  try {
    bsModal = new bootstrap.Modal(modalEl);
    console.log('Bootstrap Modal initialized successfully');
  } catch (error) {
    console.error('Failed to initialize Bootstrap Modal:', error);
    return;
  }

  // Get form elements
  const elements = {
    title: document.getElementById('pageTitle'),
    slug: document.getElementById('pageSlug'),
    status: document.getElementById('pageStatus'),
    ownerCourse: document.getElementById('ownerCourse'),
    ownerEvent: document.getElementById('ownerEvent'),
    metaTitle: document.getElementById('metaTitle'),
    metaDescription: document.getElementById('metaDescription'),
    metaKeywords: document.getElementById('metaKeywords'),
    metaImage: document.getElementById('metaImage'),
    metaImageFile: document.getElementById('metaImageFile'),
    metaImagePreview: document.getElementById('metaImagePreview'),
    metaImageSelectBtn: document.getElementById('metaImageSelectBtn'),
    metaCanonical: document.getElementById('metaCanonical')
  };

  function resolveMetaImageSrc(value = '') {
    if (!value) return '';
    if (/^(https?:)?\/\//i.test(value) || value.startsWith('data:')) {
      if (value.startsWith('//')) {
        return window.location.protocol + value;
      }
      return value;
    }
    if (value.startsWith('/')) {
      return value;
    }
    return '/' + value.replace(/^\/+/, '');
  }

  function updateMetaImagePreview(value = '') {
    const preview = elements.metaImagePreview;
    if (!preview) return;
    if (!value) {
      preview.style.display = 'none';
      preview.removeAttribute('src');
      return;
    }
    preview.src = resolveMetaImageSrc(value);
    preview.style.display = 'block';
  }

  function bindMetaImageHandlers() {
    if (elements.metaImage) {
      elements.metaImage.addEventListener('input', () => {
        if (elements.metaImageFile) elements.metaImageFile.value = '';
        updateMetaImagePreview(elements.metaImage.value.trim());
      });
    }

    if (elements.metaImageSelectBtn && elements.metaImageFile) {
      elements.metaImageSelectBtn.addEventListener('click', () => {
        elements.metaImageFile.click();
      });

      elements.metaImageFile.addEventListener('change', () => {
        const file = elements.metaImageFile.files[0];
        if (file) {
          const previewUrl = URL.createObjectURL(file);
          elements.metaImage.value = '';
          updateMetaImagePreview(previewUrl);
        } else {
          updateMetaImagePreview(elements.metaImage.value.trim());
        }
      });
    }
  }

  function setOwnerRequired(which) {
    if (elements.ownerCourse) elements.ownerCourse.required = (which === 'course');
    if (elements.ownerEvent) elements.ownerEvent.required = (which === 'event');
  }

  function resetForm() {
    form.reset();
    
    // Reset to standalone
    const standalone = form.querySelector('input[name="owner_type"][value=""]');
    if (standalone) standalone.checked = true;
    
    // Hide all owner selects
    form.querySelectorAll('.owner-select').forEach(el => el.style.display = 'none');
    setOwnerRequired('');
    
    // Remove method spoofing
    form.querySelector('input[name="_method"]')?.remove();
    
    // Clear owner_id hidden input if exists
    form.querySelector('input[name="owner_id"]')?.remove();

    if (elements.metaTitle) elements.metaTitle.value = '';
    if (elements.metaDescription) elements.metaDescription.value = '';
    if (elements.metaKeywords) elements.metaKeywords.value = '';
    if (elements.metaImage) elements.metaImage.value = '';
    if (elements.metaImageFile) elements.metaImageFile.value = '';
    updateMetaImagePreview('');
    if (elements.metaCanonical) elements.metaCanonical.value = '';
  }

  function populateForm(data) {
    console.log('Populating form with:', data);
    
    // Set form fields
    elements.title.value = data.title || '';
    elements.slug.value = data.slug || '';
    elements.status.value = data.status || 'draft';
    const meta = data.meta || {};
    if (elements.metaTitle) elements.metaTitle.value = meta.title || '';
    if (elements.metaDescription) elements.metaDescription.value = meta.description || '';
    if (elements.metaKeywords) elements.metaKeywords.value = meta.keywords || '';
    if (elements.metaImage) elements.metaImage.value = meta.image || '';
    if (elements.metaImageFile) elements.metaImageFile.value = '';
    updateMetaImagePreview(meta.image || '');
    if (elements.metaCanonical) elements.metaCanonical.value = meta.canonical || '';

    // Set owner type radio
    const ownerType = data.owner_type || '';
    form.querySelectorAll('input[name="owner_type"]').forEach(r => {
      r.checked = (r.value === ownerType);
    });

    // Show/hide and populate owner selects
    form.querySelectorAll('.owner-select').forEach(el => el.style.display = 'none');
    
    if (ownerType === 'course') {
      const courseSelect = form.querySelector('.owner-course');
      if (courseSelect) {
        courseSelect.style.display = 'block';
        if (elements.ownerCourse) {
          elements.ownerCourse.value = String(data.owner_id || '');
        }
      }
    } else if (ownerType === 'event') {
      const eventSelect = form.querySelector('.owner-event');
      if (eventSelect) {
        eventSelect.style.display = 'block';
        if (elements.ownerEvent) {
          elements.ownerEvent.value = String(data.owner_id || '');
        }
      }
    }
    
    setOwnerRequired(ownerType);
  }

  // Radio toggle for owner type
  form.addEventListener('change', (e) => {
    if (e.target.name !== 'owner_type') return;
    const val = e.target.value;
    
    form.querySelectorAll('.owner-select').forEach(el => el.style.display = 'none');
    
    if (val === 'course') {
      form.querySelector('.owner-course')?.style.setProperty('display', 'block');
    } else if (val === 'event') {
      form.querySelector('.owner-event')?.style.setProperty('display', 'block');
    }
    
    setOwnerRequired(val);
  });

  // Create button
  const createBtn = document.getElementById('openCreateModal');
  if (createBtn) {
    createBtn.addEventListener('click', (e) => {
      e.preventDefault();
      console.log('Create button clicked');
      
      titleEl.textContent = 'Create Page';
      form.action = @json(route('pb.pages.store'));
      
      resetForm();
      bsModal.show();
    });
  }

  // Edit button (delegated to handle dynamic content)
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-action="edit"]');
    if (!btn) return;
    
    e.preventDefault();
    console.log('Edit button clicked');
    
    try {
      // Parse the JSON payload
      const payload = btn.getAttribute('data-payload');
      if (!payload) {
        console.error('No data-payload found');
        alert('Error: No page data found');
        return;
      }
      
      const data = JSON.parse(payload);
      console.log('Edit data:', data);
      
      // Set modal title
      titleEl.textContent = 'Edit Page';
      
      // Set form action
      form.action = @json(route('pb.pages.update', ':id')).replace(':id', data.id);
      
      // Add PUT method
      let methodInput = form.querySelector('input[name="_method"]');
      if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
      }
      methodInput.value = 'PUT';
      
      // Populate form fields
      populateForm(data);
      
      // Show modal
      bsModal.show();
      
    } catch (error) {
      console.error('Error parsing edit data:', error);
      alert('Error loading page data. Please refresh and try again.');
    }
  });

  // Form submit - normalize owner_id
  form.addEventListener('submit', (e) => {
    const ownerType = form.querySelector('input[name="owner_type"]:checked')?.value || '';
    
    // Remove existing owner_id if present
    form.querySelector('input[name="owner_id"]')?.remove();

    // Create new hidden input
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'owner_id';

    if (ownerType === 'course') {
      hidden.value = elements.ownerCourse?.value || '';
    } else if (ownerType === 'event') {
      hidden.value = elements.ownerEvent?.value || '';
    } else {
      hidden.value = '';
    }
    
    form.appendChild(hidden);
    
    console.log('Form submitting - owner_type:', ownerType, 'owner_id:', hidden.value);
  });

  // If there are validation errors, reopen modal with old input
  @if ($errors->any() && old('title'))
    console.log('Validation errors detected, reopening modal');
    
    const oldData = {
      id: @json(old('id', '')),
      title: @json(old('title', '')),
      slug: @json(old('slug', '')),
      status: @json(old('status', 'draft')),
      owner_type: @json(old('owner_type', '')),
      owner_id: @json(old('owner_id', '')),
      meta: {
        title: @json(old('meta_title')),
        description: @json(old('meta_description')),
        keywords: @json(old('meta_keywords')),
        image: @json(old('meta_image')),
        canonical: @json(old('meta_canonical')),
      }
    };
    
    if (oldData.id) {
      // Edit mode
      titleEl.textContent = 'Edit Page';
      form.action = @json(route('pb.pages.update', ':id')).replace(':id', oldData.id);
      
      let methodInput = form.querySelector('input[name="_method"]');
      if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        form.appendChild(methodInput);
      }
      methodInput.value = 'PUT';
    } else {
      // Create mode
      titleEl.textContent = 'Create Page';
      form.action = @json(route('pb.pages.store'));
    }
    
    populateForm(oldData);
    bsModal.show();
  @endif

  bindMetaImageHandlers();
  if (elements.metaImage) {
    updateMetaImagePreview(elements.metaImage.value.trim());
  }

  console.log('Page Builder Index: Ready!');
});
</script>
@endpush
