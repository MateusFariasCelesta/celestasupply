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
        'unit_price',
        'total_price',
        'status',
        'delivered_quantity',
        'cancel_reason',
    ];

    protected $casts = [
        'status'     => ItemStatus::class,
        'quantity'    => 'decimal:3',
        'unit_price'  => 'decimal:2',
        'total_price' => 'decimal:2',
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
