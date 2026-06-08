<?php

namespace App\Http\Controllers;

use App\Models\ExternalOrder;
use App\Models\SupplyRequest;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExternalOrderController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(Request $request, SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('create', [ExternalOrder::class, $supplyRequest]);

        $data = $request->validate([
            'order_number' => ['nullable', 'integer', 'min:1'],
            'notes'        => ['nullable', 'string', 'max:500'],
            'file'         => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $fileData = $this->fileUpload->store(
            $request->file('file'),
            "attachments/external-orders/{$supplyRequest->id}"
        );

        $supplyRequest->externalOrders()->create([
            'order_number'  => $data['order_number'] ?: null,
            'notes'         => $data['notes'] ?: null,
            'registered_by' => auth()->id(),
            ...$fileData,
        ]);

        return back()->with('success', 'Pedido registrado.');
    }

    public function destroy(SupplyRequest $supplyRequest, ExternalOrder $externalOrder): RedirectResponse
    {
        $this->authorize('delete', $externalOrder);
        abort_if($externalOrder->supply_request_id !== $supplyRequest->id, 404);

        $this->fileUpload->delete($externalOrder->path);
        $externalOrder->delete();

        return back()->with('success', 'Pedido removido.');
    }

    public function download(SupplyRequest $supplyRequest, ExternalOrder $externalOrder): StreamedResponse
    {
        $this->authorize('download', $externalOrder);
        abort_if($externalOrder->supply_request_id !== $supplyRequest->id, 404);

        return Storage::disk('local')->download($externalOrder->path, $externalOrder->original_name);
    }

    public function view(SupplyRequest $supplyRequest, ExternalOrder $externalOrder): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('download', $externalOrder);
        abort_if($externalOrder->supply_request_id !== $supplyRequest->id, 404);

        return response()->file(Storage::disk('local')->path($externalOrder->path));
    }
}
