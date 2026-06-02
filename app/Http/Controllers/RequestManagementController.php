<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Models\SupplyRequest;
use App\Services\RequestStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RequestManagementController extends Controller
{
    public function __construct(private RequestStatusService $statusService) {}

    public function advanceStatus(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('advanceStatus', $supplyRequest);

        try {
            $this->statusService->advance($supplyRequest, auth()->user());
        } catch (\LogicException $e) {
            return back()->withErrors(['advance' => $e->getMessage()]);
        }

        return back()->with('success', "Status avançado para: {$supplyRequest->fresh()->status->label()}.");
    }

    public function cancelDirect(Request $request, SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('cancelDirect', $supplyRequest);

        $data = $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000'],
        ]);

        $blocked = $supplyRequest->items()
            ->whereNotIn('status', ['received', 'cancelled'])
            ->count();

        if ($blocked > 0) {
            return back()->withErrors([
                'cancel_direct' => "Há {$blocked} item(ns) que ainda não foram recebidos ou cancelados.",
            ])->withInput();
        }

        $supplyRequest->update(['cancellation_reason' => $data['cancellation_reason']]);

        $this->statusService->approveCancellation($supplyRequest, auth()->user());

        return back()->with('success', 'Solicitação cancelada.');
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

    public function jumpStatus(Request $request, SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('jumpStatus', $supplyRequest);

        $data = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_column(RequestStatus::cases(), 'value'))],
        ]);

        $to = RequestStatus::from($data['status']);

        $this->statusService->jumpToStatus($supplyRequest, $to, auth()->user());

        return back()->with('success', "Status alterado para: {$to->label()}.");
    }
}
