<!DOCTYPE html>
<html lang="cs">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>CRM — Codencio</title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- CSS Files -->
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css') }}?v=1.0.3" rel="stylesheet" />
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
    html,body{height:100%;overflow:hidden}
    .main-content{overflow-y:auto}

    /* ── Dark Mode ── */
    body.dark-mode{background-color:#1a1d21!important}
    .dark-mode .main-content{background-color:#1a1d21!important}
    .dark-mode .sidenav{background:#111315!important}
    .dark-mode .sidenav .navbar-brand span,
    .dark-mode .sidenav .nav-link-text{color:#e0e0e0!important}
    .dark-mode .sidenav .nav-link:not(.active) i{color:#adb5bd!important}
    .dark-mode .sidenav hr{border-color:rgba(255,255,255,.1)!important}
    .dark-mode .sidenav h6{color:rgba(255,255,255,.5)!important}
    .dark-mode .sidenav .nav-link:not(.active) .icon-shape{background:rgba(255,255,255,.06)!important}
    .dark-mode .sidenav .nav-link.active{background:rgba(255,255,255,.1)!important;box-shadow:none!important}
    .dark-mode .card{background:#212529!important;color:#d4d4d4!important}
    .dark-mode .card .card-header{background:#212529!important}
    .dark-mode .card-header h5,.dark-mode .card-header h6,.dark-mode .card h5,.dark-mode .card h6{color:#e8e8e8!important}
    .dark-mode .table th{color:rgba(255,255,255,.5)!important}
    .dark-mode .table td,.dark-mode .table td p,.dark-mode .table td h6,
    .dark-mode .table td span:not(.badge){color:#ccc!important}
    .dark-mode .navbar-main{background:rgba(26,29,33,.8)!important;backdrop-filter:blur(10px)}
    .navbar-main{position:relative;z-index:100}
    .navbar-main .dropdown-menu{z-index:1050!important}
    .dark-mode .navbar-main .breadcrumb-item,.dark-mode .navbar-main .breadcrumb-item a,
    .dark-mode .navbar-main h6{color:#ccc!important}
    .dark-mode .navbar-main .nav-link,.dark-mode .navbar-main .text-body{color:#d4d4d4!important}
    .dark-mode .dropdown-menu{background:#2b2f33!important;border-color:rgba(255,255,255,.1)!important}
    .dark-mode .dropdown-item{color:#d4d4d4!important}
    .dark-mode .dropdown-item:hover{background:rgba(255,255,255,.08)!important}
    .dark-mode .dropdown-divider{border-color:rgba(255,255,255,.1)!important}
    .dark-mode .form-control,.dark-mode .form-select{background:#2b2f33!important;border-color:rgba(255,255,255,.15)!important;color:#e0e0e0!important}
    .dark-mode .form-label{color:#bbb!important}
    .dark-mode .text-dark{color:#d4d4d4!important}
    .dark-mode .text-secondary{color:#aeb4bc!important}
    .dark-mode .alert-success{background:linear-gradient(310deg,#17ad37,#98ec2d)!important}
    .dark-mode .alert-danger{background:linear-gradient(310deg,#ea0606,#ff667c)!important}
    .dark-mode footer,.dark-mode footer a{color:#6c757d!important}
    .dark-mode .bg-white{background:#212529!important}
    .dark-mode .shadow{box-shadow:0 .25rem .375rem -.0625rem rgba(0,0,0,.4),0 .125rem .25rem -.0625rem rgba(0,0,0,.3)!important}
    .dark-mode .btn-outline-secondary{color:#adb5bd!important;border-color:#6c757d!important}
    .dark-mode .sidenav-toggler-line{background:#d4d4d4!important}
    .dark-mode .page-item .page-link{background:#2b2f33!important;border-color:rgba(255,255,255,.1)!important;color:#d4d4d4!important}
    .dark-mode .container-fluid .card .numbers p{color:#8a8f96!important}
    .dark-mode .list-group-item{background:transparent!important;color:#d4d4d4!important;border-color:rgba(255,255,255,.08)!important}
    .dark-mode .card-body{background:#212529!important;color:#d4d4d4!important}
    .dark-mode .card-body a:not(.btn):not(.badge){color:#7eb8f7!important}
    .dark-mode .card-footer{background:#212529!important}
    .dark-mode .table{--bs-table-bg:transparent!important}
    .dark-mode .table-responsive{background:transparent!important}
    .dark-mode .border-0{border-color:rgba(255,255,255,.08)!important}
    .dark-mode .bg-gray-100{background-color:#1a1d21!important}
    .dark-mode .page-header{background-color:#212529!important}
    .dark-mode .card .text-sm,.dark-mode .card .text-xs{color:#bbb!important}
    .dark-mode .card .font-weight-bold,.dark-mode .card h6.mb-0{color:#e0e0e0!important}
    .dark-mode textarea.form-control{background:#2b2f33!important;border-color:rgba(255,255,255,.15)!important;color:#e0e0e0!important}
    .dark-mode .input-group-text{background:#2b2f33!important;border-color:rgba(255,255,255,.15)!important;color:#adb5bd!important}
    .dark-mode .modal-content{background:#212529!important;color:#d4d4d4!important}
    .dark-mode .modal-header{background:#212529!important;border-color:rgba(255,255,255,.1)!important}
    .dark-mode .modal-header .modal-title{color:#e8e8e8!important}
    .dark-mode .modal-body{background:#212529!important;color:#d4d4d4!important}
    .dark-mode .modal-footer{background:#212529!important;border-color:rgba(255,255,255,.1)!important}
    .dark-mode .modal-header.bg-transparent{background:transparent!important}
    .dark-mode .modal-body{background:#212529!important}
    .dark-mode .modal-footer.bg-white{background:#212529!important}
    .dark-mode .alert{background:#2b2f33!important;border-color:rgba(255,255,255,.1)!important;color:#d4d4d4!important}
    .dark-mode .nav-tabs .nav-link{color:#adb5bd!important}
    .dark-mode .nav-tabs .nav-link.active{background:#212529!important;color:#fff!important;border-color:rgba(255,255,255,.15)!important}
    .dark-mode .tab-content{background:#212529!important}
    .dark-mode .border-bottom{border-color:rgba(255,255,255,.1)!important}
    .dark-mode .border-top{border-color:rgba(255,255,255,.1)!important}
    .dark-mode hr{border-color:rgba(255,255,255,.1)!important}
    .dark-mode .avatar.bg-gradient-primary{opacity:.9}
    .dark-mode p,.dark-mode span:not(.badge):not(.nav-link-text){color:#ccc}
    .dark-mode .card small{color:#8a8f96!important}
    .dark-mode .bg-light{background-color:#2b2f33!important;color:#d4d4d4!important}
    .dark-mode .bg-light p{color:#d4d4d4!important}
    .dark-mode .bg-light textarea,.dark-mode .bg-light input{background:#212529!important;color:#e0e0e0!important;border-color:rgba(255,255,255,.15)!important}
    .dark-mode .modal-body.bg-light,.dark-mode .modal-footer.bg-light{background:#212529!important}
    .dark-mode .p-3.bg-light{background:#2b2f33!important}
    .dark-mode .rounded.bg-light{background:#2b2f33!important}
    .dark-mode .text-center p,.dark-mode .text-center span:not(.badge){color:#d4d4d4!important}
    .dark-mode .table tbody tr:hover{background:rgba(255,255,255,.05)!important}
    .dark-mode .table-hover tbody tr:hover{background:#2b2f33!important}
    .dark-mode .badge.bg-light{background-color:#2b2f33!important;color:#d4d4d4!important}
    .dark-mode .badge.bg-light.text-dark{color:#d4d4d4!important}
    .dark-mode .badge.text-secondary{color:#8a8f96!important}
    .dark-mode .bg-light.rounded{background-color:#2b2f33!important;color:#d4d4d4!important}
    .dark-mode .p-3.bg-light.rounded{background-color:#2b2f33!important}
    .dark-mode .text-center.p-3.bg-light{background-color:#2b2f33!important}
    .dark-mode .text-center.p-3.bg-light.rounded{background-color:#2b2f33!important}
    .dark-mode .text-center.p-3.bg-light.rounded h5{color:#7eb8f7!important}
    .dark-mode .text-center.p-3.bg-light.rounded p{color:#8a8f96!important}
    .dark-mode .text-xs.text-secondary{color:#8a8f96!important}
    .dark-mode .modal-body .text-dark{color:#e0e0e0!important}
    .dark-mode .modal-body .text-secondary,.dark-mode .modal-body small.text-secondary{color:#8a8f96!important}
    .dark-mode .modal-body a:not(.btn){color:#7eb8f7!important}
    .dark-mode .modal-body .bg-light p{color:#d4d4d4!important}
    .dark-mode .container-fluid{background-color:#1a1d21!important}
    .dark-mode .card-header{border-color:rgba(255,255,255,.08)!important}
    .dark-mode .card-header h5{color:#e8e8e8!important}
    .dark-mode .table thead th{background-color:#2b2f33!important;color:#ccc!important;border-color:rgba(255,255,255,.08)!important}
    .dark-mode .table-light,
    .dark-mode .table-light > th,
    .dark-mode .table-light > td{background-color:#2b2f33!important;color:#cfd4da!important;border-color:rgba(255,255,255,.08)!important}
    .dark-mode .table tbody td{border-color:rgba(255,255,255,.08)!important}
    .dark-mode .btn-outline-primary{color:#7eb8f7!important;border-color:#7eb8f7!important}
    .dark-mode .btn-outline-primary:hover{background-color:rgba(126,184,247,.1)!important;color:#7eb8f7!important;border-color:#7eb8f7!important}
    .dark-mode .badge.bg-gradient-primary{opacity:.85}
    .dark-mode .badge.bg-success{background-color:rgba(23,173,55,.3)!important;color:#7eb8f7!important}
    .dark-mode .badge.bg-warning{background-color:rgba(251,207,51,.2)!important;color:#fbcf33!important}
    .dark-mode .badge.bg-danger{background-color:rgba(234,6,6,.2)!important;color:#ff667c!important}
    .dark-mode .badge.bg-info{background-color:rgba(33,82,255,.2)!important;color:#21d4fd!important}
    .dark-mode .fixed-plugin-button,
    .dark-mode .fixed-plugin-close-button{color:#d4d4d4!important}
    .dark-mode .fixed-plugin-button:hover,
    .dark-mode .fixed-plugin-close-button:hover{color:#ffffff!important}
    .dark-mode .notification-unread{background-color:rgba(126,184,247,.12)!important}

    /* Dark mode toggle button */
    .dark-mode-toggle{cursor:pointer;font-size:1.1rem;transition:color .2s}
    .dark-mode-toggle:hover{color:#fbcf33!important}
  </style>
</head>
<body class="g-sidenav-show bg-gray-100 @auth{{ auth()->user()->dark_mode ? 'dark-mode' : '' }}@endauth">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  {{-- Toast notifications --}}
  <div class="toast-container" id="toastContainer"></div>

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
  <script src="{{ asset('assets/js/soft-ui-dashboard.min.js') }}?v=1.0.3"></script>
  @stack('scripts')
</body>
</html>

