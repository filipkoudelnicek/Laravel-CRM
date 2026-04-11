@props(['task'])

<div class="card mb-4">
  <div class="card-header pb-0 d-flex justify-content-between align-items-center">
    <h6 class="mb-0">Trackování času <span class="badge bg-light text-dark">{{ $task->timeEntries->count() }}</span></h6>
    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#timeEntryModal">
      <i class="fas fa-plus"></i> Přidat čas
    </button>
  </div>
  <div class="card-body">
    @if($task->timeEntries->count())
      <div class="table-responsive">
        <table class="table table-sm table-borderless">
          <thead class="table-light">
            <tr>
              <th class="text-xs font-weight-bold">Začátek</th>
              <th class="text-xs font-weight-bold">Konec</th>
              <th class="text-xs font-weight-bold">Trvání</th>
              <th class="text-xs font-weight-bold">Poznámka</th>
              <th class="text-xs font-weight-bold">Akce</th>
            </tr>
          </thead>
          <tbody>
            @foreach($task->timeEntries as $entry)
              <tr>
                <td class="text-xs">{{ $entry->started_at->format('d.m.Y H:i') }}</td>
                <td class="text-xs">{{ $entry->ended_at ? $entry->ended_at->format('d.m.Y H:i') : '—' }}</td>
                <td class="text-xs fw-bold">{{ $entry->duration_formatted }}</td>
                <td class="text-xs text-secondary">{{ Str::limit($entry->notes, 30) }}</td>
                <td class="text-xs">
                  <button class="btn btn-xs btn-link p-0 text-primary" onclick="editTimeEntry(this)" 
                    data-entry-id="{{ $entry->id }}"
                    data-started-at="{{ $entry->started_at->format('Y-m-d\TH:i') }}"
                    data-ended-at="{{ $entry->ended_at ? $entry->ended_at->format('Y-m-d\TH:i') : '' }}"
                    data-notes="{{ $entry->notes }}">
                    <i class="fas fa-edit"></i>
                  </button>
                  <form method="POST" action="{{ route('tasks.time-entries.destroy', [$task, $entry]) }}" style="display:inline;" onsubmit="return confirm('Smazat čas?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-link p-0 text-danger">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <hr>
      <div class="row">
        <div class="col-6">
          <p class="text-xs text-secondary mb-1">Celkový čas</p>
          <h6>
            @php
              $totalMinutes = $task->timeEntries->sum(fn($e) => $e->getDurationInMinutesAttribute() ?? 0);
              $hours = intval($totalMinutes / 60);
              $mins = $totalMinutes % 60;
            @endphp
            {{ $hours }}h {{ $mins }}m
          </h6>
        </div>
        <div class="col-6 text-end">
          <p class="text-xs text-secondary mb-1">Počet záznamů</p>
          <h6>{{ $task->timeEntries->count() }}</h6>
        </div>
      </div>
    @else
      <p class="text-sm text-secondary text-center py-4">Žádné záznamy o čase.</p>
    @endif
  </div>
</div>

@push('scripts')
<script>
function editTimeEntry(btn) {
  const entryId = btn.dataset.entryId;
  const startedAt = btn.dataset.startedAt;
  const endedAt = btn.dataset.endedAt;
  const notes = btn.dataset.notes;
  
  // Set form action to update
  const form = document.querySelector('#timeEntryModal form');
  const taskId = {{ $task->id }};
  form.action = `/tasks/${taskId}/time-entries/${entryId}`;
  form.method = 'POST';
  
  // Add PUT method
  let methodInput = form.querySelector('input[name="_method"]');
  if (!methodInput) {
    methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    form.appendChild(methodInput);
  }
  methodInput.value = 'PUT';
  
  // Set values
  document.querySelector('input[name="started_at"]').value = startedAt;
  document.querySelector('input[name="ended_at"]').value = endedAt;
  document.querySelector('textarea[name="notes"]').value = notes;
  
  // Update modal title
  document.querySelector('#timeEntryModal .modal-title').textContent = 'Upravit čas';
  document.querySelector('#timeEntryModal button[type="submit"]').textContent = 'Uložit';
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('timeEntryModal'));
  modal.show();
}

// Reset modal on hide
document.getElementById('timeEntryModal')?.addEventListener('hidden.bs.modal', function() {
  const form = this.querySelector('form');
  const taskId = {{ $task->id }};
  form.action = `/tasks/${taskId}/time-entries`;
  form.method = 'POST';
  
  const methodInput = form.querySelector('input[name="_method"]');
  if (methodInput) methodInput.remove();
  
  form.reset();
  document.querySelector('#timeEntryModal .modal-title').textContent = 'Přidat čas';
  document.querySelector('#timeEntryModal button[type="submit"]').textContent = 'Přidat';
});
</script>
@endpush
