<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\StoreSupplyRequestRequest;
use App\Http\Requests\UpdateSupplyRequestRequest;
use App\Models\CostCenter;
use App\Models\Item;
use App\Models\SupplyRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplyRequestController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', SupplyRequest::class);

        $query = SupplyRequest::with(['costCenter', 'user', 'items.item'])
            ->orderByDesc('created_at');

        if (!auth()->user()->isBuyerOrAdmin()) {
            $query->where('user_id', auth()->id());
        }

        $supplyRequests = $query->get();
        $costCenters    = CostCenter::where('isActive', true)->orderBy('name')->get();
        $requesters     = auth()->user()->isBuyerOrAdmin()
            ? User::where('isActive', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('supply-requests.index', compact('supplyRequests', 'costCenters', 'requesters'));
    }

    public function create(): View
    {
        $this->authorize('create', SupplyRequest::class);

        $costCenters = CostCenter::where('isActive', true)->orderBy('name')->get();
        $items       = Item::where('isActive', true)->orderBy('name')->get();

        return view('supply-requests.create', compact('costCenters', 'items'));
    }

    public function store(StoreSupplyRequestRequest $request): RedirectResponse
    {
        $this->authorize('create', SupplyRequest::class);

        $data = $request->validated();

        $sr = SupplyRequest::create([
            'title'          => $data['title'],
            'cost_center_id' => $data['cost_center_id'],
            'urgency'        => $data['urgency'],
            'notes'          => $data['notes'] ?? null,
            'user_id'        => auth()->id(),
            'status'         => $data['action'] === 'submit' ? RequestStatus::Pending : RequestStatus::Draft,
        ]);

        foreach ($data['items'] as $row) {
            $sr->items()->create([
                'item_id'  => $this->resolveItemId($row['item_id']),
                'quantity' => $row['quantity'],
                'unit'     => $row['unit'] ?? null,
                'notes'    => $row['notes'] ?? null,
            ]);
        }

        $message = $data['action'] === 'submit'
            ? 'Solicitação enviada com sucesso.'
            : 'Rascunho salvo com sucesso.';

        return redirect()->route('requests.show', $sr)->with('success', $message);
    }

    public function show(SupplyRequest $supplyRequest): View
    {
        $this->authorize('view', $supplyRequest);

        $supplyRequest->load(['costCenter', 'user', 'items.item', 'items.supplier']);

        return view('supply-requests.show', compact('supplyRequest'));
    }

    public function edit(SupplyRequest $supplyRequest): View
    {
        $this->authorize('update', $supplyRequest);

        $costCenters = CostCenter::where('isActive', true)->orderBy('name')->get();
        $items       = Item::where('isActive', true)->orderBy('name')->get();

        $supplyRequest->load('items.item');

        return view('supply-requests.edit', compact('supplyRequest', 'costCenters', 'items'));
    }

    public function update(UpdateSupplyRequestRequest $request, SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('update', $supplyRequest);

        $data = $request->validated();

        $supplyRequest->update([
            'title'          => $data['title'],
            'cost_center_id' => $data['cost_center_id'],
            'urgency'        => $data['urgency'],
            'notes'          => $data['notes'] ?? null,
            'status'         => $data['action'] === 'submit' ? RequestStatus::Pending : RequestStatus::Draft,
        ]);

        $supplyRequest->items()->delete();
        foreach ($data['items'] as $row) {
            $supplyRequest->items()->create([
                'item_id'  => $this->resolveItemId($row['item_id']),
                'quantity' => $row['quantity'],
                'unit'     => $row['unit'] ?? null,
                'notes'    => $row['notes'] ?? null,
            ]);
        }

        $message = $data['action'] === 'submit'
            ? 'Solicitação enviada com sucesso.'
            : 'Rascunho salvo com sucesso.';

        return redirect()->route('requests.show', $supplyRequest)->with('success', $message);
    }

    public function submit(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('submit', $supplyRequest);

        $supplyRequest->update(['status' => RequestStatus::Pending]);

        return redirect()->route('requests.show', $supplyRequest)
            ->with('success', 'Solicitação enviada com sucesso.');
    }

    private function resolveItemId(string $raw): int
    {
        if (str_starts_with($raw, 'new:')) {
            $name = trim(substr($raw, 4));
            return Item::firstOrCreate(['name' => $name], ['isActive' => true])->id;
        }
        return (int) $raw;
    }

    public function cancelRequest(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('cancelRequest', $supplyRequest);

        $supplyRequest->update(['status' => RequestStatus::CancelRequested]);

        return redirect()->route('requests.show', $supplyRequest)
            ->with('success', 'Cancelamento solicitado.');
    }
}
