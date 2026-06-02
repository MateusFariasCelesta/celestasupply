<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\Urgency;
use App\Observers\SupplyRequestObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy(SupplyRequestObserver::class)]
class SupplyRequest extends Model
{
    protected $fillable = [
        'title',
        'cost_center_id',
        'user_id',
        'urgency',
        'status',
        'previous_status',
        'notes',
        'cancellation_reason',
    ];

    protected $casts = [
        'status'  => RequestStatus::class,
        'urgency' => Urgency::class,
    ];

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplyRequestItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(RequestStatusHistory::class)->orderBy('created_at');
    }

    public function isDraft(): bool
    {
        return $this->status === RequestStatus::Draft;
    }
}
