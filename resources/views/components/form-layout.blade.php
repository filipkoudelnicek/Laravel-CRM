@props(['title','submitText'=>'Uložit','backUrl','tone'=>'neutral'])
<div class="form-page">
  <x-page-heading :title="$title"><a class="btn btn-outline-secondary" href="{{ $backUrl }}">Zpět</a></x-page-heading>
  <div class="form-panel panel-{{ $tone }}">{{ $slot }}</div>
</div>
