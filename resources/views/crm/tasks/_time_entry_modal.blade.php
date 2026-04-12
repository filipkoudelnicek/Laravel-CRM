@props(['task', 'timeEntry' => null])

<div class="modal fade" id="timeEntryModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ $timeEntry ? 'Upravit čas' : 'Přidat čas' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      @if($timeEntry)
        <div class="modal-body border-bottom pb-2">
          <small class="text-secondary user-info">Zaznamenáno: {{ $timeEntry->createdBy?->name ?? 'Neznámý' }}</small>
        </div>
      @endif
      <form method="POST" action="{{ $timeEntry ? route('tasks.time-entries.update', [$task, $timeEntry]) : route('tasks.time-entries.store', $task) }}">
        @csrf
        @if($timeEntry) @method('PUT') @endif
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Začátek <span class="text-danger">*</span></label>
            <input type="datetime-local" name="started_at" class="form-control" required
              value="{{ $timeEntry ? $timeEntry->started_at->format('Y-m-d\TH:i') : '' }}">
            @error('started_at') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Konec</label>
            <input type="datetime-local" name="ended_at" class="form-control"
              value="{{ $timeEntry ? ($timeEntry->ended_at ? $timeEntry->ended_at->format('Y-m-d\TH:i') : '') : '' }}">
            @error('ended_at') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Poznámka</label>
            <textarea name="notes" rows="3" class="form-control">{{ $timeEntry ? $timeEntry->notes : '' }}</textarea>
            @error('notes') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Zrušit</button>
          <button type="submit" class="btn bg-gradient-primary btn-sm">{{ $timeEntry ? 'Uložit' : 'Přidat' }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
