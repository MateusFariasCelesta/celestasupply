<?php

namespace App\Http\Controllers;

use App\Models\ItemAttachment;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemAttachmentController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function store(Request $request, SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $this->authorize('create', [ItemAttachment::class, $supplyRequestItem]);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($existing = $supplyRequestItem->attachment) {
            $this->fileUpload->delete($existing->path);
            $existing->delete();
        }

        $data = $this->fileUpload->store(
            $request->file('file'),
            "attachments/request-items/{$supplyRequestItem->id}"
        );

        $supplyRequestItem->attachment()->create([...$data, 'uploaded_by' => auth()->id()]);

        return back()->with('success', 'Arquivo anexado com sucesso.');
    }

    public function destroy(SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): RedirectResponse
    {
        $attachment = $supplyRequestItem->attachment;
        abort_if(!$attachment, 404);
        $this->authorize('delete', $attachment);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        $this->fileUpload->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Arquivo removido.');
    }

    public function download(SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): StreamedResponse
    {
        $attachment = $supplyRequestItem->attachment;
        abort_if(!$attachment, 404);
        $this->authorize('download', $attachment);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }

    public function view(SupplyRequest $supplyRequest, SupplyRequestItem $supplyRequestItem): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $attachment = $supplyRequestItem->attachment;
        abort_if(!$attachment, 404);
        $this->authorize('download', $attachment);
        abort_if($supplyRequestItem->supply_request_id !== $supplyRequest->id, 404);

        return response()->file(Storage::disk('local')->path($attachment->path));
    }
}
