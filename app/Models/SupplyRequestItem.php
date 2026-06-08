<?php

namespace App\Models;

use App\Enums\ItemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function attachment(): HasOne
    {
        return $this->hasOne(ItemAttachment::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(ItemDelivery::class)->orderBy('created_at');
    }

    public function formattedQuantity(): string
    {
        return rtrim(rtrim(number_format((float) $this->quantity, 3, ',', ''), '0'), ',');
    }

    public function formattedOrderNumber(): ?string
    {
        return $this->order_number !== null
            ? 'PC-' . str_pad($this->order_number, 4, '0', STR_PAD_LEFT)
            : null;
    }
}
