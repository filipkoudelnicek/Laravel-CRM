@extends('layouts.user_type.auth')
@section('title','Nový úkol')
@section('content')
<div class="form-workspace">
  <x-page-heading title="Nový úkol" />
  <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data">@csrf
    @include('crm.tasks._fields',['task'=>null])
    <div class="form-actions"><a class="btn btn-outline-secondary" href="{{ $selectedProject ? route('projects.show',$selectedProject) : route('tasks.index') }}">Zrušit</a><button class="btn btn-primary">Vytvořit úkol</button></div>
  </form>
</div>
@endsection
@push('scripts')<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>@endpush
