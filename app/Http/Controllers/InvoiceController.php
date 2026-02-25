<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        $query = Invoice::with(['client', 'project']);

        if (!auth()->user()->isAdmin()) {
            $query->where(function ($q) {
                $q->whereHas('project.users', fn ($sub) => $sub->where('user_id', auth()->id()))
                  ->orWhereHas('client.projects.users', fn ($sub) => $sub->where('user_id', auth()->id()));
            });
        }

        if ($search = $request->q) {
            $query->where(fn ($q) =>
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('client', fn ($sub) => $sub->where('name', 'like', "%{$search}%"))
            );
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $invoices = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Summary stats
        $totalOutstanding = Invoice::whereIn('status', ['sent', 'overdue'])->sum('total');
        $totalPaidMonth   = Invoice::where('status', 'paid')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total');

        return view('crm.invoices.index', compact('invoices', 'totalOutstanding', 'totalPaidMonth'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Invoice::class);

        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $nextNumber = Invoice::generateNumber();

        $selectedClient  = $request->client_id ? Client::find($request->client_id) : null;
        $selectedProject = $request->project_id ? Project::find($request->project_id) : null;

        return view('crm.invoices.create', compact('clients', 'projects', 'nextNumber', 'selectedClient', 'selectedProject'));
    }

    public function store(InvoiceRequest $request)
    {
        $this->authorize('create', Invoice::class);

        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $data['invoice_number'] = Invoice::generateNumber();
        $data['created_by']     = auth()->id();

        $invoice = Invoice::create($data);

        // Create items
        foreach ($items as $i => $item) {
            $invoice->items()->create(array_merge($item, ['sort_order' => $i]));
        }

        $invoice->recalculate();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura vytvořena.');
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);
        $invoice->load(['client', 'project', 'items', 'creator']);
        return view('crm.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $invoice->load('items');
        $clients  = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        return view('crm.invoices.edit', compact('invoice', 'clients', 'projects'));
    }

    public function update(InvoiceRequest $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $data = $request->validated();
        $items = $data['items'];
        unset($data['items']);

        $invoice->update($data);

        // Sync items: delete old, create new
        $invoice->items()->delete();
        foreach ($items as $i => $item) {
            $invoice->items()->create(array_merge($item, ['sort_order' => $i]));
        }

        $invoice->recalculate();

        return redirect()->route('invoices.show', $invoice)->with('success', 'Faktura aktualizována.');
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorize('delete', $invoice);
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Faktura smazána.');
    }
}
