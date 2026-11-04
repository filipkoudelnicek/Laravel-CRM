@extends('layouts.user_type.auth')
@section('title', 'Upravit službu')
@section('content')
<x-form-layout :title="'Upravit: ' . $supportPlan->title" :backUrl="route('support-plans.show', $supportPlan)">
  <form method="POST" action="{{ route('support-plans.update', $supportPlan) }}" data-client-project-form>@csrf @method('PUT')
    @include('crm.support-plans._fields', ['source' => $supportPlan, 'renewal' => null, 'selectedClient' => $supportPlan->client])
    <x-form-actions submitText="Uložit změny" :backUrl="route('support-plans.show', $supportPlan)" />
  </form>
</x-form-layout>
@endsection
