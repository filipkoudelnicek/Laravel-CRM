@extends('layouts.user_type.auth')
@section('title', 'Faktury klientů')
@section('content')
<div class="workspace-heading">
  <div><h1>Faktury klientů</h1><p class="text-secondary mb-0">Vystavené doklady, splatnosti a úhrady.</p></div>
  @can('create', \App\Models\Invoice::class)<a href="{{ route('invoices.create', request()->only('client_id')) }}" class="btn btn-primary">+ Nová faktura</a>@endcan
</div>
@if($totalsByCurrency->isNotEmpty())
<section class="card work-panel mb-4" aria-label="Souhrn faktur podle měny">
  @foreach($totalsByCurrency as $currency=>$totals)
  <div class="work-row"><strong>{{ $currency }}</strong><span class="row-content"><small>K úhradě</small><strong>{{ number_format($totals['outstanding'],2,',',' ') }} {{ $currency }}</strong></span><span class="row-meta"><small>Uhrazeno tento měsíc</small><strong>{{ number_format($totals['paidMonth'],2,',',' ') }} {{ $currency }}</strong></span></div>
  @endforeach
</section>
@endif
<section class="list-panel">
<form method="GET" class="list-toolbar"><x-search-field placeholder="Číslo faktury nebo klient…" /><select name="client_id" class="form-select" aria-label="Klient"><option value="">Všichni klienti</option>@foreach($clients as $client)<option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->name }}</option>@endforeach</select><select name="status" class="form-select" aria-label="Stav faktury"><option value="">Všechny stavy</option>@foreach(['outstanding'=>'K úhradě','draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'] as $key=>$label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select><select class="form-select" name="currency" aria-label="Měna faktury"><option value="">Všechny měny</option>@foreach($currencies as $unit)<option value="{{ $unit }}" @selected(request('currency') === $unit)>{{ $unit }}</option>@endforeach</select><button class="btn btn-outline-secondary">Použít</button>@if(request()->query())<a href="{{ route('invoices.index') }}" class="btn btn-link">Zrušit filtry</a>@endif</form>
<table class="data-table" aria-label="Seznam faktur"><thead><tr><th class="primary-cell">Faktura / klient</th><th class="hide-mobile" style="width:140px">Splatnost</th><th class="status-cell" style="width:130px">Stav</th><th class="hide-mobile text-end" style="width:170px">Celkem</th><th class="action-cell"><span class="visually-hidden">Akce</span></th></tr></thead><tbody>
@forelse($invoices as $invoice)<tr><td class="record-cell"><a class="record-link" href="{{ route('invoices.show',$invoice) }}">{{ $invoice->invoice_number }}</a><span class="record-secondary">{{ $invoice->client->name }}@if($invoice->project) / {{ $invoice->project->name }}@endif</span></td><td class="hide-mobile muted-cell numeric">{{ $invoice->due_at?->format('j. n. Y') ?? '—' }}</td><td class="status-cell"><span class="status-chip" data-status="{{ $invoice->status }}">{{ ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'][$invoice->status] }}</span></td><td class="hide-mobile text-end numeric">{{ number_format($invoice->total,2,',',' ') }} {{ $invoice->currency }}</td><td class="action-cell"><x-record-actions :record="$invoice" resource="invoices" :label="$invoice->invoice_number" /></td></tr>
@empty<tr><td colspan="5"><div class="empty-work"><h3>Žádné odpovídající faktury</h3><p>Vytvořte doklad pro klienta nebo upravte filtry.</p></div></td></tr>@endforelse
</tbody></table><x-list-footer :items="$invoices" /></section>
@endsection
