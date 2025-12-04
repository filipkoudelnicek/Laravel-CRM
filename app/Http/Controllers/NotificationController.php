<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Notification::where('user_id', auth()->id());

        if ($request->boolean('unreadOnly')) {
            $query->where('read', false);
        }

        $notifications = $query->with('task')->latest()->take(20)->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request): JsonResponse
    {
        if ($request->filled('notification_id')) {
            Notification::where('id', $request->notification_id)
                ->where('user_id', auth()->id())
                ->update(['read' => true]);
        } else {
            Notification::where('user_id', auth()->id())
                ->update(['read' => true]);
        }

        return response()->json(['success' => true]);
    }
}

