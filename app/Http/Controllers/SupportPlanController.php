<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupportPlanRequest;
use App\Models\Client;
use App\Models\SupportPlan;
use App\Support\VisibilityScope;
use Illuminate\Http\Request;

class SupportPlanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', SupportPlan::class);
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $query = SupportPlan::with('client');
        VisibilityScope::supportPlans($query, $user);

        if ($search = $request->q) {
            $query->where(fn ($q) =>
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($sub) => $sub->where('name', 'like', "%{$search}%"))
            );
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $plans = $query->orderByDesc('period_to')->paginate(20)->withQueryString();

        // Stats (respect visibility for non-admin users)
        $statsQuery = SupportPlan::query();
        VisibilityScope::supportPlans($statsQuery, $user);

        $activeTotal = (clone $statsQuery)
            ->active()
            ->sum('price');

        $expiringSoon = (clone $statsQuery)
            ->expiringSoon(30)
            ->count();

        $expiringAmount = (clone $statsQuery)
            ->expiringSoon(30)
            ->sum('price');

        return view('crm.support-plans.index', compact('plans', 'activeTotal', 'expiringSoon', 'expiringAmount'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', SupportPlan::class);
        $clients = Client::orderBy('name')->get();
        $selectedClient = $request->client_id ? Client::find($request->client_id) : null;
        return view('crm.support-plans.create', compact('clients', 'selectedClient'));
    }

    public function store(SupportPlanRequest $request)
    {
        $this->authorize('create', SupportPlan::class);

        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $plan = SupportPlan::create($data);

        return redirect()->route('support-plans.show', $plan)->with('success', 'Podpora vytvořena.');
    }

    public function show(SupportPlan $supportPlan)
    {
        $this->authorize('view', $supportPlan);
        $supportPlan->load(['client', 'creator']);
        return view('crm.support-plans.show', compact('supportPlan'));
    }

    public function edit(SupportPlan $supportPlan)
    {
        $this->authorize('update', $supportPlan);
        $clients = Client::orderBy('name')->get();
        return view('crm.support-plans.edit', compact('supportPlan', 'clients'));
    }

    public function update(SupportPlanRequest $request, SupportPlan $supportPlan)
    {
        $this->authorize('update', $supportPlan);
        $supportPlan->update($request->validated());
        return redirect()->route('support-plans.show', $supportPlan)->with('success', 'Podpora aktualizována.');
    }

    public function destroy(SupportPlan $supportPlan)
    {
        $this->authorize('delete', $supportPlan);
        $supportPlan->delete();
        return redirect()->route('support-plans.index')->with('success', 'Podpora smazána.');
    }
}
