@props(['items'])
<footer class="list-footer"><span>{{ $items->total() ? $items->firstItem().'–'.$items->lastItem().' z '.$items->total() : 'Žádné záznamy' }}</span>{{ $items->links() }}</footer>
