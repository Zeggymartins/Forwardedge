@extends('admin.master_page')

@section('title', $item->exists ? 'Edit Scholarship' : 'Create Scholarship')

@section('main')
@php
    // helpers for defaults/old values
    $old = fn($key, $default = null) => old($key, data_get($item, $key, $default));

    $programIncludes = old('program_includes', $item->program_includes ?? []);
    $whoCanApply     = old('who_can_apply',    $item->who_can_apply    ?? []);
    $howToApply      = old('how_to_apply',     $item->how_to_apply     ?? []);

    $ensure = function ($arr) {
        $arr = is_array($arr) ? array_values($arr) : [];
        return count($arr) ? $arr : [''];
    };

    $programIncludes = $ensure($programIncludes);
    $whoCanApply     = $ensure($whoCanApply);
    $howToApply      = $ensure($howToApply);
@endphp

<style>
    .section-header{font-weight:700;letter-spacing:.3px;margin:28px 0 10px;font-size:1.05rem;text-transform:uppercase;color:#6c757d}
    .card.modern{border:0;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.06)}
    .list-chip{background:#f8f9fa;border:1px dashed #e9ecef;border-radius:12px;padding:12px}
    .list-row{display:flex;gap:10px;align-items:center;margin-bottom:10px}
    .list-row input{flex:1}
    .btn-icon-only{width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center}
    .preview-img{max-height:140px;border-radius:8px;object-fit:cover;border:1px solid #eef1f4}
    .sticky-actions{position:sticky;bottom:0;background:#fff;border-top:1px solid #eee;padding:12px;z-index:5}
    .badge-soft{background:#f1f5ff;color:#4460f1;border-radius:999px;padding:.35rem .6rem;font-weight:600}
</style>

<div class="container py-4">
    {{-- Validation --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Validation Errors:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="fw-bold mb-1">{{ $item->exists ? 'Edit Scholarship' : 'Create Scholarship' }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('scholarships.index', [], false) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <form id="scholarshipForm"
          class="card modern p-4"
          method="POST"
          enctype="multipart/form-data"
          action="{{ $item->exists
                        ? route('scholarships.update', $item, false)
                        : route('scholarships.store', [], false)
                  }}">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif

        {{-- ========== Basics ========== --}}
        <div class="section-header">Basics</div>
        <div class="row g-3">
            <div class="col-md-7">
                <label class="form-label fw-semibold">Hero Headline</label>
                <input type="text"
                       class="form-control form-control-lg @error('headline') is-invalid @enderror"
                       name="headline"
                       placeholder="e.g. Cybersecurity Bootcamp Scholarship 5.0"
                       value="{{ $old('headline') }}">
                @error('headline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Main banner headline</div>
            </div>

            <div class="col-md-5">
                <label class="form-label fw-semibold">Slug <span class="text-muted">(auto from headline; editable)</span></label>
                <input type="text"
                       class="form-control @error('slug') is-invalid @enderror"
                       name="slug" required
                       placeholder="cybersecurity-bootcamp-5"
                       value="{{ $old('slug') }}">
                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Course --}}
            <div class="col-md-6">
                <label class="form-label fw-semibold">Course</label>
                <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                    <option value="">Select a course…</option>
                    @foreach ($courses as $id => $title)
                        <option value="{{ $id }}" @selected(old('course_id', $item->course_id) == $id)>{{ $title }}</option>
                    @endforeach
                </select>
                @error('course_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                @if ($item->exists && $item->course)
                    <div class="form-text">
                        Linked:
                        <a href="{{ route('course.show', $item->course->slug ?? $item->course->id, false) }}" target="_blank">
                            {{ $item->course->title ?? 'Course' }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Status --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    @foreach (['draft','published','archived'] as $s)
                        <option value="{{ $s }}" @selected($old('status', 'draft') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Opens / Closes --}}
            <div class="col-md-4">
                <label class="form-label fw-semibold">Opens</label>
                <input type="date"
                       class="form-control @error('opens_at') is-invalid @enderror"
                       name="opens_at"
                       value="{{ old('opens_at', optional($item->opens_at)->toDateString()) }}">
                @error('opens_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Closes</label>
                <input type="date"
                       class="form-control @error('closes_at') is-invalid @enderror"
                       name="closes_at"
                       value="{{ old('closes_at', optional($item->closes_at)->toDateString()) }}">
                @error('closes_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Subtext --}}
            <div class="col-12">
                <label class="form-label fw-semibold">Hero Subtext</label>
                <textarea name="subtext" rows="3"
                          class="form-control @error('subtext') is-invalid @enderror"
                          placeholder="Short supporting line under the headline">{{ $old('subtext') }}</textarea>
                @error('subtext') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ========== Media & CTA ========== --}}
        <div class="section-header mt-4">Media & CTA</div>
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Hero Image</label>
                <input type="file"
                       name="hero_image"
                       id="heroImageInput"
                       accept="image/*"
                       class="form-control @error('hero_image') is-invalid @enderror">
                @error('hero_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Recommended 1600×900 (16:9), Max 4MB</div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    @if ($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" alt="current" class="preview-img" id="heroImagePreview">
                    @else
                        <img src="" alt="" class="preview-img d-none" id="heroImagePreview">
                    @endif
                    <span class="text-muted small">Preview</span>
                </div>
            </div>
        </div>

        {{-- ========== About ========== --}}
        <div class="section-header mt-4">About</div>
        <div class="row g-3">
            <div class="col-12">
                <textarea name="about" class="form-control @error('about') is-invalid @enderror" rows="4"
                          placeholder="Short overview for the scholarship">{{ $old('about') }}</textarea>
                @error('about') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- ========== Dynamic Lists ========== --}}
        <div class="section-header mt-4">Program Includes</div>
        <div id="list-program_includes" class="list-chip">
            @foreach ($programIncludes as $i => $val)
                <div class="list-row">
                    <input type="text" class="form-control" name="program_includes[]" value="{{ $val }}" placeholder="e.g. Full tuition coverage">
                    <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endforeach
            <button class="btn btn-outline-primary btn-sm mt-1 add-row" type="button" data-target="program_includes">
                <i class="bi bi-plus-lg"></i> Add item
            </button>
        </div>

        <div class="section-header mt-4">Who Can Apply?</div>
        <div id="list-who_can_apply" class="list-chip">
            @foreach ($whoCanApply as $i => $val)
                <div class="list-row">
                    <input type="text" class="form-control" name="who_can_apply[]" value="{{ $val }}" placeholder="e.g. Nigerian residents aged 18–35">
                    <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endforeach
            <button class="btn btn-outline-primary btn-sm mt-1 add-row" type="button" data-target="who_can_apply">
                <i class="bi bi-plus-lg"></i> Add item
            </button>
        </div>

        <div class="section-header mt-4">How To Apply (steps)</div>
        <div id="list-how_to_apply" class="list-chip">
            @foreach ($howToApply as $i => $val)
                <div class="list-row">
                    <input type="text" class="form-control" name="how_to_apply[]" value="{{ $val }}" placeholder="e.g. Complete online application form">
                    <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            @endforeach
            <button class="btn btn-outline-primary btn-sm mt-1 add-row" type="button" data-target="how_to_apply">
                <i class="bi bi-plus-lg"></i> Add step
            </button>
        </div>

        {{-- ========== Important & Closing ========== --}}
        <div class="section-header mt-4">Important & Closing</div>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-semibold">Important Note</label>
                <textarea name="important_note" class="form-control @error('important_note') is-invalid @enderror"
                          rows="3" placeholder="Any terms, selection notes, etc.">{{ $old('important_note') }}</textarea>
                @error('important_note') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Closing Headline</label>
                <input type="text" name="closing_headline"
                       class="form-control @error('closing_headline') is-invalid @enderror"
                       placeholder="Ready to Transform Your Career?"
                       value="{{ $old('closing_headline') }}">
                @error('closing_headline') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">Closing CTA Text</label>
                <input type="text" name="closing_cta_text"
                       class="form-control @error('closing_cta_text') is-invalid @enderror"
                       placeholder="Apply Now"
                       value="{{ $old('closing_cta_text') }}">
                @error('closing_cta_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-8">
                <label class="form-label fw-semibold">Closing CTA URL</label>
                <input type="url" name="closing_cta_url"
                       class="form-control @error('closing_cta_url') is-invalid @enderror"
                       placeholder="https://example.com/apply"
                       value="{{ $old('closing_cta_url') }}">
                @error('closing_cta_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="sticky-actions d-flex justify-content-end align-items-center mt-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle"></i>
                    {{ $item->exists ? 'Update Scholarship' : 'Create Scholarship' }}
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ======= Hidden template for new rows ======= --}}
<template id="tpl-row">
    <div class="list-row">
        <input type="text" class="form-control" name="__NAME__[]" placeholder="Enter item">
        <button class="btn btn-outline-danger btn-icon-only remove-row" type="button" title="Remove">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
</template>

<script>
    (function() {
        const form = document.getElementById('scholarshipForm');
        const tpl = document.getElementById('tpl-row').innerHTML;

        // Add row handler
        document.querySelectorAll('.add-row').forEach(btn => {
            btn.addEventListener('click', () => {
                const name = btn.dataset.target;
                const host = document.getElementById('list-' + name);
                const html = tpl.replace('__NAME__', name);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                host.insertBefore(wrapper.firstChild, btn);
            });
        });

        // Remove row (delegated)
        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('.remove-row').parentElement;
                const host = row.parentElement;
                row.remove();
                const hasInputs = host.querySelectorAll('input[name$="[]"]').length;
                if (!hasInputs) {
                    const addBtn = host.querySelector('.add-row');
                    const name = addBtn.dataset.target;
                    const html = tpl.replace('__NAME__', name);
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = html.trim();
                    host.insertBefore(wrapper.firstChild, addBtn);
                }
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

        // Slug auto from headline
        const headline = form.querySelector('input[name="headline"]');
        const slug = form.querySelector('input[name="slug"]');
        let slugTouched = !!slug.value;
        slug.addEventListener('input', () => slugTouched = true);

        function slugify(str) {
            return (str || '').toString().toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .substring(0, 120);
        }

        if (headline) {
            headline.addEventListener('input', () => {
                if (!slugTouched) {
                    slug.value = slugify(headline.value);
                }
            });
        }
    })();
</script>
@endsection
