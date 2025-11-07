@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Str;

    $theme = ($theme ?? 'light') === 'dark' ? 'dark' : 'light';
    $d = $block->data ?? [];

    $defaultFields = [
        [
            'label' => 'Full Name',
            'name' => 'full_name',
            'type' => 'text',
            'placeholder' => 'Jane Doe',
            'required' => true,
            'width' => 'half',
        ],
        [
            'label' => 'Email Address',
            'name' => 'email',
            'type' => 'email',
            'placeholder' => 'you@example.com',
            'required' => true,
            'width' => 'half',
        ],
        [
            'label' => 'Phone Number',
            'name' => 'phone',
            'type' => 'tel',
            'placeholder' => '+234 801 234 5678',
            'required' => false,
            'width' => 'half',
        ],
        [
            'label' => 'How can we help?',
            'name' => 'message',
            'type' => 'textarea',
            'placeholder' => 'Tell us about your project…',
            'required' => true,
            'width' => 'full',
        ],
    ];

    $fields = collect($d['fields'] ?? $defaultFields)
        ->filter(fn ($field) => is_array($field) && filled($field['label'] ?? null))
        ->values();

    if ($fields->isEmpty()) {
        $fields = collect($defaultFields);
    }

    $action = route('newsletter.subscribe', [], false);
    $method = 'POST';

    $tags = collect($d['tags'] ?? ['Newsletter'])
        ->when(is_string($d['tags'] ?? null), fn ($c) => $c->flatMap(fn ($v) => explode(',', $v)))
        ->map(fn ($tag) => trim($tag))
        ->filter()
        ->values();

    $buttonText = $d['button_text'] ?? 'Join the newsletter';
    $sectionTitle = $d['title'] ?? 'Let’s work together';
    $sectionSubtitle = $d['subtitle'] ?? 'Share a few details and our team will reach out within one business day.';
    $formId = 'dynamic-form-' . $block->id;
@endphp

