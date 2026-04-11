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
                        <button class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i> Nový uživatel
                        </button>
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

{{-- Modal pro přidání nového uživatele --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Nový uživatel</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addUserForm" method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="modal-body">
          <div id="formErrors" class="alert alert-danger d-none" role="alert"></div>
          <div id="formSuccess" class="alert alert-success d-none" role="alert">Uživatel byl vytvořen!</div>

          <div class="mb-3">
            <label class="form-label">Jméno <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
            <small class="text-danger d-none" data-error="name"></small>
          </div>

          <div class="mb-3">
            <label class="form-label">E-mail <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control" required>
            <small class="text-danger d-none" data-error="email"></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Heslo <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control" required minlength="8">
            <small class="text-danger d-none" data-error="password"></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Potvrzení hesla <span class="text-danger">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            <small class="text-danger d-none" data-error="password_confirmation"></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select" required>
              <option value="">— Vyberte roli —</option>
              <option value="member">Člen</option>
              <option value="manager">Manažer</option>
              <option value="admin">Administrátor</option>
            </select>
            <small class="text-danger d-none" data-error="role"></small>
          </div>

          <div class="mb-3">
            <label class="form-label">Telefon</label>
            <input type="text" name="phone" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Lokace</label>
            <input type="text" name="location" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Zrušit</button>
          <button type="submit" class="btn bg-gradient-primary btn-sm">
            <i class="fas fa-save me-1"></i> Vytvořit uživatele
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('addUserForm')?.addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const errorsDiv = document.getElementById('formErrors');
  const successDiv = document.getElementById('formSuccess');
  
  try {
    const response = await fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
      }
    });
    
    if (response.ok) {
      successDiv.classList.remove('d-none');
      this.reset();
      errorsDiv.classList.add('d-none');
      
      // Reload page after 1.5 seconds
      setTimeout(() => location.reload(), 1500);
    } else {
      const errors = await response.json();
      if (errors.errors) {
        let errorHtml = '<strong>Kontrolujte formulář:</strong><ul class="mb-0 mt-2">';
        Object.keys(errors.errors).forEach(field => {
          const fieldError = document.querySelector(`[data-error="${field}"]`);
          if (fieldError) {
            fieldError.textContent = errors.errors[field][0];
            fieldError.classList.remove('d-none');
          }
          errorHtml += `<li>${errors.errors[field][0]}</li>`;
        });
        errorHtml += '</ul>';
        errorsDiv.innerHTML = errorHtml;
        errorsDiv.classList.remove('d-none');
      }
      successDiv.classList.add('d-none');
    }
  } catch (error) {
    console.error('Error:', error);
    errorsDiv.textContent = 'Došlo k chybě při vytváření uživatele.';
    errorsDiv.classList.remove('d-none');
  }
});

// Reset form when modal is hidden
document.getElementById('addUserModal')?.addEventListener('hidden.bs.modal', function() {
  document.getElementById('addUserForm').reset();
  document.getElementById('formErrors').classList.add('d-none');
  document.getElementById('formSuccess').classList.add('d-none');
  document.querySelectorAll('[data-error]').forEach(el => el.classList.add('d-none'));
});
</script>
@endpush
@endsection
