<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Http\Requests\StoreSupplyRequestRequest;
use App\Http\Requests\UpdateSupplyRequestRequest;
use App\Models\CostCenter;
use App\Models\Item;
use App\Models\SupplyRequest;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\RequestStatusService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplyRequestController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}
    public function index(): View
    {
        $this->authorize('viewAny', SupplyRequest::class);

        $query = SupplyRequest::with(['costCenter', 'user', 'items.item'])
            ->orderByDesc('created_at');

        if (!auth()->user()->isBuyerOrAdmin()) {
            $query->where('user_id', auth()->id());
        } elseif (!auth()->user()->isAdmin()) {
            // buyer: vê tudo exceto rascunhos de outros usuários
            $query->where(function ($q) {
                $q->where('status', '!=', RequestStatus::Draft->value)
                  ->orWhere('user_id', auth()->id());
            });
        }

        $supplyRequests = $query->get();
        $costCenters    = CostCenter::where('isActive', true)->orderBy('id')->get();
        $requesters     = auth()->user()->isBuyerOrAdmin()
            ? User::where('isActive', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('supply-requests.index', compact('supplyRequests', 'costCenters', 'requesters'));
    }

    public function create(): View
    {
        $this->authorize('create', SupplyRequest::class);

        $costCenters = CostCenter::where('isActive', true)->orderBy('id')->get();
        $items       = Item::where('isActive', true)->orderBy('name')->get();

        return view('supply-requests.create', compact('costCenters', 'items'));
    }

    public function store(StoreSupplyRequestRequest $request, RequestStatusService $statusService): RedirectResponse
    {
        $this->authorize('create', SupplyRequest::class);

        $data = $request->validated();

        $sr = SupplyRequest::create([
            'title'          => $data['title'],
            'cost_center_id' => $data['cost_center_id'],
            'urgency'        => $data['urgency'],
            'notes'          => $data['notes'] ?? null,
            'user_id'        => auth()->id(),
            'status'         => RequestStatus::Draft,
        ]);

        foreach ($data['items'] as $idx => $row) {
            $item = $sr->items()->create([
                'item_id'  => $this->resolveItemId($row['item_id']),
                'quantity' => $row['quantity'],
                'unit'     => $row['unit'] ?? null,
                'notes'    => $row['notes'] ?? null,
            ]);

            if ($file = $request->file("items.{$idx}.attachment")) {
                $fileData = $this->fileUpload->store($file, "attachments/request-items/{$item->id}");
                $item->attachment()->create([...$fileData, 'uploaded_by' => auth()->id()]);
            }
        }

        $this->storeAttachments($request, $sr);

        if ($data['action'] === 'submit') {
            $statusService->submit($sr, auth()->user());
            return redirect()->route('requests.show', $sr)->with('success', 'Solicitação enviada com sucesso.');
        }

        return redirect()->route('requests.show', $sr)->with('success', 'Rascunho salvo com sucesso.');
    }

    public function show(SupplyRequest $supplyRequest): View
    {
        $this->authorize('view', $supplyRequest);

        $supplyRequest->load([
            'costCenter',
            'user',
            'items.item',
            'items.supplier',
            'items.attachment.uploadedBy',
            'items.deliveries.registeredBy',
            'attachments.uploadedBy',
            'statusHistory.changedBy',
        ]);

        $suppliers = auth()->user()->isBuyerOrAdmin()
            ? \App\Models\Supplier::where('isActive', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        $items = Item::where('isActive', true)->orderBy('name')->get();

        return view('supply-requests.show', compact('supplyRequest', 'suppliers', 'items'));
    }

    public function edit(SupplyRequest $supplyRequest): View
    {
        $this->authorize('update', $supplyRequest);

        $costCenters = CostCenter::where('isActive', true)->orderBy('id')->get();
        $items       = Item::where('isActive', true)->orderBy('name')->get();

        $supplyRequest->load('items.item', 'items.attachment', 'attachments');

        return view('supply-requests.edit', compact('supplyRequest', 'costCenters', 'items'));
    }

    public function update(UpdateSupplyRequestRequest $request, SupplyRequest $supplyRequest, RequestStatusService $statusService): RedirectResponse
    {
        $this->authorize('update', $supplyRequest);

        $data = $request->validated();

        $supplyRequest->update([
            'title'          => $data['title'],
            'cost_center_id' => $data['cost_center_id'],
            'urgency'        => $data['urgency'],
            'notes'          => $data['notes'] ?? null,
        ]);

        // Preserve attachment data before cascade-delete removes the records
        $existingAtts = $supplyRequest->items()
            ->with('attachment')
            ->get()
            ->keyBy('id')
            ->map(fn($i) => $i->attachment)
            ->filter();

        $supplyRequest->items()->delete();

        foreach ($data['items'] as $idx => $row) {
            $item = $supplyRequest->items()->create([
                'item_id'  => $this->resolveItemId($row['item_id']),
                'quantity' => $row['quantity'],
                'unit'     => $row['unit'] ?? null,
                'notes'    => $row['notes'] ?? null,
            ]);

            if ($file = $request->file("items.{$idx}.attachment")) {
                $fileData = $this->fileUpload->store($file, "attachments/request-items/{$item->id}");
                $item->attachment()->create([...$fileData, 'uploaded_by' => auth()->id()]);
            } elseif (!empty($row['existing_item_id']) && $att = $existingAtts->get((int) $row['existing_item_id'])) {
                $item->attachment()->create([
                    'original_name' => $att->original_name,
                    'path'          => $att->path,
                    'mime_type'     => $att->mime_type,
                    'size_kb'       => $att->size_kb,
                    'uploaded_by'   => $att->uploaded_by,
                ]);
            }
        }

        $this->storeAttachments($request, $supplyRequest);

        if ($data['action'] === 'submit') {
            $statusService->submit($supplyRequest, auth()->user());
            return redirect()->route('requests.show', $supplyRequest)->with('success', 'Solicitação enviada com sucesso.');
        }

        return redirect()->route('requests.show', $supplyRequest)->with('success', 'Rascunho salvo com sucesso.');
    }

    public function submit(SupplyRequest $supplyRequest, RequestStatusService $statusService): RedirectResponse
    {
        $this->authorize('submit', $supplyRequest);

        $statusService->submit($supplyRequest, auth()->user());

        return redirect()->route('requests.show', $supplyRequest)
            ->with('success', 'Solicitação enviada com sucesso.');
    }

    public function destroy(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('delete', $supplyRequest);

        $supplyRequest->items()->delete();
        $supplyRequest->delete();

        return redirect()->route('requests.index')
            ->with('success', 'Rascunho excluído com sucesso.');
    }

    private function storeAttachments(\Illuminate\Http\Request $request, SupplyRequest $sr): void
    {
        $files = $request->file('files', []);
        $types = $request->input('file_types', []);

        foreach ($files as $index => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }
            $fileData = $this->fileUpload->store($file, "attachments/requests/{$sr->id}");
            $sr->attachments()->create([
                'type'        => $types[$index] ?? 'other',
                'uploaded_by' => auth()->id(),
                ...$fileData,
            ]);
        }
    }

    private function resolveItemId(string $raw): int
    {
        if (str_starts_with($raw, 'new:')) {
            $name = trim(substr($raw, 4));
            return Item::firstOrCreate(['name' => $name], ['isActive' => true])->id;
        }
        return (int) $raw;
    }

    public function cancelRequest(SupplyRequest $supplyRequest, RequestStatusService $statusService): RedirectResponse
    {
        $this->authorize('cancelRequest', $supplyRequest);

        $reason = request()->validate([
            'cancellation_reason' => ['required', 'string', 'max:1000'],
        ])['cancellation_reason'];

        $statusService->requestCancellation($supplyRequest, auth()->user(), $reason);

        return redirect()->route('requests.show', $supplyRequest)
            ->with('success', 'Cancelamento solicitado.');
    }

    public function saveItems(\Illuminate\Http\Request $request, SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('saveItems', $supplyRequest);

        $data = $request->validate([
            'items'            => ['required', 'array'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit'     => ['nullable', 'string', 'max:50'],
            'items.*.notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $allowedIds = $supplyRequest->items()->pluck('id')->all();

        foreach ($data['items'] as $itemId => $row) {
            if (!in_array((int) $itemId, $allowedIds)) {
                continue;
            }
            \App\Models\SupplyRequestItem::where('id', $itemId)->update([
                'quantity' => $row['quantity'],
                'unit'     => $row['unit'] ?? null,
                'notes'    => $row['notes'] ?? null,
            ]);
        }

        return redirect()->route('requests.show', $supplyRequest)
            ->with('success', 'Itens salvos com sucesso.');
    }

    public function addItem(SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('addItem', $supplyRequest);

        $data = request()->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'numeric', 'min:0.001'],
            'unit' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $supplyRequest->items()->create([
            'item_id' => $data['item_id'],
            'quantity' => $data['quantity'],
            'unit' => $data['unit'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('requests.show', $supplyRequest)
            ->with('success', 'Item adicionado com sucesso.');
    }
}
