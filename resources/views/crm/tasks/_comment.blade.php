{{-- Recursive comment partial. Variables: $comment, $depth --}}
@php $maxDepth = 3; @endphp

<div class="d-flex mb-3 {{ $depth > 0 ? 'ms-'.min($depth * 4, 12) : '' }}"
     id="comment-{{ $comment->id }}">
  <div class="flex-shrink-0 me-2">
    <div class="icon icon-shape icon-sm bg-gradient-primary shadow border-radius-md text-center d-flex align-items-center justify-content-center" style="width:32px;height:32px">
      <span class="text-white text-xs font-weight-bold">{{ strtoupper(substr($comment->user->name ?? '?', 0, 1)) }}</span>
    </div>
  </div>
  <div class="flex-grow-1">
    <div class="bg-gray-100 border-radius-xl p-3">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <span class="text-sm font-weight-bold">{{ $comment->user->name ?? 'Neznámý' }}</span>
          <span class="text-xs text-secondary ms-2">{{ $comment->created_at->diffForHumans() }}</span>
        </div>
        <div class="d-flex gap-2">
          @can('update', $comment)
            <button class="btn btn-link text-secondary text-xs p-0"
                    onclick="document.getElementById('edit-{{ $comment->id }}').classList.toggle('d-none')">Upravit</button>
          @endcan
          @can('delete', $comment)
            <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="d-inline"
                  onsubmit="return confirm('Smazat komentář?')">
              @csrf @method('DELETE')
              <button class="btn btn-link text-danger text-xs p-0">Smazat</button>
            </form>
          @endcan
        </div>
      </div>
      {{-- Body: highlight @mentions --}}
      <p class="text-sm mb-0 mt-1">
        {!! preg_replace('/@([\w\-]+)/', '<strong class="text-primary">@$1</strong>', e($comment->body)) !!}
      </p>
    </div>

    {{-- Edit form --}}
    @can('update', $comment)
    <div id="edit-{{ $comment->id }}" class="d-none mt-2">
      <form method="POST" action="{{ route('comments.update', $comment) }}">
        @csrf @method('PUT')
        <textarea name="body" rows="2" class="form-control form-control-sm mb-1" required>{{ $comment->body }}</textarea>
        <button class="btn bg-gradient-primary btn-sm">Uložit</button>
        <button type="button" class="btn btn-outline-secondary btn-sm"
                onclick="document.getElementById('edit-{{ $comment->id }}').classList.add('d-none')">Zrušit</button>
      </form>
    </div>
    @endcan

    {{-- Reply button + form --}}
    @if($depth < $maxDepth)
    <div class="mt-1">
      <button class="btn btn-link text-secondary text-xs p-0"
              onclick="document.getElementById('reply-{{ $comment->id }}').classList.toggle('d-none')">
        ↩ Odpovědět
      </button>
      <div id="reply-{{ $comment->id }}" class="d-none mt-2">
        <form method="POST" action="{{ route('tasks.comments.store', $comment->task_id) }}">
          @csrf
          <input type="hidden" name="parent_id" value="{{ $comment->id }}">
          <textarea name="body" rows="2" class="form-control form-control-sm mb-1"
                    placeholder="Odpověď… použijte @jméno pro zmínku" required></textarea>
          <button class="btn bg-gradient-primary btn-sm">Odpovědět</button>
          <button type="button" class="btn btn-outline-secondary btn-sm"
                  onclick="document.getElementById('reply-{{ $comment->id }}').classList.add('d-none')">Zrušit</button>
        </form>
      </div>
    </div>
    @endif

    {{-- Nested replies --}}
    @foreach($comment->replies as $reply)
      @include('crm.tasks._comment', ['comment' => $reply, 'depth' => $depth + 1])
    @endforeach
  </div>
</div>

