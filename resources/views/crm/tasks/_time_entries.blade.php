@props(['task'])

<div class="card mb-4">
  <div class="card-header pb-0">
    <div class="d-flex justify-content-between align-items-center">
      <h6 class="mb-0">
        <i class="fas fa-clock me-2 text-primary"></i>Trackování času 
        <span class="badge bg-light text-dark">{{ $task->timeEntries->count() }}</span>
      </h6>
      <div class="d-flex gap-2">
        <button class="btn btn-sm btn-outline-primary" id="startTrackingBtn" onclick="startTracking(event)">
          <i class="fas fa-play"></i> Spustit
        </button>
        <button class="btn btn-sm btn-outline-danger d-none" id="stopTrackingBtn" onclick="stopTracking(event)">
          <i class="fas fa-stop"></i> Zastavit
        </button>
        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#timeEntryModal">
          <i class="fas fa-plus"></i> Přidat
        </button>
      </div>
    </div>
  </div>
  <div class="card-body pt-3">
    
    {{-- Active timer display --}}
    <div id="activeTimerContainer" class="d-none mb-3 p-3 bg-light rounded border-2 border-primary">
      <div class="text-center">
        <p class="text-xs text-secondary mb-2">BĚŽÍCÍ ČÁSOMÍRA</p>
        <h3 class="text-primary font-weight-bold" id="liveTimer">00:00:00</h3>
        <small class="text-secondary">Spuštěno v <span id="startTime">--:--</span></small>
      </div>
    </div>

    @if($task->timeEntries->count())
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr>
              <th class="text-xs fw-bold">Začátek</th>
              <th class="text-xs fw-bold">Konec</th>
              <th class="text-xs fw-bold">Trvání</th>
              <th class="text-xs fw-bold">Poznámka</th>
              <th class="text-xs fw-bold text-center" style="width: 80px;">Akce</th>
            </tr>
          </thead>
          <tbody>
            @foreach($task->timeEntries as $entry)
              <tr class="align-middle">
                <td class="text-xs">
                  <strong>{{ $entry->started_at->format('d.m.') }}</strong>
                  <span class="text-secondary">{{ $entry->started_at->format('H:i') }}</span>
                </td>
                <td class="text-xs">
                  @if($entry->ended_at)
                    <span class="text-secondary">{{ $entry->ended_at->format('H:i') }}</span>
                  @else
                    <span class="badge bg-warning">Probíhá</span>
                  @endif
                </td>
                <td class="text-xs">
                  <strong class="text-primary">{{ $entry->duration_formatted }}</strong>
                </td>
                <td class="text-xs text-secondary">
                  {{ $entry->notes ? Str::limit($entry->notes, 40) : '—' }}
                </td>
                <td class="text-xs text-center">
                  <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-secondary btn-xs p-1" onclick="editTimeEntry(this)" 
                      data-entry-id="{{ $entry->id }}"
                      data-started-at="{{ $entry->started_at->format('Y-m-d\TH:i') }}"
                      data-ended-at="{{ $entry->ended_at ? $entry->ended_at->format('Y-m-d\TH:i') : '' }}"
                      data-notes="{{ $entry->notes }}" title="Upravit">
                      <i class="fas fa-edit fa-sm"></i>
                    </button>
                    <form method="POST" action="{{ route('tasks.time-entries.destroy', [$task, $entry]) }}" style="display:inline;" onsubmit="return confirm('Smazat záznam?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger btn-xs p-1" title="Smazat">
                        <i class="fas fa-trash fa-sm"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <hr class="my-3">
      <div class="row g-3">
        <div class="col-6">
          <div class="text-center p-3 bg-light rounded">
            <p class="text-xs text-secondary mb-2 font-weight-bold">CELKOVÝ ČAS</p>
            <h5 class="text-primary">
              @php
                $totalMinutes = $task->timeEntries->sum(fn($e) => $e->getDurationInMinutesAttribute() ?? 0);
                $hours = intval($totalMinutes / 60);
                $mins = $totalMinutes % 60;
              @endphp
              {{ $hours }}h {{ $mins }}m
            </h5>
          </div>
        </div>
        <div class="col-6">
          <div class="text-center p-3 bg-light rounded">
            <p class="text-xs text-secondary mb-2 font-weight-bold">ZÁZNAMŮ</p>
            <h5 class="text-primary">{{ $task->timeEntries->count() }}</h5>
          </div>
        </div>
      </div>
    @else
      <div class="text-center py-4">
        <i class="fas fa-hourglass-end fa-3x text-secondary opacity-25 mb-3" style="display: block;"></i>
        <p class="text-sm text-secondary">Žádné záznamy o čase.</p>
      </div>
    @endif
  </div>
</div>

