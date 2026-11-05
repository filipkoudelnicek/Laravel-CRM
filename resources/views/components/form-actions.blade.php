@props(['submitText'=>'Uložit','submitVariant'=>'primary','backUrl','align'=>'between'])
<div class="form-actions">
  <a class="btn btn-outline-secondary" href="{{ $backUrl }}">Zrušit</a>
  <button type="submit" class="btn bg-gradient-{{ $submitVariant }}">{{ $submitText }}</button>
</div>
