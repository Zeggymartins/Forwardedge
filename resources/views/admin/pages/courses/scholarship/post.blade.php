@extends('admin.master_page')

@section('title', $item->exists ? 'Edit Scholarship' : 'Create Scholarship')

@section('main')
    @php
        // get old values or model values
        $old = fn($key, $default = null) => old($key, data_get($item, $key, $default));
        $programIncludes = old('program_includes', $item->program_includes ?? []);
        $whoCanApply = old('who_can_apply', $item->who_can_apply ?? []);
        $howToApply = old('how_to_apply', $item->how_to_apply ?? []);
        // normalize to arrays with at least one empty slot
        $ensure = function ($arr) {
            $arr = is_array($arr) ? array_values($arr) : [];
            return count($arr) ? $arr : [''];
        };
        $programIncludes = $ensure($programIncludes);
        $whoCanApply = $ensure($whoCanApply);
        $howToApply = $ensure($howToApply);
        $draftKey = 'scholarship_form_' . ($item->exists ? 'edit_' . $item->getKey() : 'new');
    @endphp

    <style>
        /* ---- subtle admin polish ---- */
        .section-header {
            font-weight: 700;
            letter-spacing: .3px;
            margin: 28px 0 10px;
            font-size: 1.05rem;
            text-transform: uppercase;
            color: #6c757d
        }

        .card.modern {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        }

        .tag-help {
            font-size: .85rem;
            color: #6c757d;
        }

        .form-hint {
            color: #6c757d;
            font-size: .9rem;
        }

        .list-chip {
            background: #f8f9fa;
            border: 1px dashed #e9ecef;
            border-radius: 12px;
            padding: 12px;
        }

        .list-row {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .list-row input {
            flex: 1;
        }

        .btn-icon-only {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .preview-img {
            max-height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #eef1f4;
        }

        .sticky-actions {
            position: sticky;
            bottom: 0;
            background: #fff;
            border-top: 1px solid #eee;
            padding: 12px;
            z-index: 5;
        }

        .badge-soft {
            background: #f1f5ff;
            color: #4460f1;
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 600;
        }
    </style>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="fw-bold mb-1">{{ $item->exists ? 'Edit Scholarship' : 'Create Scholarship' }}</h1>
                <div class="tag-help">Autosaves as draft in your browser <span class="badge-soft">LocalStorage</span></div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back</a>
                <button type="button" id="btnSaveDraft" class="btn btn-outline-primary"><i class="bi bi-download"></i> Save
                    draft</button>
                <button type="button" id="btnClearDraft" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i>
                    Clear draft</button>
            </div>
        </div>

        <form id="scholarshipForm" class="card modern p-4" method="POST" enctype="multipart/form-data"
            action="{{ $item->exists ? route('scholarships.update', $item) : route('scholarships.store') }}">
            @csrf
            @if ($item->exists)
                @method('PUT')
            @endif

            {{-- ========== Essentials ========== --}}
            <div class="section-header">Basics</div>
            <div class="row g-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Hero Headline</label>
                    <input type="text" class="form-control form-control-lg" name="hero_headline"
                        placeholder="e.g. Cybersecurity Bootcamp Scholarship 5.0" value="{{ $old('hero_headline') }}">
                    <div class="form-text">Main banner headline</div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Slug <span class="text-muted">(auto from headline;
                            editable)</span></label>
                    <input type="text" class="form-control" name="slug" required
                        placeholder="cybersecurity-bootcamp-5" value="{{ $old('slug') }}">
                </div>
                {{-- Course selection --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Course</label>
                    <select name="course_id" class="form-select" required>
                        <option value="">Select a course…</option>
                        @foreach ($courses as $id => $title)
                            <option value="{{ $id }}" @selected(old('course_id', $item->course_id) == $id)>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                    @if ($item->exists && $item->course)
                        <div class="form-text">
                            Linked: <a href="{{ route('course.show', $item->course->slug ?? $item->course->id) }}"
                                target="_blank">
                                {{ $item->course->title ?? 'Course' }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        @foreach (['draft', 'published', 'archived'] as $s)
                            <option value="{{ $s }}" @selected($old('status', 'draft') === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Opens</label>
                    <input type="date" class="form-control" name="opens_at"
                        value="{{ old('opens_at', optional($item->opens_at)->toDateString()) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Closes</label>
                    <input type="date" class="form-control" name="closes_at"
                        value="{{ old('closes_at', optional($item->closes_at)->toDateString()) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Hero Subtext</label>
                    <textarea name="hero_subtext" rows="3" class="form-control"
                        placeholder="Short supporting line under the headline">{{ $old('hero_subtext') }}</textarea>
                </div>
            </div>

            {{-- ========== Media / CTA ========== --}}
            <div class="section-header mt-4">Media & CTA</div>
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hero Image</label>
                    <input type="file" name="hero_image" class="form-control" id="heroImageInput" accept="image/*">
                    <div class="form-text">Recommended 1600×900 (16:9)</div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-3">
                        @if ($item->hero_image)
                            <img src="{{ asset('storage/' . $item->hero_image) }}" alt="current" class="preview-img"
                                id="heroImagePreview">
                        @else
                            <img src="" alt="" class="preview-img d-none" id="heroImagePreview">
                        @endif
                        <span class="text-muted small">Preview</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">CTA Text</label>
                    <input type="text" name="cta_text" class="form-control" placeholder="Apply Now"
                        value="{{ $old('cta_text') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">CTA URL</label>
                    <input type="url" name="cta_url" class="form-control" placeholder="https://example.com/apply"
                        value="{{ $old('cta_url') }}">
                </div>
            </div>

            {{-- ========== About ========== --}}
            <div class="section-header mt-4">About</div>
            <div class="row g-3">
                <div class="col-12">
                    <textarea name="about" class="form-control" rows="4" placeholder="Short overview for the scholarship">{{ $old('about') }}</textarea>
                </div>
            </div>

            {{-- ========== Dynamic Lists ========== --}}
            <div class="section-header mt-4">Program Includes</div>
            <div id="list-program_includes" class="list-chip">
                @foreach ($programIncludes as $i => $val)
                    <div class="list-row">
                        <input type="text" class="form-control" name="program_includes[]"
                            value="{{ $val }}" placeholder="e.g. Full tuition coverage">
                        <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                @endforeach
                <button class="btn btn-outline-primary btn-sm mt-1 add-row" type="button"
                    data-target="program_includes"><i class="bi bi-plus-lg"></i> Add item</button>
            </div>

            <div class="section-header mt-4">Who Can Apply?</div>
            <div id="list-who_can_apply" class="list-chip">
                @foreach ($whoCanApply as $i => $val)
                    <div class="list-row">
                        <input type="text" class="form-control" name="who_can_apply[]" value="{{ $val }}"
                            placeholder="e.g. Nigerian residents aged 18–35">
                        <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                @endforeach
                <button class="btn btn-outline-primary btn-sm mt-1 add-row" type="button" data-target="who_can_apply"><i
                        class="bi bi-plus-lg"></i> Add item</button>
            </div>

            <div class="section-header mt-4">How To Apply (steps)</div>
            <div id="list-how_to_apply" class="list-chip">
                @foreach ($howToApply as $i => $val)
                    <div class="list-row">
                        <input type="text" class="form-control" name="how_to_apply[]" value="{{ $val }}"
                            placeholder="e.g. Complete online application form">
                        <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove"><i
                                class="bi bi-x-lg"></i></button>
                    </div>
                @endforeach
                <button class="btn btn-outline-primary btn-sm mt-1 add-row" type="button" data-target="how_to_apply"><i
                        class="bi bi-plus-lg"></i> Add step</button>
            </div>

            {{-- ========== Important / Closing ========== --}}
            <div class="section-header mt-4">Important & Closing</div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-semibold">Important Note</label>
                    <textarea name="important_note" class="form-control" rows="3" placeholder="Any terms, selection notes, etc.">{{ $old('important_note') }}</textarea>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Closing Headline</label>
                    <input type="text" name="closing_headline" class="form-control"
                        placeholder="Ready to Transform Your Career?" value="{{ $old('closing_headline') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Closing CTA Text</label>
                    <input type="text" name="closing_cta_text" class="form-control" placeholder="Apply Now"
                        value="{{ $old('closing_cta_text') }}">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Closing CTA URL</label>
                    <input type="url" name="closing_cta_url" class="form-control"
                        placeholder="https://example.com/apply" value="{{ $old('closing_cta_url') }}">
                </div>
            </div>

            {{-- Submit --}}
            <div class="sticky-actions d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">Draft key: <code>{{ $draftKey }}</code></div>
                <div class="d-flex gap-2">
                    <button type="button" id="btnSaveDraft2" class="btn btn-outline-primary"><i
                            class="bi bi-download"></i> Save draft</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check2-circle"></i>
                        {{ $item->exists ? 'Update Scholarship' : 'Create Scholarship' }}</button>
                </div>
            </div>
        </form>
    </div>

    {{-- ======= Hidden template for new rows ======= --}}
    <template id="tpl-row">
        <div class="list-row">
            <input type="text" class="form-control" name="__NAME__[]" placeholder="Enter item">
            <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove"><i
                    class="bi bi-x-lg"></i></button>
        </div>
    </template>

    <script>
        (function() {
            const form = document.getElementById('scholarshipForm');
            const tpl = document.getElementById('tpl-row').innerHTML;
            const draftKey = @json($draftKey);

            // Add row handler
            document.querySelectorAll('.add-row').forEach(btn => {
                btn.addEventListener('click', () => {
                    const name = btn.dataset.target; // program_includes / who_can_apply / how_to_apply
                    const host = document.getElementById('list-' + name);
                    const html = tpl.replace('__NAME__', name);
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    host.insertBefore(wrapper.firstChild, btn);
                    autosave(); // save immediately
                });
            });

            // Remove row (delegated)
            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row')) {
                    const row = e.target.closest('.remove-row').parentElement;
                    const host = row.parentElement;
                    row.remove();
                    // keep at least one empty input
                    const hasInputs = host.querySelectorAll('input[name$="[]"]').length;
                    if (!hasInputs) {
                        const addBtn = host.querySelector('.add-row');
                        const name = addBtn.dataset.target;
                        const html = tpl.replace('__NAME__', name);
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = html.trim();
                        host.insertBefore(wrapper.firstChild, addBtn);
                    }
                    autosave();
                }
            });

            // Image preview
            const heroInput = document.getElementById('heroImageInput');
            const heroPrev = document.getElementById('heroImagePreview');
            if (heroInput) {
                heroInput.addEventListener('change', () => {
                    const f = heroInput.files?.[0];
                    if (!f) return;
                    const reader = new FileReader();
                    reader.onload = e => {
                        heroPrev.src = e.target.result;
                        heroPrev.classList.remove('d-none');
                    };
                    reader.readAsDataURL(f);
                });
            }

            // Slug auto from headline (only when slug untouched by user)
            const headline = form.querySelector('input[name="hero_headline"]');
            const slug = form.querySelector('input[name="slug"]');
            let slugTouched = !!slug.value;
            slug.addEventListener('input', () => slugTouched = true);

            function slugify(str) {
                return (str || '').toString().toLowerCase()
                    .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').substring(0, 120);
            }
            if (headline) {
                headline.addEventListener('input', () => {
                    if (!slugTouched) {
                        slug.value = slugify(headline.value);
                        autosave();
                    }
                });
            }

            // -------- LocalStorage autosave --------
            function serializeForm() {
                const data = {};
                // simple inputs
                form.querySelectorAll('input[name]:not([type="file"]), textarea[name], select[name]').forEach(el => {
                    const name = el.name;
                    if (name.endsWith('[]')) return; // arrays handled below
                    if (el.type === 'checkbox') {
                        data[name] = el.checked ? (el.value || true) : '';
                    } else {
                        data[name] = el.value ?? '';
                    }
                });
                // arrays
                ['program_includes[]', 'who_can_apply[]', 'how_to_apply[]'].forEach(arrName => {
                    const values = [];
                    form.querySelectorAll(`input[name="${arrName}"]`).forEach(inp => {
                        const v = (inp.value || '').trim();
                        if (v !== '') values.push(v);
                    });
                    data[arrName] = values;
                });
                return data;
            }

            function hydrateForm(data) {
                if (!data) return;
                // simple fields
                Object.entries(data).forEach(([name, val]) => {
                    if (['program_includes[]', 'who_can_apply[]', 'how_to_apply[]'].includes(name)) return;
                    const el = form.querySelector(`[name="${name}"]`);
                    if (!el) return;
                    if (el.tagName === 'SELECT') el.value = val ?? '';
                    else if (el.type === 'checkbox') el.checked = !!val;
                    else el.value = val ?? '';
                });

                // arrays
                function fillList(key, hostId) {
                    const host = document.getElementById(hostId);
                    const addBtn = host.querySelector('.add-row');
                    // remove existing rows
                    host.querySelectorAll('.list-row').forEach(r => r.remove());
                    const items = Array.isArray(data[key]) && data[key].length ? data[key] : [''];
                    items.forEach(v => {
                        const html = tpl.replace('__NAME__', key.replace('[]', ''));
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = html.trim();
                        const row = wrapper.firstChild;
                        row.querySelector('input').value = v;
                        host.insertBefore(row, addBtn);
                    });
                }
                fillList('program_includes[]', 'list-program_includes');
                fillList('who_can_apply[]', 'list-who_can_apply');
                fillList('how_to_apply[]', 'list-how_to_apply');
            }

            function autosave() {
                const payload = serializeForm();
                localStorage.setItem(draftKey, JSON.stringify(payload));
            }

            // Save when typing / changing (skip file inputs)
            form.addEventListener('input', (e) => {
                if (e.target.type === 'file') return;
                autosave();
            });
            form.addEventListener('change', (e) => {
                if (e.target.type === 'file') return;
                autosave();
            });

            // Manual draft buttons
            document.getElementById('btnSaveDraft')?.addEventListener('click', () => {
                autosave();
                alert('Draft saved locally.');
            });
            document.getElementById('btnSaveDraft2')?.addEventListener('click', () => {
                autosave();
                alert('Draft saved locally.');
            });
            document.getElementById('btnClearDraft')?.addEventListener('click', () => {
                localStorage.removeItem(draftKey);
                alert('Local draft cleared.');
            });

            // Restore on load (only if there’s a stored draft)
            try {
                const raw = localStorage.getItem(draftKey);
                if (raw) {
                    const data = JSON.parse(raw);
                    hydrateForm(data);
                }
            } catch (e) {
                console.warn('Draft restore failed', e);
            }

        })();
    </script>
@endsection