@once('dynamic-form-block-styles')
    @push('styles')
        <style>
            .dynamic-form-block {
                padding: clamp(3rem, 7vw, 5rem) 0;
            }

            .dynamic-form-card {
                margin: 0 auto;
                max-width: 840px;
                border-radius: 28px;
                padding: clamp(2.5rem, 5vw, 4rem);
                box-shadow: 0 20px 60px rgba(6, 18, 36, 0.16);
                position: relative;
                isolation: isolate;
            }

            .dynamic-form-card::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: inherit;
                pointer-events: none;
                opacity: 0.4;
            }

            .dynamic-form-card h2 {
                font-size: clamp(1.8rem, 3vw, 2.4rem);
                 color: inherit;
                margin-bottom: 0.65rem;
            }

            .dynamic-form-card p {
                margin-bottom: 2rem;
                color: inherit;
                opacity: 0.9;
            }

            .dynamic-form-grid {
                display: grid;
                gap: 1.25rem;
            }

            @media (min-width: 768px) {
                .dynamic-form-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            .form-field {
                display: flex;
                flex-direction: column;
                text-align: left;
            }

            .form-field label {
                font-weight: 600;
                font-size: 0.95rem;
                margin-bottom: 0.5rem;
            }

            .form-field--full {
                grid-column: 1 / -1;
            }

            .dynamic-form-card input,
            .dynamic-form-card textarea {
                border-radius: 14px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                background: rgba(255, 255, 255, 0.08);
                color: inherit;
                padding: 0.9rem 1.1rem;
                transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            }

            .dynamic-form-card textarea {
                min-height: 140px;
                resize: vertical;
            }

            .dynamic-form-card input:focus,
            .dynamic-form-card textarea:focus {
                outline: none;
                border-color: rgba(132, 94, 247, 0.7);
                box-shadow: 0 0 0 3px rgba(132, 94, 247, 0.25);
                background: rgba(255, 255, 255, 0.15);
            }

            .dynamic-form-block--light .dynamic-form-card {
                background: #ffffff;
                color: #101828;
            }

            .dynamic-form-block--light .dynamic-form-card::after {
                background: linear-gradient(120deg, rgba(9, 9, 121, 0.08), rgba(0, 212, 255, 0.08));
            }

            .dynamic-form-block--light input,
            .dynamic-form-block--light textarea {
                border-color: rgba(16, 24, 40, 0.12);
                background: rgba(249, 250, 251, 0.9);
                color: #0f172a;
            }

            .dynamic-form-block--dark {
                background: #050b2c;
            }

            .dynamic-form-block--dark .dynamic-form-card {
                background: radial-gradient(circle at top, rgba(11, 32, 92, 0.8), rgba(3, 9, 28, 0.95));
                color: #f8fbff;
            }

            .dynamic-form-block--dark .dynamic-form-card::after {
                background: linear-gradient(135deg, rgba(7, 155, 219, 0.25), rgba(28, 37, 110, 0.4));
                mix-blend-mode: screen;
            }

            .dynamic-form-block--dark input,
            .dynamic-form-block--dark textarea {
                border-color: rgba(255, 255, 255, 0.18);
                background: rgba(255, 255, 255, 0.08);
                color: #f8fbff;
            }

            .dynamic-form-submit {
                margin-top: 1.5rem;
                display: flex;
                justify-content: center;
            }

            .dynamic-form-card .tj-primary-btn {
                min-width: 180px;
                justify-content: center;
            }

            .dynamic-form-feedback {
                margin-top: 1rem;
                border-radius: 12px;
                padding: 0.9rem 1.2rem;
                font-weight: 600;
                opacity: 0;
                transform: translateY(6px);
                transition: opacity 0.25s ease, transform 0.25s ease;
            }

            .dynamic-form-feedback.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

            .dynamic-form-feedback.success {
                background: rgba(16, 185, 129, .12);
                color: #065f46;
            }

            .dynamic-form-feedback.error {
                background: rgba(248, 113, 113, .15);
                color: #991b1b;
            }

            .dynamic-form-modal {
                position: fixed;
                inset: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                background: rgba(4, 6, 20, 0.75);
                backdrop-filter: blur(6px);
                z-index: 9999;
                padding: 1.5rem;
            }

            .dynamic-form-modal[hidden] {
                display: none !important;
            }

            .dynamic-form-modal__dialog {
                background: #ffffff;
                border-radius: 32px;
                padding: clamp(2rem, 4vw, 3.25rem);
                max-width: min(540px, 92vw);
                width: 100%;
                text-align: center;
                box-shadow: 0 40px 90px rgba(15, 23, 42, .25);
                position: relative;
            }

            .dynamic-form-modal__dialog--dark {
                background: radial-gradient(circle at top, rgba(9, 15, 38, 0.95), rgba(3, 5, 18, 0.98));
                color: #f8fafc;
            }

            .dynamic-form-modal__close {
                position: absolute;
                top: 18px;
                right: 18px;
                border: none;
                background: transparent;
                color: inherit;
                font-size: 1.5rem;
                cursor: pointer;
                opacity: .7;
            }

            .dynamic-form-modal__close:hover {
                opacity: 1;
            }

            .dynamic-form-modal__icon {
                width: 90px;
                height: 90px;
                border-radius: 28px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 34px;
                margin-bottom: 1.25rem;
            }

            .dynamic-form-modal__icon.success {
                background: rgba(16, 185, 129, .2);
                color: #0f766e;
            }

            .dynamic-form-modal__icon.error {
                background: rgba(248, 113, 113, .22);
                color: #b91c1c;
            }

            .dynamic-form-modal__title {
                font-size: clamp(1.4rem, 3vw, 1.9rem);
                font-weight: 700;
                margin-bottom: .4rem;
            }

            .dynamic-form-modal__message {
                font-weight: 500;
                line-height: 1.7;
                color: inherit;
            }

            body.dynamic-form-modal-open {
                overflow: hidden;
            }
        </style>
    @endpush
@endonce

@once('dynamic-form-block-scripts')
    @push('scripts')
        <script>
            document.addEventListener('submit', async (event) => {
                const form = event.target.closest('.js-newsletter-form');
                if (!form || event.target !== form) {
                    return;
                }

                event.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                if (form.dataset.submitting === '1') return;
                form.dataset.submitting = '1';

                const submitBtn = form.querySelector('button[type="submit"]');
                const feedback = form.querySelector('.dynamic-form-feedback');
                const blockId = form.dataset.blockId;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content
                    || form.querySelector('input[name="_token"]')?.value;

                const fields = Array.from(form.querySelectorAll('[data-field-input]')).map((input) => ({
                    label: input.dataset.fieldLabel || '',
                    name: input.dataset.fieldName,
                    type: input.dataset.fieldType,
                    required: input.dataset.fieldRequired === '1',
                    value: input.value.trim(),
                }));

                const payload = {
                    source: 'builder_form',
                    block_id: blockId,
                    fields,
                    tags: JSON.parse(form.dataset.tags || '[]'),
                };

                submitBtn?.setAttribute('disabled', 'disabled');

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify(payload),
                    });

                    const body = await response.json().catch(() => ({}));

                    if (response.ok) {
                        form.reset();
                        showFormFeedback(feedback, body.message || 'Thanks for subscribing!', 'success');
                    } else {
                        showFormFeedback(feedback, body.message || 'Something went wrong. Please retry.', 'error');
                    }
                } catch (error) {
                    showFormFeedback(feedback, 'Network error. Please try again.', 'error');
                } finally {
                    submitBtn?.removeAttribute('disabled');
                    delete form.dataset.submitting;
                }
            });

            function showFormFeedback(el, message, state) {
                if (!el) return;
                el.textContent = message;
                el.classList.remove('success', 'error', 'is-visible');
                el.classList.add(state, 'is-visible');
                openNewsletterModal(message, state);
            }

            function ensureNewsletterModal() {
                let modal = document.querySelector('[data-dynamic-form-modal]');
                if (modal) return modal;

                modal = document.createElement('div');
                modal.className = 'dynamic-form-modal';
                modal.setAttribute('data-dynamic-form-modal', 'true');
                modal.setAttribute('hidden', 'hidden');
                modal.innerHTML = `
  <div class="dynamic-form-modal__dialog" role="dialog" aria-modal="true">
    <button type="button" class="dynamic-form-modal__close" data-modal-dismiss aria-label="Close">&times;</button>
    <div class="dynamic-form-modal__icon success" data-modal-icon>✔</div>
    <h3 class="dynamic-form-modal__title" data-modal-title>All set!</h3>
    <p class="dynamic-form-modal__message" data-modal-message>Thanks for reaching out!</p>
    <div class="mt-3">
      <button type="button" class="tj-primary-btn" data-modal-dismiss>
        <span class="btn-text"><span>Close</span></span>
      </button>
    </div>
  </div>`;
                document.body.appendChild(modal);

                modal.addEventListener('click', (ev) => {
                    if (ev.target === modal || ev.target.hasAttribute('data-modal-dismiss')) {
                        closeNewsletterModal();
                    }
                });

                document.addEventListener('keydown', (ev) => {
                    if (ev.key === 'Escape') {
                        closeNewsletterModal();
                    }
                });

                return modal;
            }

            function openNewsletterModal(message, state) {
                const modal = ensureNewsletterModal();
                const dialog = modal.querySelector('.dynamic-form-modal__dialog');
                const icon = modal.querySelector('[data-modal-icon]');
                const text = modal.querySelector('[data-modal-message]');
                const title = modal.querySelector('[data-modal-title]');

                if (icon) {
                    icon.classList.remove('success', 'error');
                    icon.classList.add(state === 'error' ? 'error' : 'success');
                    icon.textContent = state === 'error' ? '✕' : '✔';
                }

                if (text) text.textContent = message;
                if (title) {
                    title.textContent = state === 'error'
                        ? 'Something went wrong'
                        : "You're in!";
                }

                if (dialog) {
                    dialog.classList.toggle('dynamic-form-modal__dialog--dark', document.body.classList.contains('dark-mode'));
                }

                if (modal.dataset.timerId) {
                    clearTimeout(Number(modal.dataset.timerId));
                    delete modal.dataset.timerId;
                }

                modal.removeAttribute('hidden');
                document.body.classList.add('dynamic-form-modal-open');

                const timeout = setTimeout(() => {
                    closeNewsletterModal();
                }, state === 'error' ? 8000 : 5000);

                modal.dataset.timerId = timeout;
            }

            function closeNewsletterModal() {
                const modal = document.querySelector('[data-dynamic-form-modal]');
                if (modal) {
                    modal.setAttribute('hidden', 'hidden');
                    if (modal.dataset.timerId) {
                        clearTimeout(Number(modal.dataset.timerId));
                        delete modal.dataset.timerId;
                    }
                }
                document.body.classList.remove('dynamic-form-modal-open');
            }
        </script>
    @endpush
