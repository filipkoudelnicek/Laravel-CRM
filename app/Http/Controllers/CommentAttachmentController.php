<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentAttachment;
use Illuminate\Support\Facades\Storage;

class CommentAttachmentController extends Controller
{
    public function destroy(Comment $comment, CommentAttachment $attachment)
    {
        $this->authorize('update', $comment);

        if ($attachment->comment_id !== $comment->id) {
            abort(404);
        }

        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Příloha komentáře byla smazána.');
    }
}
