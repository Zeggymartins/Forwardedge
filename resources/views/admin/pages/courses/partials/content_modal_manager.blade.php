@php
    $courseOptions = $courseOptions ?? collect();
    $typeOptions = $typeOptions ?? [
        'text' => 'Text',
        'video' => 'Video',
        'pdf' => 'PDF',
        'image' => 'Image',
        'quiz' => 'Quiz',
        'assignment' => 'Assignment',
    ];
@endphp

<!-- ================================================================
     ADD CONTENT MODAL
     ================================================================ -->
<div class="modal fade" id="addContentModal" tabindex="-1" aria-labelledby="addContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('admin.course_contents.store') }}" method="POST" enctype="multipart/form-data" id="addContentForm">
            @csrf
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addContentModalLabel">Add Course Content</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <!-- Course & Title ─────────────────────────── -->
                    <div class="col-md-6">
                        <label class="form-label">Course <span class="text-danger">*</span></label>
                        <select name="course_id" class="form-select" id="addCourseSelect" required>
                            <option value="">Select Course</option>
                            @foreach ($courseOptions as $courseOption)
                                <option value="{{ $courseOption->id }}">{{ $courseOption->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Lesson title" required>
                    </div>

                    <!-- Delivery Mode Switch ────────────────────── -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">How is content delivered? <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group" id="addDeliveryModeGroup">
                            <input type="radio" class="btn-check" name="delivery_mode" id="addModeLocal"    value="local"    checked autocomplete="off">
                            <label class="btn btn-outline-secondary" for="addModeLocal"><i class="bi bi-upload me-1"></i>Local Upload</label>

                            <input type="radio" class="btn-check" name="delivery_mode" id="addModeDrive"    value="drive"          autocomplete="off">
                            <label class="btn btn-outline-secondary" for="addModeDrive"><i class="bi bi-google me-1"></i>Google Drive</label>

                            <input type="radio" class="btn-check" name="delivery_mode" id="addModeExternal" value="external"       autocomplete="off">
                            <label class="btn btn-outline-secondary" for="addModeExternal"><i class="bi bi-box-arrow-up-right me-1"></i>External URL</label>
                        </div>
                        <small class="text-muted mt-1 d-block" id="addModeHint">Upload files or write text directly on the platform.</small>
                    </div>

                    <!-- LOCAL PANEL ─────────────────────────────── -->
                    <div id="addLocalPanel" class="col-12">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" id="addContentTypeSelect" class="form-select">
                                    @foreach ($typeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3" id="addTextField">
                            <label class="form-label">Body</label>
                            <textarea name="content" rows="5" class="form-control" placeholder="Write the lesson content here…"></textarea>
                        </div>

                        <div class="mt-3 d-none" id="addFileField">
                            <label class="form-label">Upload File <span class="text-danger">*</span></label>
                            <input type="file" name="file" class="form-control" id="addFileInput">
                            <small class="text-muted d-block mt-1" id="addFileHint">Select a type to see the allowed file formats.</small>
                        </div>
                    </div>

                    <!-- DRIVE PANEL ─────────────────────────────── -->
                    <div id="addDrivePanel" class="col-12 d-none">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Google Drive Folder ID <span class="text-danger">*</span></label>
                                    <input type="text" name="drive_folder_id" class="form-control" id="addDriveFolder" placeholder="e.g. 1aBcD2EfGh…">
                                    <small class="text-muted">Last segment of the folder URL in Google Drive.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Share Link (optional)</label>
                                    <input type="url" name="drive_share_link" class="form-control" id="addDriveShare" placeholder="https://drive.google.com/…">
                                    <small class="text-muted">Direct link included in order confirmation emails.</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="addAutoGrant" name="auto_grant_access" value="1">
                                        <label for="addAutoGrant" class="form-check-label">Auto-grant Drive access when a learner pays</label>
                                    </div>
                                    <small class="text-muted">Requires Google Drive API credentials on the server.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EXTERNAL PANEL ──────────────────────────── -->
                    <div id="addExternalPanel" class="col-12 d-none">
                        <div class="border rounded-3 p-3 bg-light">
                            <label class="form-label">External URL <span class="text-danger">*</span></label>
                            <input type="url" name="external_url" class="form-control" id="addExternalUrl" placeholder="https://example.com/resource">
                            <small class="text-muted">Learners will be redirected to this link after accessing the content.</small>
                        </div>
                    </div>
                </div>

                <!-- Price ────────────────────────────────────────── -->
                <div class="row g-3 mt-2">
                    <div class="col-md-6">
                        <label class="form-label">Module Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="e.g. 45 000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Discount Price (₦)</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01" min="0" placeholder="Optional discount">
                    </div>
                </div>

                <!-- Phases & Topics ──────────────────────────────── -->
                <div class="mt-4 phase-builder p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">Phases & Topics</h6>
                            <small class="text-muted">Break the lesson into bite-sized parts.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addContentPhase">+ Add Phase</button>
                    </div>
                    <div id="contentPhasesContainer" class="d-flex flex-column gap-3">
                        <p class="text-muted mb-0" id="contentPhasesHint">No phases added yet.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Content</button>
            </div>
        </form>
    </div>
</div>

<!-- ================================================================
     EDIT CONTENT MODAL
     ================================================================ -->
<div class="modal fade" id="editContentModal" tabindex="-1" aria-labelledby="editContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" enctype="multipart/form-data" id="editContentForm">
            @csrf @method('PUT')
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="editContentModalLabel">Edit Content Block</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <!-- Title ──────────────────────────────────── -->
                    <div class="col-12">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editContentTitle" class="form-control" required>
                    </div>

                    <!-- Delivery Mode Switch ────────────────────── -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">How is content delivered? <span class="text-danger">*</span></label>
                        <div class="btn-group w-100" role="group" id="editDeliveryModeGroup">
                            <input type="radio" class="btn-check" name="delivery_mode" id="editModeLocal"    value="local"          autocomplete="off">
                            <label class="btn btn-outline-secondary" for="editModeLocal"><i class="bi bi-upload me-1"></i>Local Upload</label>

                            <input type="radio" class="btn-check" name="delivery_mode" id="editModeDrive"    value="drive"          autocomplete="off">
                            <label class="btn btn-outline-secondary" for="editModeDrive"><i class="bi bi-google me-1"></i>Google Drive</label>

                            <input type="radio" class="btn-check" name="delivery_mode" id="editModeExternal" value="external"       autocomplete="off">
                            <label class="btn btn-outline-secondary" for="editModeExternal"><i class="bi bi-box-arrow-up-right me-1"></i>External URL</label>
                        </div>
                        <small class="text-muted mt-1 d-block" id="editModeHint">Upload files or write text directly on the platform.</small>
                    </div>

                    <!-- LOCAL PANEL ─────────────────────────────── -->
                    <div id="editLocalPanel" class="col-12">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" id="editContentType" class="form-select">
                                    @foreach ($typeOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-3" id="editTextField">
                            <label class="form-label">Body</label>
                            <textarea name="content" rows="5" class="form-control" id="editContentBody" placeholder="Update the lesson body"></textarea>
                        </div>

                        <div class="mt-3 d-none" id="editFileField">
                            <label class="form-label">Upload File</label>
                            <input type="file" name="file" class="form-control" id="editContentFile">
                            <small class="text-muted d-block mt-1" id="editFileHint">Upload a new file for this content.</small>
                            <small class="d-block mt-1 text-muted" id="editCurrentFile"></small>
                        </div>
                    </div>

                    <!-- DRIVE PANEL ─────────────────────────────── -->
                    <div id="editDrivePanel" class="col-12 d-none">
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Google Drive Folder ID <span class="text-danger">*</span></label>
                                    <input type="text" name="drive_folder_id" id="editDriveFolder" class="form-control" placeholder="e.g. 1aBcD2EfGh…">
                                    <small class="text-muted">Last segment of the folder URL in Google Drive.</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Share Link (optional)</label>
                                    <input type="url" name="drive_share_link" id="editDriveShare" class="form-control" placeholder="https://drive.google.com/…">
                                    <small class="text-muted">Direct link included in order confirmation emails.</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="editAutoGrant" name="auto_grant_access" value="1">
                                        <label for="editAutoGrant" class="form-check-label">Auto-grant Drive access when a learner pays</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- EXTERNAL PANEL ──────────────────────────── -->
                    <div id="editExternalPanel" class="col-12 d-none">
                        <div class="border rounded-3 p-3 bg-light">
                            <label class="form-label">External URL <span class="text-danger">*</span></label>
                            <input type="url" name="external_url" id="editExternalUrl" class="form-control" placeholder="https://example.com/resource">
                            <small class="text-muted">Learners will be redirected to this link after accessing the content.</small>
                        </div>
                    </div>

                    <!-- Price ────────────────────────────────────── -->
                    <div class="col-md-6">
                        <label class="form-label">Module Price (₦) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="editContentPrice" class="form-control" step="0.01" min="0" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Discount Price (₦)</label>
                        <input type="number" name="discount_price" id="editContentDiscount" class="form-control" step="0.01" min="0">
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-secondary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- Phase / Topic templates (unchanged) ────────────────────────── -->
<template id="contentPhaseTemplate">
    <div class="content-phase-block border rounded-3 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
            <div class="flex-grow-1">
                <label class="form-label mb-1">Phase Title</label>
                <input type="text" class="form-control" name="phases[__INDEX__][title]" placeholder="e.g. Getting Started" required>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger remove-content-phase">Remove</button>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-4">
                <label class="form-label">Order</label>
                <input type="number" class="form-control" min="1" name="phases[__INDEX__][order]" value="__ORDER__">
            </div>
            <div class="col-md-4">
                <label class="form-label">Duration (mins)</label>
                <input type="number" class="form-control" min="0" name="phases[__INDEX__][duration]">
            </div>
            <div class="col-md-4">
                <label class="form-label">Image</label>
                <input type="file" class="form-control" name="phases[__INDEX__][image]">
            </div>
        </div>
        <div class="mt-2">
            <label class="form-label">Description</label>
            <textarea class="form-control" rows="2" name="phases[__INDEX__][content]" placeholder="Optional details"></textarea>
        </div>
        <div class="mt-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Topics</span>
                <button type="button" class="btn btn-sm btn-outline-secondary add-content-topic" data-phase="__INDEX__">+ Add Topic</button>
            </div>
            <div class="topics-list mt-2" data-phase="__INDEX__"></div>
        </div>
    </div>
</template>

<template id="contentTopicTemplate">
    <div class="content-topic-block border rounded-3 p-3 mb-2">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge bg-primary-subtle text-primary content-topic-label">Topic __ORDER__</span>
            <button type="button" class="btn btn-outline-danger btn-sm remove-content-topic">Remove</button>
        </div>
        <div class="mb-2">
            <label class="form-label">Title</label>
            <input type="text" class="form-control" name="phases[__PHASE__][topics][__TOPIC__][title]" placeholder="Topic title" required>
        </div>
        <div>
            <label class="form-label">Notes</label>
            <textarea class="form-control" rows="2" name="phases[__PHASE__][topics][__TOPIC__][content]" placeholder="Optional notes"></textarea>
        </div>
        <input type="hidden" name="phases[__PHASE__][topics][__TOPIC__][order]" value="__ORDER__" class="topic-order-input">
    </div>
</template>

<!-- ================================================================
     JAVASCRIPT
     ================================================================ -->
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── shared helpers ────────────────────────────────────────────
    const modeHints = {
        local:    'Upload files or write text directly on the platform.',
        drive:    'Link a Google Drive folder — learners get folder access after payment.',
        external: 'Paste a URL — learners will be redirected to this resource.'
    };

    const acceptMap = {
        video:      'video/*',
        pdf:        '.pdf,application/pdf',
        image:      'image/*',
        quiz:       '.pdf,.doc,.docx',
        assignment: '.pdf,.doc,.docx'
    };
    const hintMap = {
        video:      'Upload MP4, AVI, MOV or WMV files.',
        pdf:        'Upload PDF files only.',
        image:      'Upload JPG, JPEG, PNG or WEBP files.',
        quiz:       'Upload PDF or DOC/DOCX files.',
        assignment: 'Upload PDF or DOC/DOCX files.'
    };

    // ── ADD MODAL ─────────────────────────────────────────────────
    const addForm         = document.getElementById('addContentForm');
    const addModal        = document.getElementById('addContentModal');
    const addModeRadios   = document.querySelectorAll('#addDeliveryModeGroup input[type="radio"]');
    const addLocalPanel   = document.getElementById('addLocalPanel');
    const addDrivePanel   = document.getElementById('addDrivePanel');
    const addExternalPanel= document.getElementById('addExternalPanel');
    const addModeHint     = document.getElementById('addModeHint');

    // local sub-fields
    const addTypeSelect   = document.getElementById('addContentTypeSelect');
    const addTextField    = document.getElementById('addTextField');
    const addFileField    = document.getElementById('addFileField');
    const addFileInput    = document.getElementById('addFileInput');
    const addFileHint     = document.getElementById('addFileHint');

    function switchAddMode(mode) {
        addLocalPanel?.classList.toggle('d-none',    mode !== 'local');
        addDrivePanel?.classList.toggle('d-none',    mode !== 'drive');
        addExternalPanel?.classList.toggle('d-none', mode !== 'external');
        if (addModeHint) addModeHint.textContent = modeHints[mode] || '';
    }

    function toggleAddLocalFields() {
        const type = addTypeSelect?.value || 'text';
        if (type === 'text') {
            addTextField?.classList.remove('d-none');
            addFileField?.classList.add('d-none');
            addFileInput?.removeAttribute('required');
            if (addFileInput) addFileInput.value = '';
        } else {
            addTextField?.classList.add('d-none');
            addFileField?.classList.remove('d-none');
            addFileInput?.setAttribute('required', 'required');
            if (addFileInput) addFileInput.accept = acceptMap[type] || '';
            if (addFileHint)  addFileHint.textContent = hintMap[type] || '';
        }
    }

    addModeRadios.forEach(r => r.addEventListener('change', () => switchAddMode(r.value)));
    addTypeSelect?.addEventListener('change', toggleAddLocalFields);
    toggleAddLocalFields(); // init

    // pre-select course when "Add Module" button carries data-course
    addModal?.addEventListener('show.bs.modal', event => {
        const btn    = event.relatedTarget;
        const course = btn?.getAttribute('data-course');
        if (course) {
            const sel = document.getElementById('addCourseSelect');
            if (sel) sel.value = course;
        }
    });

    // reset on close
    addModal?.addEventListener('hidden.bs.modal', () => {
        addForm?.reset();
        switchAddMode('local');
        toggleAddLocalFields();
        phasesContainer?.querySelectorAll('.content-phase-block').forEach(b => b.remove());
        phaseIndex = 0;
        togglePhaseHint();
    });

    // ── EDIT MODAL ────────────────────────────────────────────────
    const editModalEl      = document.getElementById('editContentModal');
    const editForm         = document.getElementById('editContentForm');
    const editModeRadios   = document.querySelectorAll('#editDeliveryModeGroup input[type="radio"]');
    const editLocalPanel   = document.getElementById('editLocalPanel');
    const editDrivePanel   = document.getElementById('editDrivePanel');
    const editExternalPanel= document.getElementById('editExternalPanel');
    const editModeHint     = document.getElementById('editModeHint');

    // local sub-fields
    const editTitle        = document.getElementById('editContentTitle');
    const editType         = document.getElementById('editContentType');
    const editTextField    = document.getElementById('editTextField');
    const editFileField    = document.getElementById('editFileField');
    const editFileInput    = document.getElementById('editContentFile');
    const editFileHint     = document.getElementById('editFileHint');
    const editCurrentFile  = document.getElementById('editCurrentFile');
    const editBody         = document.getElementById('editContentBody');

    // drive sub-fields
    const editDriveFolder  = document.getElementById('editDriveFolder');
    const editDriveShare   = document.getElementById('editDriveShare');
    const editAutoGrant    = document.getElementById('editAutoGrant');

    // external sub-field
    const editExternalUrl  = document.getElementById('editExternalUrl');

    // price
    const editPrice        = document.getElementById('editContentPrice');
    const editDiscount     = document.getElementById('editContentDiscount');

    let editOriginalType   = 'text';
    let editHasFile        = false;

    function switchEditMode(mode) {
        editLocalPanel?.classList.toggle('d-none',    mode !== 'local');
        editDrivePanel?.classList.toggle('d-none',    mode !== 'drive');
        editExternalPanel?.classList.toggle('d-none', mode !== 'external');
        if (editModeHint) editModeHint.textContent = modeHints[mode] || '';
    }

    function toggleEditLocalFields() {
        const type      = editType?.value || 'text';
        const needsFile = type !== 'text' && (!editHasFile || type !== editOriginalType);

        if (type === 'text') {
            editTextField?.classList.remove('d-none');
            editFileField?.classList.add('d-none');
            editFileInput?.removeAttribute('required');
            if (editFileInput) editFileInput.value = '';
        } else {
            editTextField?.classList.add('d-none');
            editFileField?.classList.remove('d-none');
            if (needsFile) {
                editFileInput?.setAttribute('required', 'required');
                if (editFileHint) editFileHint.textContent = 'Upload a file (required after type change).';
            } else {
                editFileInput?.removeAttribute('required');
                if (editFileHint) editFileHint.textContent = 'Upload to replace existing file (optional).';
            }
            if (editFileInput) editFileInput.accept = acceptMap[type] || '';
        }
    }

    editModeRadios.forEach(r => r.addEventListener('change', () => switchEditMode(r.value)));
    editType?.addEventListener('change', toggleEditLocalFields);

    // populate on open
    editModalEl?.addEventListener('show.bs.modal', event => {
        const btn  = event.relatedTarget;
        if (!btn)  return;

        const data = JSON.parse(btn.getAttribute('data-content') || '{}');
        if (editForm)    editForm.action = btn.getAttribute('data-action');

        const mode = data.delivery_mode || 'local';

        // title
        if (editTitle)   editTitle.value = data.title || '';

        // price
        if (editPrice)   editPrice.value   = data.price ?? 0;
        if (editDiscount) editDiscount.value = data.discount_price ?? '';

        // ── select the correct radio & show the right panel ──
        editModeRadios.forEach(r => { r.checked = (r.value === mode); });
        switchEditMode(mode);

        // ── populate the active panel ─────────────────────────
        if (mode === 'local') {
            if (editType)  editType.value  = data.type || 'text';
            if (editBody)  editBody.value  = data.content || '';
            editOriginalType = data.type || 'text';
            editHasFile      = Boolean(data.has_file);
            if (editCurrentFile) {
                editCurrentFile.textContent = data.file_name ? `Current file: ${data.file_name}` : 'No file uploaded yet.';
            }
            if (editFileInput) editFileInput.value = '';
            toggleEditLocalFields();
        } else if (mode === 'drive') {
            if (editDriveFolder) editDriveFolder.value = data.drive_folder_id   || '';
            if (editDriveShare)  editDriveShare.value  = data.drive_share_link  || '';
            if (editAutoGrant)   editAutoGrant.checked  = Boolean(data.auto_grant_access);
        } else if (mode === 'external') {
            if (editExternalUrl) editExternalUrl.value = data.external_url || '';
        }
    });

    // reset on close
    editModalEl?.addEventListener('hidden.bs.modal', () => {
        editForm?.reset();
        if (editFileInput)  editFileInput.value  = '';
        if (editPrice)      editPrice.value      = '';
        if (editDiscount)   editDiscount.value   = '';
        switchEditMode('local');
    });

    // ── PHASE / TOPIC BUILDER ─────────────────────────────────────
    const phasesContainer = document.getElementById('contentPhasesContainer');
    const phasesHint      = document.getElementById('contentPhasesHint');
    const addPhaseBtn     = document.getElementById('addContentPhase');
    const phaseTemplate   = document.getElementById('contentPhaseTemplate');
    const topicTemplate   = document.getElementById('contentTopicTemplate');
    let   phaseIndex      = 0;

    function togglePhaseHint() {
        if (!phasesHint) return;
        phasesHint.classList.toggle('d-none', !!phasesContainer?.querySelector('.content-phase-block'));
    }

    function renumberTopics(list) {
        list?.querySelectorAll('.content-topic-block').forEach((block, idx) => {
            block.querySelector('.content-topic-label').textContent = `Topic ${idx + 1}`;
            block.querySelector('.topic-order-input').value = idx + 1;
        });
    }

    function addTopicBlock(phase) {
        if (!topicTemplate || !phasesContainer) return;
        const list  = phasesContainer.querySelector(`.topics-list[data-phase="${phase}"]`);
        if (!list)  return;
        const order = list.querySelectorAll('.content-topic-block').length + 1;
        const wrap  = document.createElement('div');
        wrap.innerHTML = topicTemplate.innerHTML
            .replace(/__PHASE__/g, phase)
            .replace(/__TOPIC__/g, order - 1)
            .replace(/__ORDER__/g, order);
        list.appendChild(wrap.firstElementChild);
        renumberTopics(list);
    }

    function addPhaseBlock() {
        if (!phaseTemplate || !phasesContainer) return;
        const wrap  = document.createElement('div');
        wrap.innerHTML = phaseTemplate.innerHTML
            .replace(/__INDEX__/g, phaseIndex)
            .replace(/__ORDER__/g, phaseIndex + 1);
        phasesContainer.appendChild(wrap.firstElementChild);
        togglePhaseHint();
        phaseIndex++;
    }

    addPhaseBtn?.addEventListener('click', () => addPhaseBlock());

    document.addEventListener('click', event => {
        if (event.target.closest('.remove-content-phase')) {
            event.target.closest('.content-phase-block')?.remove();
            togglePhaseHint();
        }
        const topicTrigger = event.target.closest('.add-content-topic');
        if (topicTrigger)  addTopicBlock(topicTrigger.getAttribute('data-phase'));

        if (event.target.closest('.remove-content-topic')) {
            const block = event.target.closest('.content-topic-block');
            const list  = block?.closest('.topics-list');
            block?.remove();
            if (list) renumberTopics(list);
        }
    });
});
</script>
@endpush
