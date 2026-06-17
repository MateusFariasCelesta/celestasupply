<?php

namespace App\Http\Controllers;

use App\Models\RequestAttachment;
use App\Models\SupplyRequest;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequestAttachmentController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(Request $request, SupplyRequest $supplyRequest): RedirectResponse
    {
        $this->authorize('create', [RequestAttachment::class, $supplyRequest]);

        $data = $request->validate([
            'type' => ['required', 'in:quote,invoice,receipt,purchase_order,other'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'order_number' => ['nullable', 'string'],
        ]);

        $fileData = $this->fileUpload->store(
            $request->file('file'),
            "attachments/requests/{$supplyRequest->id}"
        );

        $supplyRequest->attachments()->create([
            'type'        => $data['type'],
            'order_number' => $data['order_number'] ?? null,
            'uploaded_by' => auth()->id(),
            ...$fileData,
        ]);

        return back()->with('success', 'Anexo adicionado com sucesso.');
    }

    public function destroy(SupplyRequest $supplyRequest, RequestAttachment $requestAttachment): RedirectResponse
    {
        $this->authorize('delete', $requestAttachment);
        abort_if($requestAttachment->supply_request_id !== $supplyRequest->id, 404);

        $this->fileUpload->delete($requestAttachment->path);
        $requestAttachment->delete();

        return back()->with('success', 'Anexo removido.');
    }

    public function download(SupplyRequest $supplyRequest, RequestAttachment $requestAttachment): StreamedResponse
    {
        $this->authorize('download', $requestAttachment);
        abort_if($requestAttachment->supply_request_id !== $supplyRequest->id, 404);

        return Storage::disk('local')->download($requestAttachment->path, $requestAttachment->original_name);
    }

    public function view(SupplyRequest $supplyRequest, RequestAttachment $requestAttachment): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('download', $requestAttachment);
        abort_if($requestAttachment->supply_request_id !== $supplyRequest->id, 404);

        return response()->file(Storage::disk('local')->path($requestAttachment->path));
    }
}
