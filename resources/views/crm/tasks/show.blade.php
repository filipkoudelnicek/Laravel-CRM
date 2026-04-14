@extends('layouts.user_type.auth')

@section('content')
@php
  $sc = ['todo'=>'secondary','in_progress'=>'primary','review'=>'warning','done'=>'success'];
  $pc = ['low'=>'info','medium'=>'warning','high'=>'danger'];
@endphp

<div class="row">
  {{-- Main content: Title + Description + Comments --}}
  <div class="col-lg-8">
    {{-- Title & Description Card --}}
    <div class="card mb-4">
      <div class="card-header pb-0 pt-4 border-0">
        <div class="d-flex justify-content-between align-items-start">
          <div style="flex: 1;">
            <h4 class="mb-3">{{ $task->title }}</h4>
            <div class="d-flex gap-3 mb-3">
              <span class="badge bg-gradient-{{ $sc[$task->status] ?? 'secondary' }}">
                {{ ['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'][$task->status] ?? $task->status }}
              </span>
              <span class="badge bg-gradient-{{ $pc[$task->priority] ?? 'secondary' }}">
                {{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] ?? $task->priority }}
              </span>
              @if($task->starts_at)
                <small class="text-secondary align-self-center">
                  <i class="fas fa-play fa-xs me-1"></i>{{ $task->starts_at->format('d.m.Y') }}
                </small>
              @endif
              @if($task->due_at)
                <small class="text-secondary align-self-center">
                  <i class="fas fa-flag-checkered fa-xs me-1"></i>{{ $task->due_at->format('d.m.Y') }}
                </small>
              @endif
            </div>
          </div>
          <div class="btn-group btn-group-sm" role="group">
            @can('update', $task)
              <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary" title="Upravit">
                <i class="fas fa-edit"></i>
              </a>
            @endcan
            @can('delete', $task)
              <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-inline" onsubmit="return confirm('Smazat úkol?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger" title="Smazat" type="submit">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            @endcan
          </div>
        </div>
      </div>
      @if($task->description)
      <div class="card-body pt-2">
        <div class="p-3 bg-light rounded rich-content">
          {!! $task->description !!}
        </div>
      </div>
      @endif

      <div class="card-body pt-0">
        <h6 class="text-sm mb-2">
          <i class="fas fa-paperclip me-1 opacity-75"></i>Přílohy ({{ $task->attachments->count() }})
        </h6>

        @if($task->attachments->count())
          <div class="row g-2">
            @foreach($task->attachments as $attachment)
              <div class="col-md-6">
                <div class="border rounded p-2 d-flex align-items-center justify-content-between gap-2">
                  <div class="d-flex align-items-center gap-2" style="min-width:0;">
                    @if($attachment->isImage())
                      <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer">
                        <img src="{{ $attachment->url() }}" alt="{{ $attachment->original_name }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
                      </a>
                    @else
                      <i class="fas fa-file-alt text-secondary"></i>
                    @endif
                    <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer" class="text-sm text-truncate d-inline-block" style="max-width:220px;">
                      {{ $attachment->original_name }}
                    </a>
                  </div>
                  @can('update', $task)
                    <form method="POST" action="{{ route('tasks.attachments.destroy', [$task, $attachment]) }}" onsubmit="return confirm('Smazat přílohu?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-link text-danger p-0" type="submit" title="Smazat">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  @endcan
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-sm text-secondary mb-0">Žádné přílohy.</p>
        @endif
      </div>
    </div>

    {{-- Comments --}}
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h6 class="mb-0">
          <i class="fas fa-comments me-2 text-primary opacity-75"></i>Komentáře ({{ $commentCount }})
        </h6>
        <small class="text-secondary">Použijte @uživatelské_jméno pro zmínku</small>
      </div>
      <div class="card-body">

        {{-- New top-level comment --}}
        <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="mb-4" enctype="multipart/form-data">
          @csrf
          <div class="mb-2">
            <textarea name="body" rows="3" class="form-control rich-editor-source" placeholder="Napište komentář… použijte @jméno pro zmínku"
              required>{{ old('body') }}</textarea>
            @error('body') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
          </div>
          <div class="mb-2">
            <input type="file" name="attachments[]" class="form-control form-control-sm" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
          </div>
          <button class="btn bg-gradient-primary btn-sm">Přidat komentář</button>
        </form>

        {{-- Threaded comments --}}
        @forelse($task->comments as $comment)
          @include('crm.tasks._comment', ['comment' => $comment, 'depth' => 0])
        @empty
          <p class="text-sm text-secondary">Žádné komentáře.</p>
        @endforelse

      </div>
    </div>
  </div>

  {{-- Sidebar: Assignees + Time Tracking --}}
  <div class="col-lg-4">
    {{-- Assignees --}}
    <div class="card mb-3">
      <div class="card-header pb-3 pt-3">
        <h6 class="mb-0 text-sm">
          <i class="fas fa-user me-2 text-primary opacity-75"></i>Přiřazeno
        </h6>
      </div>
      <div class="card-body pt-2 pb-2">
        @if($task->assignees->count())
          @foreach($task->assignees as $assignee)
            <span class="badge bg-light text-dark d-block mb-2">{{ $assignee->name }}</span>
          @endforeach
        @else
          <small class="text-secondary">Nikdo</small>
        @endif
      </div>
    </div>

    {{-- Project & Client --}}
    <div class="card mb-3">
      <div class="card-body p-3">
        <div class="mb-3">
          <small class="text-secondary d-block mb-1 fw-bold">PROJEKT</small>
          <a href="{{ route('projects.show', $task->project) }}" class="text-xs">
            {{ $task->project->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
          </a>
        </div>
        <div class="mb-0">
          <small class="text-secondary d-block mb-1 fw-bold">KLIENT</small>
          <a href="{{ route('clients.show', $task->project->client) }}" class="text-xs">
            {{ $task->project->client->name }} <i class="fas fa-external-link-alt fa-xs ms-1"></i>
          </a>
        </div>
      </div>
    </div>

    {{-- Full Time Tracking Component --}}
    @include('crm.tasks._time_entries', ['task' => $task])
  </div>
</div>

@include('crm.tasks._time_entry_modal', $task)
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style>
.rich-content ul,
.rich-content ol { margin-bottom: 0.5rem; padding-left: 1.25rem; }
.rich-content p:last-child { margin-bottom: 0; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var editors = new Map();

  function initEditor(textarea) {
    if (!textarea || editors.has(textarea)) {
      return;
    }

    ClassicEditor.create(textarea, {
      toolbar: ['undo', 'redo', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
    }).then(function (editor) {
      editors.set(textarea, editor);
    }).catch(function () {});
  }

  // Initialize visible editors immediately
  document.querySelectorAll('textarea.rich-editor-source').forEach(function (textarea) {
    if (textarea.offsetParent !== null) {
      initEditor(textarea);
    }
  });

  // Initialize hidden editors lazily when user focuses them
  document.addEventListener('focusin', function (event) {
    var target = event.target;
    if (target && target.matches('textarea.rich-editor-source')) {
      initEditor(target);
    }
  });

  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function () {
      editors.forEach(function (editor) {
        editor.updateSourceElement();
      });
    });
  });
});

function toggleCommentReplies(trigger) {
  if (!trigger) return;

  var commentId = trigger.getAttribute('data-comment-id');
  if (!commentId) return;

  var replies = document.getElementById('replies-' + commentId);
  if (!replies) return;

  var hidden = replies.classList.contains('d-none');
  replies.classList.toggle('d-none');

  var countMatch = trigger.textContent.match(/\((\d+)\)/);
  var countLabel = countMatch ? countMatch[1] : '';

  if (hidden) {
    trigger.textContent = countLabel ? 'Skrýt odpovědi (' + countLabel + ')' : 'Skrýt odpovědi';
  } else {
    trigger.textContent = countLabel ? 'Zobrazit odpovědi (' + countLabel + ')' : 'Zobrazit odpovědi';
  }
}
</script>
@endpush


