@extends('admin.master_page')
@section('main')
<div class="pagetitle">
    <h1 class="fw-bold">Services</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index">Home</a></li>
            <li class="breadcrumb-item">Forms</li>
            <li class="breadcrumb-item active">Create Service</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" id="serviceForm">
                @csrf

                {{-- Service Info --}}
                <div class="card mb-4 shadow border-0 rounded-3">
                    <div class="card-header bg-gradient-primary text-white fw-bold py-3 rounded-top">
                        <i class="bi bi-info-circle me-2"></i> Service Info
                    </div>
                    <div class="card-body p-4">
                        <div class="form-floating mb-4">
                            <input type="text" name="title" class="form-control rounded-3" id="title"
                                placeholder="Service Title" required>
                            <label for="title">Service Title</label>
                        </div>

                        <input type="hidden" name="slug" id="slug">

                        <div class="form-floating mb-4">
                            <input type="file" name="thumbnail" class="form-control rounded-3" id="thumbnail">
                            <label for="thumbnail">Thumbnail</label>
                        </div>

                        <div class="form-floating mb-2">
                            <textarea name="brief_description" class="form-control rounded-3" id="brief_description"
                                placeholder="Brief Description" style="height: 140px;" required></textarea>
                            <label for="brief_description">Brief Description</label>
                        </div>
                    </div>
                </div>

                {{-- Service Details --}}
                <div class="card mb-4 shadow border-0 rounded-3">
                    <div class="card-header bg-gradient-secondary text-white fw-bold py-3 rounded-top">
                        <i class="bi bi-layout-text-sidebar-reverse me-2"></i> Service Details
                    </div>
                    <div class="card-body p-4" id="serviceDetails" style="max-height: 500px; overflow-y: auto;">

                        {{-- Default first block --}}
                        <div class="content-block mb-4 p-3 border rounded shadow-sm bg-light position-relative" data-index="0">
                            <span class="badge bg-primary position-absolute top-0 start-0 translate-middle rounded-pill">1</span>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-semibold m-0">
                                    <i class="bi bi-ui-checks-grid me-1"></i> Content Type
                                </label>
                                <button type="button" class="btn btn-sm btn-outline-danger remove-block d-none">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                            <select class="form-select content-type rounded-3" name="contents[0][type]" required>
                                <option value="" disabled selected>-- Select Type --</option>
                                <option value="heading">Heading</option>
                                <option value="paragraph">Paragraph</option>
                                <option value="list">List</option>
                                <option value="image">Image</option>
                                <option value="feature">Feature</option>
                            </select>
                            <div class="content-fields mt-3"></div>
                            <input type="hidden" name="contents[0][position]" value="1">
                        </div>

                    </div>

                    <div class="card-footer text-center bg-light">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-4" id="addContent">
                            <i class="bi bi-plus-circle me-1"></i> Add Content Block
                        </button>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-success rounded-pill px-5 me-2 shadow-sm">
                        <i class="bi bi-save me-1"></i> Save Service
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-5 shadow-sm" id="clearForm">
                        <i class="bi bi-trash me-1"></i> Clear Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>


