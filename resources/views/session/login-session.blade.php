@extends('layouts.user_type.guest')

@section('content')
  <style>
    .login-shell {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      background:
        radial-gradient(1200px 600px at 10% -10%, rgba(33, 82, 255, 0.26), transparent 60%),
        radial-gradient(900px 500px at 95% 105%, rgba(23, 173, 55, 0.24), transparent 60%),
        linear-gradient(145deg, #0f1115 0%, #181b21 45%, #111318 100%);
      position: relative;
      overflow: hidden;
    }

    .login-shell::before {
      content: "";
      position: absolute;
      inset: 0;
      background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
      background-size: 28px 28px;
      mask-image: radial-gradient(circle at center, black 45%, transparent 100%);
      pointer-events: none;
    }

    .login-card {
      width: 100%;
      max-width: 420px;
      border-radius: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.1);
      background: rgba(20, 24, 31, 0.9);
      box-shadow: 0 20px 45px rgba(0, 0, 0, 0.45);
      backdrop-filter: blur(8px);
      position: relative;
      z-index: 1;
    }

    .login-card .form-control {
      background: rgba(255, 255, 255, 0.06);
      border-color: rgba(255, 255, 255, 0.14);
      color: #e6ebf2;
    }

    .login-card .form-control::placeholder {
      color: #95a1b2;
    }

    .login-card .form-control:focus {
      border-color: rgba(33, 212, 253, 0.65);
      box-shadow: 0 0 0 0.18rem rgba(33, 212, 253, 0.18);
    }

    .login-card .form-check-label,
    .login-card .text-muted {
      color: #b8c2d1 !important;
    }
  </style>

  <main class="login-shell">
    <div class="card login-card p-4 p-md-5">
      <div class="mb-4 text-center">
        <h2 class="text-white mb-1">Přihlášení</h2>
        <p class="mb-0 text-sm text-secondary">Zadejte své přihlašovací údaje</p>
      </div>

      <form role="form" method="POST" action="/session">
        @csrf

        <label class="text-white-50">E-mail</label>
        <div class="mb-3">
          <input type="email" class="form-control" name="email" id="email" placeholder="E-mail" value="{{ old('email') }}" aria-label="Email" aria-describedby="email-addon" required>
          @error('email')
            <p class="text-danger text-xs mt-2">{{ $message }}</p>
          @enderror
        </div>

        <label class="text-white-50">Heslo</label>
        <div class="mb-3">
          <input type="password" class="form-control" name="password" id="password" placeholder="Heslo" aria-label="Password" aria-describedby="password-addon" required>
          @error('password')
            <p class="text-danger text-xs mt-2">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-check form-switch mb-2">
          <input class="form-check-input" type="checkbox" id="rememberMe" name="remember" checked>
          <label class="form-check-label" for="rememberMe">Zapamatovat si mě</label>
        </div>

        <button type="submit" class="btn bg-gradient-info w-100 mt-3 mb-2">Přihlásit se</button>

        <div class="text-center mt-3">
          <small class="text-muted">Zapomněli jste heslo?
            <a href="/login/forgot-password" class="text-info font-weight-bold">Obnovit heslo</a>
          </small>
        </div>
      </form>
    </div>
  </main>

@endsection

