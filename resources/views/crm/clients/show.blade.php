@extends('layouts.user_type.auth')
@section('title', $client->name)
@section('content')
@php $tab = in_array(request('tab'), ['services','invoices'], true) ? request('tab') : 'projects'; @endphp
<div class="workspace-heading"><div><a class="context-line" href="{{ route('clients.index') }}">Klienti</a><h1>{{ $client->name }}</h1><p class="context-line">{{ $client->company }}</p></div><div class="heading-actions"><x-record-actions :record="$client" resource="clients" :label="$client->name" />@can('create',\App\Models\Invoice::class)<a class="btn btn-primary" href="{{ route('invoices.create',['client_id'=>$client->id]) }}"><x-icon name="plus" />Faktura klientovi</a>@endcan</div></div>
<div class="detail-layout">
  <div>
    <nav class="work-tabs" aria-label="Sekce klienta">@foreach(['projects'=>'Projekty','services'=>'Služby a období','invoices'=>'Faktury'] as $key=>$label)<a class="{{ $tab === $key ? 'active' : '' }}" href="{{ route('clients.show',[$client,'tab'=>$key]) }}" @if($tab === $key) aria-current="page" @endif>{{ $label }}</a>@endforeach</nav>
    @if($tab === 'projects')
    <section class="list-panel"><header class="panel-heading"><h2>Projekty klienta</h2>@can('create',\App\Models\Project::class)<a href="{{ route('projects.create',['client_id'=>$client->id]) }}">+ Nový projekt</a>@endcan</header>
      @forelse($client->projects as $project)<a class="work-row" href="{{ route('projects.show',$project) }}"><span class="row-content"><strong>{{ $project->name }}</strong><small>{{ $project->tasks_count }} úkolů · {{ $project->due_date?->format('j. n. Y') ?? 'Bez termínu' }}</small></span><span class="status-chip" data-status="{{ $project->status }}">{{ ['planned'=>'Plánovaný','active'=>'Aktivní','on_hold'=>'Pozastavený','done'=>'Dokončený'][$project->status] }}</span></a>@empty<div class="empty-work"><h3>První projekt pro klienta</h3><p>Založte zakázku a přidejte do ní úkoly i domluvy s kolegou.</p>@can('create',\App\Models\Project::class)<a href="{{ route('projects.create',['client_id'=>$client->id]) }}" class="btn btn-outline-secondary">Vytvořit projekt</a>@endcan</div>@endforelse
    </section>
    @elseif($tab === 'services')
    <section class="list-panel"><header class="panel-heading"><div><h2>Služby a zaplacená období</h2><p>Aktuální období i historie</p></div>@can('create',\App\Models\SupportPlan::class)<a href="{{ route('support-plans.create',['client_id'=>$client->id]) }}">+ Nová služba</a>@endcan</header>
      @forelse($client->supportPlans as $service)<a href="{{ route('support-plans.show',$service) }}" class="work-row"><span class="row-content"><strong>{{ $service->title }}</strong><small>{{ $service->period_from->format('j. n. Y') }} – {{ $service->period_to->format('j. n. Y') }}</small></span><span class="row-meta"><strong>{{ number_format($service->price,0,',',' ') }} {{ $service->currency }}</strong><small class="{{ $service->paid_at ? 'text-success' : '' }}">{{ $service->paid_at ? 'Uhrazeno' : 'Úhrada neevidována' }}</small></span></a>@empty<div class="empty-work"><h3>Zatím žádná služba</h3><p>Přidejte hosting, správu nebo jinou službu a zaznamenejte období, které pokrývá.</p></div>@endforelse
    </section>
    @else
    <section class="list-panel"><header class="panel-heading"><div><h2>Faktury klienta</h2><p>Posledních pět dokladů</p></div><a href="{{ route('invoices.index',['client_id'=>$client->id]) }}">Všechny faktury</a></header>
      @forelse($client->invoices as $invoice)<a href="{{ route('invoices.show',$invoice) }}" class="work-row"><span class="row-content"><strong>{{ $invoice->invoice_number }}</strong><small>{{ ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'][$invoice->status] }} · {{ $invoice->issued_at?->format('j. n. Y') }}</small></span><strong class="numeric">{{ number_format($invoice->total,2,',',' ') }} {{ $invoice->currency }}</strong></a>@empty<div class="empty-work"><h3>Zatím žádná faktura</h3><p>Vystavte první doklad pro klienta.</p></div>@endforelse
    </section>
    @endif
  </div>
  <aside class="detail-sidebar" aria-label="Informace o klientovi">
    <section class="card work-panel"><header class="panel-heading"><h2>Kontakt</h2>@can('update',$client)<a href="{{ route('clients.edit',$client) }}">Upravit</a>@endcan</header><div class="p-4">@if($client->email)<p><a href="mailto:{{ $client->email }}">{{ $client->email }}</a></p>@endif @if($client->phone)<p><a href="tel:{{ $client->phone }}">{{ $client->phone }}</a></p>@endif @if($client->address)<p class="text-sm text-secondary">{{ $client->address }}</p>@endif @if($client->notes)<hr><h3 class="text-sm">Poznámky</h3><p class="note-body mb-0">{{ $client->notes }}</p>@elseif(!$client->email && !$client->phone)<p class="text-sm text-secondary">Doplňte kontaktní údaje a důležité informace o klientovi.</p>@endif</div></section>
    <section class="card work-panel"><header class="panel-heading"><h2>Fakturace</h2></header><div class="p-4">@forelse($invoiceTotals as $currency=>$totals)<div class="mb-3"><p class="text-sm text-secondary mb-1">{{ $currency }} · Fakturováno</p><strong class="numeric">{{ number_format($totals['invoiced'],2,',',' ') }} {{ $currency }}</strong><small class="d-block text-success numeric">Uhrazeno {{ number_format($totals['paid'],2,',',' ') }} {{ $currency }}</small></div>@empty<p class="text-sm text-secondary mb-0">Přehled se doplní po vytvoření první faktury.</p>@endforelse</div></section>
  </aside>
</div>
@endsection