{{-- Stop tracking modal --}}
<div class="modal fade" id="stopTrackingModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Potvrzení zastavení</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-center p-3 bg-light rounded mb-3">
          <p class="text-xs text-secondary mb-2">ČAS TRVÁNÍ</p>
          <h4 class="text-primary" id="stopModal_duration">00:00</h4>
        </div>
        <form id="stopTrackingForm" method="POST" action="{{ route('tasks.time-entries.store', $task) }}">
          @csrf
          <input type="hidden" name="started_at" id="stopModal_startedAt">
          <input type="hidden" name="ended_at" id="stopModal_endedAt">
          <div class="mb-3">
            <label class="form-label">Poznámka <small class="text-secondary">(volitelné)</small></label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Přidejte poznámku k času…"></textarea>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Zrušit</button>
            <button type="submit" class="btn btn-primary flex-grow-1">Uložit čas</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let trackingState = {
  isRunning: false,
  startTime: null,
  intervals: [],
  taskId: {{ $task->id }},
  localStorage: {
    key: `tracking_task_${{{ $task->id }}}`,
    
    save(data) {
      localStorage.setItem(this.key, JSON.stringify(data));
    },
    
    load() {
      const saved = localStorage.getItem(this.key);
      return saved ? JSON.parse(saved) : null;
    },
    
    clear() {
      localStorage.removeItem(this.key);
    }
  }
};

// Initialize tracking from localStorage
function initTracking() {
  const savedData = trackingState.localStorage.load();
  if (savedData && savedData.startTime) {
    trackingState.startTime = new Date(savedData.startTime);
    trackingState.isRunning = true;
    updateUI();
    startTimer();
  }
}

function updateUI() {
  const startBtn = document.getElementById('startTrackingBtn');
  const stopBtn = document.getElementById('stopTrackingBtn');
  const timerContainer = document.getElementById('activeTimerContainer');
  
  if (trackingState.isRunning) {
    startBtn.classList.add('d-none');
    stopBtn.classList.remove('d-none');
    timerContainer.classList.remove('d-none');
    updateStartTime();
  } else {
    startBtn.classList.remove('d-none');
    stopBtn.classList.add('d-none');
    timerContainer.classList.add('d-none');
  }
}

function startTracking(e) {
  e.preventDefault();
  trackingState.startTime = new Date();
  trackingState.isRunning = true;
  trackingState.localStorage.save({ startTime: trackingState.startTime });
  updateUI();
  startTimer();
}

function startTimer() {
  // Clear previous intervals
  trackingState.intervals.forEach(id => clearInterval(id));
  trackingState.intervals = [];
  
  // Update timer every 100ms
  const intervalId = setInterval(() => {
    updateTimer();
  }, 100);
  
  trackingState.intervals.push(intervalId);
  updateTimer();
}

function updateTimer() {
  if (!trackingState.isRunning) return;
  
  const now = new Date();
  const diff = now - trackingState.startTime;
  
  const hours = Math.floor(diff / 3600000);
  const mins = Math.floor((diff % 3600000) / 60000);
  const secs = Math.floor((diff % 60000) / 1000);
  
  const formatted = `${String(hours).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
  document.getElementById('liveTimer').textContent = formatted;
}

function updateStartTime() {
  if (trackingState.startTime) {
    const hours = String(trackingState.startTime.getHours()).padStart(2, '0');
    const mins = String(trackingState.startTime.getMinutes()).padStart(2, '0');
    document.getElementById('startTime').textContent = `${hours}:${mins}`;
  }
}

function stopTracking(e) {
  e.preventDefault();
  if (!trackingState.isRunning) return;
  
  const now = new Date();
  const diff = now - trackingState.startTime;
  
  const hours = Math.floor(diff / 3600000);
  const mins = Math.floor((diff % 3600000) / 60000);
  
  // Update modal
  document.getElementById('stopModal_duration').textContent = `${hours}h ${mins}m`;
  document.getElementById('stopModal_startedAt').value = trackingState.startTime.toISOString().slice(0, 16);
  document.getElementById('stopModal_endedAt').value = now.toISOString().slice(0, 16);
  
  // Stop timer
  trackingState.intervals.forEach(id => clearInterval(id));
  trackingState.intervals = [];
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('stopTrackingModal'));
  modal.show();
  
  // Handle modal close
  document.getElementById('stopTrackingModal').addEventListener('hidden.bs.modal', function() {
    trackingState.isRunning = false;
    trackingState.startTime = null;
    trackingState.localStorage.clear();
    updateUI();
  }, { once: true });
}

function editTimeEntry(btn) {
  const entryId = btn.dataset.entryId;
  const startedAt = btn.dataset.startedAt;
  const endedAt = btn.dataset.endedAt;
  const notes = btn.dataset.notes;
  
  const form = document.querySelector('#timeEntryModal form');
  form.action = `/tasks/{{ $task->id }}/time-entries/${entryId}`;
  
  let methodInput = form.querySelector('input[name="_method"]');
  if (!methodInput) {
    methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    form.appendChild(methodInput);
  }
  methodInput.value = 'PUT';
  
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
