@props(['label'])
<div class="row-actions" data-row-actions>
    <button type="button" class="action-trigger" aria-label="Další akce: {{ $label }}" aria-haspopup="menu" aria-expanded="false" data-action-trigger><x-icon name="more" /></button>
    <div class="action-menu" role="menu" hidden>{{ $slot }}</div>
</div>
