@props(['title', 'description' => null])
<div class="workspace-heading"><div><h1>{{ $title }}</h1>@if($description)<p class="context-line">{{ $description }}</p>@endif</div><div class="heading-actions">{{ $slot }}</div></div>
