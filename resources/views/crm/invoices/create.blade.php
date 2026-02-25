@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h5 class="mb-0">Nová faktura</h5>
      </div>
      <div class="card-body">
        @include('crm.partials.errors')
        <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
          @csrf
          <div class="row">
            <div class="col-md-4 mb-3">
              <label class="form-label">Číslo faktury</label>
              <input type="text" class="form-control" value="{{ $nextNumber }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Klient <span class="text-danger">*</span></label>
              <select name="client_id" class="form-select" required>
                <option value="">— Vyberte klienta —</option>
                @foreach($clients as $c)
                  <option value="{{ $c->id }}" {{ (old('client_id', $selectedClient?->id) == $c->id) ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 mb-3">
              <label class="form-label">Projekt</label>
              <select name="project_id" class="form-select">
                <option value="">— Bez projektu —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}" {{ (old('project_id', $selectedProject?->id) == $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Stav <span class="text-danger">*</span></label>
              <select name="status" class="form-select" required>
                @php $invStatusLabels = ['draft'=>'Koncept','sent'=>'Odesláno','paid'=>'Zaplaceno','overdue'=>'Po splatnosti','cancelled'=>'Zrušeno']; @endphp
                @foreach(\App\Models\Invoice::STATUSES as $s)
                  <option value="{{ $s }}" {{ old('status', 'draft') === $s ? 'selected' : '' }}>{{ $invStatusLabels[$s] ?? ucfirst($s) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Měna</label>
              <input type="text" name="currency" value="{{ old('currency', 'CZK') }}" class="form-control" maxlength="3" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">DPH %</label>
              <input type="number" name="tax_rate" value="{{ old('tax_rate', '21') }}" step="0.01" min="0" max="100" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Datum vystavení</label>
              <input type="date" name="issued_at" value="{{ old('issued_at', now()->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Splatnost</label>
              <input type="date" name="due_at" value="{{ old('due_at', now()->addDays(14)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3 mb-3">
              <label class="form-label">Datum úhrady</label>
              <input type="date" name="paid_at" value="{{ old('paid_at') }}" class="form-control">
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

          <div class="row mt-2">
            <div class="col-md-8">
              <label class="form-label">Poznámky</label>
              <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
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

          <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary btn-sm">Zrušit</a>
            <button type="submit" class="btn bg-gradient-primary btn-sm">Vytvořit fakturu</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

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

