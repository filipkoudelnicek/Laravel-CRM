<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionNotification;
use App\Notifications\NewCommentNotification;
use App\Support\RichTextSanitizer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Check user has access to the task's project
        if (!$user->isAdmin() &&
            !$task->project()->whereHas('users', fn ($q) => $q->where('user_id', $user->id))->exists()) {
            abort(403);
        }

        $data = $request->validate([
            'body'      => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:comments,id',
            'attachments'   => 'nullable|array|max:6',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ]);

        $data['body'] = RichTextSanitizer::sanitize($data['body']);
        if (RichTextSanitizer::plainTextLength($data['body']) === 0) {
            throw ValidationException::withMessages([
                'body' => 'Komentář nesmí být prázdný.',
            ]);
        }

        $data['task_id'] = $task->id;
        $data['user_id'] = auth()->id();

        $comment = Comment::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $comment->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'path' => $file->store('comment-attachments/' . $comment->id, 'public'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize() ?? 0,
                ]);
            }
        }

        // Detect and store @mentions, send notifications
        $this->processMentions($comment);

        // Notify assigned users (except commenter & mentioned) about new comment
        $mentionedIds = $comment->mentions->pluck('id')->toArray();
        $task->assignees
            ->reject(fn ($u) => $u->id === auth()->id() || in_array($u->id, $mentionedIds))
            ->each->notify(new NewCommentNotification($comment));

        return redirect()->route('tasks.show', $task)->with('success', 'Komentář přidán.');
    }

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $data = $request->validate([
            'body' => 'required|string|max:5000',
            'attachments'   => 'nullable|array|max:6',
            'attachments.*' => 'file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip',
        ]);

        $body = RichTextSanitizer::sanitize($data['body']);
        if (RichTextSanitizer::plainTextLength($body) === 0) {
            throw ValidationException::withMessages([
                'body' => 'Komentář nesmí být prázdný.',
            ]);
        }

        $comment->update(['body' => $body]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $comment->attachments()->create([
                    'uploaded_by' => auth()->id(),
                    'path' => $file->store('comment-attachments/' . $comment->id, 'public'),
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize() ?? 0,
                ]);
            }
        }

        // Reprocess mentions
        $comment->mentions()->detach();
        $this->processMentions($comment);

        return back()->with('success', 'Komentář upraven.');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $taskId = $comment->task_id;
        $comment->delete();
        return back()->with('success', 'Komentář smazán.');
    }

    // ── private helpers ───────────────────────────────────────

    private function processMentions(Comment $comment): void
    {
        preg_match_all('/@([\w\-]+)/', strip_tags($comment->body), $matches);

        if (empty($matches[1])) return;

        $usernames = array_unique($matches[1]);
        $comment->load('task'); // ensure task is loaded for notification

        foreach ($usernames as $username) {
            $user = User::where('name', $username)->first();
            if (!$user || $user->id === $comment->user_id) continue;

            // Sync pivot (ignore if already exists)
            $comment->mentions()->syncWithoutDetaching([$user->id]);

            // Send database notification
            $user->notify(new MentionNotification($comment));
        }
    }
}
