@props(['placeholder' => 'Hledat…'])
<div class="search-field"><x-icon name="search" /><label class="visually-hidden" for="list-search">{{ $placeholder }}</label><input id="list-search" type="search" name="q" class="form-control" placeholder="{{ $placeholder }}" value="{{ request('q') }}"></div>
