@extends('layouts.app')

@section('auth')

    @include('layouts.navbars.auth.sidebar')
    <main class="workspace-main" id="main-content">
        @include('layouts.navbars.auth.nav')
        <div class="workspace-content">
            @include('components.running-timer')
            @if($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Údaje se nepodařilo uložit.</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
            @include('layouts.footers.auth.footer')
        </div>
    </main>

@endsection

