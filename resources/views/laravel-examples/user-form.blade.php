@extends('layouts.user_type.auth')

@section('content')

<div>
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h5 class="mb-0">{{ $user ? 'Upravit uživatele' : 'Nový uživatel' }}</h5>
                </div>
                <div class="card-body">

                    @if($errors->any())
                    <div class="alert alert-danger text-white">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST"
                          action="{{ $user ? route('users.update', $user) : route('users.store') }}">
                        @csrf
                        @if($user) @method('PUT') @endif

                        {{-- Jméno --}}
                        <div class="mb-3">
                            <label class="form-label">Jméno <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $user->name ?? '') }}" required>
                        </div>

                        {{-- E-mail --}}
                        <div class="mb-3">
                            <label class="form-label">E-mail <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $user->email ?? '') }}" required>
                        </div>

                        {{-- Heslo --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    Heslo
                                    @if($user)
                                        <small class="text-muted">(ponechte prázdné pro zachování)</small>
                                    @else
                                        <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="password" name="password" class="form-control"
                                       {{ $user ? '' : 'required' }} minlength="8">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Potvrzení hesla</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                       {{ $user ? '' : 'required' }} minlength="8">
                            </div>
                        </div>

                        {{-- Role --}}
                        <div class="mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                @foreach($roles as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('role', $user->role ?? 'member') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Telefon --}}
                        <div class="mb-3">
                            <label class="form-label">Telefon</label>
                            <input type="text" name="phone" class="form-control"
                                   value="{{ old('phone', $user->phone ?? '') }}">
                        </div>

                        {{-- Lokace --}}
                        <div class="mb-3">
                            <label class="form-label">Lokace</label>
                            <input type="text" name="location" class="form-control"
                                   value="{{ old('location', $user->location ?? '') }}">
                        </div>

                        {{-- Tlačítka --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user-management') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Zpět
                            </a>
                            <button type="submit" class="btn bg-gradient-primary">
                                <i class="fas fa-save me-1"></i>
                                {{ $user ? 'Uložit změny' : 'Vytvořit uživatele' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
