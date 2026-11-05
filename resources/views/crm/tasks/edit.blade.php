@extends('layouts.user_type.auth')
@section('title','Upravit úkol')
@section('content')
<div class="form-workspace">
  <x-page-heading title="Upravit úkol" />
  <form method="POST" action="{{ route('tasks.update',$task) }}" enctype="multipart/form-data">@csrf @method('PUT')
    @include('crm.tasks._fields',['selectedProject'=>null])
    <div class="form-actions"><a class="btn btn-outline-secondary" href="{{ route('tasks.show',$task) }}">Zrušit</a><button class="btn btn-primary">Uložit změny</button></div>
  </form>
</div>
@endsection
@push('scripts')<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>@endpush
