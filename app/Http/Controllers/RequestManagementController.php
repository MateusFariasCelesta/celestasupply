<?php

namespace App\Http\Controllers;

use App\Models\SupplyRequest;
use App\Services\RequestStatusService;
use Illuminate\Http\RedirectResponse;

class RequestManagementController extends Controller
{
    public function __construct(private RequestStatusService $statusService) {}

    public function advanceStatus(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('advanceStatus', $supplyRequest);

        $this->statusService->advance($supplyRequest, auth()->user());

        return back()->with('success', "Status avançado para: {$supplyRequest->fresh()->status->label()}.");
    }

    public function approveCancellation(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('approveCancellation', $supplyRequest);

        $this->statusService->approveCancellation($supplyRequest, auth()->user());

        return back()->with('success', 'Cancelamento aprovado. Solicitação cancelada.');
    }

    public function refuseCancellation(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('refuseCancellation', $supplyRequest);

        $this->statusService->refuseCancellation($supplyRequest, auth()->user());

        return back()->with('success', 'Cancelamento recusado. Solicitação retornou ao status anterior.');
    }
}
