@extends('layouts.user_type.auth')
@section('title', $renewal ? 'Prodloužit službu' : 'Nová služba')
@section('content')
<x-form-layout :title="$renewal ? 'Prodloužit: ' . $renewal->title : 'Nová služba'" :backUrl="route('support-plans.index')">
  @if($renewal)<p class="text-sm text-secondary">Vytváříte nové navazující období. Předchozí záznam a jeho úhrada zůstanou zachované.</p>@endif
  <form method="POST" action="{{ route('support-plans.store') }}" data-client-project-form>@csrf
    @include('crm.support-plans._fields', ['source' => $renewal])
    <x-form-actions :submitText="$renewal ? 'Uložit nové období' : 'Vytvořit službu'" :backUrl="route('support-plans.index')" />
  </form>
</x-form-layout>
@endsection
