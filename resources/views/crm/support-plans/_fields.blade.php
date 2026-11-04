@php
  $from = $renewal ? $renewal->period_to->copy()->addDay() : ($source?->period_from ?? today());
  $to = $renewal ? $from->copy()->addYearNoOverflow()->subDay() : ($source?->period_to ?? today()->addYearNoOverflow()->subDay());
@endphp
<div class="row">
  <div class="col-md-6"><x-form-field name="client_id" label="Klient" type="select" required><option value="">Vyberte klienta</option>@foreach($clients as $client)<option value="{{ $client->id }}" @selected(old('client_id', $selectedClient?->id) == $client->id)>{{ $client->name }}</option>@endforeach</x-form-field></div>
  <div class="col-md-6"><x-form-field name="project_id" label="Projekt (volitelně)" type="select"><option value="">Služba pro klienta bez konkrétního projektu</option>@foreach($projects as $project)<option value="{{ $project->id }}" data-client="{{ $project->client_id }}" @selected(old('project_id', $source?->project_id) == $project->id)>{{ $project->name }}</option>@endforeach</x-form-field></div>
</div>
<x-form-field name="title" label="Služba" :value="old('title', $source?->title)" placeholder="Například hosting webu nebo měsíční správa" required />
<div class="row">
  <div class="col-md-6"><x-form-field name="period_from" label="Období od" type="date" :value="old('period_from', $from->format('Y-m-d'))" required /></div>
  <div class="col-md-6"><x-form-field name="period_to" label="Období do (včetně)" type="date" :value="old('period_to', $to->format('Y-m-d'))" required /></div>
</div>
<div class="row">
  <div class="col-md-4"><x-form-field name="price" label="Cena za období" type="number" min="0" step="0.01" :value="old('price', $source?->price)" required /></div>
  <div class="col-md-3"><x-form-field name="currency" label="Měna" :value="old('currency', $source?->currency ?? 'CZK')" maxlength="3" required /></div>
  <div class="col-md-5"><x-form-field name="paid_at" label="Datum úhrady" type="date" :value="old('paid_at', !$renewal ? $source?->paid_at?->format('Y-m-d') : null)" /></div>
</div>
<p class="text-sm text-secondary">Datum úhrady vyplňte po zaplacení. Prázdné datum znamená, že úhrada zatím není evidovaná.</p>
<x-form-field name="status" label="Stav služby" type="select" required>@foreach(['active'=>'Aktivní / naplánovaná','expired'=>'Ukončená','cancelled'=>'Zrušená'] as $key=>$label)<option value="{{ $key }}" @selected(old('status', $renewal ? 'active' : ($source?->status ?? 'active')) === $key)>{{ $label }}</option>@endforeach</x-form-field>
<x-form-field name="notes" label="Poznámky" type="textarea" rows="3" :value="old('notes', $source?->notes)" />
