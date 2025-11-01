@extends('admin.master_page')
@section('title', 'Page Builder • ' . $page->title)

@push('styles')
    <style>
        /* Force scrollable modals (resists theme overrides) */
        #blockModal .modal {
            --bs-modal-margin: 1rem;
        }

        /* give space on small screens */
        #blockModal .modal-dialog {
            height: calc(100% - 2rem);
            max-height: 100%;
            margin: 1rem auto;
            /* top & bottom margin */
        }

        #blockModal .modal-content {
            height: 100%;
            display: flex;
            flex-direction: column;
            min-height: 0;
            /* prevents flex children from forcing overflow */
        }

        #blockModal .modal-header,
        #blockModal .modal-footer {
            flex: 0 0 auto;
        }

        #blockModal .modal-body {
            flex: 1 1 auto;
            /* take remaining space */
            overflow-y: auto;
            /* scroll just the body */
            min-height: 0;
        }

        /* Optional: if you want inner section to scroll independently */
        /* #blockModal #fieldMount { max-height: none; } */

        @media (max-width: 576px) {
            #blockModal .modal-dialog {
                height: calc(100% - 1rem);
                margin: .5rem auto;
            }
        }

        /* your other styles */
        .rounded-12 {
            border-radius: 12px
        }

        .shadow-soft {
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08)
        }

        .drag-handle {
            cursor: grab;
            user-select: none;
            font-size: 18px;
            padding: 6px 8px
        }

        .block-row[draggable="true"].dragging {
            opacity: .6;
            cursor: grabbing
        }

        .drop-marker {
            height: 10px;
            border-radius: 5px;
            background: rgba(25, 135, 84, .25);
            margin: 4px 0
        }

        .small-label {
            font-size: .9rem;
            font-weight: 600
        }

        .modal-lg {
            --bs-modal-width: 900px;
        }

        .repeater-item {
            position: relative;
            transition: background-color .2s;
        }

        .repeater-item:hover {
            background-color: rgba(0, 0, 0, .02);
        }

        .card-preview {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: .5rem;
            background: #f8f9fa;
        }

        .section-divider {
            border-top: 2px solid #e9ecef;
            margin: 1.5rem 0;
            position: relative;
        }

        .section-divider-label {
            position: absolute;
            top: -12px;
            left: 20px;
            background: white;
            padding: 0 10px;
            font-weight: 600;
            font-size: .85rem;
            color: #6c757d;
        }
    </style>
@endpush


