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
        <table class="table table-sm">
          <tbody>
            @foreach($task->timeEntries as $entry)
              <tr class="align-middle" style="cursor: pointer;" onclick="editTimeEntry(this)" 
                data-entry-id="{{ $entry->id }}"
                data-started-at="{{ $entry->started_at->format('Y-m-d\TH:i') }}"
                data-ended-at="{{ $entry->ended_at ? $entry->ended_at->format('Y-m-d\TH:i') : '' }}"
                data-notes="{{ $entry->notes }}"
                data-created-by="{{ $entry->createdBy?->name ?? 'Neznámý' }}">
                <td class="text-xs">
                  <strong>{{ $entry->started_at->format('d.m.') }}</strong>
                  <span class="text-secondary">{{ $entry->started_at->format('H:i') }}</span>
                </td>
                <td class="text-xs text-end">
                  <strong class="text-primary">{{ $entry->duration_formatted }}</strong>
                </td>
                <td class="text-xs text-end" style="width: 60px;">
                  <small class="text-secondary">{{ $entry->createdBy?->name ?? 'Neznámý' }}</small>
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
        <form id="stopTrackingForm" method="POST" action="{{ route('tasks.time-entries.store', $task) }}" onsubmit="handleStopTrackingSubmit()">
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
  if (!trackingState.isRunning || !trackingState.startTime) return;
  
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

function resetTracking() {
  // Stop all timers
  trackingState.intervals.forEach(id => clearInterval(id));
  trackingState.intervals = [];
  
  // Reset state
  trackingState.isRunning = false;
  trackingState.startTime = null;
  trackingState.localStorage.clear();
  
  // Update UI
  updateUI();
}

function stopTracking(e) {
  e.preventDefault();
  if (!trackingState.isRunning) return;
  
  const now = new Date();
  const diff = now - trackingState.startTime;
  
  const hours = Math.floor(diff / 3600000);
  const mins = Math.floor((diff % 3600000) / 60000);
  
  // Update modal with current time
  document.getElementById('stopModal_duration').textContent = `${hours}h ${mins}m`;
  document.getElementById('stopModal_startedAt').value = trackingState.startTime.toISOString().slice(0, 16);
  document.getElementById('stopModal_endedAt').value = now.toISOString().slice(0, 16);
  
  // Stop the timer during modal display
  resetTracking();
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('stopTrackingModal'));
  modal.show();
}

function editTimeEntry(btn) {
  const entryId = btn.dataset.entryId;
  const startedAt = btn.dataset.startedAt;
  const endedAt = btn.dataset.endedAt;
  const notes = btn.dataset.notes;
  const createdBy = btn.dataset.createdBy || 'Neznámý';
  
  const form = document.getElementById('timeEntryForm');
  const deleteForm = document.getElementById('deleteTimeEntryForm');
  const deleteBtn = document.getElementById('deleteTimeEntryBtn');
  form.action = `/tasks/{{ $task->id }}/time-entries/${entryId}`;
  
  let methodInput = form.querySelector('input[name="_method"]');
  if (!methodInput) {
    methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    form.appendChild(methodInput);
  }
  methodInput.value = 'PUT';
  
  form.querySelector('input[name="started_at"]').value = startedAt || '';
  form.querySelector('input[name="ended_at"]').value = endedAt || '';
  form.querySelector('textarea[name="notes"]').value = notes || '';

  // Configure delete action for current entry
  if (deleteForm && deleteBtn) {
    deleteForm.action = `/tasks/{{ $task->id }}/time-entries/${entryId}`;
    deleteBtn.classList.remove('d-none');
  }
  
  // Update modal title and user info
  document.querySelector('#timeEntryModal .modal-title').textContent = 'Upravit čas';
  document.querySelector('#timeEntryModal .modal-body .user-info').textContent = `Zaznamenáno: ${createdBy}`;
  document.querySelector('#timeEntryModal button[type="submit"]').textContent = 'Uložit';
  
  // Show modal
  const modal = new bootstrap.Modal(document.getElementById('timeEntryModal'));
  modal.show();
}

// Reset modal on hide
document.getElementById('timeEntryModal')?.addEventListener('hidden.bs.modal', function() {
  const form = document.getElementById('timeEntryForm');
  const deleteForm = document.getElementById('deleteTimeEntryForm');
  const deleteBtn = document.getElementById('deleteTimeEntryBtn');
  const taskId = {{ $task->id }};
  form.action = `/tasks/${taskId}/time-entries`;
  form.method = 'POST';
  
  const methodInput = form.querySelector('input[name="_method"]');
  if (methodInput) methodInput.remove();
  
  form.reset();
  document.querySelector('#timeEntryModal .modal-title').textContent = 'Přidat čas';
  document.querySelector('#timeEntryModal .user-info').textContent = '';
  document.querySelector('#timeEntryModal button[type="submit"]').textContent = 'Přidat';

  if (deleteBtn) {
    deleteBtn.classList.add('d-none');
  }
  if (deleteForm) {
    deleteForm.action = `/tasks/${taskId}/time-entries`;
  }
});

function submitDeleteTimeEntry() {
  const deleteForm = document.getElementById('deleteTimeEntryForm');
  if (!deleteForm) return;

  if (confirm('Opravdu odstranit tento záznam času?')) {
    deleteForm.submit();
  }
}

// Show notification helper
function showNotification(message, type = 'info') {
  const alertClass = {
    'success': 'alert-success',
    'error': 'alert-danger',
    'warning': 'alert-warning',
    'info': 'alert-info'
  }[type] || 'alert-info';
  
  const alertHtml = `
    <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  `;
  
  document.body.insertAdjacentHTML('beforeend', alertHtml);
  
  // Auto-remove after 3 seconds
  const alertEl = document.querySelector('.position-fixed.alert');
  if (alertEl) {
    setTimeout(() => alertEl.remove(), 3000);
  }
}

// Handle form submission - normal POST, will reload page
function handleStopTrackingSubmit(e) {
  // Allow normal form submission - server will handle it and reload
  return true;
}

// Reset modal button action
document.getElementById('stopTrackingModal')?.addEventListener('hidden.bs.modal', function() {
  // Clear form
  const form = document.getElementById('stopTrackingForm');
  form.reset();
  document.querySelector('textarea[name="notes"]').value = '';
});

// Initialize tracking when page loads
document.addEventListener('DOMContentLoaded', function() {
  initTracking();
});

// Also try to initialize immediately in case DOMContentLoaded already fired
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initTracking);
} else {
  initTracking();
}
</script>
@endpush
