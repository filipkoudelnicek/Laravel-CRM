@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-9">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h5 class="mb-0">Upravit úkol: {{ $task->title }}</h5>
      </div>
      <div class="card-body">
        @include('crm.partials.errors')
        <form method="POST" action="{{ route('tasks.update', $task) }}">
          @csrf @method('PUT')
          <div class="row">
            <div class="col-12 mb-3">
              <label class="form-label">Název <span class="text-danger">*</span></label>
              <input type="text" name="title" value="{{ old('title', $task->title) }}" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Projekt <span class="text-danger">*</span></label>
              <select name="project_id" class="form-select" required>
                @foreach($projects as $project)
                  <option value="{{ $project->id }}" @selected(old('project_id', $task->project_id) == $project->id)>
                    {{ $project->name }} ({{ $project->client->name }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stav <span class="text-danger">*</span></label>
              <select name="status" class="form-select" required>
                @foreach(['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'] as $s => $label)
                  <option value="{{ $s }}" @selected(old('status', $task->status) === $s)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Priorita <span class="text-danger">*</span></label>
              <select name="priority" class="form-select" required>
                @foreach(['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'] as $p => $label)
                  <option value="{{ $p }}" @selected(old('priority', $task->priority) === $p)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Termín</label>
              <input type="date" name="due_date" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Přiřazení</label>
              <select name="assignees[]" class="form-select" multiple>
                @foreach($allUsers as $u)
                  <option value="{{ $u->id }}"
                    @selected(in_array($u->id, old('assignees', $task->assignees->pluck('id')->toArray())))>
                    {{ $u->name }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Popis</label>
              <textarea name="description" rows="4" class="form-control">{{ old('description', $task->description) }}</textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary btn-sm">Zrušit</a>
            <button type="submit" class="btn bg-gradient-primary btn-sm">Uložit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

