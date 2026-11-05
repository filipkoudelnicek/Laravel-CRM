@props(['months','currency'])
@php
  $maximum = max(1, (float) $months->max('total'));
  $step = 10 ** floor(log10($maximum));
  $ceiling = ceil($maximum / $step) * $step;
  $points = $months->values()->map(fn ($month,$i) => [
    'x'=>10 + $i * 580 / max(1,$months->count()-1),
    'y'=>190 - max(0,$month['total']) / $ceiling * 180,
    'label'=>$month['label'], 'total'=>$month['total'],
  ]);
  $line = $points->map(fn ($point) => round($point['x'],2).','.round($point['y'],2))->implode(' ');
  $area = '10,190 '.$line.' 590,190';
  $labelEvery = max(1,(int)ceil(($months->count()-1)/5));
@endphp
<figure class="revenue-chart" aria-label="Zaplacené faktury po měsících v {{ $currency }}">
  <div class="revenue-axis" aria-hidden="true">@foreach([1,2/3,1/3,0] as $ratio)<span>{{ number_format($ceiling*$ratio,0,',',' ') }}</span>@endforeach</div>
  <div class="revenue-plot">
    <svg viewBox="0 0 600 200" preserveAspectRatio="none" role="img" aria-label="Měsíční vývoj příjmů; přesné částky jsou v tabulce pod grafem.">
      @foreach([10,70,130,190] as $y)<line class="revenue-grid-line" x1="10" x2="590" y1="{{ $y }}" y2="{{ $y }}" />@endforeach
      <polygon class="revenue-area" points="{{ $area }}" />
      <polyline class="revenue-line" points="{{ $line }}" />
      @foreach($points as $point)<circle class="revenue-point" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="3.5"><title>{{ $point['label'] }}: {{ number_format($point['total'],2,',',' ') }} {{ $currency }}</title></circle>@endforeach
    </svg>
    <div class="revenue-months" aria-hidden="true">@foreach($months->values() as $i=>$month)@if($i % $labelEvery === 0 || $loop->last)<span style="left:{{ 100*$i/max(1,$months->count()-1) }}%">{{ \Carbon\Carbon::parse($month['key'].'-01')->format('n/y') }}</span>@endif @endforeach</div>
  </div>
</figure>
<details class="chart-data"><summary>Zobrazit částky po měsících</summary><div class="chart-data-table"><table><caption class="visually-hidden">Zaplacené faktury v {{ $currency }}</caption><thead><tr><th>Měsíc</th><th class="text-end">Částka</th></tr></thead><tbody>@foreach($months as $month)<tr><td>{{ $month['label'] }}</td><td class="text-end numeric">{{ number_format($month['total'],2,',',' ') }} {{ $currency }}</td></tr>@endforeach</tbody></table></div></details>
