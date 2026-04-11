@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Nový plán podpory" 
  submitText="Vytvořit"
  backUrl="{{ route('support-plans.index') }}">
  
  <form method="POST" action="{{ route('support-plans.store') }}">
    @csrf

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="client_id" label="Klient" type="select" required>
          <option value="">— Vyberte klienta —</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(old('client_id', $selectedClient?->id) == $c->id)>{{ $c->name }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-6">
        <x-form-field name="title" label="Název" value="{{ old('title') }}" required placeholder="Např. Podpora webu" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
        <x-form-field name="price" label="Cena" type="number" step="0.01" min="0" value="{{ old('price') }}" required />
      </div>
      <div class="col-md-2">
        <x-form-field name="currency" label="Měna" value="{{ old('currency', 'CZK') }}" maxlength="3" required />
      </div>
      <div class="col-md-3">
        <x-form-field name="status" label="Stav" type="select" required>
          @php $spStatusLabels = ['active'=>'Aktivní','expired'=>'Vypršelo','cancelled'=>'Zrušeno']; @endphp
          @foreach(\App\Models\SupportPlan::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', 'active') === $s)>{{ $spStatusLabels[$s] ?? ucfirst($s) }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="period_from" label="Od" type="date" value="{{ old('period_from', now()->format('Y-m-d')) }}" required />
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <x-form-field name="period_to" label="Do" type="date" value="{{ old('period_to', now()->addYear()->format('Y-m-d')) }}" required />
      </div>
    </div>

    <x-form-field name="notes" label="Poznámky" type="textarea" rows="3" value="{{ old('notes') }}" />

    <x-form-actions submitText="Vytvořit" backUrl="{{ route('support-plans.index') }}" />
  </form>
</x-form-layout>
@endsection

