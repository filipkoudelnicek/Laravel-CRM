@props(['title' => null, 'icon' => null, 'columns' => [], 'rows' => []])

<div class="card mb-4">
  @if($title || $icon)
    <div class="card-header pb-3 pt-3 border-bottom-0">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
          @if($icon)
            <i class="fas fa-{{ $icon }} me-2 text-primary opacity-75"></i>
          @endif
          {{ $title }}
        </h6>
        {{ $actions ?? '' }}
      </div>
    </div>
  @endif
  
  <div class="card-body px-0 py-3">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        @if($columns)
          <thead class="table-light border-top border-bottom">
            <tr>
              @foreach($columns as $column)
                <th class="text-xs fw-bold text-secondary px-4 py-3">
                  {{ $column['label'] ?? $column }}
                </th>
              @endforeach
            </tr>
          </thead>
        @endif
        <tbody>
          {{ $slot }}
        </tbody>
      </table>
    </div>
  </div>
</div>
