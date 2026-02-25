<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'project_id', 'invoice_number', 'issued_at', 'due_at',
        'paid_at', 'status', 'currency', 'subtotal', 'tax_rate', 'tax_amount',
        'total', 'notes', 'created_by',
    ];

    protected $casts = [
        'issued_at'  => 'date',
        'due_at'     => 'date',
        'paid_at'    => 'date',
        'subtotal'   => 'decimal:2',
        'tax_rate'   => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public const STATUSES = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

    // ── relationships ─────────────────────────────────────────

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── helpers ───────────────────────────────────────────────

    /**
     * Recalculate totals from items and persist.
     */
    public function recalculate(): static
    {
        $subtotal   = $this->items()->sum(\DB::raw('qty * unit_price'));
        $taxAmount  = round($subtotal * ($this->tax_rate / 100), 2);

        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmount,
            'total'      => $subtotal + $taxAmount,
        ]);

        // also update the line-item totals
        foreach ($this->items as $item) {
            $item->update(['total' => round($item->qty * $item->unit_price, 2)]);
        }

        return $this;
    }

    /**
     * Generate next invoice number: INV-YYYYMM-XXXX
     */
    public static function generateNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last   = static::where('invoice_number', 'like', $prefix . '%')
                        ->orderByDesc('invoice_number')
                        ->value('invoice_number');

        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'paid'
            && $this->status !== 'cancelled'
            && $this->due_at
            && $this->due_at->isPast();
    }
}