@endonce

<section class="dynamic-form-block dynamic-form-block--{{ $theme }} section-gap-x">
    <div class="container">
        <div class="dynamic-form-card">
            @if ($sectionTitle)
                <h2>{{ $sectionTitle }}</h2>
            @endif

            @if ($sectionSubtitle)
                <p>{{ $sectionSubtitle }}</p>
            @endif

            <form id="{{ $formId }}"
                action="{{ $action }}"
                method="POST"
                class="js-newsletter-form"
                data-block-id="{{ $block->id }}"
                data-tags='@json($tags->all())'
                novalidate>
                @csrf
                <input type="hidden" name="block_id" value="{{ $block->id }}">
                <input type="hidden" name="form_tags" value="{{ $tags->implode(',') }}">

                <div class="dynamic-form-grid">
                    @foreach ($fields as $field)
                        @php
                            $label = $field['label'] ?? ('Field ' . ($loop->iteration));
                            $type = in_array($field['type'] ?? 'text', ['text', 'email', 'tel', 'textarea'], true)
                                ? $field['type']
                                : 'text';
                            $placeholder = $field['placeholder'] ?? '';
                            $required = Arr::get($field, 'required', false);
                            $name = Str::slug($field['name'] ?? $label, '_');
                            if ($name === '') {
                                $name = 'field_' . $loop->index;
                            }
                            $inputName = "newsletter_form[{$block->id}][{$name}]";
                            $fieldClasses = 'form-field ' . (($field['width'] ?? 'full') === 'half' ? '' : 'form-field--full');
                        @endphp
                        <div class="{{ trim($fieldClasses) }}">
                            <label for="{{ $formId }}_{{ $name }}">
                                {{ $label }}
                                @if ($required)
                                    <span aria-hidden="true">*</span>
                                @endif
                            </label>

                            <input type="hidden" name="field_meta[{{ $block->id }}][{{ $name }}][label]" value="{{ $label }}">
                            <input type="hidden" name="field_meta[{{ $block->id }}][{{ $name }}][type]" value="{{ $type }}">
                            <input type="hidden" name="field_meta[{{ $block->id }}][{{ $name }}][required]" value="{{ $required ? 1 : 0 }}">

                            @if ($type === 'textarea')
                                <textarea id="{{ $formId }}_{{ $name }}" name="{{ $inputName }}"
                                    placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
                                    data-field-input
                                    data-field-label="{{ $label }}"
                                    data-field-name="{{ $name }}"
                                    data-field-type="{{ $type }}"
                                    data-field-required="{{ $required ? '1' : '0' }}"></textarea>
                            @else
                                <input id="{{ $formId }}_{{ $name }}" type="{{ $type }}" name="{{ $inputName }}"
                                    placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
                                    data-field-input
                                    data-field-label="{{ $label }}"
                                    data-field-name="{{ $name }}"
                                    data-field-type="{{ $type }}"
                                    data-field-required="{{ $required ? '1' : '0' }}">
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="dynamic-form-submit">
                    <button class="tj-primary-btn" type="submit">
                        <span class="btn-text"><span>{{ $buttonText }}</span></span>
                        <span class="btn-icon"><i class="tji-arrow-right-long"></i></span>
                    </button>
                </div>

                <div class="dynamic-form-feedback" role="status" aria-live="polite"></div>
            </form>
        </div>
    </div>
</section>
