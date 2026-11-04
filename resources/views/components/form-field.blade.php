@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'placeholder' => null, 'rows' => null, 'class' => 'mb-3', 'inputClass' => ''])
@php $fieldId = $attributes->get('id', 'field-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $name)); @endphp

<div class="{{ $class }}">
  <label class="form-label" for="{{ $fieldId }}">
    {{ $label }}
    @if ($required) <span class="text-danger">*</span> @endif
  </label>

  @if ($type === 'textarea')
    <textarea id="{{ $fieldId }}" name="{{ $name }}" class="form-control {{ $inputClass }} @error($name) is-invalid @enderror"
              placeholder="{{ $placeholder }}" rows="{{ $rows ?? 4 }}" {{ $required ? 'required' : '' }} {{ $attributes->except(['class','id']) }}>{{ old($name, $value) }}</textarea>
  @elseif ($type === 'select')
    <select id="{{ $fieldId }}" name="{{ $name }}" class="form-select {{ $inputClass }} @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }} {{ $attributes->except(['class','id']) }}>
      {{ $slot }}
    </select>
  @else
    <input id="{{ $fieldId }}" type="{{ $type }}" name="{{ $name }}" class="form-control {{ $inputClass }} @error($name) is-invalid @enderror"
           value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{ $attributes->except(['class','id']) }}>
  @endif

  @error($name)
    <div class="text-danger text-xs mt-1">{{ $message }}</div>
  @enderror
</div>
