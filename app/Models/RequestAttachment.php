<?php

namespace App\Models;

use App\Enums\AttachmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequestAttachment extends Model
{

    protected $fillable = [
        'supply_request_id',
        'type',
        'original_name',
        'path',
        'mime_type',
        'size_kb',
        'uploaded_by',
        'order_number',
    ];

    protected $casts = [
        'type' => AttachmentType::class,
    ];

    public function supplyRequest(): BelongsTo
    {
        return $this->belongsTo(SupplyRequest::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
