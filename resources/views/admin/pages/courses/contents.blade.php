@extends('admin.master_page')

@section('title', 'Course Contents')

<style>
    .course-card {
        transition: all 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .course-card .card-img-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .course-card .card-img-top {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .course-card .overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.55);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: opacity 0.3s ease;
    }

    .course-card .card-img-wrapper:hover .overlay {
        opacity: 1;
    }

    .course-card .overlay .btn {
        min-width: 80px;
    }

    .publish-status {
        margin-top: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 10px;
        display: inline-block;
    }

    .publish-status.published {
        background: #20c997;
        color: #fff;
    }

    .publish-status.draft {
        background: #ffc107;
        color: #000;
    }

    /* small helper for file preview images */
    .file-preview img {
        max-height: 90px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 8px;
    }

    .file-list {
        font-size: 0.9rem;
    }
</style>

@section('main')
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold">📚 Course Contents</h2>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addContentModal">
            + Add Course Content
        </button>
    </div>

    <div class="row g-4">
        @foreach ($contents as $content)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card course-card shadow-sm border-0">

                    <div class="card-img-wrapper">
                        <a href="{{ route('admin.course_contents.show', $content->course_id) }}">
                            @if ($content->course->thumbnail)
                                <img src="{{ asset('storage/' . $content->course->thumbnail) }}" class="card-img-top"
                                    alt="{{ $content->course->title }}">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center text-white"
                                    style="height:100%;">No Thumbnail</div>
                            @endif
                        </a>

                        <div class="overlay">
                            <div class="d-flex justify-content-center gap-2 mt-2">
                                <a href="{{ route('admin.course_contents.show', $content->course_id) }}"
                                    class="btn btn-light btn-sm">View</a>

                                <form action="{{ route('admin.course_contents.destroy', $content->id) }}" method="POST"
                                    onsubmit="return confirm('Delete this content?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm ">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-body text-center">
                        <h6 class="fw-bold mb-1 mt-3">{{ $content->course->title }}</h6>
                        <p class="mb-2 text-muted">{{ $content->title }}</p>

                        @if ($content->course->discount_price)
                            <div class="price">
                                <span class="old">₦{{ number_format($content->course->price, 2) }}</span>
                                <span
                                    class="text-success fw-bold">₦{{ number_format($content->course->discount_price, 2) }}</span>
                            </div>
                        @else
                            <div class="price text-dark fw-bold">
                                ₦{{ number_format($content->course->price, 2) }}
                            </div>
                        @endif

                        <div class="mt-2">
                            <span class="publish-status {{ $content->course->is_published ? 'published' : 'draft' }}">
                                {{ $content->course->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Add Content Modal -->
    <div class="modal fade" id="addContentModal" tabindex="-1" aria-labelledby="addContentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('admin.course_contents.store') }}" method="POST" enctype="multipart/form-data" id="addContentForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-gradient">
                        <h5 class="modal-title text-white" id="addContentModalLabel">➕ Add Course Content</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Select Course -->
                        <div class="mb-3">
                            <label class="form-label">Select Course</label>
                            <select name="course_id" class="form-select" required>
                                <option value="">-- Select --</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Content Blocks wrapper -->
                        <div id="content-wrapper">
                            <!-- initial block (index 0) -->
                            <div class="content-block border rounded p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <label class="form-label fw-semibold">Content Title</label>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-block" aria-label="Remove block">
                                        Remove
                                    </button>
                                </div>

                                <input type="text" name="contents[0][title]" class="form-control mb-3" placeholder="Title" required>

                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select name="contents[0][type]" class="form-select type-select" required>
                                        <option value="">-- Select --</option>
                                        <option value="text">Text</option>
                                        <option value="video">Video</option>
                                        <option value="pdf">PDF</option>
                                        <option value="image">Image</option>
                                        <option value="quiz">Quiz</option>
                                        <option value="assignment">Assignment</option>
                                    </select>
                                </div>

                                <!-- dynamic fields inserted here -->
                                <div class="type-fields"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="button" id="addAnother" class="btn btn-outline-primary btn-sm">
                                + Add Another
                            </button>
                            <small class="text-muted ms-2">You can add multiple content blocks. File uploads support multiple files per block.</small>
                        </div>

                        <hr>

                        <!-- Course Price -->
                        <div class="row mt-2">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₦)</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discounted Price (₦)</label>
                                <input type="number" name="discount_price" class="form-control" step="0.01" min="0">
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Content</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JS: dynamic fields, multi-file support, reindexing, simple image preview --}}
    <script>
        (function() {
            let nextIndex = 1; // next block index (0 already used)

            // Map types to HTML generator
            function renderFieldsHtml(idx, type, existing) {
                existing = existing || {};
                // For file-based types we use name="contents[idx][files][]" and multiple attribute
                const multipleFileInput = (accept) => `
                    <div class="mb-3">
                        <label class="form-label">Upload Files <small class="text-muted">(multiple allowed)</small></label>
                        <input type="file" name="contents[${idx}][files][]" class="form-control file-input" accept="${accept}" multiple>
                        <div class="file-preview mt-2 d-flex align-items-center"></div>
                        <div class="file-list mt-1 text-muted small"></div>
                    </div>`;

                switch (type) {
                    case 'text':
                        return `<div class="mb-3">
                                    <label class="form-label">Text Content</label>
                                    <textarea name="contents[${idx}][content]" class="form-control" rows="4" placeholder="Enter text...">${ existing.content || '' }</textarea>
                                </div>`;
                    case 'video':
                        return multipleFileInput('video/*');
                    case 'pdf':
                        return multipleFileInput('.pdf,application/pdf');
                    case 'image':
                        return multipleFileInput('image/*');
                    case 'quiz':
                    case 'assignment':
                        return multipleFileInput('.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                    default:
                        return '';
                }
            }

            // Helper to reindex all blocks so names use contiguous indices (0..N-1)
            function reindexBlocks() {
                const blocks = document.querySelectorAll('#content-wrapper .content-block');
                blocks.forEach((block, newIndex) => {
                    block.dataset.index = newIndex;
                    // title
                    const titleEl = block.querySelector('input[name^="contents[');
                    if (titleEl) titleEl.name = `contents[${newIndex}][title]`;

                    // type
                    const typeEl = block.querySelector('select[name^="contents[');
                    if (typeEl) typeEl.name = `contents[${newIndex}][type]`;

                    // update any inner dynamic fields names (textarea, file inputs, etc.)
                    // textarea or inputs inside .type-fields
                    const tf = block.querySelector('.type-fields');
                    if (tf) {
                        // update all file inputs
                        tf.querySelectorAll('input[type="file"]').forEach(fi => {
                            // make it contents[newIndex][files][]
                            fi.name = `contents[${newIndex}][files][]`;
                        });
                        // update textareas inputs for content/quiz/assignment
                        tf.querySelectorAll('textarea').forEach(ta => {
                            // try to detect whether this should be content/quiz/assignment
                            // if current name contains 'content' or 'quiz' or 'assignment' replace index
                            let current = ta.name || '';
                            if (current.includes('[content]')) {
                                ta.name = `contents[${newIndex}][content]`;
                            } else if (current.includes('[quiz]')) {
                                ta.name = `contents[${newIndex}][quiz]`;
                            } else if (current.includes('[assignment]')) {
                                ta.name = `contents[${newIndex}][assignment]`;
                            } else {
                                // default to content
                                ta.name = `contents[${newIndex}][content]`;
                            }
                        });
                    }
                });
                // update nextIndex
                nextIndex = document.querySelectorAll('#content-wrapper .content-block').length;
            }

            // Add new block
            function addBlock(withValues = null) {
                const wrapper = document.getElementById('content-wrapper');
                const idx = nextIndex;
                const block = document.createElement('div');
                block.className = 'content-block border rounded p-3 mb-3';
                block.dataset.index = idx;

                block.innerHTML = `
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <label class="form-label fw-semibold">Content Title</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-block" aria-label="Remove block">Remove</button>
                    </div>

                    <input type="text" name="contents[${idx}][title]" class="form-control mb-3" placeholder="Title" required>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select name="contents[${idx}][type]" class="form-select type-select" required>
                            <option value="">-- Select --</option>
                            <option value="text">Text</option>
                            <option value="video">Video</option>
                            <option value="pdf">PDF</option>
                            <option value="image">Image</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                        </select>
                    </div>

                    <div class="type-fields"></div>
                `;

                wrapper.appendChild(block);
                nextIndex++;
                return block;
            }

            // Event delegation: handle type selection change
            document.addEventListener('change', function(e) {
                // Type select changed
                if (e.target.matches('.type-select')) {
                    const block = e.target.closest('.content-block');
                    const idx = block.dataset.index;
                    const type = e.target.value;
                    const tf = block.querySelector('.type-fields');
                    tf.innerHTML = renderFieldsHtml(idx, type);

                    // If newly added file inputs exist, wire preview/listeners
                    wireFileInputs(tf);
                }
            });

            // Add Another button
            document.getElementById('addAnother').addEventListener('click', function() {
                addBlock();
                // ensure remove and file wiring remains active
            });

            // Remove block (delegated)
            document.addEventListener('click', function(e) {
                if (e.target.matches('.remove-block')) {
                    const block = e.target.closest('.content-block');
                    block.remove();
                    reindexBlocks();
                }
            });

            // Wire file inputs inside a container (tf) for previews & file list display
            function wireFileInputs(container) {
                container.querySelectorAll('.file-input').forEach(input => {
                    // prevent binding twice
                    if (input.dataset.wired === '1') return;
                    input.dataset.wired = '1';

                    input.addEventListener('change', function() {
                        const previewDiv = input.closest('.type-fields').querySelector('.file-preview');
                        const listDiv = input.closest('.type-fields').querySelector('.file-list');
                        previewDiv.innerHTML = '';
                        listDiv.textContent = '';

                        const files = Array.from(input.files || []);
                        if (files.length === 0) return;

                        const accept = input.getAttribute('accept') || '';
                        // if accept contains 'image' or input type likely images, show thumbnail previews
                        const isImage = accept.includes('image') || files.every(f => f.type.startsWith('image/'));
                        if (isImage) {
                            files.forEach(f => {
                                const reader = new FileReader();
                                reader.onload = (ev) => {
                                    const img = document.createElement('img');
                                    img.src = ev.target.result;
                                    img.alt = f.name;
                                    previewDiv.appendChild(img);
                                };
                                reader.readAsDataURL(f);
                            });
                            // center
                            previewDiv.style.justifyContent = 'center';
                            previewDiv.style.display = 'flex';
                        } else {
                            // list filenames
                            listDiv.innerHTML = files.map(f => `<div>${f.name} <small class="text-muted">(${Math.round(f.size/1024)} KB)</small></div>`).join('');
                        }
                    });
                });
            }

            // wire file inputs for initial block if user switches type
            // also wire any new blocks later via mutation observer
            const observer = new MutationObserver(mutations => {
                for (const mut of mutations) {
                    for (const node of Array.from(mut.addedNodes)) {
                        if (!(node instanceof HTMLElement)) continue;
                        // If new content-block added, just ensure any existing type-select is wired if pre-selected
                        const tf = node.querySelector('.type-fields');
                        if (tf) wireFileInputs(tf);
                    }
                }
            });

            observer.observe(document.getElementById('content-wrapper'), { childList: true });

            // When modal opens, ensure the initial block has data-index and wiring
            const addContentModal = document.getElementById('addContentModal');
            addContentModal.addEventListener('shown.bs.modal', () => {
                // initial content-block may be present; set dataset index 0
                const first = document.querySelector('#content-wrapper .content-block');
                if (first && typeof first.dataset.index === 'undefined') {
                    first.dataset.index = 0;
                }
                // wire file inputs (none likely yet)
                document.querySelectorAll('#content-wrapper .type-fields').forEach(tf => wireFileInputs(tf));
            });

            // On submit: no special JS — the form will post multipart/form-data with multiple files arrays.
            // But we trim empty content blocks client-side (optional):
            document.getElementById('addContentForm').addEventListener('submit', function(e) {
                // Optional: remove blocks with empty title to avoid server noise
                document.querySelectorAll('#content-wrapper .content-block').forEach(block => {
                    const title = block.querySelector(`input[name^="contents["]`)?.value?.trim();
                    if (!title) block.remove();
                });
                reindexBlocks(); // ensure indices contiguous before submit
            });

            // Initialize: wire existing (index 0)
            document.addEventListener('DOMContentLoaded', function() {
                reindexBlocks();
            });

        })();
    </script>

@endsection
