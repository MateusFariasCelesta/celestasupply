@extends('layouts.app')
@section('title', $supplyRequest->code . ' — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h1 class="cs-page-title mb-0">{{ $supplyRequest->title }}</h1>
            <span class="badge bg-light text-dark border fw-semibold">{{ $supplyRequest->code }}</span>
            <span class="cs-badge {{ $supplyRequest->status->badgeClass() }}">{{ $supplyRequest->status->label() }}</span>
            <span class="cs-badge {{ $supplyRequest->urgency->badgeClass() }}">{{ $supplyRequest->urgency->label() }}</span>
        </div>
    </div>
</div>

<x-status-timeline :supply-request="$supplyRequest" />

<div class="row g-4">
    {{-- Details --}}
    <div class="col-md-3">
        <div class="cs-card h-100">
            <h6 class="fw-semibold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Detalhes</h6>
            <dl class="mb-0" style="font-size:14px">
                <dt class="text-muted fw-normal" style="font-size:12px">Centro de Custo</dt>
                <dd class="mb-3">{{ $supplyRequest->costCenter->name }}</dd>

                <dt class="text-muted fw-normal" style="font-size:12px">Solicitante</dt>
                <dd class="mb-3">{{ $supplyRequest->user->name }}</dd>

                <dt class="text-muted fw-normal" style="font-size:12px">Data</dt>
                <dd class="mb-3">{{ $supplyRequest->created_at->format('d/m/Y H:i') }}</dd>

                @if($supplyRequest->notes)
                <dt class="text-muted fw-normal" style="font-size:12px">Observações</dt>
                <dd class="mb-3">{{ $supplyRequest->notes }}</dd>
                @endif

                @if($supplyRequest->cancellation_reason)
                <dt class="text-muted fw-normal" style="font-size:12px">Motivo do Cancelamento</dt>
                <dd class="mb-0">
                    <span class="text-danger">{{ $supplyRequest->cancellation_reason }}</span>
                </dd>
                @endif
            </dl>
        </div>
    </div>

    {{-- Items --}}
    <div class="col-md-9">
        <div class="cs-card">
            <h6 class="fw-semibold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Itens</h6>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Item</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Qtd.</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Unidade</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Fornecedor</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Status</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Nº PC</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Obs.</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Anexo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplyRequest->items as $item)
                        <tr @if($item->status === \App\Enums\ItemStatus::Cancelled) style="opacity:.55" @endif>
                            <td style="font-size:14px;font-weight:500">{{ $item->item->name }}</td>
                            <td style="font-size:14px">{{ $item->quantity }}</td>
                            <td style="font-size:13px;color:#64748B">{{ $item->unit ?? '—' }}</td>
                            <td style="font-size:13px;min-width:160px">
                                @can('setSupplier', $item)
                                <form method="POST" action="{{ route('requests.items.supplier', [$supplyRequest, $item]) }}"
                                      class="d-flex align-items-center gap-1">
                                    @csrf @method('PATCH')
                                    <select name="supplier_id" class="form-select form-select-sm"
                                            onchange="this.form.submit()"
                                            style="font-size:12px;min-width:120px">
                                        <option value="">— Nenhum —</option>
                                        @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}" @selected($item->supplier_id == $s->id)>
                                            {{ $s->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </form>
                                @else
                                <span style="color:#64748B">{{ $item->supplier?->name ?? '—' }}</span>
                                @endcan
                            </td>
                            <td style="min-width:200px">
                                <span class="cs-badge {{ $item->status->badgeClass() }}">
                                    {{ $item->status->label() }}
                                </span>
                                @if($item->cancel_reason)
                                <div style="font-size:11px;color:#94A3B8;margin-top:3px">{{ $item->cancel_reason }}</div>
                                @endif

                                @can('jumpStatus', $item)
                                <form method="POST" action="{{ route('requests.items.jumpStatus', [$supplyRequest, $item]) }}" class="mt-2">
                                    @csrf @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()"
                                            style="font-size:12px;border-color:#FDE047;color:#92400E;background:#FFFBEB">
                                        <option value="" disabled selected>⚡ Forçar para…</option>
                                        @foreach(\App\Enums\ItemStatus::cases() as $s)
                                            @if($s !== $item->status)
                                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </form>
                                @endcan

                                @if(auth()->user()->isBuyerOrAdmin() || $supplyRequest->user_id === auth()->id())
                                <div class="d-flex gap-1 flex-wrap mt-2">
                                    @if(auth()->user()->isBuyerOrAdmin())
                                        @can('updateStatus', $item)
                                        @if($item->status === \App\Enums\ItemStatus::Quoting)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                style="font-size:11px;padding:2px 8px"
                                                data-bs-toggle="modal"
                                                data-bs-target="#orderNumberModal"
                                                data-action="{{ route('requests.items.status', [$supplyRequest, $item]) }}"
                                                data-item-name="{{ $item->item->name }}">
                                            <i class="bi bi-arrow-right"></i> {{ $item->status->nextStatus()->label() }}
                                        </button>
                                        @else
                                        <form method="POST" action="{{ route('requests.items.status', [$supplyRequest, $item]) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    title="Avançar para {{ $item->status->nextStatus()->label() }}"
                                                    style="font-size:11px;padding:2px 8px">
                                                <i class="bi bi-arrow-right"></i> {{ $item->status->nextStatus()->label() }}
                                            </button>
                                        </form>
                                        @endif
                                        @endcan

                                        @can('approveCancellation', $item)
                                        <form method="POST" action="{{ route('requests.items.approveCancellation', [$supplyRequest, $item]) }}" class="d-inline"
                                              onsubmit="return confirm('Aprovar o cancelamento deste item?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    style="font-size:11px;padding:2px 8px">
                                                <i class="bi bi-check"></i> Aprovar
                                            </button>
                                        </form>
                                        @endcan

                                        @can('refuseCancellation', $item)
                                        <form method="POST" action="{{ route('requests.items.refuseCancellation', [$supplyRequest, $item]) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                    style="font-size:11px;padding:2px 8px">
                                                <i class="bi bi-arrow-counterclockwise"></i> Recusar
                                            </button>
                                        </form>
                                        @endcan

                                        @can('cancel', $item)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                style="font-size:11px;padding:2px 7px"
                                                title="Cancelar item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cancelItemModal"
                                                data-item-id="{{ $item->id }}"
                                                data-item-name="{{ $item->item->name }}"
                                                data-action="{{ route('requests.items.cancel', [$supplyRequest, $item]) }}">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        @endcan
                                    @endif

                                    @can('requestCancellation', $item)
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            style="font-size:11px;padding:2px 8px"
                                            data-bs-toggle="modal"
                                            data-bs-target="#requestCancelItemModal"
                                            data-action="{{ route('requests.items.requestCancellation', [$supplyRequest, $item]) }}"
                                            data-item-name="{{ $item->item->name }}">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                    @endcan
                                </div>
                                @endif
                            </td>
                            <td style="font-size:13px">
                                @if($item->order_number)
                                <span style="font-family:monospace;font-weight:600;color:#0369A1">
                                    PC-{{ str_pad($item->order_number, 4, '0', STR_PAD_LEFT) }}
                                </span>
                                @else
                                <span style="color:#CBD5E1">—</span>
                                @endif
                            </td>
                            <td style="font-size:13px;color:#64748B">{{ $item->notes ?? '—' }}</td>
                            <td style="min-width:130px">
                                @if($item->attachment)
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <a href="{{ route('requests.items.attachment.view', [$supplyRequest, $item]) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 8px"
                                       title="{{ $item->attachment->original_name }}">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                    <a href="{{ route('requests.items.attachment.download', [$supplyRequest, $item]) }}"
                                       class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 8px"
                                       title="{{ $item->attachment->original_name }}">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    @can('delete', $item->attachment)
                                    <form method="POST" action="{{ route('requests.items.attachment.destroy', [$supplyRequest, $item]) }}"
                                          onsubmit="return confirm('Remover este arquivo?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                style="font-size:11px;padding:2px 5px">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                                @else
                                @can('create', [\App\Models\ItemAttachment::class, $item])
                                <form method="POST" action="{{ route('requests.items.attachment.store', [$supplyRequest, $item]) }}"
                                      enctype="multipart/form-data">
                                    @csrf
                                    <label class="btn btn-sm btn-outline-secondary"
                                           style="font-size:11px;padding:2px 8px;cursor:pointer;margin:0">
                                        <i class="bi bi-paperclip"></i> Anexar
                                        <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png"
                                               style="display:none" onchange="this.form.submit()">
                                    </label>
                                </form>
                                @endcan
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- External Orders --}}
<div class="cs-card mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-semibold mb-0" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">
            <i class="bi bi-file-earmark-text me-1"></i>Pedidos
        </h6>
        @can('create', [\App\Models\ExternalOrder::class, $supplyRequest])
        <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:12px"
                data-bs-toggle="collapse" data-bs-target="#addExternalOrderForm">
            <i class="bi bi-plus"></i> Adicionar
        </button>
        @endcan
    </div>

    @can('create', [\App\Models\ExternalOrder::class, $supplyRequest])
    <div class="collapse mb-3" id="addExternalOrderForm">
        <form method="POST" action="{{ route('requests.external-orders.store', $supplyRequest) }}"
              enctype="multipart/form-data"
              class="row g-2 align-items-end p-3 rounded-2" style="background:#F8FAFC;border:1px solid #E2E9F4">
            @csrf
            <div class="col-md-2">
                <label class="form-label" style="font-size:12px">Nº Pedido</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text fw-semibold" style="font-family:monospace;color:#0369A1;font-size:12px">0000</span>
                    <input type="number" name="order_number" class="form-control form-control-sm"
                           min="1" max="9999" placeholder="1" style="font-family:monospace">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:12px">Observações</label>
                <input type="text" name="notes" class="form-control form-control-sm"
                       maxlength="500" placeholder="Opcional">
            </div>
            <div class="col-md-4">
                <label class="form-label" style="font-size:12px">
                    Arquivo <span class="text-danger">*</span>
                    <span class="text-muted fw-normal">(PDF, JPG ou PNG · máx. 10 MB)</span>
                </label>
                <input type="file" name="file" class="form-control form-control-sm"
                       accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Salvar</button>
            </div>
        </form>
    </div>
    @endcan

    @if($supplyRequest->externalOrders->isEmpty())
    <p class="text-muted mb-0" style="font-size:13px">Nenhum pedido registrado.</p>
    @else
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Nº Pedido</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Observações</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Arquivo</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Registrado por</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($supplyRequest->externalOrders as $eo)
                <tr>
                    <td style="font-size:13px;font-family:monospace;font-weight:600;color:#0369A1">
                        @if($eo->order_number)
                            {{ str_pad($eo->order_number, 4, '0', STR_PAD_LEFT) }}
                        @else
                            <span style="color:#CBD5E1">—</span>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#64748B">{{ $eo->notes ?? '—' }}</td>
                    <td style="font-size:13px">
                        <div class="d-flex align-items-center gap-2">
                            <span style="color:#374151">{{ Str::limit($eo->original_name, 22) }}</span>
                            <a href="{{ route('requests.external-orders.view', [$supplyRequest, $eo]) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 7px">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ route('requests.external-orders.download', [$supplyRequest, $eo]) }}"
                               class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 7px">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#64748B">{{ $eo->registeredBy->name }}</td>
                    <td style="font-size:12px;color:#94A3B8;white-space:nowrap">
                        {{ $eo->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        @can('delete', $eo)
                        <form method="POST" action="{{ route('requests.external-orders.destroy', [$supplyRequest, $eo]) }}"
                              onsubmit="return confirm('Remover este pedido?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    style="font-size:11px;padding:2px 6px">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Request Attachments --}}
<div class="cs-card mt-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-semibold mb-0" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">
            <i class="bi bi-paperclip me-1"></i>Anexos da Solicitação
        </h6>
        @can('create', [\App\Models\RequestAttachment::class, $supplyRequest])
        <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size:12px"
                data-bs-toggle="collapse" data-bs-target="#addRequestAttachmentForm">
            <i class="bi bi-plus"></i> Adicionar
        </button>
        @endcan
    </div>

    @can('create', [\App\Models\RequestAttachment::class, $supplyRequest])
    <div class="collapse mb-3" id="addRequestAttachmentForm">
        <form method="POST" action="{{ route('requests.attachments.store', $supplyRequest) }}"
              enctype="multipart/form-data"
              class="row g-2 align-items-end p-3 rounded-2" style="background:#F8FAFC;border:1px solid #E2E9F4">
            @csrf
            <div class="col-md-3">
                <label class="form-label" style="font-size:12px">
                    Tipo <span class="text-danger">*</span>
                </label>
                <select name="type" class="form-select form-select-sm" required>
                    <option value="" disabled selected>Selecionar…</option>
                    @foreach(\App\Enums\AttachmentType::cases() as $t)
                    <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label" style="font-size:12px">
                    Arquivo <span class="text-danger">*</span>
                    <span class="text-muted fw-normal">(PDF, JPG ou PNG · máx. 10 MB)</span>
                </label>
                <input type="file" name="file" class="form-control form-control-sm"
                       accept=".pdf,.jpg,.jpeg,.png" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Salvar</button>
            </div>
        </form>
    </div>
    @endcan

    @if($supplyRequest->attachments->isEmpty())
    <p class="text-muted mb-0" style="font-size:13px">Nenhum anexo adicionado.</p>
    @else
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Tipo</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Arquivo</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Tamanho</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Enviado por</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($supplyRequest->attachments as $att)
                <tr>
                    <td>
                        <span class="cs-badge cs-badge-inProgress" style="font-size:10px">
                            {{ $att->type->label() }}
                        </span>
                    </td>
                    <td style="font-size:13px">
                        <div class="d-flex align-items-center gap-2">
                            <span style="color:#374151">{{ Str::limit($att->original_name, 24) }}</span>
                            <a href="{{ route('requests.attachments.view', [$supplyRequest, $att]) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 7px">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="{{ route('requests.attachments.download', [$supplyRequest, $att]) }}"
                               class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 7px">
                                <i class="bi bi-download"></i>
                            </a>
                        </div>
                    </td>
                    <td style="font-size:12px;color:#94A3B8">{{ $att->size_kb }} KB</td>
                    <td style="font-size:13px;color:#64748B">{{ $att->uploadedBy->name }}</td>
                    <td style="font-size:12px;color:#94A3B8;white-space:nowrap">
                        {{ $att->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        @can('delete', $att)
                        <form method="POST" action="{{ route('requests.attachments.destroy', [$supplyRequest, $att]) }}"
                              onsubmit="return confirm('Remover este anexo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    style="font-size:11px;padding:2px 6px">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Actions --}}
@php
    $itemsDone      = $supplyRequest->items->every(fn($i) => in_array($i->status->value, ['received', 'cancelled']));
    $pendingItems   = $supplyRequest->items->filter(fn($i) => !in_array($i->status->value, ['received', 'cancelled']))->count();
    $blockTooltip   = "Há {$pendingItems} item(ns) que ainda não foram recebidos ou cancelados.";
@endphp
<div class="d-flex gap-2 mt-4 flex-wrap align-items-center">
    @can('update', $supplyRequest)
    <a href="{{ route('requests.edit', $supplyRequest) }}" class="btn btn-outline-secondary">
        <i class="bi bi-pencil me-1"></i>Editar Rascunho
    </a>
    @endcan

    @can('submit', $supplyRequest)
    <form method="POST" action="{{ route('requests.submit', $supplyRequest) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-send me-1"></i>Enviar Solicitação
        </button>
    </form>
    @endcan

    @can('delete', $supplyRequest)
    <form method="POST" action="{{ route('requests.destroy', $supplyRequest) }}" class="d-inline"
          onsubmit="return confirm('Excluir este rascunho permanentemente? Esta ação não pode ser desfeita.')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-trash me-1"></i>Excluir Rascunho
        </button>
    </form>
    @endcan

    {{-- Buyer/Admin: avançar status no fluxo normal --}}
    @can('advanceStatus', $supplyRequest)
    @php
        $isCompleting = $supplyRequest->status === \App\Enums\RequestStatus::InProgress;
        $canAdvance   = !$isCompleting || $itemsDone;
        $advanceLabel = $isCompleting ? 'Confirmar Conclusão' : 'Iniciar Atendimento';
    @endphp
    <form method="POST" action="{{ route('requests.advanceStatus', $supplyRequest) }}" class="d-inline">
        @csrf
        <button type="submit"
                class="btn {{ $canAdvance ? 'btn-primary' : 'btn-secondary' }}"
                @disabled(!$canAdvance)
                title="{{ !$canAdvance ? $blockTooltip : '' }}">
            <i class="bi bi-arrow-right-circle me-1"></i>{{ $advanceLabel }}
        </button>
    </form>
    @endcan

    {{-- Comprador: cancelar diretamente --}}
    @can('cancelDirect', $supplyRequest)
    <button type="button"
            class="btn {{ $itemsDone ? 'btn-outline-danger' : 'btn-outline-secondary' }}"
            @disabled(!$itemsDone)
            title="{{ !$itemsDone ? $blockTooltip : '' }}"
            @if($itemsDone) data-bs-toggle="modal" data-bs-target="#cancelDirectModal" @endif>
        <i class="bi bi-x-circle me-1"></i>Cancelar Solicitação
    </button>
    @endcan

    {{-- Comprador/Admin: aprovar ou recusar cancelamento --}}
    @can('approveCancellation', $supplyRequest)
    <form method="POST" action="{{ route('requests.approveCancellation', $supplyRequest) }}" class="d-inline"
          onsubmit="return confirm('Aprovar o cancelamento desta solicitação?')">
        @csrf
        <button type="submit" class="btn btn-danger">
            <i class="bi bi-check-circle me-1"></i>Aprovar Cancelamento
        </button>
    </form>
    @endcan

    @can('refuseCancellation', $supplyRequest)
    <form method="POST" action="{{ route('requests.refuseCancellation', $supplyRequest) }}" class="d-inline"
          onsubmit="return confirm('Recusar o cancelamento e retornar ao status anterior?')">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Recusar Cancelamento
        </button>
    </form>
    @endcan

    {{-- Solicitante: solicitar cancelamento --}}
    @can('cancelRequest', $supplyRequest)
    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
        <i class="bi bi-x-circle me-1"></i>Solicitar Cancelamento
    </button>
    @endcan
</div>

{{-- Painel admin: forçar status da solicitação --}}
@can('jumpStatus', $supplyRequest)
<div class="mt-3 rounded-3 p-3 d-flex align-items-center gap-3 flex-wrap"
     style="background:#FFFBEB;border:1.5px solid #FDE047">
    <div class="d-flex align-items-center gap-2" style="color:#92400E">
        <i class="bi bi-lightning-charge-fill fs-5"></i>
        <span style="font-size:13px;font-weight:700;letter-spacing:.02em">Controle Admin</span>
    </div>
    <form method="POST" action="{{ route('requests.jumpStatus', $supplyRequest) }}"
          class="d-flex align-items-center gap-2 flex-grow-1">
        @csrf
        <select name="status" class="form-select"
                style="font-size:14px;border-color:#FDE047;color:#92400E;background:#fff;max-width:240px">
            <option value="" disabled selected>Forçar status para…</option>
            @foreach(\App\Enums\RequestStatus::cases() as $s)
                @if($s !== \App\Enums\RequestStatus::Draft && $s !== $supplyRequest->status)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endif
            @endforeach
        </select>
        <button type="submit" class="btn fw-semibold"
                style="background:#FEF08A;border-color:#FDE047;color:#7C2D12">
            <i class="bi bi-lightning-charge-fill me-1"></i>Forçar
        </button>
    </form>
</div>
@endcan

@endsection

@if(auth()->user()->isBuyerOrAdmin())
@push('modals')
{{-- Modal: número do pedido (quoting → awaitingPayment) --}}
<div class="modal fade" id="orderNumberModal" tabindex="-1" aria-labelledby="orderNumberModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <form method="POST" id="orderNumberForm">
            @csrf @method('PATCH')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderNumberModalLabel">Número do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:14px">
                        Item: <strong id="orderNumberItemName"></strong>
                    </p>
                    <label for="order_number_input" class="form-label fw-semibold">
                        Nº do Pedido<span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text fw-semibold" style="font-family:monospace;color:#0369A1">PC-</span>
                        <input type="number" id="order_number_input" name="order_number"
                               class="form-control" min="1" max="9999" required
                               placeholder="0001" style="font-family:monospace">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('orderNumberModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('orderNumberForm').action = btn.dataset.action;
    document.getElementById('orderNumberItemName').textContent = btn.dataset.itemName;
    document.getElementById('order_number_input').value = '';
});
</script>

{{-- Modal: cancelar item --}}
<div class="modal fade" id="cancelItemModal" tabindex="-1" aria-labelledby="cancelItemModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <form method="POST" id="cancelItemForm">
            @csrf @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelItemModalLabel">Cancelar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:14px">
                        Cancelar: <strong id="cancelItemName"></strong>
                    </p>
                    <label for="cancel_reason_item" class="form-label fw-semibold">
                        Motivo <span class="text-danger">*</span>
                    </label>
                    <textarea id="cancel_reason_item" name="cancel_reason"
                              class="form-control" rows="3" maxlength="500" required
                              placeholder="Motivo do cancelamento do item…"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-danger">Cancelar Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('cancelItemModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('cancelItemForm').action = btn.dataset.action;
    document.getElementById('cancelItemName').textContent = btn.dataset.itemName;
    document.getElementById('cancel_reason_item').value = '';
});
</script>
@endpush
@endif

@push('modals')
<div class="modal fade" id="requestCancelItemModal" tabindex="-1" aria-labelledby="requestCancelItemModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <form method="POST" id="requestCancelItemForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestCancelItemModalLabel">Solicitar Cancelamento de Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3" style="font-size:14px">
                        Item: <strong id="requestCancelItemName"></strong>
                    </p>
                    <label for="request_cancel_item_reason" class="form-label fw-semibold">
                        Motivo <span class="text-danger">*</span>
                    </label>
                    <textarea id="request_cancel_item_reason" name="cancel_reason"
                              class="form-control" rows="3" maxlength="500" required
                              placeholder="Descreva o motivo do cancelamento…"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-danger">Solicitar Cancelamento</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('requestCancelItemModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    document.getElementById('requestCancelItemForm').action = btn.dataset.action;
    document.getElementById('requestCancelItemName').textContent = btn.dataset.itemName;
    document.getElementById('request_cancel_item_reason').value = '';
});
</script>
@endpush

