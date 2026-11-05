@extends('layouts.user_type.auth')
@section('title', $user ? 'Upravit uživatele' : 'Nový uživatel')
@section('content')
<x-form-layout :title="$user ? 'Upravit uživatele' : 'Nový uživatel'" :backUrl="route('user-management')">
  <form method="POST" action="{{ $user ? route('users.update', $user) : route('users.store') }}">
    @csrf
    @if($user) @method('PUT') @endif
    <div class="row">
      <div class="col-md-6"><x-form-field name="name" label="Jméno" :value="$user?->name" required autocomplete="name" /></div>
      <div class="col-md-6"><x-form-field name="email" label="E-mail" type="email" :value="$user?->email" required autocomplete="email" /></div>
    </div>
    <div class="row">
      <div class="col-md-6"><x-form-field name="password" :label="$user ? 'Nové heslo (volitelně)' : 'Heslo'" type="password" :required="!$user" minlength="8" autocomplete="new-password" /></div>
      <div class="col-md-6"><x-form-field name="password_confirmation" label="Potvrzení hesla" type="password" :required="!$user" minlength="8" autocomplete="new-password" /></div>
    </div>
    @if($user)<p class="context-line form-help">Pro zachování současného hesla ponechte obě pole prázdná.</p>@endif
    <x-form-field name="role" label="Role" type="select" required>
      @foreach($roles as $value=>$label)<option value="{{ $value }}" @selected(old('role', $user?->role ?? 'member') === $value)>{{ $label }}</option>@endforeach
    </x-form-field>
    <div class="row">
      <div class="col-md-6"><x-form-field name="phone" label="Telefon" :value="$user?->phone" autocomplete="tel" /></div>
      <div class="col-md-6"><x-form-field name="location" label="Lokace" :value="$user?->location" /></div>
    </div>
    <x-form-actions :submitText="$user ? 'Uložit změny' : 'Vytvořit uživatele'" :backUrl="route('user-management')" />
  </form>
</x-form-layout>
@endsection
