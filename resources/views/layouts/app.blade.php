<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>@yield('title', 'CRM') · Codencio</title>
  @auth<link rel="preload" href="{{ asset('fonts/Manrope-Variable.ttf') }}" as="font" type="font/ttf" crossorigin>@endauth
  <!--     Fonts and icons     -->
  @guest<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />@endguest
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css') }}?v=1.0.3" rel="stylesheet" />
  <link href="{{ asset('css/workspace.css') }}?v={{ file_exists(public_path('css/workspace.css')) ? filemtime(public_path('css/workspace.css')) : '1' }}" rel="stylesheet" />
  <style>
    .toast-container{position:fixed;top:1rem;right:1rem;z-index:9999;display:flex;flex-direction:column;gap:.5rem;pointer-events:none}
    .toast-msg{pointer-events:auto;min-width:280px;max-width:400px;padding:.75rem 1.25rem;border-radius:.5rem;color:#fff;font-size:.875rem;box-shadow:0 .5rem 1rem rgba(0,0,0,.15);animation:toastIn .35s ease-out}
    .toast-msg.toast-success{background:linear-gradient(310deg,#17ad37,#98ec2d)}
    .toast-msg.toast-error{background:linear-gradient(310deg,#ea0606,#ff667c)}
    .toast-msg.toast-warning{background:linear-gradient(310deg,#f53939,#fbcf33)}
    .toast-msg.toast-info{background:linear-gradient(310deg,#2152ff,#21d4fd)}
    .toast-hide{animation:toastOut .3s ease-in forwards}
    @keyframes toastIn{from{opacity:0;transform:translateX(40px)}to{opacity:1;transform:translateX(0)}}
    @keyframes toastOut{from{opacity:1;transform:translateX(0)}to{opacity:0;transform:translateX(40px)}}
    .navbar-vertical.navbar-expand-xs .navbar-collapse{height:calc(100vh - 150px)!important}
    .nav-link.active .icon-shape{background:linear-gradient(310deg,#7928ca,#ff0080)!important;box-shadow:0 3px 5px -1px rgba(121,40,202,.3)!important}
    .nav-link.active .icon-shape i{color:#fff!important;opacity:1!important}
    .icon-sm i{top:0!important;font-size:1rem!important}
    html,body{min-height:100%}

    .navbar-main{position:relative;z-index:100}
    .navbar-main .dropdown-menu{z-index:1050!important}
    /* Dark mode toggle button */
    .dark-mode-toggle{cursor:pointer;font-size:1.1rem;transition:color .2s}
    .dark-mode-toggle:hover{color:#fbcf33!important}
  </style>
</head>
<body class="bg-gray-100 @auth workspace {{ auth()->user()->dark_mode ? 'dark-mode' : '' }}@endauth">
  @auth<a class="skip-to-content" href="#main-content">Přejít k obsahu</a>@endauth
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  {{-- Toast notifications --}}
  <div class="toast-container" id="toastContainer" role="status" aria-live="polite"></div>

  <!--   Core JS Files   -->
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = { damping: '0.5' }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // Toast helper
    function showToast(message, type) {
      type = type || 'success';
      var container = document.getElementById('toastContainer');
      var el = document.createElement('div');
      el.className = 'toast-msg toast-' + type;
      el.textContent = message;
      container.appendChild(el);
      setTimeout(function(){ el.classList.add('toast-hide'); }, 4000);
      setTimeout(function(){ el.remove(); }, 4350);
    }

    // Auto-show session flash messages
    @if(session('success'))
      showToast(@json(session('success')), 'success');
    @endif
    @if(session('error'))
      showToast(@json(session('error')), 'error');
    @endif
    @if(session('warning'))
      showToast(@json(session('warning')), 'warning');
    @endif
    @if(session('info'))
      showToast(@json(session('info')), 'info');
    @endif
  </script>
  <!-- Control Center for Soft Dashboard -->
  <script src="{{ asset('js/workspace.js') }}?v={{ file_exists(public_path('js/workspace.js')) ? filemtime(public_path('js/workspace.js')) : '1' }}" defer></script>
  @stack('scripts')
</body>
</html>

