@extends('layouts.user_type.auth')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4 mx-0">
      <div class="card-header pb-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Notifikace</h5>
        @if(auth()->user()->unreadNotifications->count() > 0)
          <form method="POST" action="{{ route('notifications.readAll') }}">
            @csrf
            <button class="btn btn-outline-secondary btn-sm">Označit vše jako přečtené</button>
          </form>
        @endif
      </div>
      <div class="card-body px-0 pt-0 pb-2">
        <div class="list-group list-group-flush">
          @forelse($notifications as $n)
          <div class="list-group-item d-flex justify-content-between align-items-start px-4 py-3 {{ $n->read_at ? '' : 'bg-gray-100' }}">
            <div class="d-flex align-items-start">
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-3 d-flex align-items-center justify-content-center flex-shrink-0">
                @php
                  $type = $n->data['type'] ?? '';
                  $icon = match(true) {
                    str_contains($type, 'project_assigned') => 'fa-project-diagram text-primary',
                    str_contains($type, 'task_assigned')    => 'fa-tasks text-info',
                    str_contains($type, 'comment')          => 'fa-comment text-success',
                    str_contains($type, 'mention')          => 'fa-at text-warning',
                    str_contains($type, 'status')           => 'fa-exchange-alt text-secondary',
                    default                                  => 'fa-bell text-dark',
                  };
                @endphp
                <i class="fas {{ $icon }} text-sm opacity-10"></i>
              </div>
              <div>
                <p class="text-sm mb-0 {{ $n->read_at ? '' : 'font-weight-bold' }}">
                  {{ $n->data['message'] ?? 'Notifikace' }}
                </p>
                <p class="text-xs text-secondary mb-0">
                  <i class="fa fa-clock me-1"></i>{{ $n->created_at->diffForHumans() }}
                </p>
              </div>
            </div>
            @unless($n->read_at)
              <form method="POST" action="{{ route('notifications.read', $n->id) }}">
                @csrf
                <button class="btn btn-link text-xs text-secondary p-0 m-0">Přečteno</button>
              </form>
            @endunless
          </div>
          @empty
          <div class="text-center py-4 text-sm text-secondary">Žádné notifikace.</div>
          @endforelse
        </div>
        <div class="px-4 pt-3">{{ $notifications->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection

