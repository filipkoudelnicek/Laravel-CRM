<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'name', 'description', 'qty', 'unit_price', 'total', 'sort_order',
    ];

    protected $casts = [
        'qty'        => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    // ── relationships ─────────────────────────────────────────

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // ── event hooks ───────────────────────────────────────────

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            $item->total = round($item->qty * $item->unit_price, 2);
        });
    }
}
