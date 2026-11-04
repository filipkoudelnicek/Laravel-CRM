@extends('layouts.user_type.auth')
@section('title', 'Faktury klientů')
@section('content')
<div class="workspace-heading">
  <div><h1>Faktury klientů</h1><p class="text-secondary mb-0">Doklady, jejich splatnost a úhrady. Projekt je pouze volitelné upřesnění.</p></div>
  @can('create', \App\Models\Invoice::class)<a href="{{ route('invoices.create', request()->only('client_id')) }}" class="btn btn-primary">+ Nová faktura</a>@endcan
</div>
@if($totalsByCurrency->isNotEmpty())
<section class="card work-panel mb-4" aria-label="Souhrn faktur podle měny">
  @foreach($totalsByCurrency as $currency=>$totals)
  <div class="work-row"><strong>{{ $currency }}</strong><span class="row-content"><small>K úhradě</small><strong>{{ number_format($totals['outstanding'],2,',',' ') }} {{ $currency }}</strong></span><span class="row-meta"><small>Uhrazeno tento měsíc</small><strong>{{ number_format($totals['paidMonth'],2,',',' ') }} {{ $currency }}</strong></span></div>
  @endforeach
</section>
@endif
<section class="card work-panel">
  <header class="panel-heading">
    <form method="GET" action="{{ route('invoices.index') }}" class="row g-2 w-100">
      <div class="col-md-4"><label class="visually-hidden" for="invoice-search">Hledat fakturu</label><input class="form-control" id="invoice-search" name="q" value="{{ request('q') }}" placeholder="Číslo faktury nebo klient"></div>
      <div class="col-md-3"><label class="visually-hidden" for="invoice-client">Klient</label><select class="form-select" name="client_id" id="invoice-client"><option value="">Všichni klienti</option>@foreach($clients as $client)<option value="{{ $client->id }}" @selected(request('client_id') == $client->id)>{{ $client->name }}</option>@endforeach</select></div>
      <div class="col-md-3"><label class="visually-hidden" for="invoice-status">Stav faktury</label><select class="form-select" name="status" id="invoice-status"><option value="">Všechny stavy</option>@foreach(['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'] as $key=>$label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach</select></div>
      <div class="col-md-2"><button class="btn btn-outline-primary mb-0" type="submit">Filtrovat</button></div>
      @if(request()->query())<div class="col-12"><a class="text-sm" href="{{ route('invoices.index') }}">Zrušit filtry</a></div>@endif
    </form>
  </header>
  <div class="table-responsive">
    <table class="table mb-0">
      <thead><tr><th class="px-4 py-3">Faktura</th><th>Klient</th><th>Splatnost</th><th>Stav</th><th class="text-end pe-4">Celkem</th></tr></thead>
      <tbody>
      @forelse($invoices as $invoice)
      <tr><td class="px-4 py-3"><a class="fw-bold" href="{{ route('invoices.show',$invoice) }}">{{ $invoice->invoice_number }}</a>@if($invoice->project)<small class="d-block text-secondary">{{ $invoice->project->name }}</small>@endif</td><td><a href="{{ route('clients.show',$invoice->client) }}">{{ $invoice->client->name }}</a></td><td>{{ $invoice->due_at?->format('j. n. Y') ?? 'Nevyplněna' }}</td><td><span class="status-label">{{ ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'][$invoice->status] }}</span></td><td class="text-end pe-4">{{ number_format($invoice->total,2,',',' ') }} {{ $invoice->currency }}</td></tr>
      @empty
      <tr><td colspan="5"><div class="empty-work"><h3>Žádné odpovídající faktury</h3><p>Vytvořte doklad pro klienta nebo upravte filtry.</p></div></td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  @if($invoices->hasPages())<div class="p-3">{{ $invoices->links() }}</div>@endif
</section>
@endsection
