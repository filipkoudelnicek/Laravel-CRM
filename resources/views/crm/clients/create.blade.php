@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Nový klient" 
  submitText="Vytvořit klienta"
  backUrl="{{ route('clients.index') }}">
  
  <form method="POST" action="{{ route('clients.store') }}">
    @csrf

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="name" label="Jméno" value="{{ old('name') }}" required />
      </div>
      <div class="col-md-6">
        <x-form-field name="company" label="Firma" value="{{ old('company') }}" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="email" label="E-mail" type="email" value="{{ old('email') }}" />
      </div>
      <div class="col-md-6">
        <x-form-field name="phone" label="Telefon" value="{{ old('phone') }}" />
      </div>
    </div>

    <x-form-field name="address" label="Adresa" value="{{ old('address') }}" />

    <x-form-field name="notes" label="Poznámky" type="textarea" rows="3" value="{{ old('notes') }}" />

    <x-form-actions submitText="Vytvořit klienta" backUrl="{{ route('clients.index') }}" />
  </form>
</x-form-layout>
@endsection

