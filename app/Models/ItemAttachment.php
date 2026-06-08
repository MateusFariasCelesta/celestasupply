<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemAttachment extends Model
{

    protected $fillable = [
        'supply_request_item_id',
        'original_name',
        'path',
        'mime_type',
        'size_kb',
        'uploaded_by',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(SupplyRequestItem::class, 'supply_request_item_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
