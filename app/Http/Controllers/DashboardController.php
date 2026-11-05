<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Invoice;
use App\Models\ProjectNote;
use App\Models\SupportPlan;
use App\Models\Task;
use App\Models\TimeEntry;
use App\Support\VisibilityScope;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $today = now(config('app.display_timezone'));
        $months = in_array((int) $request->input('months'), [6, 12, 24], true)
            ? (int) $request->input('months') : 12;
        $visibleInvoices = VisibilityScope::invoices(Invoice::query(), $user);
        $visibleProjects = VisibilityScope::projects(Project::query(), $user);
        $visibleServices = VisibilityScope::supportPlans(SupportPlan::query(), $user);

        // Amounts always belong to one visible invoice currency, never a mixed sum.
        $currencies = (clone $visibleInvoices)->distinct()->orderBy('currency')->pluck('currency');
        if ($currencies->isEmpty()) $currencies = collect(['CZK']);
        $currency = $currencies->contains($request->input('currency')) ? $request->input('currency')
            : ($currencies->contains('CZK') ? 'CZK' : $currencies->first());
        $invoices = (clone $visibleInvoices)->where('currency', $currency);
        $outstanding = (clone $invoices)->whereIn('status', ['sent', 'overdue']);
        $outstandingTotal = (float) (clone $outstanding)->sum('total');
        $outstandingCount = $outstanding->count();
        $activeProjects = (clone $visibleProjects)->where('status', 'active')->count();

        $start = $today->copy()->startOfMonth()->subMonthsNoOverflow($months - 1);
        $payments = (clone $invoices)->where('status', 'paid')->whereNotNull('paid_at')
            ->whereDate('paid_at', '>=', $start->toDateString())
            ->whereDate('paid_at', '<=', $today->toDateString())->get(['paid_at', 'total']);
        $byMonth = $payments->groupBy(fn ($invoice) => $invoice->paid_at->format('Y-m'));
        $revenueMonths = collect(range(0, $months - 1))->map(function ($offset) use ($start, $byMonth) {
            $month = $start->copy()->addMonthsNoOverflow($offset)->locale('cs');
            return ['key' => $month->format('Y-m'), 'label' => $month->translatedFormat('F Y'),
                'short' => $month->format('n/Y'),
                'total' => round((float) $byMonth->get($month->format('Y-m'), collect())->sum('total'), 2)];
        });
        $revenueTotal = (float) $revenueMonths->sum('total');
        $paidThisMonth = (float) $revenueMonths->last()['total'];
        $taskCounts = Task::whereHas('project', fn ($query) =>
            VisibilityScope::projects($query, $user)->whereIn('status', ['active', 'on_hold']))
            ->selectRaw('status, COUNT(*) as aggregate')->groupBy('status')->pluck('aggregate', 'status');
        $tasksByStatus = collect(Task::STATUSES)->mapWithKeys(fn ($status) => [$status => (int) ($taskCounts[$status] ?? 0)]);
        $activeServices = (clone $visibleServices)->active()->get(['price', 'currency']);
        $activeServiceCount = $activeServices->count();
        $activeServiceTotals = $activeServices->groupBy('currency')->map(fn ($rows) => (float) $rows->sum('price'));
        $expiring = (clone $visibleServices)->expiringSoon(30);
        $expiringCount = (clone $expiring)->count();
        $expiringServices = $expiring->with('client')->orderBy('period_to')->limit(5)->get();
        $recentInvoices = (clone $invoices)->with('client')->latest()->orderByDesc('id')->limit(5)->get();

        return view('dashboard', compact('months', 'currencies', 'currency', 'outstandingTotal',
            'outstandingCount', 'activeProjects', 'revenueMonths', 'revenueTotal', 'paidThisMonth',
            'tasksByStatus', 'activeServiceCount', 'activeServiceTotals', 'expiringCount',
            'expiringServices', 'recentInvoices'));
    }

    public function work(Request $request)
    {
        $user = $request->user();
        $taskQuery = Task::whereHas('project', fn ($q) => VisibilityScope::projects($q, $user));
        $myQuery = (clone $taskQuery)->whereHas('assignees', fn ($q) => $q->where('users.id', $user->id))
            ->where('status', '!=', 'done');
        $myTaskCount = (clone $myQuery)->count();
        $overdueCount = (clone $myQuery)->whereDate('due_at', '<', now(config('app.display_timezone'))->toDateString())->count();
        $myTasks = (clone $myQuery)->with(['project.client', 'assignees'])
            ->orderByRaw('due_at IS NULL')->orderBy('due_at')->orderByDesc('created_at')->limit(8)->get();

        $activeProjects = VisibilityScope::projects(Project::query(), $user)->where('status', 'active')->count();
        $serviceQuery = VisibilityScope::supportPlans(SupportPlan::query(), $user)->expiringSoon(30);
        $expiringCount = (clone $serviceQuery)->count();
        $expiringServices = $serviceQuery->with('client')->orderBy('period_to')->limit(5)->get();
        $recentNotes = ProjectNote::with(['user', 'project.client'])
            ->whereHas('project', fn ($q) => VisibilityScope::projects($q, $user))->latest()->limit(5)->get();

        $weekStart = now(config('app.display_timezone'))->startOfWeek()->utc();
        $weekMinutes = TimeEntry::where('created_by', $user->id)->whereNotNull('ended_at')
            ->where('ended_at', '>=', $weekStart)->where('started_at', '<=', now())->get()
            ->sum(fn ($e) => (int) max(0, $e->started_at->max($weekStart)->diffInMinutes($e->ended_at->min(now()), false)));

        return view('work', compact('myTasks', 'myTaskCount', 'overdueCount', 'activeProjects',
            'expiringCount', 'expiringServices', 'recentNotes', 'weekMinutes'));
    }
}
