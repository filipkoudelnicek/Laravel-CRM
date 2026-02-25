@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">Uživatelé</h5>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('users.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                            <i class="fas fa-plus me-1"></i> Nový uživatel
                        </a>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 mb-0 text-white" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 mb-0 text-white" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Jméno</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">E-mail</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Role</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vytvořeno</th>
                                    @if(auth()->user()->isAdmin())
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Akce</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $u)
                                @php
                                    $roleLabels = ['admin' => 'Administrátor', 'manager' => 'Manažer', 'member' => 'Člen'];
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <p class="text-xs font-weight-bold mb-0">{{ $u->id }}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="avatar avatar-sm me-3 bg-gradient-primary d-flex align-items-center justify-content-center rounded-circle">
                                                <i class="fas fa-user text-white text-xs"></i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $u->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <p class="text-xs font-weight-bold mb-0">{{ $u->email }}</p>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-sm bg-gradient-{{ $u->role === 'admin' ? 'danger' : ($u->role === 'manager' ? 'info' : 'secondary') }}">
                                            {{ $roleLabels[$u->role] ?? ucfirst($u->role ?? 'Člen') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-secondary text-xs font-weight-bold">{{ $u->created_at->format('d.m.Y') }}</span>
                                    </td>
                                    @if(auth()->user()->isAdmin())
                                    <td class="text-center">
                                        <a href="{{ route('users.edit', $u) }}" class="btn btn-link text-info mb-0 px-2" title="Upravit">
                                            <i class="fas fa-pencil-alt text-sm"></i>
                                        </a>
                                        @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Opravdu smazat uživatele {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger mb-0 px-2" title="Smazat">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-sm text-secondary">Žádní uživatelé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