@section('main')
    <div class="container py-5">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Page Builder • {{ $page->title }}</h3>
                <div class="text-muted">Add blocks, edit content in modals, and drag to reorder.</div>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-dark" href="{{ route('page.show', $page->slug) }}" target="_blank">Preview</a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <div class="fw-semibold mb-1">Please fix the following:</div>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Block toolbar --}}
        <div class="card rounded-12 shadow-soft mb-4 p-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small-label">Add Block</label>
                        <select class="form-select" id="add_type">
                            <option value="" selected disabled>Choose a block…</option>
                            @foreach ($blockTypes as $t)
                                <option value="{{ $t }}">{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small-label">Variant</label>
                        <select class="form-select" id="add_variant">
                            <option value="">Default</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button class="btn btn-dark w-100" id="openAddModal" disabled>Configure & Add</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Blocks List --}}
        <div class="card rounded-12 shadow-soft">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="fw-semibold">Blocks (drag to reorder)</div>
                <small class="text-muted">Order autosaves after each drag.</small>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="blockList">
                    @forelse($blocks as $b)
                        <li class="list-group-item d-flex align-items-center gap-3 block-row" draggable="true"
                            data-id="{{ $b->id }}">
                            <span class="drag-handle">☰</span>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-capitalize">
                                    {{ str_replace('_', ' ', $b->type) }}
                                    <span class="text-muted">• {{ $b->variant ?: 'default' }}</span>
                                </div>
                                <div class="small text-muted">
                                    #{{ $b->order }}
                                    @unless ($b->is_published)
                                        • <span class="text-danger">unpublished</span>
                                    @endunless
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-secondary btn-sm" data-action="edit"
                                    data-payload='@json($b)'>Edit</button>
                                <form method="POST" action="{{ route('pb.blocks.destroy', $b) }}"
                                    onsubmit="return confirm('Delete this block?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted py-4">
                            No blocks yet. Use the toolbar above to add one.
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- ============== Modal (Add/Edit) ============== --}}
    <div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="modalForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Configure Block</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small-label">Block Type</label>
                                <select class="form-select" id="modal_type" name="type" required>
                                    @foreach ($blockTypes as $t)
                                        <option value="{{ $t }}">{{ ucfirst(str_replace('_', ' ', $t)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small-label">Variant</label>
                                <select class="form-select" id="modal_variant" name="variant">
                                    <option value="">Default</option>
                                    {{-- options filled by JS based on type --}}
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small-label">Published</label>
                                <select class="form-select" name="is_published" id="modal_published">
                                    <option value="1" selected>Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-3">

                        {{-- Dynamic fields mount point --}}
                        <div id="fieldMount"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark" id="modalSubmit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ===================== JS ===================== --}}
    <script>
        /* ====== CONFIG ====== */
        const VARIANTS = @json($variants);
        const PUBLIC_ROOT = @json(asset('storage/')) + '/';

        /* ====== UTILITIES ====== */
        function html(markup) {
            const t = document.createElement('template');
            t.innerHTML = markup.trim();
            return t.content.firstElementChild;
        }

        function slugify(str) {
            return (str || '').toString().normalize('NFKD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .replace(/-{2,}/g, '-');
        }

        function getCSRF() {
            return document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value || '';
        }

        function setVal(name, val, scope = document) {
            const el = scope.querySelector(`[name='${CSS.escape(name)}']`);
            if (!el || el.type === 'file') return;
            if (el.type === 'checkbox') {
                el.checked = !!val;
            } else {
                el.value = val ?? '';
            }
        }

        /* ====== FILE PREVIEW HANDLER ====== */
        function setupFilePreviewHandlers(scope, publicRoot, existingData) {
            // Universal file change handler
            scope.addEventListener('change', (e) => {
                const input = e.target;
                if (input.type !== 'file' || !input.files?.[0]) return;

                const key = input.getAttribute('data-file-key');
                if (!key) return;

                const img = scope.querySelector(`img[data-preview-key="${key}"]`);
                if (img) {
                    img.src = URL.createObjectURL(input.files[0]);
                    img.style.display = 'block';
                }
            });

            // Show existing images
            if (existingData && typeof existingData === 'object') {
                showExistingImages(scope, publicRoot, existingData);
            }
        }

        function showExistingImages(scope, publicRoot, data, prefix = '') {
            for (const [key, val] of Object.entries(data)) {
                const fullKey = prefix ? `${prefix}_${key}` : key;

                if (typeof val === 'string' && (key === 'image' || key === 'photo' || key === 'bg' ||
                        key.includes('_image') || key === 'banner_image' || key === 'hero_image' || key === 'about_image'
                    )) {
                    const img = scope.querySelector(`img[data-preview-key="${fullKey}"]`);
                    if (img && val) {
                        img.src = publicRoot + val.replace(/^\/+/, '');
                        img.style.display = 'block';
                    }
                } else if (Array.isArray(val)) {
                    val.forEach((item, i) => {
                        if (typeof item === 'object') {
                            showExistingImages(scope, publicRoot, item, `${fullKey}_${i}`);
                        }
                    });
                } else if (typeof val === 'object' && val !== null) {
                    showExistingImages(scope, publicRoot, val, fullKey);
                }
            }
        }

        /* ====== ADD BLOCK BAR ====== */
        const addType = document.getElementById('add_type');
        const addVariant = document.getElementById('add_variant');
        const openAddModalBtn = document.getElementById('openAddModal');

        function updateVariantSelect(type, selectEl) {
            const opts = VARIANTS[type] || ['default'];
            selectEl.innerHTML = '<option value="">Default</option>' +
                opts.filter(o => o !== 'default')
                .map(o => `<option value="${o}">${o}</option>`).join('');
        }

        addType?.addEventListener('change', () => {
            updateVariantSelect(addType.value, addVariant);
            openAddModalBtn.disabled = !addType.value;
        });

        openAddModalBtn?.addEventListener('click', () => {
            openModal('create', {
                type: addType.value,
                variant: addVariant.value
            });
        });

        /* ====== MODAL ====== */
        let bsModal;

        function ensureModal() {
            if (!bsModal) bsModal = new bootstrap.Modal(document.getElementById('blockModal'));
            return bsModal;
        }

        function openModal(mode, data) {
            const modal = ensureModal();
            const form = document.getElementById('modalForm');
            const titleEl = document.getElementById('modalTitle');
            const typeSel = document.getElementById('modal_type');
            const varSel = document.getElementById('modal_variant');
            const pubSel = document.getElementById('modal_published');
            const mount = document.getElementById('fieldMount');

            // CSRF
            if (!form.querySelector('input[name="_token"]')) {
                const tok = document.createElement('input');
                tok.type = 'hidden';
                tok.name = '_token';
                tok.value = getCSRF();
                form.appendChild(tok);
            }

            // Setup action
            if (mode === 'create') {
                form.action = @json(route('pb.blocks.store', $page));
                form.querySelector('input[name="_method"]')?.remove();
                titleEl.textContent = 'Add Block';
            } else {
                form.action = @json(route('pb.blocks.update', ':id')).replace(':id', data.id);
                let m = form.querySelector('input[name="_method"]');
                if (!m) {
                    m = document.createElement('input');
                    m.type = 'hidden';
                    m.name = '_method';
                    form.appendChild(m);
                }
                m.value = 'PUT';
                titleEl.textContent = 'Edit Block';
            }

            // Set selects
            typeSel.value = data.type || '';
            updateVariantSelect(typeSel.value, varSel);
            varSel.value = data.variant || '';
            pubSel.value = data.is_published === false || data.is_published === 0 ? '0' : '1';

            // Render fields
            mount.innerHTML = '';
            const fieldsEl = renderFieldsForType(typeSel.value);
            if (fieldsEl) {
                mount.appendChild(fieldsEl);

                // 1. Initialize repeaters
                initRepeater(mount);

                // 2. Then hydrate data with LONGER delay to ensure DOM is ready
                if (data.data && typeof data.data === 'object') {
                    setTimeout(() => {
                        hydrateFields(mount, data.data);
                    }, 200); // 👈 Increased delay
                }

                // 3. Setup file previews
                setupFilePreviewHandlers(mount, PUBLIC_ROOT, data.data || {});

                // 4. Type-specific bindings
                attachTypeBindings(typeSel.value, mount, mode, data.data || {});
            }

            // Type change handler
            typeSel.onchange = () => {
                updateVariantSelect(typeSel.value, varSel);
                mount.innerHTML = '';
                const newFields = renderFieldsForType(typeSel.value);
                if (newFields) {
                    mount.appendChild(newFields);
                    initRepeater(mount);
                    setupFilePreviewHandlers(mount, PUBLIC_ROOT, {});
                    attachTypeBindings(typeSel.value, mount, 'create', {});
                }
            };

            // Form submit handler
            // Form submit handler
            if (!form.dataset.finalizeHooked) {
                // Replace your form submit listener with this enhanced version:
                form.addEventListener('submit', (e) => {
                    console.log('🚀 Form submitting...');

                    const mount = document.getElementById('fieldMount');

                    // Step 1: Remove completely empty items (no text in ANY field)
                    pruneEmptyRepeaterItems(mount);

                    // Step 2: Remove empty list entries specifically
                    pruneEmptyListInputs(mount);

                    // Step 3: Final comprehensive renumbering
                    finalizeRepeaters(mount);

                    // Step 4: Clean up any remaining placeholders
                    stripLeftoverPlaceholders(mount);

                    // Step 5: Debug check
                    debugFormData(mount);

                    console.log('✅ Form preparation complete');

                });
                form.dataset.finalizeHooked = '1';
            }

            modal.show();
        }

        /* ====== FIELD RENDERERS ====== */
        function renderFieldsForType(type) {
            switch (type) {
                case 'hero':
                    return html(`
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label small-label">Slug (auto)</label>
    <input class="form-control" name="data[slug]" data-hero-slug readonly>
  </div>
  <div class="col-md-8">
    <label class="form-label small-label">Title*</label>
    <input class="form-control" name="data[title]" required>
  </div>
  <div class="col-md-5">
    <label class="form-label small-label">Link Text</label>
    <input class="form-control" name="data[link_text]">
  </div>
  <div class="col-md-7">
    <label class="form-label small-label">Link</label>
    <input class="form-control" name="data[link]" type="url">
  </div>
  <div class="col-md-5">
    <label class="form-label small-label">Secondary Link Text</label>
    <input class="form-control" name="data[link_text_secondary]">
  </div>
  <div class="col-md-7">
    <label class="form-label small-label">Secondary Link</label>
    <input class="form-control" name="data[link_secondary]" type="url">
  </div>
  <div class="col-12">
    <label class="form-label small-label">Sub Text</label>
    <textarea class="form-control" name="data[sub_text]" rows="3"></textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label small-label">Banner Image</label>
    <input class="form-control" type="file" name="banner_image" accept=".webp,.jpg,.jpeg,.png" data-file-key="banner_image">
    <div class="mt-2"><img data-preview-key="banner_image" style="max-width:100%;height:auto;display:none"></div>
  </div>
</div>`);

                case 'hero2':
                    return html(`
<div class="row g-3">
  <div class="col-12">
    <label class="form-label small-label">Title*</label>
    <input class="form-control" name="data[title]" required>
  </div>
  <div class="col-md-5">
    <label class="form-label small-label">Link Text</label>
    <input class="form-control" name="data[link_text]">
  </div>
  <div class="col-md-7">
    <label class="form-label small-label">Link</label>
    <input class="form-control" type="url" name="data[link]">
  </div>
  <div class="col-12">
    <label class="form-label small-label">Description</label>
    <textarea class="form-control" name="data[desc]" rows="3"></textarea>
  </div>
  <div class="col-md-6">
    <label class="form-label small-label">Hero Image</label>
    <input class="form-control" type="file" name="hero_image" accept=".webp,.jpg,.jpeg,.png" data-file-key="hero_image">
    <div class="mt-2"><img data-preview-key="hero_image" style="max-width:100%;height:auto;display:none"></div>
  </div>
</div>`);

                case 'hero3':
                    return html(`
<div class="row g-3">
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Title Segments</span></div></div>
  <div class="col-12 d-flex justify-content-between align-items-center">
    <label class="form-label small-label mb-0">Segments</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="title_segments">Add Segment</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="title_segments">
      <template data-repeater-template>
        <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
          <span class="drag-handle">☰</span>
          <input class="form-control" name="data[title_segments][__INDEX__]">
          <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>×</button>
        </div>
      </template>
    </div>
  </div>
  <div class="col-md-4">
    <label class="form-label small-label">Highlight Index</label>
    <input class="form-control" type="number" min="0" name="data[highlight_index]" value="1">
  </div>
  <div class="col-md-8">
    <label class="form-label small-label">Banner Image</label>
    <input class="form-control" type="file" name="banner_image" accept=".webp,.jpg,.jpeg,.png" data-file-key="banner_image">
    <div class="mt-2"><img data-preview-key="banner_image" style="max-width:100%;height:auto;display:none"></div>
  </div>
</div>`);

                case 'overview':
                    return html(`
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label small-label">Kicker</label>
    <input class="form-control" name="data[kicker]">
  </div>
  <div class="col-md-8">
    <label class="form-label small-label">Title*</label>
    <input class="form-control" name="data[title]" required>
  </div>
  <div class="col-12">
    <label class="form-label small-label">Description</label>
    <textarea class="form-control" name="data[description]" rows="3"></textarea>
  </div>
  <div class="col-md-5">
    <label class="form-label small-label">CTA Text</label>
    <input class="form-control" name="data[link_text]">
  </div>
  <div class="col-md-7">
    <label class="form-label small-label">CTA Link</label>
    <input class="form-control" name="data[link]" type="url">
  </div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Cards</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Cards</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="items">Add Card</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items" data-max="5">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-4">
                <label class="form-label small-label">Icon</label>
                <input class="form-control" name="data[items][__INDEX__][icon]">
              </div>
              <div class="col-md-8">
                <label class="form-label small-label">Subtitle*</label>
                <input class="form-control" name="data[items][__INDEX__][subtitle]" required>
              </div>
              <div class="col-12">
                <label class="form-label small-label">Text</label>
                <textarea class="form-control" name="data[items][__INDEX__][text]" rows="2"></textarea>
              </div>
              <div class="col-md-5">
                <label class="form-label small-label">Link Text</label>
                <input class="form-control" name="data[items][__INDEX__][link_text]">
              </div>
              <div class="col-md-7">
                <label class="form-label small-label">Link</label>
                <input class="form-control" name="data[items][__INDEX__][link]" type="url">
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'about':
                    return html(`
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label small-label">Kicker</label>
    <input class="form-control" name="data[kicker]">
  </div>
  <div class="col-md-8">
    <label class="form-label small-label">Title*</label>
    <input class="form-control" name="data[title]" required>
  </div>
  <div class="col-12">
    <label class="form-label small-label">Subtitle</label>
    <input class="form-control" name="data[subtitle]">
  </div>
  <div class="col-12">
    <label class="form-label small-label">Text</label>
    <textarea class="form-control" name="data[text]" rows="3"></textarea>
  </div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">List</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Items</label>
    <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="list">Add Item</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="list">
      <template data-repeater-template>
        <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
          <span class="drag-handle">☰</span>
          <input class="form-control" name="data[list][__INDEX__]">
          <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>×</button>
        </div>
      </template>
    </div>
  </div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">CTA</span></div></div>
  <div class="col-md-5">
    <label class="form-label small-label">CTA Text</label>
    <input class="form-control" name="data[cta][link_text]">
  </div>
  <div class="col-md-7">
    <label class="form-label small-label">CTA Link</label>
    <input class="form-control" name="data[cta][link]" type="url">
  </div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Cards</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Cards</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="cards">Add Card</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="cards">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6">
                <label class="form-label small-label">Title*</label>
                <input class="form-control" name="data[cards][__INDEX__][title]" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small-label">Text</label>
                <input class="form-control" name="data[cards][__INDEX__][text]">
              </div>
              <div class="col-12">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="cards[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="cards___INDEX___image">
                <div class="mt-2"><img data-preview-key="cards___INDEX___image" style="max-width:100%;height:auto;display:none"></div>
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Tiles</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Tiles</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="tiles">Add Tile</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="tiles">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-4">
                <label class="form-label small-label">Type</label>
                <select class="form-select" name="data[tiles][__INDEX__][type]" data-tile-type="__INDEX__">
                  <option value="counter">Counter</option>
                  <option value="image">Image</option>
                  <option value="customers">Customers</option>
                </select>
              </div>
              <div class="col-12" data-tile-group="counter-__INDEX__" style="display:none">
                <div class="row g-2">
                  <div class="col-md-3"><label class="form-label small-label">Label</label><input class="form-control" name="data[tiles][__INDEX__][label]"></div>
                  <div class="col-md-3"><label class="form-label small-label">Value</label><input class="form-control" name="data[tiles][__INDEX__][value]"></div>
                  <div class="col-md-3"><label class="form-label small-label">Suffix</label><input class="form-control" name="data[tiles][__INDEX__][suffix]"></div>
                  <div class="col-md-3"><label class="form-label small-label">Note</label><input class="form-control" name="data[tiles][__INDEX__][note]"></div>
                </div>
              </div>
              <div class="col-12" data-tile-group="image-__INDEX__" style="display:none">
                <label class="form-label small-label">BG Image</label>
                <input class="form-control" type="file" name="tiles[__INDEX__][bg]" accept=".webp,.jpg,.jpeg,.png" data-file-key="tiles___INDEX___bg">
                <div class="mt-2"><img data-preview-key="tiles___INDEX___bg" style="max-width:100%;height:auto;display:none"></div>
              </div>
              <div class="col-12" data-tile-group="customers-__INDEX__" style="display:none">
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label small-label">BG Image</label>
                    <input class="form-control" type="file" name="tiles[__INDEX__][bg]" accept=".webp,.jpg,.jpeg,.png" data-file-key="tiles___INDEX___bg">
                    <div class="mt-2"><img data-preview-key="tiles___INDEX___bg" style="max-width:100%;height:auto;display:none"></div>
                  </div>
                  <div class="col-12"><label class="form-label small-label">Text</label><input class="form-control" name="data[tiles][__INDEX__][text]"></div>
                  <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[tiles][__INDEX__][link_text]"></div>
                  <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[tiles][__INDEX__][link]"></div>
                </div>
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'about2':
                    return html(`
<div class="row g-3">
  <div class="col-md-4"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[kicker]"></div>
  <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-12"><label class="form-label small-label">Text</label><textarea class="form-control" name="data[text]" rows="3"></textarea></div>
  <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[link_text]"></div>
  <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[link]"></div>
  <div class="col-md-6">
    <label class="form-label small-label">Image</label>
    <input class="form-control" type="file" name="about_image" accept=".webp,.jpg,.jpeg,.png" data-file-key="about_image">
    <div class="mt-2"><img data-preview-key="about_image" style="max-width:100%;height:auto;display:none"></div>
  </div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Columns</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Columns</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="columns">Add Column</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="columns" data-max="2">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6"><label class="form-label small-label">Head</label><input class="form-control" name="data[columns][__INDEX__][head]"></div>
              <div class="col-md-6"><label class="form-label small-label">Subhead</label><input class="form-control" name="data[columns][__INDEX__][subhead]"></div>
              <div class="col-12"><label class="form-label small-label">Description</label><textarea class="form-control" rows="2" name="data[columns][__INDEX__][description]"></textarea></div>
              <div class="col-12">
                <div class="d-flex justify-content-between mb-2">
                  <label class="form-label small-label mb-0">List</label>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-repeater-add="columns___INDEX___list">Add Item</button>
                </div>
                <div class="repeater" data-repeater data-name="columns___INDEX___list">
                  <template data-repeater-template>
                    <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
                      <span class="drag-handle">☰</span>
                      <input class="form-control" name="data[columns][__INDEX__][list][__SUBINDEX__]">
                      <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>×</button>
                    </div>
                  </template>
                </div>
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'sections':
                    return html(`
<div class="row g-3">
  <div class="col-md-4"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[kicker]"></div>
  <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Items</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Services</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="items">Add Service</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-4"><label class="form-label small-label">Icon</label><input class="form-control" name="data[items][__INDEX__][icon]"></div>
              <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[items][__INDEX__][title]" required></div>
              <div class="col-md-6"><label class="form-label small-label">Subtitle</label><input class="form-control" name="data[items][__INDEX__][subtitle]"></div>
              <div class="col-md-6"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[items][__INDEX__][link_text]"></div>
              <div class="col-12"><label class="form-label small-label">Text</label><textarea class="form-control" name="data[items][__INDEX__][text]" rows="2"></textarea></div>
              <div class="col-md-6"><label class="form-label small-label">Link</label><input class="form-control" name="data[items][__INDEX__][link]" type="url"></div>
              <div class="col-md-6">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="items[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="items___INDEX___image">
                <div class="mt-2"><img data-preview-key="items___INDEX___image" style="max-width:100%;height:auto;display:none"></div>
              </div>
              <div class="col-12">
                <div class="d-flex justify-content-between mb-2 mt-2">
                  <label class="form-label small-label mb-0">List</label>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-repeater-add="items___INDEX___list">Add Item</button>
                </div>
                <div class="repeater" data-repeater data-name="items___INDEX___list">
                  <template data-repeater-template>
                    <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
                      <span class="drag-handle">☰</span>
                      <input class="form-control" name="data[items][__INDEX__][list][__SUBINDEX__]">
                      <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>×</button>
                    </div>
                  </template>
                </div>
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'sections2':
                    return html(`
<div class="row g-3">
  <div class="col-md-4"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[kicker]"></div>
  <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-12"><label class="form-label small-label">Description</label><textarea class="form-control" rows="2" name="data[desc]"></textarea></div>
  <div class="col-md-6"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[link_text]"></div>
  <div class="col-md-6"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[link]"></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Items</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Items</label>
    <button class="btn btn-dark btn-sm" type="button" data-repeater-add="items">Add Item</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6"><label class="form-label small-label">Title*</label><input class="form-control" name="data[items][__INDEX__][title]" required></div>
              <div class="col-md-6">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="items[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="items___INDEX___image">
                <div class="mt-2"><img data-preview-key="items___INDEX___image" style="max-width:100%;height:auto;display:none"></div>
              </div>
              <div class="col-12"><label class="form-label small-label">Subtitle</label><input class="form-control" name="data[items][__INDEX__][subtitle]"></div>
              <div class="col-12"><label class="form-label small-label">Description</label><textarea class="form-control" rows="2" name="data[items][__INDEX__][description]"></textarea></div>
              <div class="col-12">
                <div class="d-flex justify-content-between mb-2">
                  <label class="form-label small-label mb-0">List</label>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-repeater-add="items___INDEX___list">Add Item</button>
                </div>
                <div class="repeater" data-repeater data-name="items___INDEX___list">
                  <template data-repeater-template>
                    <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
                      <span class="drag-handle">☰</span>
                      <input class="form-control" name="data[items][__INDEX__][list][__SUBINDEX__]">
                      <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>×</button>
                    </div>
                  </template>
                </div>
              </div>
              <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[items][__INDEX__][link_text]"></div>
              <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[items][__INDEX__][link]"></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);
                case 'overview2':
                    return html(`
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label small-label">Kicker</label>
    <input class="form-control" name="data[kicker]" placeholder="OUR PROGRAM">
  </div>
  <div class="col-md-8">
    <label class="form-label small-label">Title*</label>
    <input class="form-control" name="data[title]" placeholder="Program Includes:" required>
  </div>
  <div class="col-12">
    <label class="form-label small-label">Subtitle</label>
    <input class="form-control" name="data[subtitle]" placeholder="What you get">
  </div>
  <div class="col-12">
    <label class="form-label small-label">Description</label>
    <textarea class="form-control" name="data[desc]" rows="2" placeholder="Short overview paragraph..."></textarea>
  </div>
  
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Top-level List (Optional)</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Bullet Points</label>
    <button type="button" class="btn btn-outline-primary btn-sm" data-repeater-add="list">Add Item</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="list">
      <template data-repeater-template>
        <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
          <span class="drag-handle">☰</span>
          <input class="form-control" name="data[list][__INDEX__]" placeholder="Live classes, Hands-on labs, etc.">
          <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove">×</button>
        </div>
      </template>
    </div>
  </div>

  <div class="col-md-5">
    <label class="form-label small-label">Default Link Text</label>
    <input class="form-control" name="data[link_text]" placeholder="Learn More">
  </div>
  <div class="col-md-7">
    <label class="form-label small-label">Default Link URL</label>
    <input class="form-control" type="url" name="data[link]" placeholder="#">
  </div>

  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Program Items</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Items</label>
    <button class="btn btn-dark btn-sm" type="button" data-repeater-add="items">Add Item</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6">
                <label class="form-label small-label">Title*</label>
                <input class="form-control" name="data[items][__INDEX__][title]" placeholder="Foundations (5 Weeks)" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="items[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="items___INDEX___image">
                <div class="mt-2"><img data-preview-key="items___INDEX___image" style="max-width:100%;height:auto;display:none"></div>
              </div>
              <div class="col-12">
                <label class="form-label small-label">Description</label>
                <textarea class="form-control" rows="2" name="data[items][__INDEX__][description]" placeholder="15 live classes, mentor support..."></textarea>
              </div>
              
              <div class="col-12">
                <div class="d-flex justify-content-between mb-2 mt-2">
                  <label class="form-label small-label mb-0">Item Features List</label>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-repeater-add="items___INDEX___list">Add Feature</button>
                </div>
                <div class="repeater" data-repeater data-name="items___INDEX___list">
                  <template data-repeater-template>
                    <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
                      <span class="drag-handle">☰</span>
                      <input class="form-control" name="data[items][__INDEX__][list][__SUBINDEX__]" placeholder="Live cohort, Projects, etc.">
                      <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove">×</button>
                    </div>
                  </template>
                </div>
              </div>

              <div class="col-md-12">
                <label class="form-label small-label">Item Link URL (overrides default)</label>
                <input class="form-control" type="url" name="data[items][__INDEX__][link]" placeholder="/programs/foundations">
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove">Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);
                case 'logo_slider':
                    return html(`
<div class="row g-3">
  <div class="col-md-4"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[kicker]"></div>
  <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[link_text]"></div>
  <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[link]"></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Logos</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Logos</label>
    <button class="btn btn-dark btn-sm" type="button" data-repeater-add="logos">Add Logo</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="logos">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="logos[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="logos___INDEX___image">
                <div class="mt-2"><img data-preview-key="logos___INDEX___image" style="max-width:220px;height:auto;display:none"></div>
              </div>
              <div class="col-md-6"><label class="form-label small-label">Alt</label><input class="form-control" name="data[logos][__INDEX__][alt]"></div>
              <div class="col-12"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[logos][__INDEX__][href]"></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'gallary':
                    return html(`
<div class="row g-3">
  <div class="col-md-4"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[kicker]"></div>
  <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[section_title]" required></div>
  <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[link_text]"></div>
  <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[link]"></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Items</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Projects</label>
    <button class="btn btn-dark btn-sm" data-repeater-add="items" type="button">Add</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6"><label class="form-label small-label">Title*</label><input class="form-control" name="data[items][__INDEX__][title]" required></div>
              <div class="col-md-6">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="items[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="items___INDEX___image">
                <div class="mt-2"><img data-preview-key="items___INDEX___image" style="max-width:100%;height:auto;display:none"></div>
              </div>
              <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[items][__INDEX__][link_text]"></div>
              <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[items][__INDEX__][link]"></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'testimonial':
                    return html(`
<div class="row g-3">
  <div class="col-md-4"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[kicker]"></div>
  <div class="col-md-8"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Testimonials</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Items</label>
    <button class="btn btn-dark btn-sm" data-repeater-add="items" type="button">Add</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-4"><label class="form-label small-label">Name*</label><input class="form-control" name="data[items][__INDEX__][name]" required></div>
              <div class="col-md-4"><label class="form-label small-label">Designation</label><input class="form-control" name="data[items][__INDEX__][designation]"></div>
              <div class="col-md-4">
                <label class="form-label small-label">Photo</label>
                <input class="form-control" type="file" name="items[__INDEX__][photo]" accept=".webp,.jpg,.jpeg,.png" data-file-key="items___INDEX___photo">
                <div class="mt-2"><img data-preview-key="items___INDEX___photo" style="max-width:100%;height:60px;display:none;border-radius:50%"></div>
              </div>
              <div class="col-md-9"><label class="form-label small-label">Text*</label><textarea class="form-control" rows="3" name="data[items][__INDEX__][text]" required></textarea></div>
              <div class="col-md-3"><label class="form-label small-label">Rating %</label><input class="form-control" type="number" min="0" max="100" name="data[items][__INDEX__][rating_fill]" value="100"></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'pricing':
                    return html(`
<div class="row g-3">
  <div class="col-md-5"><label class="form-label small-label">Kicker</label><input class="form-control" name="data[sidebar_kicker]"></div>
  <div class="col-md-7"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-12"><label class="form-label small-label">Description</label><textarea class="form-control" name="data[desc]" rows="2"></textarea></div>
  <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[link_text]"></div>
  <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[link]"></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">Plans (At least 1 required)</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Plans</label>
    <button class="btn btn-dark btn-sm" type="button" data-repeater-add="plans">Add Plan</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="plans" data-min="1">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6"><label class="form-label small-label">Title*</label><input class="form-control" name="data[plans][__INDEX__][title]" required></div>
              <div class="col-md-6"><label class="form-label small-label">Subtitle</label><input class="form-control" name="data[plans][__INDEX__][subtitle]"></div>
              <div class="col-md-6"><label class="form-label small-label">Price</label><input class="form-control" name="data[plans][__INDEX__][price_naira]"></div>
              <div class="col-12">
                <div class="d-flex justify-content-between mb-2 mt-1">
                  <label class="form-label small-label mb-0">Features</label>
                  <button class="btn btn-outline-primary btn-sm" type="button" data-repeater-add="plans___INDEX___features">Add Feature</button>
                </div>
                <div class="repeater" data-repeater data-name="plans___INDEX___features">
                  <template data-repeater-template>
                    <div class="repeater-item d-flex align-items-center gap-2 mb-2" data-repeater-item draggable="true">
                      <span class="drag-handle">☰</span>
                      <input class="form-control" name="data[plans][__INDEX__][features][__SUBINDEX__]" placeholder="Feature text">
                      <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>×</button>
                    </div>
                  </template>
                </div>
              </div>
              <div class="col-md-5"><label class="form-label small-label">Link Text</label><input class="form-control" name="data[plans][__INDEX__][link_text]"></div>
              <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[plans][__INDEX__][link]"></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove data-confirm="Remove this plan?">Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'faq':
                    return html(`
<div class="row g-3">
  <div class="col-md-6"><label class="form-label small-label">Title</label><input class="form-control" name="data[title]"></div>
  <div class="col-md-6"><label class="form-label small-label">Subtitle</label><input class="form-control" name="data[subtitle]"></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">FAQs</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Items</label>
    <button class="btn btn-dark btn-sm" type="button" data-repeater-add="items">Add FAQ</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="items">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-12"><label class="form-label small-label">Question*</label><input class="form-control" name="data[items][__INDEX__][q]" required></div>
              <div class="col-12"><label class="form-label small-label">Answer*</label><textarea class="form-control" rows="3" name="data[items][__INDEX__][a]" required></textarea></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'closing_cta':
                    return html(`
<div class="row g-3">
  <div class="col-12"><label class="form-label small-label">Title*</label><input class="form-control" name="data[title]" required></div>
  <div class="col-12"><label class="form-label small-label">Subtitle</label><textarea class="form-control" rows="2" name="data[subtitle]"></textarea></div>
  <div class="col-12"><div class="section-divider"><span class="section-divider-label">CTAs</span></div></div>
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Buttons</label>
    <button class="btn btn-dark btn-sm" type="button" data-repeater-add="ctas">Add CTA</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="ctas" data-max="3">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-5"><label class="form-label small-label">Text*</label><input class="form-control" name="data[ctas][__INDEX__][link_text]" required></div>
              <div class="col-md-7"><label class="form-label small-label">Link</label><input class="form-control" type="url" name="data[ctas][__INDEX__][link]"></div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                case 'marquees':
                    return html(`
<div class="row g-3">
  <div class="col-12 d-flex justify-content-between">
    <label class="form-label small-label mb-0">Slides</label>
    <button type="button" class="btn btn-dark btn-sm" data-repeater-add="slides">Add Slide</button>
  </div>
  <div class="col-12">
    <div class="repeater" data-repeater data-name="slides">
      <template data-repeater-template>
        <div class="repeater-item border rounded-12 p-3 mb-3" data-repeater-item draggable="true">
          <div class="d-flex gap-3 align-items-start">
            <span class="drag-handle">☰</span>
            <div class="row g-2 flex-grow-1">
              <div class="col-md-6"><label class="form-label small-label">Title*</label><input class="form-control" name="data[slides][__INDEX__][title]" required></div>
              <div class="col-md-6">
                <label class="form-label small-label">Image</label>
                <input class="form-control" type="file" name="slides[__INDEX__][image]" accept=".webp,.jpg,.jpeg,.png" data-file-key="slides___INDEX___image">
                <div class="mt-2"><img data-preview-key="slides___INDEX___image" style="max-width:100%;height:auto;display:none"></div>
              </div>
            </div>
            <button class="btn btn-outline-danger btn-sm" type="button" data-repeater-remove>Remove</button>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>`);

                default:
                    return html(`<div class="alert alert-info">Select a block type to see its fields.</div>`);
            }
        }

        /* ====== TYPE-SPECIFIC BINDINGS ====== */
        function attachTypeBindings(type, mount, mode, existing) {
            if (type === 'hero') {
                const title = mount.querySelector('input[name="data[title]"]');
                const slug = mount.querySelector('input[name="data[slug]"][data-hero-slug]');
                if (slug && title) {
                    slug.value = existing.slug || slugify(title.value || '');
                    title.addEventListener('input', () => {
                        slug.value = slugify(title.value);
                    });
                }
            }

            if (type === 'about') {
                // Tile type toggle
                mount.addEventListener('change', (e) => {
                    const sel = e.target.closest('[data-tile-type]');
                    if (sel) {
                        const idx = sel.getAttribute('data-tile-type');
                        toggleTileGroups(mount, idx, sel.value);
                    }
                });

                // Initialize existing tiles
                if (existing.tiles && Array.isArray(existing.tiles)) {
                    setTimeout(() => {
                        existing.tiles.forEach((tile, i) => {
                            const sel = mount.querySelector(`[data-tile-type="${i}"]`);
                            if (sel) {
                                sel.value = tile.type || 'counter';
                                toggleTileGroups(mount, i, sel.value);
                            }
                        });
                    }, 100);
                }
            }
        }

        function toggleTileGroups(scope, idx, typeVal) {
            ['counter', 'image', 'customers'].forEach((g) => {
                const el = scope.querySelector(`[data-tile-group="${g}-${idx}"]`);
                if (el) {
                    el.style.display = g === typeVal ? '' : 'none';
                }
            });
        }

        /* ====== HYDRATION ====== */

        function isSimpleObject(obj) {

            return obj && typeof obj === 'object' && !Array.isArray(obj);

        }



        /* ====== REPEATER SYSTEM ====== */

        /* ====== ENHANCED REPEATER SYSTEM WITH PROPER LIST HANDLING ====== */



        // Global index counter for unique IDs

        let globalRepeaterCounter = 0;



        function initRepeater(root = document) {

            root.querySelectorAll('.repeater[data-name]').forEach((rep) => {

                if (rep.dataset.repeaterReady) return;

                rep.dataset.repeaterReady = '1';



                const name = rep.getAttribute('data-name');

                const tpl = rep.querySelector('template[data-repeater-template]');

                if (!tpl) return;



                // Setup add buttons

                root.querySelectorAll(`[data-repeater-add="${name}"]`).forEach((btn) => {

                    if (btn.dataset.listenerAdded) return;

                    btn.dataset.listenerAdded = '1';



                    btn.addEventListener('click', (e) => {

                        e.preventDefault();

                        e.stopPropagation();



                        const max = parseInt(rep.dataset.max || '0', 10);

                        const count = rep.querySelectorAll(':scope > [data-repeater-item]').length;



                        if (max && count >= max) {

                            alert(`Maximum ${max} items allowed`);

                            return;
                        }



                        addRepeaterItem(rep, tpl);

                    });

                });



                // Remove delegation

                rep.addEventListener('click', (e) => {

                    const btn = e.target.closest('[data-repeater-remove]');

                    if (!btn) return;



                    const item = btn.closest('[data-repeater-item]');

                    if (!item || item.parentElement !== rep) return;



                    e.preventDefault();

                    e.stopPropagation();

                    item.remove();

                    renumberRepeater(rep);



                    // Renumber parent if this is nested

                    const parentRepeater = rep.closest('[data-repeater-item]')?.closest(

                        '.repeater[data-name]');

                    if (parentRepeater) {

                        renumberRepeater(parentRepeater);

                    }

                });



                // Drag and drop

                setupDragDrop(rep);

            });

        }



        function addRepeaterItem(rep, tpl) {

            const item = tpl.content.firstElementChild.cloneNode(true);

            item.setAttribute('draggable', 'true');



            // Assign unique temporary ID for nested repeater tracking

            item.dataset.tempId = `temp_${globalRepeaterCounter++}`;



            rep.appendChild(item);



            // Renumber this repeater

            renumberRepeater(rep);



            // Initialize nested repeaters inside the new item

            initRepeater(item);



            // If this repeater has a parent repeater, trigger full renumbering

            const parentRepeater = rep.closest('[data-repeater-item]')?.closest('.repeater[data-name]');

            if (parentRepeater) {

                setTimeout(() => renumberRepeater(parentRepeater), 50);

            }

        }



        function renumberRepeater(rep) {



            const items = [...rep.querySelectorAll(':scope > [data-repeater-item]')];







            items.forEach((item, i) => {



                // Update ALL field names in this item



                item.querySelectorAll('[name]').forEach((field) => {



                    let name = field.name;







                    // Handle __INDEX__ replacement



                    name = name.replace(/\[__INDEX__\]/g, `[${i}]`);







                    // Handle __SUBINDEX__ for nested items



                    if (name.includes('__SUBINDEX__')) {



                        // Don't replace __SUBINDEX__ at parent level - let nested renumber handle it



                        return;



                    }







                    field.name = name;



                });







                // Update data attributes



                item.querySelectorAll('[data-file-key], [data-preview-key], [data-tile-type], [data-tile-group]')



                    .forEach((el) => {



                        ['data-file-key', 'data-preview-key', 'data-tile-type', 'data-tile-group'].forEach((



                            attr) => {



                            if (el.hasAttribute(attr)) {



                                let val = el.getAttribute(attr);



                                val = val.replace(/___INDEX___/g, `_${i}_`);



                                val = val.replace(/__INDEX__/g, i);



                                el.setAttribute(attr, val);



                            }



                        });



                    });







                // Update nested repeater add button names



                item.querySelectorAll('[data-repeater-add]').forEach((btn) => {



                    const name = btn.getAttribute('data-repeater-add');



                    const updated = name.replace(/___INDEX___/g, `_${i}_`);



                    btn.setAttribute('data-repeater-add', updated);



                });







                // Update nested repeater data-name attributes AND renumber them



                item.querySelectorAll('.repeater[data-name]').forEach((nestedRep) => {



                    const name = nestedRep.getAttribute('data-name');



                    const updated = name.replace(/___INDEX___/g, `_${i}_`);



                    nestedRep.setAttribute('data-name', updated);







                    // Renumber nested items



                    const nestedItems = [...nestedRep.querySelectorAll(':scope > [data-repeater-item]')];



                    nestedItems.forEach((nestedItem, j) => {



                        nestedItem.querySelectorAll('[name]').forEach((field) => {



                            let name = field.name;







                            // Replace subindex first



                            name = name.replace(/\[__SUBINDEX__\]/g, `[${j}]`);







                            // Then replace parent index



                            name = name.replace(/\[__INDEX__\]/g, `[${i}]`);







                            field.name = name;



                        });



                    });



                });



            });



        }







        function setupDragDrop(rep) {



            let dragging = null;







            rep.addEventListener('dragstart', (e) => {



                const item = e.target.closest('[data-repeater-item]');



                if (!item || item.parentElement !== rep) return;







                dragging = item;



                item.classList.add('dragging');



                e.dataTransfer.effectAllowed = 'move';



            });







            rep.addEventListener('dragend', (e) => {



                const item = e.target.closest('[data-repeater-item]');



                if (!item) return;







                item.classList.remove('dragging');



                dragging = null;



                renumberRepeater(rep);



            });







            rep.addEventListener('dragover', (e) => {



                e.preventDefault();



                if (!dragging) return;







                const items = [...rep.querySelectorAll(':scope > [data-repeater-item]:not(.dragging)')];



                const afterElement = items.find((item) => {



                    const box = item.getBoundingClientRect();



                    return e.clientY < box.top + box.height / 2;



                });







                if (afterElement) {



                    rep.insertBefore(dragging, afterElement);



                } else {



                    rep.appendChild(dragging);



                }



            });



        }







        /* ====== IMPROVED HYDRATION WITH BETTER LIST HANDLING ====== */



        function hydrateFields(scope, data) {

            if (!data || typeof data !== 'object') return;



            console.log('📄 Starting hydration with data:', data);



            // Hydrate scalars

            for (const [key, val] of Object.entries(data)) {

                if (val === null || val === undefined) continue;

                if (Array.isArray(val) || (typeof val === 'object' && !isSimpleObject(val))) continue;

                setVal(`data[${key}]`, val, scope);

            }



            // Hydrate nested objects (non-arrays)

            for (const [key, val] of Object.entries(data)) {

                if (!val || typeof val !== 'object' || Array.isArray(val)) continue;

                for (const [k2, v2] of Object.entries(val)) {

                    if (v2 === null || v2 === undefined) continue;

                    if (Array.isArray(v2) || (typeof v2 === 'object')) continue;

                    setVal(`data[${key}][${k2}]`, v2, scope);

                }

            }



            // Hydrate arrays

            for (const [arrKey, arrVal] of Object.entries(data)) {

                if (!Array.isArray(arrVal)) continue;



                console.log(`📋 Processing array: ${arrKey}`, arrVal);



                const addBtn = scope.querySelector(`[data-repeater-add="${arrKey}"]`);

                const repeater = scope.querySelector(`.repeater[data-name="${arrKey}"]`);



                if (!addBtn || !repeater) {

                    console.warn(`⚠ Repeater not found for: ${arrKey}`);

                    continue;

                }



                // Add items

                console.log(`➕ Adding ${arrVal.length} items to ${arrKey}`);

                for (let i = 0; i < arrVal.length; i++) {

                    addBtn.click();

                }



                // Hydrate with delay

                setTimeout(() => {

                    console.log(`💾 Hydrating ${arrKey} items...`);



                    arrVal.forEach((row, i) => {

                        if (row && typeof row === 'object' && !Array.isArray(row)) {

                            Object.entries(row).forEach(([rk, rv]) => {

                                if (rv === null || rv === undefined) return;



                                if (Array.isArray(rv)) {

                                    console.log(`🔗 Found nested array: ${arrKey}[${i}].${rk}`, rv);

                                    hydrateNestedArray(scope, arrKey, i, rk, rv);

                                } else if (typeof rv === 'object') {

                                    return;

                                } else {

                                    setVal(`data[${arrKey}][${i}][${rk}]`, rv, scope);

                                }

                            });

                        } else {

                            setVal(`data[${arrKey}][${i}]`, row, scope);

                        }

                    });



                    console.log(`✅ Finished hydrating ${arrKey}`);

                }, 200);

            }

        }



        function hydrateNestedArray(scope, parentKey, parentIdx, childKey, childArray) {

            setTimeout(() => {

                const nestedName = `${parentKey}_${parentIdx}_${childKey}`;

                let addBtn = scope.querySelector(`[data-repeater-add="${nestedName}"]`);



                if (!addBtn) {

                    console.warn(`⚠ Add button not found for: ${nestedName}`);

                    return;

                }



                console.log(`✅ Found nested repeater: ${nestedName}, adding ${childArray.length} items`);



                // Add all items first

                for (let j = 0; j < childArray.length; j++) {

                    addBtn.click();

                }



                // Force renumber of parent

                const parentRepeater = scope.querySelector(`.repeater[data-name="${parentKey}"]`);

                if (parentRepeater) {

                    renumberRepeater(parentRepeater);

                }



                // Hydrate after renumbering

                setTimeout(() => {

                    childArray.forEach((item, j) => {

                        if (item && typeof item === 'object' && !Array.isArray(item)) {

                            Object.entries(item).forEach(([k, v]) => {

                                if (v !== null && v !== undefined && typeof v !==

                                    'object') {

                                    const fieldName =

                                        `data[${parentKey}][${parentIdx}][${childKey}][${j}][${k}]`;

                                    setVal(fieldName, v, scope);

                                    console.log(`  💾 Set: ${fieldName} = ${v}`);

                                }

                            });

                        } else {

                            const fieldName = `data[${parentKey}][${parentIdx}][${childKey}][${j}]`;

                            setVal(fieldName, item, scope);

                            console.log(`  💾 Set: ${fieldName} = ${item}`);

                        }

                    });



                    console.log(

                        `✅ Hydrated ${childArray.length} items in ${parentKey}[${parentIdx}].${childKey}`

                    );

                }, 150);

            }, 250);

        }



        /* ====== ENHANCED FINALIZATION WITH PROPER EMPTY CHECKING ====== */



        function pruneEmptyRepeaterItems(root = document) {

            const allRepeaters = root.querySelectorAll('.repeater[data-name]');



            allRepeaters.forEach((rep) => {

                const items = [...rep.querySelectorAll(':scope > [data-repeater-item]')];



                items.forEach((item) => {

                    // Check ALL text/textarea inputs (not file inputs)

                    const textInputs = item.querySelectorAll(

                        'input:not([type="file"]):not([type="hidden"]), textarea, select');



                    if (textInputs.length === 0) return;



                    // Check if ALL text inputs are empty

                    const allEmpty = [...textInputs].every(field => {

                        const val = field.value || '';

                        return val.trim() === '';

                    });



                    if (allEmpty) {

                        console.log('🗑️ Removing empty item');

                        item.remove();

                    }

                });

            });



            // Renumber after pruning

            allRepeaters.forEach(rep => renumberRepeater(rep));

        }



        function pruneEmptyListInputs(root = document) {

            // Find all simple list items (single input repeater items)

            root.querySelectorAll('.repeater[data-name*="list"]').forEach((listRepeater) => {

                const items = [...listRepeater.querySelectorAll(':scope > [data-repeater-item]')];



                items.forEach((item) => {

                    const inputs = item.querySelectorAll(

                        'input:not([type="file"]):not([type="hidden"]), textarea');



                    // If this item only has one input and it's empty, remove it

                    if (inputs.length === 1) {

                        const val = (inputs[0].value || '').trim();

                        if (val === '') {

                            console.log('🗑️ Removing empty list item');

                            item.remove();

                        }

                    }

                });

            });

        }



        function finalizeRepeaters(root = document) {

            // Get all repeaters in proper order (parents first)

            const repeaters = [...root.querySelectorAll('.repeater[data-name]')];



            // Sort by nesting depth (fewer ancestors first)

            repeaters.sort((a, b) => {

                const depthA = a.closest('[data-repeater-item]') ? 1 : 0;

                const depthB = b.closest('[data-repeater-item]') ? 1 : 0;

                return depthA - depthB;

            });



            // Renumber each repeater

            repeaters.forEach(rep => renumberRepeater(rep));

        }



        function stripLeftoverPlaceholders(root = document) {

            root.querySelectorAll('[name]').forEach((el) => {

                // Only replace if it's still a placeholder (shouldn't happen after proper renumbering)

                if (el.name.includes('__INDEX__') || el.name.includes('__SUBINDEX__')) {

                    console.warn('⚠ Found leftover placeholder:', el.name);

                    el.name = el.name.replace(/\[__INDEX__\]/g, '[0]');

                    el.name = el.name.replace(/\[__SUBINDEX__\]/g, '[0]');

                }

            });

        }



        function debugFormData(root = document) {

            console.log('=== FORM DEBUG ===');



            // Check for placeholders

            const badFields = root.querySelectorAll('[name*="__INDEX__"], [name*="__SUBINDEX__"]');

            if (badFields.length > 0) {

                console.error('⚠ Found fields with placeholders:', badFields.length);

                badFields.forEach(f => console.error('  -', f.name));

            } else {

                console.log('✅ No placeholder fields found');

            }



            // Show all data fields

            const dataFields = root.querySelectorAll('[name^="data["]');

            console.log(`📊 Total data fields: ${dataFields.length}`);



            // Group by top-level key

            const grouped = {};

            dataFields.forEach(f => {

                const match = f.name.match(/^data\[([^\]]+)\]/);

                if (match) {

                    const key = match[1];

                    grouped[key] = (grouped[key] || 0) + 1;

                }

            });



            console.table(grouped);



            // Check for empty required fields

            const emptyRequired = [...root.querySelectorAll('[required]')].filter(f => !f.value?.trim());

            if (emptyRequired.length > 0) {

                console.warn('⚠ Empty required fields:', emptyRequired.length);

            }

        }



        // Add validation for minimum plan requirement:

        function validatePricingBlock(mount) {

            const plansRepeater = mount.querySelector('.repeater[data-name="plans"]');

            if (!plansRepeater) return true;



            const planItems = plansRepeater.querySelectorAll(':scope > [data-repeater-item]');

            const filledPlans = [...planItems].filter(item => {

                const titleInput = item.querySelector('input[name*="[title]"]');

                return titleInput && titleInput.value.trim() !== '';

            });



            if (filledPlans.length === 0) {

                alert('Please add at least one pricing plan');

                return false;

            }



            return true;

        }



        function stripLeftoverPlaceholders(root = document) {

            root.querySelectorAll('[name]').forEach((el) => {

                el.name = el.name.replace(/\[__INDEX__\]/g, '[0]');

                el.name = el.name.replace(/\[__SUBINDEX__\]/g, '[0]');

            });

        }

        /* ====== BLOCK LIST (DRAG REORDER) ====== */
        const blockList = document.getElementById('blockList');
        if (blockList) {
            // Edit button
            blockList.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-action="edit"]');
                if (!btn) return;

                try {
                    const payload = JSON.parse(btn.getAttribute('data-payload') || '{}');
                    openModal('edit', payload);
                } catch (err) {
                    console.error('Error parsing block data:', err);
                    alert('Error loading block data');
                }
            });

            // Drag reorder
            let dragging = null;

            blockList.addEventListener('dragstart', (e) => {
                const li = e.target.closest('.block-row[draggable="true"]');
                if (!li) return;

                dragging = li;
                li.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });

            blockList.addEventListener('dragend', () => {
                if (dragging) {
                    dragging.classList.remove('dragging');
                    dragging = null;
                    saveBlockOrder();
                }
            });

            blockList.addEventListener('dragover', (e) => {
                e.preventDefault();
                if (!dragging) return;

                const items = [...blockList.querySelectorAll('.block-row[draggable="true"]:not(.dragging)')];
                const afterElement = items.find((item) => {
                    const box = item.getBoundingClientRect();
                    return e.clientY < box.top + box.height / 2;
                });

                if (afterElement) {
                    blockList.insertBefore(dragging, afterElement);
                } else {
                    blockList.appendChild(dragging);
                }
            });

            async function saveBlockOrder() {
                const order = [...blockList.querySelectorAll('.block-row')].map((el, idx) => ({
                    id: el.dataset.id,
                    order: (idx + 1) * 10
                }));

                try {
                    const res = await fetch(@json(route('pb.blocks.reorder', $page)), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCSRF()
                        },
                        body: JSON.stringify({
                            order
                        })
                    });

                    if (!res.ok) throw new Error('Failed to save order');
                } catch (err) {
                    console.error('Error saving order:', err);
                    alert('Could not save block order. Please refresh and try again.');
                }
            }
        }

        /* ====== INITIALIZATION ====== */
        if (addType) {
            updateVariantSelect(addType.value || '', addVariant);
        }

        console.log('✅ Page Builder initialized');
    </script>
@endsection
