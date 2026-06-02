<?php

namespace App\Observers;

use App\Models\SupplyRequest;

class SupplyRequestObserver
{
    public function created(SupplyRequest $supplyRequest): void
    {
        $supplyRequest->code = 'SC-' . str_pad($supplyRequest->id, 4, '0', STR_PAD_LEFT);
        $supplyRequest->saveQuietly();
    }
}
