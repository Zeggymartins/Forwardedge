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

<!-- Add Content Modal -->
<div class="modal fade" id="addContentModal" tabindex="-1" aria-labelledby="addContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form class="modal-content" action="{{ route('admin.course_contents.store') }}" method="POST" enctype="multipart/form-data" id="addContentForm">
            @csrf
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="addContentModalLabel">Add Course Content</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
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

                    <div class="col-md-6">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="contentTypeSelect" class="form-select" required>
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3" data-content-field="text">
                    <label class="form-label">Body</label>
                    <textarea name="content" rows="5" class="form-control" id="contentBody" placeholder="Write the lesson content here..."></textarea>
                </div>

                <div class="mt-3 d-none" data-content-field="file">
                    <label class="form-label">Upload File <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" id="contentFileInput">
                    <small class="text-muted d-block mt-1" id="fileHint">
                        Select a type to see the allowed file formats.
                    </small>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Course Price (₦)</label>
                        <input type="number" name="price" class="form-control" step="0.01" min="0" placeholder="Leave empty to keep current price">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Discount Price (₦)</label>
                        <input type="number" name="discount_price" class="form-control" step="0.01" min="0" placeholder="Optional discount">
                    </div>
                </div>

                <div class="mt-4 phase-builder p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-semibold mb-0">Phases & Topics</h6>
                            <small class="text-muted">Break the lesson into bite-sized parts.</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addContentPhase">
                            + Add Phase
                        </button>
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

