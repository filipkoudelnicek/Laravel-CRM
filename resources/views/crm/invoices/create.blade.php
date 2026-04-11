@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Nová faktura" 
  submitText="Vytvořit fakturu"
  backUrl="{{ route('invoices.index') }}">
  
  <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
    @csrf

    <div class="row">
      <div class="col-md-4">
        <label class="form-label">Číslo faktury</label>
        <input type="text" class="form-control" value="{{ $nextNumber }}" disabled>
      </div>
      <div class="col-md-4">
        <x-form-field name="client_id" label="Klient" type="select" required>
          <option value="">— Vyberte klienta —</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(old('client_id', $selectedClient?->id) == $c->id)>{{ $c->name }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-4">
        <x-form-field name="project_id" label="Projekt" type="select">
          <option value="">— Bez projektu —</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected(old('project_id', $selectedProject?->id) == $p->id)>{{ $p->name }}</option>
          @endforeach
        </x-form-field>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <x-form-field name="status" label="Stav" type="select" required>
          @php $invStatusLabels = ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno']; @endphp
          @foreach(\App\Models\Invoice::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', 'draft') === $s)>{{ $invStatusLabels[$s] ?? ucfirst($s) }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-3">
        <x-form-field name="currency" label="Měna" value="{{ old('currency', 'CZK') }}" maxlength="3" required />
      </div>
      <div class="col-md-3">
        <x-form-field name="tax_rate" label="DPH %" type="number" step="0.01" min="0" max="100" value="{{ old('tax_rate', '21') }}" required />
      </div>
      <div class="col-md-3">
        <x-form-field name="issued_at" label="Datum vystavení" type="date" value="{{ old('issued_at', now()->format('Y-m-d')) }}" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <x-form-field name="due_at" label="Splatnost" type="date" value="{{ old('due_at', now()->addDays(14)->format('Y-m-d')) }}" />
      </div>
      <div class="col-md-3">
        <x-form-field name="paid_at" label="Datum úhrady" type="date" value="{{ old('paid_at') }}" />
      </div>
    </div>

    {{-- Invoice items --}}
    <h6 class="mt-4 mb-3">Položky faktury</h6>
    <div id="items-container">
      @php $oldItems = old('items', [['name'=>'', 'description'=>'', 'qty'=>'1', 'unit_price'=>'']]); @endphp
      @foreach($oldItems as $i => $item)
      <div class="row align-items-end item-row mb-2" data-index="{{ $i }}">
        <div class="col-md-4 mb-2">
          <label class="form-label text-xs">Název <span class="text-danger">*</span></label>
          <input type="text" name="items[{{ $i }}][name]" value="{{ $item['name'] ?? '' }}" class="form-control form-control-sm" required>
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label text-xs">Množství</label>
          <input type="number" name="items[{{ $i }}][qty]" value="{{ $item['qty'] ?? 1 }}" step="0.01" min="0.01" class="form-control form-control-sm item-qty" required>
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label text-xs">Jedn. cena</label>
          <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" step="0.01" min="0" class="form-control form-control-sm item-price" required>
        </div>
        <div class="col-md-2 mb-2">
          <label class="form-label text-xs">Celkem</label>
          <input type="text" class="form-control form-control-sm item-total" readonly tabindex="-1">
        </div>
        <div class="col-md-1 mb-2">
          <label class="form-label text-xs">Popis</label>
          <input type="text" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" class="form-control form-control-sm" placeholder="…">
        </div>
        <div class="col-md-1 mb-2 text-center">
          <button type="button" class="btn btn-outline-danger btn-sm remove-item" title="Odebrat"><i class="fas fa-trash"></i></button>
        </div>
      </div>
      @endforeach
    </div>
    <button type="button" id="add-item" class="btn btn-outline-secondary btn-sm mb-3">+ Přidat položku</button>

    <div class="row mt-4">
      <div class="col-md-8">
        <x-form-field name="notes" label="Poznámky" type="textarea" rows="3" value="{{ old('notes') }}" />
      </div>
      <div class="col-md-4">
        <div class="card bg-gray-100 p-3">
          <p class="text-sm mb-1">Mezisoučet: <strong id="calc-subtotal">0</strong></p>
          <p class="text-sm mb-1">DPH (<span id="calc-tax-rate">21</span>%): <strong id="calc-tax">0</strong></p>
          <hr class="my-2">
          <p class="text-sm mb-0 font-weight-bolder">Celkem: <strong id="calc-total">0</strong></p>
        </div>
      </div>
    </div>

    <x-form-actions submitText="Vytvořit fakturu" backUrl="{{ route('invoices.index') }}" />
  </form>
</x-form-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let idx = {{ count($oldItems) }};

    document.getElementById('add-item').addEventListener('click', function() {
        const row = document.querySelector('.item-row').cloneNode(true);
        row.dataset.index = idx;
        row.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, '[' + idx + ']');
            if (!input.readOnly) input.value = input.name.includes('qty') ? '1' : '';
        });
        document.getElementById('items-container').appendChild(row);
        idx++;
        recalc();
    });

    document.getElementById('items-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                e.target.closest('.item-row').remove();
                recalc();
            }
        }
    });

    document.getElementById('items-container').addEventListener('input', recalc);
    document.querySelector('[name="tax_rate"]').addEventListener('input', recalc);

    function recalc() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
            const price = parseFloat(row.querySelector('.item-price')?.value) || 0;
            const total = qty * price;
            const totalField = row.querySelector('.item-total');
            if (totalField) totalField.value = total.toFixed(2);
            subtotal += total;
        });
        const taxRate = parseFloat(document.querySelector('[name="tax_rate"]').value) || 0;
        const tax = subtotal * (taxRate / 100);
        document.getElementById('calc-subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('calc-tax-rate').textContent = taxRate;
        document.getElementById('calc-tax').textContent = tax.toFixed(2);
        document.getElementById('calc-total').textContent = (subtotal + tax).toFixed(2);
    }

    recalc();
});
</script>
@endpush
@endsection

