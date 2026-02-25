@extends('layouts.user_type.auth')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-7">
    <div class="card mb-4">
      <div class="card-header pb-0">
        <h5 class="mb-0">Upravit: {{ $password->title }}</h5>
      </div>
      <div class="card-body">
        @include('crm.partials.errors')
        <form method="POST" action="{{ route('passwords.update', $password) }}">
          @csrf @method('PUT')
          <div class="row">
            <div class="col-12 mb-3">
              <label class="form-label">Název <span class="text-danger">*</span></label>
              <input type="text" name="title" value="{{ old('title', $password->title) }}" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Uživ. jméno</label>
              <input type="text" name="username" value="{{ old('username', $password->username) }}" class="form-control" autocomplete="off">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Nové heslo <small class="text-muted">(ponechte prázdné pro zachování)</small></label>
              <div class="input-group">
                <input type="password" name="password" id="pw-field" class="form-control" autocomplete="new-password">
                <button type="button" class="btn btn-outline-secondary" onclick="togglePw()">
                  <i class="fas fa-eye" id="pw-eye"></i>
                </button>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">URL</label>
              <input type="url" name="url" value="{{ old('url', $password->url) }}" class="form-control" placeholder="https://…">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Propojit s klientem</label>
              <select name="client_id" class="form-select">
                <option value="">— Žádný —</option>
                @foreach($clients as $c)
                  <option value="{{ $c->id }}" @selected(old('client_id', $password->client_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Propojit s projektem</label>
              <select name="project_id" class="form-select">
                <option value="">— Žádný —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}" @selected(old('project_id', $password->project_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12 mb-3">
              <label class="form-label">Poznámky</label>
              <textarea name="notes" rows="3" class="form-control">{{ old('notes', $password->notes) }}</textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('passwords.show', $password) }}" class="btn btn-outline-secondary btn-sm">Zrušit</a>
            <button type="submit" class="btn bg-gradient-primary btn-sm">Uložit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
function togglePw() {
  const f = document.getElementById('pw-field');
  const e = document.getElementById('pw-eye');
  if (f.type === 'password') { f.type = 'text'; e.classList.replace('fa-eye','fa-eye-slash'); }
  else { f.type = 'password'; e.classList.replace('fa-eye-slash','fa-eye'); }
}
</script>
@endpush
@endsection

