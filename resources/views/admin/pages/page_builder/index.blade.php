@extends('admin.master_page')
@section('title', 'Page Builder - Pages')

@section('main')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-0">Page Builder</h3>
                <p class="text-muted mb-0">Create and manage dynamic pages for courses, events, and more</p>
            </div>

            {{-- Fixed id + added data attributes as a safe fallback --}}
            <button class="btn btn-primary" id="openCreateModal" type="button" data-bs-toggle="modal"
                data-bs-target="#pageModal">
                <i class="bi bi-plus-lg me-2"></i>Create Page
            </button>
        </div>

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
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $p)
                                @php
                                    $typeDisplay = 'Standalone';
                                    $typeBadge = 'secondary';
                                    if ($p->pageable_type === \App\Models\Course::class) {
                                        $typeDisplay = 'Course';
                                        $typeBadge = 'info';
                                    } elseif ($p->pageable_type === \App\Models\Event::class) {
                                        $typeDisplay = 'Event';
                                        $typeBadge = 'warning';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $p->title }}</div>
                                        <small class="text-muted">/p/{{ $p->slug }}</small>
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
                                    <td>
                                        <small>{{ $p->updated_at?->diffForHumans() }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a class="btn btn-outline-secondary" href="{{ route('pb.blocks', $p) }}"
                                                title="Manage Blocks">
                                                <i class="bi bi-puzzle"></i>
                                            </a>

                                            @php
                                                $payload = [
                                                    'id' => $p->id,
                                                    'title' => $p->title,
                                                    'slug' => $p->slug,
                                                    'status' => $p->status,
                                                    'owner_type' =>
                                                        $p->pageable_type === \App\Models\Course::class
                                                            ? 'course'
                                                            : ($p->pageable_type === \App\Models\Event::class
                                                                ? 'event'
                                                                : ''),
                                                    'owner_id' => $p->pageable_id,
                                                ];
                                            @endphp

                                            <button class="btn btn-sm btn-outline-primary" type="button"
                                                id="openCreateModal" data-bs-toggle="modal" data-bs-target="#pageModal"
                                                data-action="edit" data-payload='@json($payload)'>
                                                Edit
                                            </button>

                                            <a class="btn btn-outline-dark" href="{{ route('page.show', $p->slug) }}"
                                                target="_blank" title="Preview">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <form class="d-inline" method="post"
                                                action="{{ route('pb.pages.destroy', $p) }}"
                                                onsubmit="return confirm('Delete this page and all its blocks?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger" type="submit" title="Delete">
                                                    <i class="bi bi-trash"></i>
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
                <form id="pageForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="pageModalTitle">Create Page</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-7">
                                <label class="form-label">Title*</label>
                                <input class="form-control" name="title" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Slug</label>
                                <input class="form-control" name="slug">
                                <small class="form-text text-muted">Leave blank to auto-generate</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Status*</label>
                                <select class="form-select" name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <hr>
                                <label class="form-label fw-semibold">Attach To (Optional)</label>
                                <div class="d-flex gap-3 flex-wrap mt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="owner_type" value=""
                                            id="type_standalone" checked>
                                        <label class="form-check-label" for="type_standalone">Standalone Page</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="owner_type" value="course"
                                            id="type_course">
                                        <label class="form-check-label" for="type_course">Course</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="owner_type" value="event"
                                            id="type_event">
                                        <label class="form-check-label" for="type_event">Event</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 owner-select owner-course" style="display:none;">
                                <label class="form-label">Select Course</label>
                                <select class="form-select" name="owner_id_course">
                                    <option value="">Choose course…</option>
                                    @foreach ($courses as $c)
                                        <option value="{{ $c->id }}">{{ $c->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 owner-select owner-event" style="display:none;">
                                <label class="form-label">Select Event</label>
                                <select class="form-select" name="owner_id_event">
                                    <option value="">Choose event…</option>
                                    @foreach ($events as $e)
                                        <option value="{{ $e->id }}">{{ $e->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Save Page</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Page JS --}}
    <script>
        (function() {
            // Ensure Bootstrap bundle (with Popper) is loaded in admin.master_page
            const modalEl = document.getElementById('pageModal');
            const bsModal = new bootstrap.Modal(modalEl);
            const form = document.getElementById('pageForm');
            const titleEl = document.getElementById('pageModalTitle');

            function setOwnerRequired(which) {
                const courseSel = form.querySelector('select[name="owner_id_course"]');
                const eventSel = form.querySelector('select[name="owner_id_event"]');
                if (courseSel) courseSel.required = (which === 'course');
                if (eventSel) eventSel.required = (which === 'event');
            }

            // Radio toggle for owner type
            form.addEventListener('change', (e) => {
                if (e.target.name !== 'owner_type') return;
                const val = e.target.value;
                form.querySelectorAll('.owner-select').forEach(el => el.style.display = 'none');
                if (val === 'course') {
                    form.querySelector('.owner-course')?.style.setProperty('display', 'block');
                }
                if (val === 'event') {
                    form.querySelector('.owner-event')?.style.setProperty('display', 'block');
                }
                setOwnerRequired(val);
            });

            // Open Create Modal (prepares the form before showing)
            document.getElementById('openCreateModal')?.addEventListener('click', () => {
                titleEl.textContent = 'Create Page';
                form.action = @json(route('pb.pages.store'));
                form.querySelector('input[name="_method"]')?.remove();
                form.reset();
                // Default to standalone
                const standalone = form.querySelector('input[name="owner_type"][value=""]');
                if (standalone) standalone.checked = true;
                form.querySelectorAll('.owner-select').forEach(el => el.style.display = 'none');
                setOwnerRequired('');
                bsModal.show();
            });

            // Open Edit Modal
            document.body.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-action="edit"]');
                if (!btn) return;
                const data = JSON.parse(btn.getAttribute('data-payload') || '{}');

                titleEl.textContent = 'Edit Page';
                form.action = @json(route('pb.pages.update', ['page' => '__ID__'])).replace('__ID__', data.id);

                // method spoofing
                let m = form.querySelector('input[name="_method"]');
                if (!m) {
                    m = document.createElement('input');
                    m.type = 'hidden';
                    m.name = '_method';
                    form.appendChild(m);
                }
                m.value = 'PUT';

                // Fill inputs
                form.querySelector('input[name="title"]').value = data.title || '';
                form.querySelector('input[name="slug"]').value = data.slug || '';
                form.querySelector('select[name="status"]').value = data.status || 'draft';

                // Owner radios
                const ownerType = data.owner_type || '';
                form.querySelectorAll('input[name="owner_type"]').forEach(r => r.checked = (r.value ===
                    ownerType));

                // Show relevant selector and set value
                form.querySelectorAll('.owner-select').forEach(el => el.style.display = 'none');
                if (ownerType === 'course') {
                    form.querySelector('.owner-course')?.style.setProperty('display', 'block');
                    const sel = form.querySelector('select[name="owner_id_course"]');
                    if (sel) sel.value = data.owner_id || '';
                } else if (ownerType === 'event') {
                    form.querySelector('.owner-event')?.style.setProperty('display', 'block');
                    const sel = form.querySelector('select[name="owner_id_event"]');
                    if (sel) sel.value = data.owner_id || '';
                }
                setOwnerRequired(ownerType);

                bsModal.show();
            });

            // Normalize owner_id before submit
            form.addEventListener('submit', () => {
                const ownerType = form.querySelector('input[name="owner_type"]:checked')?.value || '';
                form.querySelector('input[name="owner_id"]')?.remove();

                const hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'owner_id';

                if (ownerType === 'course') {
                    hidden.value = form.querySelector('select[name="owner_id_course"]')?.value || '';
                } else if (ownerType === 'event') {
                    hidden.value = form.querySelector('select[name="owner_id_event"]')?.value || '';
                } else {
                    hidden.value = '';
                }

                form.appendChild(hidden);
            });
        })();
    </script>
@endsection
