<?php

namespace App\Models;

use App\Enums\ItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyRequestItem extends Model
{
    protected $fillable = [
        'supply_request_id',
        'item_id',
        'quantity',
        'unit',
        'notes',
        'supplier_id',
        'order_number',
        'status',
        'delivered_quantity',
        'cancel_reason',
        'previous_status',
    ];

    protected $casts = [
        'status'           => ItemStatus::class,
        'previous_status'  => ItemStatus::class,
        'quantity'          => 'decimal:3',
        'delivered_quantity' => 'decimal:3',
    ];

    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
