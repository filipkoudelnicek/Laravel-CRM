<!-- Navbar -->
<nav class="navbar navbar-expand-lg position-absolute top-0 z-index-3 my-3 blur blur-rounded shadow py-2 start-0 end-0 mx4">
  <div class="container-fluid">
    <a class="navbar-brand font-weight-bolder ms-lg-0 ms-3" href="{{ url('dashboard') }}">
      CRM Codencio
    </a>
    <button class="navbar-toggler shadow-none ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navigation" aria-controls="navigation" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon mt-2">
        <span class="navbar-toggler-bar bar1"></span>
        <span class="navbar-toggler-bar bar2"></span>
        <span class="navbar-toggler-bar bar3"></span>
      </span>
    </button>
    <div class="collapse navbar-collapse" id="navigation">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item">
          <a class="nav-link me-2" href="{{ url('login') }}">
            <i class="fas fa-key opacity-6 me-1 text-dark"></i>
            Přihlášení
          </a>
        </li>
        {{-- Registration disabled – accounts are created by admin --}}
      </ul>
    </div>
  </div>
</nav>
<!-- End Navbar -->

