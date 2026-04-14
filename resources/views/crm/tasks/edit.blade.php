@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Upravit úkol: {{ $task->title }}" 
  submitText="Uložit změny"
  backUrl="{{ route('tasks.show', $task) }}">
  
  <form method="POST" action="{{ route('tasks.update', $task) }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <x-form-field name="title" label="Název" value="{{ $task->title }}" required />

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="project_id" label="Projekt" type="select" required>
          @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected(old('project_id', $task->project_id) == $project->id)>
              {{ $project->name }} <small class="text-muted">({{ $project->client->name }})</small>
            </option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="status" label="Stav" type="select" required>
          @foreach(['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'] as $s => $label)
            <option value="{{ $s }}" @selected(old('status', $task->status) === $s)>{{ $label }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="priority" label="Priorita" type="select" required>
          @foreach(['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'] as $p => $label)
            <option value="{{ $p }}" @selected(old('priority', $task->priority) === $p)>{{ $label }}</option>
          @endforeach
        </x-form-field>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <x-form-field name="starts_at" label="Začátek" type="date" value="{{ $task->starts_at?->format('Y-m-d') }}" />
      </div>
      <div class="col-md-3">
        <x-form-field name="due_at" label="Termín (do)" type="date" value="{{ $task->due_at?->format('Y-m-d') }}" />
      </div>
      <div class="col-md-6">
        <x-form-field name="assignees[]" label="Přiřazení" type="select">
          @foreach($allUsers as $u)
            <option value="{{ $u->id }}"
              @selected(in_array($u->id, old('assignees', $task->assignees->pluck('id')->toArray())))>
              {{ $u->name }}
            </option>
          @endforeach
        </x-form-field>
        <small class="text-muted">Držte Ctrl / Cmd pro vícenásobný výběr</small>
      </div>
    </div>

    <x-form-field name="description" label="Popis" type="textarea" rows="4" value="{{ $task->description }}" inputClass="rich-editor-source" />

    <x-form-field name="attachments[]" label="Přidat přílohy / screenshoty" type="file" inputClass="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" />
    <small class="text-muted d-block mb-3">Max 8 souborů na upload, každý do 10 MB.</small>

    <x-form-actions submitText="Uložit změny" backUrl="{{ route('tasks.show', $task) }}" />
  </form>
</x-form-layout>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var editors = [];
  document.querySelectorAll('textarea.rich-editor-source').forEach(function (textarea) {
    ClassicEditor.create(textarea, {
      toolbar: ['undo', 'redo', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote']
    }).then(function (editor) {
      editors.push(editor);
    }).catch(function () {});
  });

  document.querySelectorAll('form').forEach(function (form) {
    form.addEventListener('submit', function () {
      editors.forEach(function (editor) {
        editor.updateSourceElement();
      });
    });
  });
});
</script>
@endpush