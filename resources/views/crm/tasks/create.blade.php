@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Nový úkol" 
  submitText="Vytvořit úkol"
  backUrl="{{ route('tasks.index') }}">
  
  <form method="POST" action="{{ route('tasks.store') }}">
    @csrf

    <x-form-field name="title" label="Název" placeholder="Např. Implementovat login" required />

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="project_id" label="Projekt" type="select" required>
          <option value="">— Vyberte projekt —</option>
          @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected(old('project_id', $selectedProject?->id) == $project->id)>
              {{ $project->name }} <small class="text-muted">({{ $project->client->name }})</small>
            </option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="status" label="Stav" type="select" required>
          @foreach(['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'] as $s => $label)
            <option value="{{ $s }}" @selected(old('status','todo') === $s)>{{ $label }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="priority" label="Priorita" type="select" required>
          @foreach(['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'] as $p => $label)
            <option value="{{ $p }}" @selected(old('priority','medium') === $p)>{{ $label }}</option>
          @endforeach
        </x-form-field>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="due_date" label="Termín" type="date" />
      </div>
      <div class="col-md-6">
        <x-form-field name="assignees[]" label="Přiřazení" type="select">
          @foreach($allUsers as $u)
            <option value="{{ $u->id }}" @selected(in_array($u->id, old('assignees', [])))>{{ $u->name }}</option>
          @endforeach
        </x-form-field>
        <small class="text-muted">Držte Ctrl / Cmd pro vícenásobný výběr</small>
      </div>
    </div>

    <x-form-field name="description" label="Popis" type="textarea" rows="4" />

    <x-form-actions submitText="Vytvořit úkol" backUrl="{{ route('tasks.index') }}" />
  </form>
</x-form-layout>
@endsection