@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h5 class="mb-0">Upravit klienta: {{ $client->name }}</h5>
      </div>
      <div class="card-body">
        @include('crm.partials.errors')
        <form method="POST" action="{{ route('clients.update', $client) }}">
          @csrf @method('PUT')
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Jméno <span class="text-danger">*</span></label>
              <input type="text" name="name" value="{{ old('name', $client->name) }}" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Firma</label>
              <input type="text" name="company" value="{{ old('company', $client->company) }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">E-mail</label>
              <input type="email" name="email" value="{{ old('email', $client->email) }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Telefon</label>
              <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" class="form-control">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Adresa</label>
              <input type="text" name="address" value="{{ old('address', $client->address) }}" class="form-control">
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Poznámky</label>
              <textarea name="notes" rows="3" class="form-control">{{ old('notes', $client->notes) }}</textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('clients.show', $client) }}" class="btn btn-outline-secondary btn-sm">Zrušit</a>
            <button type="submit" class="btn bg-gradient-primary btn-sm">Uložit změny</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

