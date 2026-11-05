@props(['name' => 'more'])
@php
$paths = [
 'more'=>'M5 12h.01M12 12h.01M19 12h.01',
 'home'=>'m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-8H9v8H4a1 1 0 0 1-1-1Z',
 'folder'=>'M3 7V5a1 1 0 0 1 1-1h5l2 3h9a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z',
 'tasks'=>'m3 6 2 2 4-4M12 6h9M3 13l2 2 4-4M12 13h9M12 20h9M3 20h5',
 'clients'=>'M3 21V5h12v16M15 10h6v11M7 9h4M7 13h4M7 17h4M1 21h22',
 'calendar'=>'M8 2v4M16 2v4M3 10h18M4 4h16a1 1 0 0 1 1 1v15a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z',
 'invoice'=>'M5 3h14v19l-3-2-4 2-4-2-3 2ZM8 8h8M8 12h8M8 16h4',
 'key'=>'M15 15a6 6 0 1 0-6-6L2 16v6h6v-3h3v-3Z',
 'bell'=>'M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4',
 'people'=>'M16 21v-3a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v3M17 14a4 4 0 0 1 5 4v3M9 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8M17 2a4 4 0 0 1 0 8',
 'plus'=>'M12 5v14M5 12h14', 'search'=>'M21 21l-5-5M18 10a8 8 0 1 0-16 0 8 8 0 0 0 16 0',
 'arrow'=>'M5 12h14m-5-5 5 5-5 5', 'menu'=>'M4 6h16M4 12h16M4 18h16',
 'clock'=>'M12 8v4l3 2M22 12a10 10 0 1 0-20 0 10 10 0 0 0 20 0',
 'logout'=>'M9 4H4v16h5M10 12h12m-5-5 5 5-5 5',
 'play'=>'m8 4 12 8-12 8Z', 'stop'=>'M6 6h12v12H6Z',
 'filter'=>'M4 7h16M7 12h10M10 17h4',
 'edit'=>'m15 4 5 5M4 15 17 2l5 5L9 20l-7 2Z',
 'trash'=>'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7',
 'close'=>'m6 6 12 12M6 18 18 6', 'chevron'=>'m9 5 7 7-7 7',
];
@endphp
<svg {{ $attributes->class(['ui-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['more'] }}" /></svg>
