@extends('layouts.user_type.auth')
@section('title', 'Klienti')
@section('content')
<x-page-heading title="Klienti" description="Kontakty, projekty a služby na jednom místě.">@can('create', \App\Models\Client::class)<a href="{{ route('clients.create') }}" class="btn btn-primary"><x-icon name="plus" />Nový klient</a>@endcan</x-page-heading>
<section class="list-panel">
  <form method="GET" class="list-toolbar"><x-search-field placeholder="Hledat klienta, firmu nebo e-mail…" /><button class="btn btn-outline-secondary">Hledat</button>@if(request('q'))<a href="{{ route('clients.index') }}" class="btn btn-link">Zrušit filtr</a>@endif<span class="toolbar-end result-count">{{ $clients->total() }} klientů</span></form>
  <table class="data-table" aria-label="Seznam klientů"><thead><tr><th class="primary-cell">Klient</th><th class="hide-mobile">Kontakt</th><th class="hide-tablet" style="width:170px">Telefon</th><th class="hide-mobile" style="width:100px">Projekty</th><th class="action-cell"><span class="visually-hidden">Akce</span></th></tr></thead><tbody>
  @forelse($clients as $client)
  <tr><td class="record-cell"><a class="record-link" href="{{ route('clients.show',$client) }}" title="{{ $client->name }}">{{ $client->name }}</a><span class="record-secondary">{{ $client->company ?: ($client->email ?: 'Bez názvu firmy') }}</span></td><td class="hide-mobile">@if($client->email)<a href="mailto:{{ $client->email }}">{{ $client->email }}</a>@else<span class="record-secondary">—</span>@endif</td><td class="hide-tablet muted-cell numeric">{{ $client->phone ?: '—' }}</td><td class="hide-mobile numeric">{{ $client->projects_count }}</td><td class="action-cell"><x-record-actions :record="$client" resource="clients" :label="$client->name" /></td></tr>
  @empty<tr><td colspan="5"><div class="empty-work"><h3>Žádní odpovídající klienti</h3><p>Přidejte prvního klienta nebo upravte vyhledávání.</p></div></td></tr>@endforelse
  </tbody></table>
  <x-list-footer :items="$clients" />
</section>
@endsection
