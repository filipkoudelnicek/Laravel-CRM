@props(['label', 'name', 'type' => 'text', 'value' => null, 'required' => false, 'placeholder' => null, 'rows' => null, 'class' => 'mb-3'])

<div class="{{ $class }}">
  <label class="form-label">
    {{ $label }}
    @if ($required) <span class="text-danger">*</span> @endif
  </label>

  @if ($type === 'textarea')
    <textarea name="{{ $name }}" class="form-control @error($name) is-invalid @enderror" 
              placeholder="{{ $placeholder }}" rows="{{ $rows ?? 4 }}" {{ $required ? 'required' : '' }}>{{ old($name, $value) }}</textarea>
  @elseif ($type === 'select')
    <select name="{{ $name }}" class="form-select @error($name) is-invalid @enderror" {{ $required ? 'required' : '' }}>
      {{ $slot }}
    </select>
  @else
    <input type="{{ $type }}" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror"
           value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>
  @endif

  @error($name)
    <div class="text-danger text-xs mt-1">{{ $message }}</div>
  @enderror
</div>
