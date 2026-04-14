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
      <div class="text-sm mb-0 mt-1 rich-content">{!! $comment->body !!}</div>

      @if($comment->attachments->count())
      <div class="mt-2 d-flex flex-wrap gap-2">
        @foreach($comment->attachments as $attachment)
          <div class="border rounded px-2 py-1 d-flex align-items-center gap-2" style="max-width: 300px;">
            @if($attachment->isImage())
              <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer">
                <img src="{{ $attachment->url() }}" alt="{{ $attachment->original_name }}" style="width:28px;height:28px;object-fit:cover;border-radius:4px;">
              </a>
            @else
              <i class="fas fa-paperclip text-secondary"></i>
            @endif

            <a href="{{ $attachment->url() }}" target="_blank" rel="noopener noreferrer" class="text-xs text-truncate d-inline-block" style="max-width:180px;">
              {{ $attachment->original_name }}
            </a>

            @can('update', $comment)
              <form method="POST" action="{{ route('comments.attachments.destroy', [$comment, $attachment]) }}" onsubmit="return confirm('Smazat přílohu?')">
                @csrf @method('DELETE')
                <button class="btn btn-link text-danger p-0" type="submit" title="Smazat">
                  <i class="fas fa-times"></i>
                </button>
              </form>
            @endcan
          </div>
        @endforeach
      </div>
      @endif
    </div>

    {{-- Edit form --}}
    @can('update', $comment)
    <div id="edit-{{ $comment->id }}" class="d-none mt-2">
      <form method="POST" action="{{ route('comments.update', $comment) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <textarea name="body" rows="2" class="form-control form-control-sm mb-1 rich-editor-source" required>{{ $comment->body }}</textarea>
        <input type="file" name="attachments[]" class="form-control form-control-sm mb-1" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
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
        <form method="POST" action="{{ route('tasks.comments.store', $comment->task_id) }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="parent_id" value="{{ $comment->id }}">
          <textarea name="body" rows="2" class="form-control form-control-sm mb-1 rich-editor-source"
                    placeholder="Odpověď… použijte @jméno pro zmínku" required></textarea>
          <input type="file" name="attachments[]" class="form-control form-control-sm mb-1" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
          <button class="btn bg-gradient-primary btn-sm">Odpovědět</button>
          <button type="button" class="btn btn-outline-secondary btn-sm"
                  onclick="document.getElementById('reply-{{ $comment->id }}').classList.add('d-none')">Zrušit</button>
        </form>
      </div>
    </div>
    @endif

    {{-- Nested replies --}}
    @if($comment->replies->count())
      <div class="mt-2">
        <button
          type="button"
          class="btn btn-link text-secondary text-xs p-0"
          data-comment-replies-toggle
          data-comment-id="{{ $comment->id }}"
          onclick="toggleCommentReplies(this)">
          Zobrazit odpovědi ({{ $comment->replies->count() }})
        </button>
      </div>

      <div id="replies-{{ $comment->id }}" class="d-none mt-2">
        @foreach($comment->replies as $reply)
          @include('crm.tasks._comment', ['comment' => $reply, 'depth' => $depth + 1])
        @endforeach
      </div>
    @endif
  </div>
</div>

