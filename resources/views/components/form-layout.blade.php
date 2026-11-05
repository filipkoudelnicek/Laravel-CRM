@props(['title','submitText'=>'Uložit','backUrl'])
<div class="form-page">
  <x-page-heading :title="$title"><a class="btn btn-outline-secondary" href="{{ $backUrl }}">Zpět</a></x-page-heading>
  {{ $slot }}
</div>
