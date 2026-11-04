@props(['record', 'resource', 'label'])
<x-row-actions :label="$label">
    @unless(request()->routeIs($resource.'.show'))<a role="menuitem" href="{{ route($resource.'.show', $record) }}"><x-icon name="arrow" />Otevřít detail</a>@endunless
    @can('update', $record)<a role="menuitem" href="{{ route($resource.'.edit', $record) }}"><x-icon name="edit" />Upravit</a>@endcan
    @can('delete', $record)
    <form method="POST" action="{{ route($resource.'.destroy', $record) }}" data-confirm="Smazat „{{ $label }}“?" data-confirm-detail="Tuto akci nelze vrátit zpět.">@csrf @method('DELETE')<button role="menuitem" type="submit" class="danger-action"><x-icon name="trash" />Smazat</button></form>
    @endcan
</x-row-actions>
