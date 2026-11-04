@extends('layouts.user_type.auth')
@section('title', 'Přístupy')
@section('content')
<x-page-heading title="Přístupy" description="Přihlašovací údaje ke klientům a projektům."><a href="{{ route('passwords.create') }}" class="btn btn-primary"><x-icon name="plus" />Nový přístup</a></x-page-heading>
<section class="list-panel"><form method="GET" class="list-toolbar"><x-search-field placeholder="Hledat přístup…" /><button class="btn btn-outline-secondary">Hledat</button>@if(request('q'))<a href="{{ route('passwords.index') }}" class="btn btn-link">Zrušit filtr</a>@endif</form>
<table class="data-table" aria-label="Seznam přístupů"><thead><tr><th class="primary-cell">Přístup</th><th class="hide-mobile" style="width:120px">Typ</th><th class="hide-mobile">Uživatelské jméno</th><th class="hide-tablet">Klient / projekt</th><th class="action-cell"><span class="visually-hidden">Akce</span></th></tr></thead><tbody>
@forelse($entries as $entry)<tr><td><a class="record-link" href="{{ route('passwords.show',$entry) }}" title="{{ $entry->title }}">{{ $entry->title }}</a><span class="record-secondary">{{ $entry->url ? parse_url($entry->url,PHP_URL_HOST) : ($entry->client?->name ?? 'Bez webové adresy') }}</span></td><td class="hide-mobile muted-cell">{{ ['general'=>'Obecný','sftp'=>'SFTP','admin'=>'Administrace','hosting'=>'Hosting'][$entry->type] ?? $entry->type }}</td><td class="hide-mobile">{{ $entry->username ?: '—' }}</td><td class="hide-tablet"><span class="record-link">{{ $entry->client?->name ?? '—' }}</span><span class="record-secondary">{{ $entry->project?->name }}</span></td><td class="action-cell"><x-record-actions :record="$entry" resource="passwords" :label="$entry->title" /></td></tr>
@empty<tr><td colspan="5"><div class="empty-work"><h3>Žádné odpovídající přístupy</h3><p>Přidejte záznam nebo upravte vyhledávání.</p></div></td></tr>@endforelse
</tbody></table><x-list-footer :items="$entries" /></section>
@endsection
