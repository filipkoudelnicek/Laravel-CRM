<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\MentionNotification;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        // Check user has access to the task's project
        if (!auth()->user()->isAdmin() &&
            !$task->project()->whereHas('users', fn ($q) => $q->where('user_id', auth()->id()))->exists()) {
            abort(403);
        }

        $data = $request->validate([
            'body'      => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // XSS: strip all tags, store plain text
        $data['body']    = strip_tags($data['body']);
        $data['task_id'] = $task->id;
        $data['user_id'] = auth()->id();

        $comment = Comment::create($data);

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
        ]);

        $comment->update(['body' => strip_tags($data['body'])]);

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
        preg_match_all('/@([\w\-]+)/', $comment->body, $matches);

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
