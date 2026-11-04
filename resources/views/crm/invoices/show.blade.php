@extends('layouts.user_type.auth')

@section('content')
<div class="workspace-heading"><div><a class="context-line" href="{{ route('clients.show',$invoice->client) }}">{{ $invoice->client->name }}</a><h1>Faktura {{ $invoice->invoice_number }}</h1><span class="status-chip" data-status="{{ $invoice->status }}">{{ ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno'][$invoice->status] }}</span></div><div class="heading-actions">@can('update',$invoice)<a href="{{ route('invoices.edit',$invoice) }}" class="btn btn-outline-secondary">Upravit fakturu</a>@endcan<x-record-actions :record="$invoice" resource="invoices" :label="$invoice->invoice_number" /></div></div>
<div class="row"><div class="col-lg-8"><div class="card mb-4"><div class="card-header"><h2>Položky faktury</h2></div>
      <div class="card-body pt-2">
        {{-- Items table --}}
        <div class="table-responsive">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Položka</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Množství</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Jedn. cena</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Celkem</th>
              </tr>
            </thead>
            <tbody>
              @foreach($invoice->items as $item)
              <tr>
                <td class="ps-4">
                  <p class="text-sm font-weight-bold mb-0">{{ $item->name }}</p>
                  @if($item->description)<p class="text-xs text-secondary mb-0">{{ $item->description }}</p>@endif
                </td>
                <td class="text-center"><span class="text-sm">{{ $item->qty }}</span></td>
                <td class="text-center"><span class="text-sm">{{ number_format($item->unit_price, 2, ',', ' ') }}</span></td>
                <td class="text-center"><span class="text-sm font-weight-bold">{{ number_format($item->total, 2, ',', ' ') }}</span></td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" class="text-end pe-3"><strong>Mezisoučet:</strong></td>
                <td class="text-center"><strong>{{ number_format($invoice->subtotal, 2, ',', ' ') }}</strong></td>
              </tr>
              <tr>
                <td colspan="3" class="text-end pe-3">DPH ({{ $invoice->tax_rate }}%):</td>
                <td class="text-center">{{ number_format($invoice->tax_amount, 2, ',', ' ') }}</td>
              </tr>
              <tr>
                <td colspan="3" class="text-end pe-3"><strong class="text-lg">Celkem:</strong></td>
                <td class="text-center"><strong class="text-lg">{{ number_format($invoice->total, 2, ',', ' ') }} {{ $invoice->currency }}</strong></td>
              </tr>
            </tfoot>
          </table>
        </div>

        @if($invoice->notes)
          <div class="mt-3 p-3 bg-gray-100 border-radius-lg">
            <small class="text-secondary">Poznámky</small><br>
            {{ $invoice->notes }}
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Side info --}}
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Klient</small><br>
            <a href="{{ route('clients.show', $invoice->client) }}" class="font-weight-bold text-dark">{{ $invoice->client->name }}</a>
          </li>
          @if($invoice->project)
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Projekt</small><br>
            <a href="{{ route('projects.show', $invoice->project) }}" class="font-weight-bold text-dark">{{ $invoice->project->name }}</a>
          </li>
          @endif
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Vystaveno</small><br>
            {{ $invoice->issued_at?->format('d.m.Y') ?? '—' }}
          </li>
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Splatnost</small><br>
            <span class="{{ $invoice->isOverdue() ? 'text-danger font-weight-bold' : '' }}">
              {{ $invoice->due_at?->format('d.m.Y') ?? '—' }}
            </span>
          </li>
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Uhrazeno</small><br>
            {{ $invoice->paid_at?->format('d.m.Y') ?? '—' }}
          </li>
          <li class="list-group-item ps-0 border-0">
            <small class="text-secondary">Vytvořil</small><br>
            {{ $invoice->creator?->name ?? '—' }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

