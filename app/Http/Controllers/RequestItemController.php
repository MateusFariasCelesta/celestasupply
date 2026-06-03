<?php

namespace App\Http\Controllers;

use App\Enums\ItemStatus;
use App\Enums\RequestStatus;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Services\RequestStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class RequestItemController extends Controller
{
    public function __construct(private RequestStatusService $statusService) {}

    public function updateStatus(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('updateStatus', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $next = $supplyRequestItem->status->nextStatus();

        $extra = [];
        if ($supplyRequestItem->status === ItemStatus::Quoting) {
            $extra = $request->validate([
                'order_number' => ['required', 'integer', 'min:1'],
            ]);
        }

        $supplyRequestItem->update(['status' => $next, ...$extra]);

        $this->autoAdvanceRequest($supplyRequest);

        return back()->with('success', "Item avançado para: {$next->label()}.");
    }

    public function setSupplier(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('setSupplier', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $supplyRequestItem->update(['supplier_id' => $data['supplier_id'] ?: null]);

        return back()->with('success', 'Fornecedor atualizado.');
    }

    public function jumpStatus(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('jumpStatus', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $data = $request->validate([
            'status' => ['required', new Enum(ItemStatus::class)],
        ]);

        $to = ItemStatus::from($data['status']);

        $keepReason = in_array($to, [ItemStatus::Cancelled, ItemStatus::CancelRequested]);

        $supplyRequestItem->update([
            'status'          => $to,
            'cancel_reason'   => $keepReason ? $supplyRequestItem->cancel_reason : null,
            'previous_status' => $keepReason ? $supplyRequestItem->previous_status : null,
        ]);

        $this->autoAdvanceRequest($supplyRequest);

        return back()->with('success', "Item alterado para: {$to->label()}.");
    }

    private function autoAdvanceRequest(SupplyRequest $supplyRequest): void
    {
        if ($supplyRequest->status === RequestStatus::Pending) {
            $this->statusService->advance($supplyRequest, auth()->user());
        }
    }

    public function registerDelivery(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('registerDelivery', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $remaining = $supplyRequestItem->quantity - $supplyRequestItem->delivered_quantity;

        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.001', 'max:' . $remaining],
            'notes'    => ['nullable', 'string', 'max:500'],
        ]);

        $supplyRequestItem->deliveries()->create([
            'quantity'      => $data['quantity'],
            'notes'         => $data['notes'] ?? null,
            'registered_by' => auth()->id(),
        ]);

        $newDelivered = $supplyRequestItem->delivered_quantity + $data['quantity'];
        $updates = ['delivered_quantity' => $newDelivered];

        if ($newDelivered >= $supplyRequestItem->quantity) {
            $updates['status'] = ItemStatus::Received;
        }

        $supplyRequestItem->update($updates);
        $this->autoAdvanceRequest($supplyRequest);

        return back()->with('success', 'Entrega registrada com sucesso.');
    }

    public function requestCancellation(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('requestCancellation', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:500'],
        ]);

        $supplyRequestItem->update([
            'previous_status' => $supplyRequestItem->status->value,
            'status'          => ItemStatus::CancelRequested,
            'cancel_reason'   => $data['cancel_reason'],
        ]);

        return back()->with('success', 'Cancelamento do item solicitado.');
    }

    public function approveCancellation(SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('approveCancellation', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $supplyRequestItem->update([
            'status'          => ItemStatus::Cancelled,
            'previous_status' => null,
        ]);

        return back()->with('success', 'Cancelamento do item aprovado.');
    }

    public function refuseCancellation(SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('refuseCancellation', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $restoreTo = $supplyRequestItem->previous_status ?? ItemStatus::Pending;

        $supplyRequestItem->update([
            'status'          => $restoreTo,
            'cancel_reason'   => null,
            'previous_status' => null,
        ]);

        return back()->with('success', 'Cancelamento do item recusado. Item restaurado.');
    }

    public function cancel(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('cancel', $supplyRequestItem);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $data = $request->validate([
            'cancel_reason' => ['required', 'string', 'max:500'],
        ]);

        $supplyRequestItem->update([
            'status'        => ItemStatus::Cancelled,
            'cancel_reason' => $data['cancel_reason'],
        ]);

        return back()->with('success', 'Item cancelado.');
    }
}
