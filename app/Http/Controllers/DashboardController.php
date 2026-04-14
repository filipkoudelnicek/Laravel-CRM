<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Project;
use App\Models\SupportPlan;
use App\Models\Task;
use App\Support\VisibilityScope;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $months = (int) ($request->months ?? 12);
        $currency = $request->currency;
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $visibleInvoices = Invoice::query();
        $visibleProjects = Project::query();
        $visibleSupportPlans = SupportPlan::query();

        VisibilityScope::projects($visibleProjects, $user);
        VisibilityScope::invoices($visibleInvoices, $user);
        VisibilityScope::supportPlans($visibleSupportPlans, $user);

        // ── Monthly revenue (paid invoices, last N months) ────────
        // Using PHP-side grouping for DB-agnostic compatibility (SQLite/MySQL)
        $revenueQuery = (clone $visibleInvoices)
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths($months)->startOfMonth())
            ->whereNotNull('paid_at');

        if ($currency) {
            $revenueQuery->where('currency', $currency);
        }

        $paidInvoices = $revenueQuery->get(['paid_at', 'total']);

        // Build month-keyed revenue map
        $revenueMap = [];
        foreach ($paidInvoices as $inv) {
            $key = $inv->paid_at->format('Y-m');
            $revenueMap[$key] = ($revenueMap[$key] ?? 0) + (float) $inv->total;
        }

        // Fill all months with 0 for missing ones
        $allMonths = collect();
        for ($i = $months - 1; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $allMonths[$key] = $revenueMap[$key] ?? 0;
        }

        // ── Outstanding invoices ─────────────────────────────────
        $totalOutstandingQ = (clone $visibleInvoices)
            ->whereIn('status', ['sent', 'overdue']);
        if ($currency) $totalOutstandingQ->where('currency', $currency);
        $totalOutstanding = $totalOutstandingQ->sum('total');

        // ── Active projects + task breakdown ─────────────────────
        $activeProjects = (clone $visibleProjects)
            ->whereIn('status', ['active', 'on_hold'])
            ->count();

        $tasksByStatus = Task::whereHas('project', function ($q) use ($user) {
                $q->whereIn('status', ['active', 'on_hold']);

                if (!$user->isAdmin()) {
                    $q->whereHas('users', fn ($sub) => $sub->where('user_id', $user->id));
                }
            })
            ->get(['status'])
            ->groupBy('status')
            ->map(fn ($g) => $g->count());

        // ── Support plan stats ───────────────────────────────────
        $activeSupportTotalQ = (clone $visibleSupportPlans)->active();
        if ($currency) $activeSupportTotalQ->where('currency', $currency);
        $activeSupportTotal = $activeSupportTotalQ->sum('price');
        $activeSupportCount = (clone $visibleSupportPlans)->active()->count();

        $expiringSoonCount  = (clone $visibleSupportPlans)->expiringSoon(30)->count();
        $expiringSoonAmountQ = (clone $visibleSupportPlans)->expiringSoon(30);
        if ($currency) $expiringSoonAmountQ->where('currency', $currency);
        $expiringSoonAmount = $expiringSoonAmountQ->sum('price');

        // ── Available currencies for filter ──────────────────────
        $currencies = (clone $visibleInvoices)
            ->select('currency')
            ->distinct()
            ->orderBy('currency')
            ->pluck('currency');

        $supportCurrencies = (clone $visibleSupportPlans)
            ->select('currency')
            ->distinct()
            ->pluck('currency');
        $allCurrencies = $currencies->merge($supportCurrencies)->unique()->sort()->values();

        // ── Recent invoices ──────────────────────────────────────
        $recentInvoices = (clone $visibleInvoices)
            ->with('client')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'allMonths', 'totalOutstanding', 'activeProjects', 'tasksByStatus',
            'activeSupportTotal', 'activeSupportCount', 'expiringSoonCount',
            'expiringSoonAmount', 'allCurrencies', 'currency', 'months',
            'recentInvoices'
        ));
    }
}
