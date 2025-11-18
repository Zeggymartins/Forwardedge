@extends('admin.master_page')

@push('styles')
<style>
    @media (max-width: 767.98px) {
        .fe-campaign-block__head {
            flex-direction: column;
            align-items: flex-start;
            gap: .5rem;
        }
    }
</style>
@endpush

@section('main')
    <div class="container-fluid py-4 px-4 px-xl-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <p class="text-muted text-uppercase small mb-1">Email Engine</p>
                <h4 class="mb-0">Compose Campaign</h4>
                <p class="text-muted mb-0">Build a Forward Edge branded email with hero, cards, lists, and images.</p>
            </div>
            <a href="{{ route('admin.emails.campaigns.index') }}" class="btn btn-outline-secondary btn-sm">Back to campaigns</a>
        </div>

        <form action="{{ route('admin.emails.campaigns.store') }}" method="POST" id="campaignBuilderForm" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Meta</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Internal title</label>
                                <input type="text" class="form-control" name="title" value="{{ old('title') }}" required placeholder="Cyber1000 weekly update">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email subject</label>
                                <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" required placeholder="Forward Edge update">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Subtitle</label>
                                <input type="text" class="form-control" name="subtitle" value="{{ old('subtitle') }}" placeholder="Optional secondary headline">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Intro paragraph</label>
                                <textarea class="form-control" name="intro" rows="4" placeholder="Warm welcome message">{{ old('intro') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Hero image</label>
                                <input type="file" class="form-control" name="hero_image" accept="image/*">
                                <small class="text-muted">Upload a JPG/PNG/WebP up to 4MB.</small>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">CTA</h5>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Button text</label>
                                <input type="text" class="form-control" name="cta_text" value="{{ old('cta_text') }}" placeholder="Claim your seat">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Button link</label>
                                <input type="url" class="form-control" name="cta_link" value="{{ old('cta_link') }}" placeholder="https://forwardedge.africa/...">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send-check me-1"></i> Save campaign
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                                <div>
                                    <h5 class="card-title mb-1">Content blocks</h5>
                                    <p class="text-muted small mb-0">Mix text, bullet lists, hero images, and cards. Drag handles keep things tidy.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <select id="block-type-selector" class="form-select form-select-sm">
                                        <option value="text">Text section</option>
                                        <option value="list">Bullet list</option>
                                        <option value="image">Image highlight</option>
                                        <option value="cards">Card deck</option>
                                    </select>
                                    <button type="button" id="add-block-btn" class="btn btn-sm btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i> Add block
                                    </button>
                                </div>
                            </div>

                            <div id="blocks-wrapper" class="fe-campaign-blocks"></div>
                            <div id="block-placeholder" class="text-center text-muted py-5 {{ old('blocks') ? 'd-none' : '' }}">
                                <p class="mb-1">No blocks yet.</p>
                                <small>Use the selector above to drop your first section.</small>
                            </div>
                            @error('blocks')
                                <p class="text-danger small mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const wrapper = document.getElementById('blocks-wrapper');
            const placeholder = document.getElementById('block-placeholder');
            const typeSelector = document.getElementById('block-type-selector');
            const addBlockBtn = document.getElementById('add-block-btn');
            let blockIndex = Date.now();

            const initialBlocks = @json(old('blocks', []));

            const listInputTemplate = (name, value = '') => `
                <div class="input-group input-group-sm mb-2" data-list-item>
                    <input type="text" class="form-control form-control-sm" name="${name}" value="${escapeHtml(value ?? '')}" placeholder="Bullet item">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-list-item"><i class="bi bi-dash"></i></button>
                </div>
            `;

            const notifyWarning = (message) => {
                if (window.iziToast) {
                    iziToast.warning({ title: 'Notice', message, position: 'topRight' });
                } else {
                    alert(message);
                }
            };

            function initListEditors(scope = document) {
                scope.querySelectorAll('[data-list-editor]').forEach(editor => {
                    if (!editor.querySelector('[data-list-items]')) {
                        const wrapper = document.createElement('div');
                        wrapper.setAttribute('data-list-items', '1');
                        wrapper.innerHTML = listInputTemplate(editor.dataset.name);
                        editor.insertBefore(wrapper, editor.querySelector('[data-action="add-list-item"]'));
                    }
                });
            }

            const templates = {
                text: (index, data = {}) => `
                    <input type="hidden" name="blocks[${index}][type]" value="text">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Heading</label>
                        <input type="text" class="form-control" name="blocks[${index}][heading]" value="${escapeHtml(data.heading ?? '')}">
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Body copy</label>
                        <textarea class="form-control" rows="4" name="blocks[${index}][body]">${escapeHtml(data.body ?? '')}</textarea>
                    </div>
                `,
                list: (index, data = {}) => {
                    const items = Array.isArray(data.items) && data.items.length ? data.items : [''];
                    const inputs = items.map(item => listInputTemplate(`blocks[${index}][items][]`, item)).join('');
                    return `
                        <input type="hidden" name="blocks[${index}][type]" value="list">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Headline</label>
                            <input type="text" class="form-control" name="blocks[${index}][heading]" value="${escapeHtml(data.heading ?? '')}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Intro</label>
                            <textarea class="form-control" rows="3" name="blocks[${index}][body]">${escapeHtml(data.body ?? '')}</textarea>
                        </div>
                        <div class="mb-0 list-editor" data-list-editor data-name="blocks[${index}][items][]" data-compact="1">
                            <label class="form-label fw-semibold">Bullet items</label>
                            <div data-list-items>${inputs}</div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" data-action="add-list-item">+ Add bullet</button>
                        </div>
                    `;
                },
                image: (index, data = {}) => `
                    <input type="hidden" name="blocks[${index}][type]" value="image">
                    ${data.image_url ? `<input type="hidden" name="blocks[${index}][existing_image]" value="${escapeHtml(data.image_url)}">` : ''}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Section title</label>
                        <input type="text" class="form-control" name="blocks[${index}][heading]" value="${escapeHtml(data.heading ?? '')}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Image upload</label>
                        <input type="file" class="form-control" name="blocks[${index}][image_file]" accept="image/*" ${data.image_url ? '' : 'required'}>
                        <small class="text-muted">Upload a branded image for this section.</small>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Alt text</label>
                            <input type="text" class="form-control" name="blocks[${index}][alt]" value="${escapeHtml(data.alt ?? '')}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Caption</label>
                            <input type="text" class="form-control" name="blocks[${index}][caption]" value="${escapeHtml(data.caption ?? '')}">
                        </div>
                    </div>
                `,
                cards: (index, data = {}) => {
                    const cardsHtml = (data.cards || []).map(card => buildCard(index, generateId(), card)).join('');
                    return `
                        <input type="hidden" name="blocks[${index}][type]" value="cards">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Section Heading</label>
                            <input type="text" class="form-control" name="blocks[${index}][heading]" value="${escapeHtml(data.heading ?? '')}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" rows="3" name="blocks[${index}][body]">${escapeHtml(data.body ?? '')}</textarea>
                        </div>
                        <div class="fe-card-collection" data-next-card="${Date.now()}">
                            ${cardsHtml || buildCard(index, generateId(), {})}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-3" data-action="add-card">
                            <i class="bi bi-plus-circle me-1"></i> Add card
                        </button>
                    `;
                }
            };

            function buildCard(blockIndex, cardIndex, card = {}) {
                return `
                    <div class="fe-campaign-card" data-card-index="${cardIndex}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Card</h6>
                            <button type="button" class="btn btn-link btn-sm text-danger p-0" data-action="remove-card">Remove</button>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Title</label>
                            <input type="text" class="form-control form-control-sm" name="blocks[${blockIndex}][cards][${cardIndex}][title]" value="${escapeHtml(card.title ?? '')}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Description</label>
                            <textarea class="form-control form-control-sm" rows="3" name="blocks[${blockIndex}][cards][${cardIndex}][body]">${escapeHtml(card.body ?? '')}</textarea>
                        </div>
                        ${card.image ? `<input type="hidden" name="blocks[${blockIndex}][cards][${cardIndex}][existing_image]" value="${escapeHtml(card.image)}">` : ''}
                        <div>
                            <label class="form-label small fw-semibold">Image upload</label>
                            <input type="file" class="form-control form-control-sm" name="blocks[${blockIndex}][cards][${cardIndex}][image_file]" accept="image/*">
                        </div>
                    </div>
                `;
            }

            function generateId() {
                return Date.now() + Math.floor(Math.random() * 1000);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function addBlock(type, data = {}) {
                const index = blockIndex++;
                const block = document.createElement('div');
                block.className = 'fe-campaign-block';
                block.dataset.index = index;
                block.dataset.type = type;
                block.innerHTML = `
                    <div class="fe-campaign-block__head">
                        <div>
                            <span class="badge text-bg-dark text-uppercase small">${type}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger" data-action="remove-block">
                            Remove
                        </button>
                    </div>
                    <div class="fe-campaign-block__body">
                        ${templates[type](index, data)}
                    </div>
                `;

                wrapper.appendChild(block);
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
                initListEditors(block);
            }

            wrapper.addEventListener('click', (event) => {
                if (event.target.closest('[data-action="remove-block"]')) {
                    event.preventDefault();
                    const block = event.target.closest('.fe-campaign-block');
                    block?.remove();
                    if (!wrapper.children.length) {
                        placeholder.classList.remove('d-none');
                    }
                    return;
                }

                if (event.target.closest('[data-action="add-card"]')) {
                    event.preventDefault();
                    const block = event.target.closest('.fe-campaign-block');
                    const cardsWrapper = block.querySelector('.fe-card-collection');
                    const index = block.dataset.index;
                    const nextId = generateId();
                    cardsWrapper.insertAdjacentHTML('beforeend', buildCard(index, nextId, {}));
                    return;
                }

                if (event.target.closest('[data-action="remove-card"]')) {
                    event.preventDefault();
                    const card = event.target.closest('.fe-campaign-card');
                    const cardsWrapper = event.target.closest('.fe-card-collection');
                    if (cardsWrapper.children.length === 1) {
                        notifyWarning('At least one card is needed.');
                        return;
                    }
                    card.remove();
                    return;
                }

                if (event.target.closest('[data-action="add-list-item"]')) {
                    event.preventDefault();
                    const editor = event.target.closest('[data-list-editor]');
                    const container = editor.querySelector('[data-list-items]');
                    container.insertAdjacentHTML('beforeend', listInputTemplate(editor.dataset.name));
                    return;
                }

                if (event.target.closest('[data-action="remove-list-item"]')) {
                    event.preventDefault();
                    const editor = event.target.closest('[data-list-editor]');
                    const container = editor.querySelector('[data-list-items]');
                    const items = container.querySelectorAll('[data-list-item]');
                    if (items.length <= 1) {
                        notifyWarning('Keep at least one bullet.');
                        return;
                    }
                    event.target.closest('[data-list-item]').remove();
                }
            });

            addBlockBtn.addEventListener('click', () => {
                const type = typeSelector.value;
                addBlock(type);
            });

            initialBlocks.forEach((block) => {
                if (block.type && templates[block.type]) {
                    addBlock(block.type, block);
                }
            });
            initListEditors(wrapper);
        });
    </script>
@endpush
