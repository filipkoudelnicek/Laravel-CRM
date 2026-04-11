@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Upravit: {{ $supportPlan->title }}" 
  submitText="Uložit změny"
  backUrl="{{ route('support-plans.show', $supportPlan) }}">
  
  <form method="POST" action="{{ route('support-plans.update', $supportPlan) }}">
    @csrf @method('PUT')

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="client_id" label="Klient" type="select" required>
          <option value="">— Vyberte klienta —</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(old('client_id', $supportPlan->client_id) == $c->id)>{{ $c->name }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-6">
        <x-form-field name="title" label="Název" value="{{ old('title', $supportPlan->title) }}" required />
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <x-form-field name="price" label="Cena" type="number" step="0.01" min="0" value="{{ old('price', $supportPlan->price) }}" required />
      </div>
      <div class="col-md-2">
        <x-form-field name="currency" label="Měna" value="{{ old('currency', $supportPlan->currency) }}" maxlength="3" required />
      </div>
      <div class="col-md-3">
        <x-form-field name="status" label="Stav" type="select" required>
          @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
          @foreach(\App\Models\SupportPlan::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', $supportPlan->status) === $s)>{{ $spStatusLabels[$s] ?? ucfirst($s) }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="period_from" label="Od" type="date" value="{{ old('period_from', $supportPlan->period_from->format('Y-m-d')) }}" required />
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <x-form-field name="period_to" label="Do" type="date" value="{{ old('period_to', $supportPlan->period_to->format('Y-m-d')) }}" required />
      </div>
    </div>

    <x-form-field name="notes" label="Poznámky" type="textarea" rows="3" value="{{ old('notes', $supportPlan->notes) }}" />

    <x-form-actions submitText="Uložit změny" backUrl="{{ route('support-plans.show', $supportPlan) }}" />
  </form>
</x-form-layout>
@endsection

