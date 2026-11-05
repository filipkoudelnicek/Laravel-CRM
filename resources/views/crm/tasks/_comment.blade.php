{{-- Recursive comment partial. Variables: $comment, $depth --}}
@php $maxDepth = 3; @endphp

<div class="comment-item" id="comment-{{ $comment->id }}">
  <span class="person-avatar comment-avatar">{{ mb_substr($comment->user?->name ?? '?',0,1) }}</span>
  <div class="comment-content">
    <div>
      <div class="comment-header">
        <strong>{{ $comment->user?->name ?? 'Odstraněný uživatel' }}</strong><time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
        @if(auth()->user()->can('update',$comment) || auth()->user()->can('delete',$comment))
        <x-row-actions :label="'Komentář od '.($comment->user?->name ?? 'uživatele')">
          @can('update',$comment)<button type="button" role="menuitem" data-comment-edit="edit-{{ $comment->id }}"><x-icon name="edit" />Upravit</button>@endcan
          @can('delete',$comment)<form method="POST" action="{{ route('comments.destroy',$comment) }}" data-confirm="Smazat tento komentář?">@csrf @method('DELETE')<button class="danger-action" role="menuitem"><x-icon name="trash" />Smazat</button></form>@endcan
        </x-row-actions>
        @endif
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
    <div id="edit-{{ $comment->id }}" class="d-none comment-editor">
      <form method="POST" action="{{ route('comments.update', $comment) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <textarea name="body" rows="2" class="form-control form-control-sm mb-1 rich-editor-source" aria-label="Upravit komentář" required>{{ $comment->body }}</textarea>
        <input type="file" name="attachments[]" class="form-control form-control-sm mb-1" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip">
        <button class="btn bg-gradient-primary btn-sm">Uložit</button>
        <button type="button" class="btn btn-outline-secondary btn-sm"
                onclick="document.getElementById('edit-{{ $comment->id }}').classList.add('d-none')">Zrušit</button>
      </form>
    </div>
    @endcan

    {{-- Reply button + form --}}
    @if($depth < $maxDepth)
    <div class="comment-options">
      <button class="btn btn-link text-secondary text-xs p-0"
              onclick="document.getElementById('reply-{{ $comment->id }}').classList.toggle('d-none')">
        ↩ Odpovědět
      </button>
      <div id="reply-{{ $comment->id }}" class="d-none comment-editor">
        <form method="POST" action="{{ route('tasks.comments.store', $comment->task_id) }}" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="parent_id" value="{{ $comment->id }}">
          <textarea name="body" rows="2" class="form-control form-control-sm mb-1 rich-editor-source"
                    aria-label="Odpověď na komentář" placeholder="Odpověď… použijte @jméno pro zmínku" required></textarea>
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

      <div id="replies-{{ $comment->id }}" class="d-none comment-replies">
        @foreach($comment->replies as $reply)
          @include('crm.tasks._comment', ['comment' => $reply, 'depth' => $depth + 1])
        @endforeach
      </div>
    @endif
  </div>
</div>