@can('cancelDirect', $supplyRequest)
@push('modals')
<div class="modal fade" id="cancelDirectModal" tabindex="-1" aria-labelledby="cancelDirectModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('requests.cancelDirect', $supplyRequest) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelDirectModalLabel">Cancelar Solicitação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="cancellation_reason_direct" class="form-label fw-semibold">
                        Motivo <span class="text-danger">*</span>
                    </label>
                    <textarea id="cancellation_reason_direct" name="cancellation_reason"
                              class="form-control @error('cancellation_reason') is-invalid @enderror"
                              rows="4" maxlength="1000" required
                              placeholder="Descreva o motivo do cancelamento…">{{ old('cancellation_reason') }}</textarea>
                    @error('cancellation_reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush
@endcan

@can('cancelRequest', $supplyRequest)
@push('modals')
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('requests.cancelRequest', $supplyRequest) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Solicitar Cancelamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="cancellation_reason" class="form-label fw-semibold">
                        Motivo <span class="text-danger">*</span>
                    </label>
                    <textarea id="cancellation_reason" name="cancellation_reason"
                              class="form-control @error('cancellation_reason') is-invalid @enderror"
                              rows="4" maxlength="1000" required
                              placeholder="Descreva o motivo do cancelamento…">{{ old('cancellation_reason') }}</textarea>
                    @error('cancellation_reason')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
                </div>
            </div>
        </form>
    </div>
</div>
@if($errors->has('cancellation_reason'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
});
</script>
@endif
@endpush
@endcan
