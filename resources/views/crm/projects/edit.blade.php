@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h5 class="mb-0">Upravit projekt: {{ $project->name }}</h5>
      </div>
      <div class="card-body">
        @include('crm.partials.errors')
        <form method="POST" action="{{ route('projects.update', $project) }}">
          @csrf @method('PUT')
          <div class="row">
            <div class="col-md-8 mb-3">
              <label class="form-label">Název projektu <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name', $project->name) }}" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Stav <span class="text-danger">*</span></label>
              <select name="status" class="form-select" required>
                @foreach(['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'] as $s => $label)
                  <option value="{{ $s }}" @selected(old('status', $project->status) === $s)>{{ $label }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Klient <span class="text-danger">*</span></label>
              <select name="client_id" class="form-select" required>
                @foreach($clients as $client)
                  <option value="{{ $client->id }}" @selected(old('client_id', $project->client_id) == $client->id)>{{ $client->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Termín</label>
              <input type="date" name="due_date" value="{{ old('due_date', $project->due_date?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Popis</label>
              <textarea name="description" rows="4" class="form-control">{{ old('description', $project->description) }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Odhadované náklady (Kč)</label>
              <input type="number" step="0.01" min="0" name="estimated_cost" value="{{ old('estimated_cost', $project->estimated_cost) }}" class="form-control" placeholder="0.00">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Hodinová sazba (Kč)</label>
              <input type="number" step="0.01" min="0" name="hourly_rate" value="{{ old('hourly_rate', $project->hourly_rate) }}" class="form-control" placeholder="0.00">
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">Zrušit</a>
            <button type="submit" class="btn bg-gradient-primary btn-sm">Uložit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