<!-- Edit Content Modal -->
<div class="modal fade" id="editContentModal" tabindex="-1" aria-labelledby="editContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form class="modal-content" method="POST" enctype="multipart/form-data" id="editContentForm">
            @csrf @method('PUT')
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="editContentModalLabel">Edit Content Block</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editContentTitle" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="editContentType" class="form-select" required>
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3" data-edit-field="text">
                    <label class="form-label">Body</label>
                    <textarea name="content" rows="5" class="form-control" id="editContentBody" placeholder="Update the lesson body"></textarea>
                </div>

                <div class="mt-3 d-none" data-edit-field="file">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file" class="form-control" id="editContentFile">
                    <small class="text-muted d-block mt-1" id="editFileHint">Upload a new file for this content.</small>
                    <small class="d-block mt-1" id="editCurrentFile"></small>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-secondary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

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
                <button type="button" class="btn btn-sm btn-outline-secondary add-content-topic" data-phase="__INDEX__">
                    + Add Topic
                </button>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const acceptMap = {
                video: 'video/*',
                pdf: '.pdf,application/pdf',
                image: 'image/*',
                quiz: '.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                assignment: '.pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            };

            const hintMap = {
                video: 'Upload MP4, AVI, MOV or WMV files.',
                pdf: 'Upload PDF files only.',
                image: 'Upload JPG, JPEG, PNG or WEBP files.',
                quiz: 'Upload PDF or DOC/DOCX files.',
                assignment: 'Upload PDF or DOC/DOCX files.'
            };

            const addTypeSelect = document.getElementById('contentTypeSelect');
            const addTextField = document.querySelector('[data-content-field="text"]');
            const addFileField = document.querySelector('[data-content-field="file"]');
            const addFileInput = document.getElementById('contentFileInput');
            const fileHint = document.getElementById('fileHint');

            function toggleAddFields() {
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
                    if (fileHint) fileHint.textContent = hintMap[type] || 'Upload the relevant file for this content type.';
                }
            }

            addTypeSelect?.addEventListener('change', toggleAddFields);
            toggleAddFields();

            const addModal = document.getElementById('addContentModal');
            addModal?.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const courseId = button?.getAttribute('data-course');
                if (courseId) {
                    const select = document.getElementById('addCourseSelect');
                    if (select) select.value = courseId;
                }
            });

            const phasesContainer = document.getElementById('contentPhasesContainer');
            const phasesHint = document.getElementById('contentPhasesHint');
            const addPhaseBtn = document.getElementById('addContentPhase');
            const phaseTemplate = document.getElementById('contentPhaseTemplate');
            const topicTemplate = document.getElementById('contentTopicTemplate');
            let phaseIndex = 0;

            function togglePhaseHint() {
                if (!phasesHint) return;
                const hasBlock = phasesContainer?.querySelector('.content-phase-block');
                phasesHint.classList.toggle('d-none', !!hasBlock);
            }

            function renumberTopics(list) {
                list?.querySelectorAll('.content-topic-block').forEach((block, idx) => {
                    block.querySelector('.content-topic-label').textContent = `Topic ${idx + 1}`;
                    block.querySelector('.topic-order-input').value = idx + 1;
                });
            }

            function addTopicBlock(phase) {
                if (!topicTemplate || !phasesContainer) return;
                const list = phasesContainer.querySelector(`.topics-list[data-phase="${phase}"]`);
                if (!list) return;
                const order = list.querySelectorAll('.content-topic-block').length + 1;
                const html = topicTemplate.innerHTML
                    .replace(/__PHASE__/g, phase)
                    .replace(/__TOPIC__/g, order - 1)
                    .replace(/__ORDER__/g, order);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                list.appendChild(wrapper.firstElementChild);
                renumberTopics(list);
            }

            function addPhaseBlock() {
                if (!phaseTemplate || !phasesContainer) return;
                const html = phaseTemplate.innerHTML
                    .replace(/__INDEX__/g, phaseIndex)
                    .replace(/__ORDER__/g, phaseIndex + 1);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();
                phasesContainer.appendChild(wrapper.firstElementChild);
                togglePhaseHint();
                phaseIndex++;
            }

            addPhaseBtn?.addEventListener('click', () => addPhaseBlock());

            document.addEventListener('click', event => {
                if (event.target.closest('.remove-content-phase')) {
                    const block = event.target.closest('.content-phase-block');
                    block?.remove();
                    togglePhaseHint();
                }

                const addTopicTrigger = event.target.closest('.add-content-topic');
                if (addTopicTrigger) {
                    addTopicBlock(addTopicTrigger.getAttribute('data-phase'));
                }

                if (event.target.closest('.remove-content-topic')) {
                    const block = event.target.closest('.content-topic-block');
                    const list = block?.closest('.topics-list');
                    block?.remove();
                    if (list) renumberTopics(list);
                }
            });

            addModal?.addEventListener('hidden.bs.modal', () => {
                phasesContainer?.querySelectorAll('.content-phase-block').forEach(block => block.remove());
                phaseIndex = 0;
                togglePhaseHint();
                document.getElementById('addContentForm')?.reset();
                toggleAddFields();
            });

            // Edit modal logic
            const editModalEl = document.getElementById('editContentModal');
            const editForm = document.getElementById('editContentForm');
            const editTitle = document.getElementById('editContentTitle');
            const editType = document.getElementById('editContentType');
            const editBody = document.getElementById('editContentBody');
            const editFileWrap = document.querySelector('[data-edit-field="file"]');
            const editTextWrap = document.querySelector('[data-edit-field="text"]');
            const editFileInput = document.getElementById('editContentFile');
            const editFileHint = document.getElementById('editFileHint');
            const editCurrentFile = document.getElementById('editCurrentFile');
            let editOriginalType = 'text';
            let editHasFile = false;

            function toggleEditFields() {
                const type = editType?.value || 'text';
                const needsFile = type !== 'text' && (!editHasFile || type !== editOriginalType);

                if (type === 'text') {
                    editTextWrap?.classList.remove('d-none');
                    editFileWrap?.classList.add('d-none');
                    editFileInput?.removeAttribute('required');
                    if (editFileInput) editFileInput.value = '';
                } else {
                    editTextWrap?.classList.add('d-none');
                    editFileWrap?.classList.remove('d-none');
                    if (needsFile) {
                        editFileInput?.setAttribute('required', 'required');
                        if (editFileHint) editFileHint.textContent = 'Upload a file for this content type. (Required after type change)';
                    } else {
                        editFileInput?.removeAttribute('required');
                        if (editFileHint) editFileHint.textContent = 'Upload to replace the existing file (optional).';
                    }
                    if (editFileInput) editFileInput.accept = acceptMap[type] || '';
                }
            }

            editType?.addEventListener('change', toggleEditFields);

            editModalEl?.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                if (!button) return;
                const payload = button.getAttribute('data-content');
                const data = payload ? JSON.parse(payload) : {};

                if (editForm) editForm.action = button.getAttribute('data-action');
                if (editTitle) editTitle.value = data.title || '';
                if (editType) editType.value = data.type || 'text';
                if (editBody) editBody.value = data.content || '';
                editOriginalType = data.type || 'text';
                editHasFile = Boolean(data.has_file);
                if (editCurrentFile) {
                    editCurrentFile.textContent = data.file_name ? `Current file: ${data.file_name}` : 'No file uploaded yet.';
                }
                if (editFileInput) editFileInput.value = '';
                toggleEditFields();
            });

            editModalEl?.addEventListener('hidden.bs.modal', () => {
                editForm?.reset();
                if (editFileInput) editFileInput.value = '';
            });
        });
    </script>
@endpush
