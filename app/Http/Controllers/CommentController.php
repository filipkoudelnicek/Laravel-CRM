<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Task;
use App\Services\MentionService;
use App\Notifications\CommentMention;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    protected MentionService $mentionService;

    public function __construct(MentionService $mentionService)
    {
        $this->mentionService = $mentionService;
    }

    public function store(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'parent_comment_id' => 'nullable|exists:comments,id',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['task_id'] = $task->id;

        $comment = Comment::create($validated);

        // Zpracování @mentions
        $mentionedUsers = $this->mentionService->extractMentions($request->content);
        foreach ($mentionedUsers as $user) {
            if ($user->id !== auth()->id()) {
                $user->notify(new CommentMention($comment));
            }
        }

        // Nahrání přílohy pokud je
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('attachments', 'public');
            
            $comment->attachments()->create([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);
        }

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Komentář byl přidán.');
    }

    public function uploadAttachment(Request $request, Comment $comment): RedirectResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $comment->attachments()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);

        return redirect()->back()
            ->with('success', 'Příloha byla nahrána.');
    }
}

