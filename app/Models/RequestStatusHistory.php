<?php

namespace App\Models;

use App\Enums\RequestStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestStatusHistory extends Model
{
    protected $table = 'request_status_history';

    protected $fillable = [
        'supply_request_id',
        'from_status',
        'to_status',
        'changed_by',
    ];

    protected $casts = [
        'from_status' => RequestStatus::class,
        'to_status'   => RequestStatus::class,
    ];

    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
