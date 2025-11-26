@php
    $fieldName = $attributes->get('name', 'hp_field');
    $fieldId = $attributes->get('id', $fieldName . '_' . uniqid());
@endphp
<div class="hp-field" style="position:absolute;left:-9999px;opacity:0;" aria-hidden="true">
    <label for="{{ $fieldId }}">Leave this field blank</label>
    <input type="text" name="{{ $fieldName }}" id="{{ $fieldId }}" tabindex="-1" autocomplete="off">
</div>
