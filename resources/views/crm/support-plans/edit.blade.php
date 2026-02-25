@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h5 class="mb-0">Upravit: {{ $supportPlan->title }}</h5>
      </div>
      <div class="card-body">
        @include('crm.partials.errors')
        <form method="POST" action="{{ route('support-plans.update', $supportPlan) }}">
          @csrf @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Klient <span class="text-danger">*</span></label>
              <select name="client_id" class="form-select" required>
                <option value="">— Vyberte klienta —</option>
                @foreach($clients as $c)
                  <option value="{{ $c->id }}" {{ old('client_id', $supportPlan->client_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Název <span class="text-danger">*</span></label>
              <input type="text" name="title" value="{{ old('title', $supportPlan->title) }}" class="form-control" required>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Cena <span class="text-danger">*</span></label>
              <input type="number" name="price" value="{{ old('price', $supportPlan->price) }}" step="0.01" min="0" class="form-control" required>
            </div>
            <div class="col-md-2 mb-3">
              <label class="form-label">Měna</label>
              <input type="text" name="currency" value="{{ old('currency', $supportPlan->currency) }}" class="form-control" maxlength="3" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stav</label>
              <select name="status" class="form-select" required>
                @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
                @foreach(\App\Models\SupportPlan::STATUSES as $s)
                  <option value="{{ $s }}" {{ old('status', $supportPlan->status) === $s ? 'selected' : '' }}>{{ $spStatusLabels[$s] ?? ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Od <span class="text-danger">*</span></label>
              <input type="date" name="period_from" value="{{ old('period_from', $supportPlan->period_from->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Do <span class="text-danger">*</span></label>
              <input type="date" name="period_to" value="{{ old('period_to', $supportPlan->period_to->format('Y-m-d')) }}" class="form-control" required>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Poznámky</label>
              <textarea name="notes" rows="3" class="form-control">{{ old('notes', $supportPlan->notes) }}</textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('support-plans.show', $supportPlan) }}" class="btn btn-outline-secondary btn-sm">Zrušit</a>
            <button type="submit" class="btn bg-gradient-primary btn-sm">Uložit změny</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