<script>
    // ========================
    // Slug Auto-Generator
    // ========================
    const titleInput = document.getElementById('title');
    const slugInput = document.getElementById('slug');

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function () {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/\s+/g, '-')
                .replace(/[^a-z0-9\-]/g, '');
        });
    }

    // ========================
    // Content Block Handling
    // ========================
    let contentIndex = 0;

    function addContentBlock(type = null, values = null) {
        const container = document.getElementById('serviceDetails');
        if (!container) return;

        const block = document.createElement('div');
        block.classList.add('content-block', 'mb-4', 'p-3', 'border', 'rounded');
        block.setAttribute('data-index', contentIndex);

        block.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label fw-semibold mb-0">Content Type</label>
                <button type="button" class="btn btn-sm btn-outline-danger remove-block">Remove</button>
            </div>
            <select class="form-select content-type" name="contents[${contentIndex}][type]" required>
                <option value="" disabled ${!type ? 'selected' : ''}>-- Select Type --</option>
                <option value="heading" ${type === 'heading' ? 'selected' : ''}>Heading</option>
                <option value="paragraph" ${type === 'paragraph' ? 'selected' : ''}>Paragraph</option>
                <option value="list" ${type === 'list' ? 'selected' : ''}>List</option>
                <option value="image" ${type === 'image' ? 'selected' : ''}>Image</option>
                <option value="feature" ${type === 'feature' ? 'selected' : ''}>Feature</option>
            </select>
            <div class="content-fields mt-3"></div>
            <input type="hidden" name="contents[${contentIndex}][position]" value="${contentIndex + 1}">
        `;

        container.appendChild(block);

        // If restoring, populate fields
        if (type) renderFields(block, type, values);

        contentIndex++;
    }

    // Event: Add content
    document.getElementById('addContent')?.addEventListener('click', () => addContentBlock());

    // Event: Remove content block
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-block')) {
            e.target.closest('.content-block').remove();
            saveForm(); // update LS immediately
        }
    });

    // Render fields by type
    function renderFields(block, type, values = null) {
        const index = block.getAttribute('data-index');
        const fieldsDiv = block.querySelector('.content-fields');
        fieldsDiv.innerHTML = '';

        switch (type) {
            case 'heading':
                fieldsDiv.innerHTML = `
                    <input type="text" class="form-control" 
                           name="contents[${index}][content]" 
                           placeholder="Heading Text" required>`;
                if (values) fieldsDiv.querySelector('input').value = values;
                break;

            case 'paragraph':
                fieldsDiv.innerHTML = `
                    <textarea class="form-control" 
                              name="contents[${index}][content]" 
                              placeholder="Paragraph Text" style="height:100px;" required></textarea>`;
                if (values) fieldsDiv.querySelector('textarea').value = values;
                break;

            case 'list':
                const listGroup = document.createElement('div');
                listGroup.classList.add('list-group');
                if (Array.isArray(values) && values.length) {
                    values.forEach(v => {
                        listGroup.appendChild(makeListInput(index, v));
                    });
                } else {
                    listGroup.appendChild(makeListInput(index));
                }
                const btn = document.createElement('button');
                btn.type = "button";
                btn.classList.add("btn", "btn-sm", "btn-outline-secondary", "add-list-item");
                btn.textContent = "+ Add Item";
                listGroup.appendChild(btn);
                fieldsDiv.appendChild(listGroup);
                break;

            case 'image':
                fieldsDiv.innerHTML = `
                    <input type="file" class="form-control" 
                           name="contents[${index}][content][]" multiple required>`;
                break;

            case 'feature':
                fieldsDiv.innerHTML = `
                    <input type="text" class="form-control mb-2" 
                           name="contents[${index}][content][heading]" 
                           placeholder="Feature Heading" required>
                    <textarea class="form-control" 
                              name="contents[${index}][content][paragraph]" 
                              placeholder="Feature Paragraph" style="height:80px;" required></textarea>`;
                if (values) {
                    fieldsDiv.querySelector(`[name="contents[${index}][content][heading]"]`).value = values.heading || '';
                    fieldsDiv.querySelector(`[name="contents[${index}][content][paragraph]"]`).value = values.paragraph || '';
                }
                break;
        }
    }

    function makeListInput(index, value = '') {
        const input = document.createElement('input');
        input.type = 'text';
        input.classList.add('form-control', 'mb-2');
        input.name = `contents[${index}][content][]`;
        input.value = value;
        input.placeholder = 'List Item';
        return input;
    }

    // Handle content type change
    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('content-type')) {
            const block = e.target.closest('.content-block');
            renderFields(block, e.target.value);
        }
    });

    // Add extra list items
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('add-list-item')) {
            e.preventDefault();
            const block = e.target.closest('.content-block');
            const index = block.getAttribute('data-index');
            const container = e.target.closest('.list-group');
            container.insertBefore(makeListInput(index), e.target);
        }
    });

    // ========================
    // Local Storage Save/Load
    // ========================
    const form = document.getElementById('serviceForm');
    const LS_KEY = "serviceFormData";

    function saveForm() {
        if (!form) return;
        const data = {};
        const fd = new FormData(form);

        for (let [key, value] of fd.entries()) {
            // Skip file inputs
            if (value instanceof File && value.name) continue;

            if (key.includes("[]")) {
                const base = key.replace("[]", "");
                if (!data[base]) data[base] = [];
                data[base].push(value);
            } else {
                data[key] = value;
            }
        }

        localStorage.setItem(LS_KEY, JSON.stringify(data));
    }

    if (form) {
        form.addEventListener('input', saveForm);
        form.addEventListener('change', saveForm);
    }

    // Restore on page load
    window.addEventListener('DOMContentLoaded', () => {
        const saved = localStorage.getItem(LS_KEY);
        if (saved) {
            const data = JSON.parse(saved);

            // Restore basic fields
            Object.keys(data).forEach(key => {
                const el = form.querySelector(`[name="${key}"]`);
                if (el) el.value = data[key];
            });

            // Restore contents
            const contents = {};
            Object.keys(data).forEach(key => {
                let match = key.match(/^contents\[(\d+)]\[type]$/);
                if (match) {
                    const i = match[1];
                    contents[i] = contents[i] || {};
                    contents[i].type = data[key];
                }
                match = key.match(/^contents\[(\d+)]\[content](.*)$/);
                if (match) {
                    const i = match[1];
                    contents[i] = contents[i] || {};
                    contents[i].content = data[key];
                }
            });

            Object.entries(contents).forEach(([i, block]) => {
                addContentBlock(block.type, block.content);
            });
        }
    });

    // Clear form + storage
    document.getElementById('clearForm')?.addEventListener('click', () => {
        if (confirm("Clear all saved data?")) {
            localStorage.removeItem(LS_KEY);
            form.reset();
            document.getElementById('serviceDetails').innerHTML = "";
            contentIndex = 0;
        }
    });
</script>



   @endsection
