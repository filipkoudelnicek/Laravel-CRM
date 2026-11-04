<?php

namespace App\Http\Middleware;

use App\Models\TimeEntry;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShareActiveTimer
{
    public function handle(Request $request, Closure $next)
    {
        View::share('activeTimer', TimeEntry::with('task.project')
            ->where('created_by', $request->user()->id)
            ->whereNull('ended_at')->oldest('started_at')->first());

        return $next($request);
    }
}
