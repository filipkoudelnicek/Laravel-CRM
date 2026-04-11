@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Upravit klienta: {{ $client->name }}" 
  submitText="Uložit změny"
  backUrl="{{ route('clients.show', $client) }}">
  
  <form method="POST" action="{{ route('clients.update', $client) }}">
    @csrf @method('PUT')

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="name" label="Jméno" value="{{ old('name', $client->name) }}" required />
      </div>
      <div class="col-md-6">
        <x-form-field name="company" label="Firma" value="{{ old('company', $client->company) }}" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="email" label="E-mail" type="email" value="{{ old('email', $client->email) }}" />
      </div>
      <div class="col-md-6">
        <x-form-field name="phone" label="Telefon" value="{{ old('phone', $client->phone) }}" />
      </div>
    </div>

    <x-form-field name="address" label="Adresa" value="{{ old('address', $client->address) }}" />

    <x-form-field name="notes" label="Poznámky" type="textarea" rows="3" value="{{ old('notes', $client->notes) }}" />

    <x-form-actions submitText="Uložit změny" backUrl="{{ route('clients.show', $client) }}" />
  </form>
</x-form-layout>
@endsection