@extends('layouts.user_type.auth')
@section('title',$task->title)
@section('content')
@php $statusLabels=['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno']; @endphp
<div class="workspace-heading">
  <div><a class="context-line" href="{{ route('projects.show',$task->project) }}">{{ $task->project->client->name }} / {{ $task->project->name }}</a><h1>{{ $task->title }}</h1></div>
  <div class="heading-actions"><x-task-timer :task="$task" /><x-record-actions :record="$task" resource="tasks" :label="$task->title" /></div>
</div>
<div class="mobile-task-meta"><span class="status-chip" data-status="{{ $task->status }}">{{ $statusLabels[$task->status] }}</span><span>{{ $task->due_at ? 'Termín '.$task->due_at->format('j. n. Y') : 'Bez termínu' }}</span></div>
<div class="task-detail-grid">
  <div class="task-document">
    <section class="document-section" aria-label="Zadání úkolu">
      <header class="section-heading"><h2>Zadání</h2>@can('update',$task)<a href="{{ route('tasks.edit',$task) }}">Upravit zadání</a>@endcan</header>
      @if($task->description)<div class="rich-content">{!! $task->description !!}</div>@else<p class="document-placeholder">Zadání zatím není vyplněné.</p>@endif
      @if($task->attachments->isNotEmpty())
      <div class="document-attachments" aria-label="Přílohy úkolu">
        @foreach($task->attachments as $attachment)
          <div class="attachment-item"><a class="attachment-link" href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer">@if($attachment->isImage())<img src="{{ $attachment->url() }}" alt="">@endif<span>{{ $attachment->original_name }}</span></a>@can('update',$task)<form method="POST" action="{{ route('tasks.attachments.destroy',[$task,$attachment]) }}" data-confirm="Smazat přílohu „{{ $attachment->original_name }}“?">@csrf @method('DELETE')<button class="action-trigger" aria-label="Smazat přílohu {{ $attachment->original_name }}"><x-icon name="close" /></button></form>@endcan</div>
        @endforeach
      </div>
      @endif
    </section>
    <section class="document-section" aria-label="Komentáře k úkolu">
      <header class="section-heading"><h2>Komentáře <span class="text-secondary">{{ $commentCount }}</span></h2><a href="#comment-form">Napsat komentář</a></header>
      <div class="comment-list">
        @forelse($task->comments as $comment)
          @include('crm.tasks._comment',['comment'=>$comment,'depth'=>0])
        @empty
          <p class="document-placeholder">Zatím tu nejsou žádné komentáře.</p>
        @endforelse
      </div>
      <form id="comment-form" class="comment-composer" method="POST" action="{{ route('tasks.comments.store',$task) }}" enctype="multipart/form-data">
        @csrf
        <textarea name="body" rows="3" class="form-control rich-editor-source" aria-label="Nový komentář" placeholder="Napište komentář nebo domluvu s kolegou…" required>{{ old('body') }}</textarea>
        @error('body')<div class="text-danger mt-2">{{ $message }}</div>@enderror
        <details class="attachment-picker"><summary>Přidat přílohy</summary><input type="file" name="attachments[]" aria-label="Přílohy komentáře" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip"></details>
        <div class="composer-actions"><small>Pro zmínku napište @jméno.</small><button class="btn btn-primary">Přidat komentář</button></div>
      </form>
    </section>
  </div>
  <aside class="task-properties" aria-label="Podrobnosti a čas úkolu">
    <h2>Podrobnosti</h2>
    <dl class="property-list">
      <dt>Stav</dt><dd>@can('update',$task)<form method="POST" action="{{ route('tasks.status',$task) }}" data-status-form>@csrf @method('PATCH')<select name="status" class="form-select status-select" data-status="{{ $task->status }}" aria-label="Stav úkolu">@foreach($statusLabels as $key=>$label)<option value="{{ $key }}" @selected($task->status === $key)>{{ $label }}</option>@endforeach</select><noscript><button class="btn btn-sm">Uložit</button></noscript></form>@else<span class="status-chip" data-status="{{ $task->status }}">{{ $statusLabels[$task->status] }}</span>@endcan</dd>
      <dt>Priorita</dt><dd><span class="priority-{{ $task->priority }}">{{ ['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'][$task->priority] }}</span></dd>
      <dt>Řeší</dt><dd><div class="property-people">@forelse($task->assignees as $person)<span class="person-avatar" title="{{ $person->name }}">{{ mb_substr($person->name,0,1) }}</span><span>{{ $person->name }}</span>@empty<span class="text-secondary">Nepřiřazeno</span>@endforelse</div></dd>
      <dt>Termín</dt><dd class="numeric">{{ $task->due_at?->format('j. n. Y') ?? 'Bez termínu' }}</dd>
      @if($task->starts_at)<dt>Zahájení</dt><dd class="numeric">{{ $task->starts_at->format('j. n. Y') }}</dd>@endif
      <dt>Projekt</dt><dd><a href="{{ route('projects.show',$task->project) }}">{{ $task->project->name }}</a></dd>
      <dt>Klient</dt><dd><a href="{{ route('clients.show',$task->project->client) }}">{{ $task->project->client->name }}</a></dd>
    </dl>
    <div class="task-time-section">@include('crm.tasks._time_entries',['task'=>$task,'showControls'=>false])</div>
  </aside>
</div>
@include('crm.tasks._time_entry_modal',$task)
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
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


