@props(['submitText' => 'Uložit', 'submitVariant' => 'primary', 'backUrl', 'align' => 'between'])

<div class="d-flex justify-content-{{ $align }} gap-2 mt-4 pt-3 border-top">
  <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-times me-1" aria-hidden="true"></i> Zrušit
  </a>
  <button type="submit" class="btn bg-gradient-{{ $submitVariant }} btn-sm">
    <i class="fas fa-save me-1" aria-hidden="true"></i> {{ $submitText }}
  </button>
</div>
