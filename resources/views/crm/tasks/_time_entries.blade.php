@props(['task'])
<section class="card work-panel mb-4">
  <header class="panel-heading"><div><h2>Odpracovaný čas</h2><p>{{ intdiv((int)$task->timeEntries->sum(fn($e) => $e->duration_in_minutes ?? 0),60) }} h {{ (int)$task->timeEntries->sum(fn($e) => $e->duration_in_minutes ?? 0) % 60 }} min celkem</p></div></header>
  <div class="p-3 d-flex gap-2 flex-wrap">
    @if(!$activeTimer)
    <form method="POST" action="{{ route('tasks.timer.start', $task) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">Spustit časovač</button></form>
    @elseif($activeTimer->task_id === $task->id)
    <form method="POST" action="{{ route('timers.stop', $activeTimer) }}">@csrf<button class="btn btn-primary btn-sm" type="submit">Zastavit a uložit</button></form>
    @else
    <p class="text-sm">Časovač běží u úkolu <a href="{{ route('tasks.show', $activeTimer->task_id) }}">{{ $activeTimer->task->title }}</a>.</p>
    @endif
    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#timeEntryModal">Zapsat ručně</button>
  </div>
  @forelse($task->timeEntries as $entry)
  @php $localStart = $entry->started_at->copy()->timezone(config('app.display_timezone')); $localEnd = $entry->ended_at?->copy()->timezone(config('app.display_timezone')); @endphp
  <div class="work-row"><span class="row-content"><strong>{{ $entry->createdBy?->name }}</strong><small>{{ $localStart->format('j. n. Y H:i') }}@if($entry->notes) · {{ $entry->notes }}@endif</small></span><strong>{{ $entry->duration_formatted }}</strong>
  @can('update', $entry)@if($entry->ended_at)<button type="button" class="btn btn-link p-0 m-0 text-sm" data-time-edit data-update-url="{{ route('tasks.time-entries.update', [$task,$entry]) }}" data-delete-url="{{ route('tasks.time-entries.destroy', [$task,$entry]) }}" data-start="{{ $localStart->format('Y-m-d\TH:i') }}" data-end="{{ $localEnd->format('Y-m-d\TH:i') }}" data-notes="{{ $entry->notes }}">Upravit</button>@endif @endcan</div>
  @empty<div class="empty-work compact"><p>Spusťte časovač při práci nebo zapište hotovou práci ručně.</p></div>@endforelse
</section>
@push('scripts')
<script>
(() => {
  const modal = document.getElementById('timeEntryModal');
  const form = document.getElementById('timeEntryForm');
  const deleteForm = document.getElementById('deleteTimeEntryForm');
  const deleteButton = document.getElementById('deleteTimeEntryBtn');
  const originalAction = form.action;
  document.querySelectorAll('[data-time-edit]').forEach(button => button.addEventListener('click', () => {
    form.action = button.dataset.updateUrl;
    const method = document.createElement('input'); method.type = 'hidden'; method.name = '_method'; method.value = 'PUT'; form.appendChild(method);
    form.elements.started_at.value = button.dataset.start;
    form.elements.ended_at.value = button.dataset.end;
    form.elements.notes.value = button.dataset.notes;
    deleteForm.action = button.dataset.deleteUrl; deleteButton.classList.remove('d-none');
    modal.querySelector('.modal-title').textContent = 'Upravit čas';
    modal.querySelector('button[type=submit]').textContent = 'Uložit změny';
    bootstrap.Modal.getOrCreateInstance(modal).show();
  }));
  modal.addEventListener('hidden.bs.modal', () => {
    form.action = originalAction; form.querySelector('[name=_method]')?.remove(); form.reset();
    deleteButton.classList.add('d-none');
    modal.querySelector('.modal-title').textContent = 'Zapsat čas ručně';
    modal.querySelector('button[type=submit]').textContent = 'Uložit čas';
  });
  deleteButton.addEventListener('click', () => { if(confirm('Odstranit tento časový záznam?')) deleteForm.submit(); });
})();
</script>
@endpush
