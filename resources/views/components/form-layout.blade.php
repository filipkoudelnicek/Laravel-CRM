@props(['title', 'submitText' => 'Uložit', 'backUrl'])

<div class="row justify-content-center">
  <div class="col-lg-8 col-xl-7">
    <div class="card mb-4">
      <div class="card-header pb-3 pt-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-0">{{ $title }}</h5>
          <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Zpět
          </a>
        </div>
      </div>
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Kontrolujte formulář:</strong>
            <ul class="mb-0 mt-2">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        {{ $slot }}
      </div>
    </div>
  </div>
</div>
