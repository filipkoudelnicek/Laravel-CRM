@extends('layouts.user_type.auth')
@section('title','Nástěnka')
@section('content')
<div class="workspace-heading">
  <div><h1>Nástěnka</h1><p class="context-line">Přehled zakázek, faktur a klientských služeb.</p></div>
  <div class="heading-actions">@can('create',\App\Models\Invoice::class)<a class="btn btn-primary" href="{{ route('invoices.create') }}"><x-icon name="plus" />Nová faktura</a>@endcan</div>
</div>
<form class="dashboard-filters" method="GET">
  <label>Období<select name="months" class="form-select" aria-label="Období">@foreach([6,12,24] as $length)<option value="{{ $length }}" @selected($months===$length)>{{ $length }} měsíců</option>@endforeach</select></label>
  <label>Měna faktur<select name="currency" class="form-select" aria-label="Měna faktur">@foreach($currencies as $option)<option value="{{ $option }}" @selected($currency===$option)>{{ $option }}</option>@endforeach</select></label>
  <button class="btn btn-outline-secondary">Použít</button>
  <a class="dashboard-work-link" href="{{ route('work.index') }}">Přejít na moji práci <x-icon name="arrow" /></a>
</form>
<div class="dashboard-metrics">
  <a href="{{ route('invoices.index',['currency'=>$currency,'status'=>'outstanding']) }}" class="dashboard-metric metric-finance"><span class="metric-heading"><x-icon name="invoice" />K úhradě</span><strong class="numeric">{{ number_format($outstandingTotal,0,',',' ') }} <small>{{ $currency }}</small></strong><span>{{ $outstandingCount }} neuhrazených faktur</span></a>
  <a href="{{ route('projects.index',['status'=>'active']) }}" class="dashboard-metric metric-work"><span class="metric-heading"><x-icon name="folder" />Aktivní projekty</span><strong class="numeric">{{ $activeProjects }}</strong><span>Rozpracované zakázky</span></a>
  <a href="{{ route('support-plans.index') }}" class="dashboard-metric metric-services"><span class="metric-heading"><x-icon name="calendar" />Aktivní služby</span><strong class="numeric">{{ $activeServiceCount }}</strong><span>@forelse($activeServiceTotals as $unit=>$total)<span class="metric-amount numeric">{{ number_format($total,0,',',' ') }} {{ $unit }}</span>@emptyŽádná aktivní období@endforelse</span></a>
  <a href="{{ route('support-plans.index',['expiring'=>1]) }}" class="dashboard-metric metric-attention"><span class="metric-heading"><x-icon name="clock" />Končí do 30 dnů</span><strong class="numeric">{{ $expiringCount }}</strong><span>Služby k prodloužení</span></a>
</div>
<div class="dashboard-charts">
  <section class="section-panel panel-finance">
    <header class="section-heading"><div><h2>Měsíční příjmy</h2><p class="panel-description">Zaplacené faktury za {{ $months }} měsíců v {{ $currency }}</p></div><div class="chart-total"><strong class="numeric">{{ number_format($revenueTotal,0,',',' ') }} {{ $currency }}</strong><small>Celkem za období</small></div></header>
    @if($revenueTotal>0)<x-revenue-chart :months="$revenueMonths" :currency="$currency" />@else<div class="dashboard-empty"><x-icon name="invoice" /><h3>Zatím bez uhrazených faktur</h3><p>Po zaznamenání úhrady se tady zobrazí měsíční příjmy.</p><a href="{{ route('invoices.index') }}">Přehled faktur</a></div>@endif
    <footer class="panel-note">Tento měsíc uhrazeno <strong class="numeric">{{ number_format($paidThisMonth,0,',',' ') }} {{ $currency }}</strong></footer>
  </section>
  <section class="section-panel panel-work">
    <header class="section-heading"><div><h2>Úkoly podle stavu</h2><p class="panel-description">Aktivní a pozastavené projekty</p></div></header>
    @php $allTasks=$tasksByStatus->sum(); $statusLabels=['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno']; @endphp
    <div class="task-breakdown-total"><strong>{{ $allTasks }}</strong><span>úkolů celkem</span></div>
    <div class="task-status-breakdown">@foreach($tasksByStatus as $key=>$count)<div class="status-breakdown-row"><span class="status-chip" data-status="{{ $key }}">{{ $statusLabels[$key] }}</span><span class="status-breakdown-track" aria-hidden="true"><span class="breakdown-{{ $key }}" style="width:{{ $allTasks ? $count/$allTasks*100 : 0 }}%"></span></span><strong class="numeric">{{ $count }}</strong></div>@endforeach</div>
    <a class="panel-more-link" href="{{ route('tasks.index') }}">Všechny úkoly <x-icon name="arrow" /></a>
  </section>
</div>
<div class="dashboard-bottom">
  <section class="section-panel panel-neutral">
    <header class="section-heading"><h2>Poslední faktury <span class="panel-currency">{{ $currency }}</span></h2><a href="{{ route('invoices.index') }}">Všechny faktury</a></header>
    <div class="dashboard-invoices">@forelse($recentInvoices as $invoice)<a class="dashboard-invoice" href="{{ route('invoices.show',$invoice) }}"><span class="row-content"><strong>{{ $invoice->invoice_number }}</strong><small>{{ $invoice->client->name }}</small></span><span class="invoice-summary"><strong class="numeric">{{ number_format($invoice->total,2,',',' ') }} {{ $invoice->currency }}</strong><span class="status-chip" data-status="{{ $invoice->status }}">{{ ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'][$invoice->status] }}</span></span></a>@empty<div class="dashboard-empty"><h3>Zatím žádné faktury</h3><p>Vystavte první doklad klientovi nebo vyberte jinou měnu.</p></div>@endforelse</div>
  </section>
  <section class="section-panel panel-services">
    <header class="section-heading"><h2>Služby k prodloužení</h2><a href="{{ route('support-plans.index',['expiring'=>1]) }}">Všechny služby</a></header>
    @forelse($expiringServices as $service)<a class="context-item" href="{{ route('support-plans.show',$service) }}"><strong>{{ $service->title }}</strong><small>{{ $service->client->name }}</small><div class="context-item-footer"><span>Do {{ $service->period_to->format('j. n. Y') }}</span><span @class(['text-success'=>$service->paid_at])>{{ $service->paid_at ? 'Uhrazeno' : 'Úhrada neevidována' }}</span></div></a>@empty<div class="dashboard-empty"><h3>Žádné blízké termíny</h3><p>V následujících 30 dnech nekončí žádná aktivní služba.</p></div>@endforelse
  </section>
</div>
@endsection
