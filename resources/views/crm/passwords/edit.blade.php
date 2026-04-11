@extends('layouts.user_type.auth')

@section('content')
<x-form-layout 
  title="Upravit: {{ $password->title }}" 
  submitText="Uložit změny"
  backUrl="{{ route('passwords.show', $password) }}">
  
  <form method="POST" action="{{ route('passwords.update', $password) }}">
    @csrf @method('PUT')

    <x-form-field name="title" label="Název" value="{{ old('title', $password->title) }}" required />

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="type" label="Typ hesla" type="select" id="passwordType" onchange="updateTypeFields()" required>
          <option value="">— Vyberte typ —</option>
          @foreach($types as $key => $label)
            <option value="{{ $key }}" @selected(old('type', $password->type) === $key)>{{ $label }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-6">
        <x-form-field name="username" label="Uživ. jméno" value="{{ old('username', $password->username) }}" autocomplete="off" />
      </div>
    </div>

    <div class="row">
      <div class="col-md-6">
        <label class="form-label">Nové heslo <small class="text-muted">(ponechte prázdné pro zachování)</small></label>
        <div class="input-group">
          <input type="password" name="password" id="pw-field" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
          <button type="button" class="btn btn-outline-secondary" onclick="togglePw()">
            <i class="fas fa-eye" id="pw-eye"></i>
          </button>
        </div>
        @error('password') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-6">
        <x-form-field name="url" label="URL" type="url" value="{{ old('url', $password->url) }}" placeholder="https://…" />
      </div>
    </div>

    {{-- Dynamic type-specific fields --}}
    <div id="typeFields" class="row">
      @include('crm.passwords._type_fields', ['password' => $password, 'type' => old('type', $password->type)])
    </div>

    <div class="row">
      <div class="col-md-6">
        <x-form-field name="client_id" label="Propojit s klientem" type="select">
          <option value="">— Žádný —</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" @selected(old('client_id', $password->client_id) == $c->id)>{{ $c->name }}</option>
          @endforeach
        </x-form-field>
      </div>
      <div class="col-md-6">
        <x-form-field name="project_id" label="Propojit s projektem" type="select">
          <option value="">— Žádný —</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" @selected(old('project_id', $password->project_id) == $p->id)>{{ $p->name }}</option>
          @endforeach
        </x-form-field>
      </div>
    </div>

    <x-form-field name="notes" label="Poznámky" type="textarea" rows="3" value="{{ old('notes', $password->notes) }}" />

    <x-form-actions submitText="Uložit změny" backUrl="{{ route('passwords.show', $password) }}" />
  </form>
</x-form-layout>

@push('scripts')
<script>
function togglePw() {
  const f = document.getElementById('pw-field');
  const e = document.getElementById('pw-eye');
  if (f.type === 'password') { f.type = 'text'; e.classList.replace('fa-eye','fa-eye-slash'); }
  else { f.type = 'password'; e.classList.replace('fa-eye-slash','fa-eye'); }
}

function updateTypeFields() {
  const type = document.getElementById('passwordType').value;
  const container = document.getElementById('typeFields');
  
  if (!type) {
    container.innerHTML = '';
    return;
  }
  
  const typeFieldsMap = {
    'sftp': `
      <div class="col-md-6 mb-3">
        <label class="form-label">Host <span class="text-danger">*</span></label>
        <input type="text" name="sftp_host" value="{{ old('sftp_host', $password->sftp_host ?? '') }}" class="form-control">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Port</label>
        <input type="number" name="sftp_port" value="{{ old('sftp_port', $password->sftp_port ?? '22') }}" class="form-control" min="1" max="65535">
      </div>
      <div class="col-12 mb-3">
        <label class="form-label">Cesta</label>
        <input type="text" name="sftp_path" value="{{ old('sftp_path', $password->sftp_path ?? '') }}" class="form-control" placeholder="/home/user/">
      </div>
    `,
    'hosting': `
      <div class="col-md-6 mb-3">
        <label class="form-label">Poskytovatel</label>
        <input type="text" name="hosting_provider" value="{{ old('hosting_provider', $password->hosting_provider ?? '') }}" class="form-control" placeholder="Např. WEDOS, Forpsi...">
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">FTP Host</label>
        <input type="text" name="ftp_host" value="{{ old('ftp_host', $password->ftp_host ?? '') }}" class="form-control" placeholder="ftp.example.com">
      </div>
    `,
    'admin': '',
    'general': ''
  };
  
  container.innerHTML = typeFieldsMap[type] || '';
}
</script>
@endpush

@endsection

