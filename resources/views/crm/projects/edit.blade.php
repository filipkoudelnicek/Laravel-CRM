@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Upravit projekt: {{ $project->name }}" 
  submitText="Uložit změny"
  backUrl="{{ route('projects.show', $project) }}">
  
  <form method="POST" action="{{ route('projects.update', $project) }}">
    @csrf @method('PUT')

    <div class="row">
      <div class="col-md-8">
        <x-form-field name="name" label="Název projektu" value="{{ old('name', $project->name) }}" required />
      </div>
      <div class="col-md-4">
        <x-form-field name="status" label="Stav" type="select" required>
          @foreach(['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'] as $s => $label)
            <option value="{{ $s }}" @selected(old('status', $project->status) === $s)>{{ $label }}</option>
          @endforeach
        </x-form-field>
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="client_id" label="Klient" type="select" required>
          @foreach($clients as $client)
            <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id) == $client->id)>
              {{ $client->name }}
            </option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-6">
        <x-form-field name="due_date" label="Termín" type="date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}" />
      </div>
    </div>

    <x-form-field name="description" label="Popis" type="textarea" rows="4" value="{{ old('description', $project->description) }}" />

    <x-form-field name="web_url" label="Web URL" type="url" value="{{ old('web_url', $project->web_url) }}" placeholder="https://example.com" />

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="estimated_cost" label="Odhadované náklady (Kč)" type="number" step="0.01" min="0" value="{{ old('estimated_cost', $project->estimated_cost) }}" placeholder="0.00" />
      </div>
      <div class="col-md-6">
        <x-form-field name="hourly_rate" label="Hodinová sazba (Kč)" type="number" step="0.01" min="0" value="{{ old('hourly_rate', $project->hourly_rate) }}" placeholder="0.00" />
      </div>
    </div>

    <x-form-actions submitText="Uložit změny" backUrl="{{ route('projects.show', $project) }}" />
  </form>
</x-form-layout>
@endsection

