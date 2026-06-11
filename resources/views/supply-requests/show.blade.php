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
    <div class="dropdown">
        <button class="btn btn-sm fw-semibold dropdown-toggle" id="btn-pdf-main"
                style="background:#DC2626;color:#fff;border:none;border-radius:6px"
                type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <button type="button" class="dropdown-item btn-pdf-dl" style="font-size:13px"
                        data-url="{{ route('requests.export.pdf', [$supplyRequest, 'filter' => 'all']) }}">
                    <i class="bi bi-list-ul me-2"></i>Todos os itens
                </button>
            </li>
            <li>
                <button type="button" class="dropdown-item btn-pdf-dl" style="font-size:13px"
                        data-url="{{ route('requests.export.pdf', [$supplyRequest, 'filter' => 'pending']) }}">
                    <i class="bi bi-hourglass-split me-2"></i>Somente pendentes
                </button>
            </li>
        </ul>
    </div>
</div>

{{-- Bloco unificado: timeline + detalhes --}}
<div class="cs-card mb-4">
    <div class="row g-0 align-items-start">
        <div class="col">
            <p class="mb-3" style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#64748B;font-weight:600;margin-bottom:0">Linha do Tempo</p>
            <x-status-timeline :supply-request="$supplyRequest" :standalone="false" />
        </div>
    </div>
    <div class="d-flex flex-wrap gap-4 pt-3 mt-3" style="border-top:1px solid #F1F5F9;font-size:13px">
        <div>
            <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Centro de Custo</div>
            <div style="font-weight:500;color:#1E293B">{{ $supplyRequest->costCenter->name }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Solicitante</div>
            <div style="font-weight:500;color:#1E293B">{{ $supplyRequest->user->name }}</div>
        </div>
        <div>
            <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Data</div>
            <div style="font-weight:500;color:#1E293B">{{ $supplyRequest->created_at->format('d/m/Y H:i') }}</div>
        </div>
        @if($supplyRequest->notes)
        <div>
            <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Observações</div>
            <div style="font-weight:500;color:#1E293B">{{ $supplyRequest->notes }}</div>
        </div>
        @endif
        @if($supplyRequest->cancellation_reason)
        <div>
            <div style="font-size:11px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Motivo do Cancelamento</div>
            <div style="font-weight:500;color:#DC2626">{{ $supplyRequest->cancellation_reason }}</div>
        </div>
        @endif
    </div>
</div>

{{-- Items --}}
<div class="cs-card mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="fw-semibold mb-0" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Itens</h6>
        @if(!in_array($supplyRequest->status->value, ['completed', 'cancelled']))
        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="bi bi-plus"></i> Adicionar Item
        </button>
        @endif
    </div>

    @if(!in_array($supplyRequest->status->value, ['completed', 'cancelled']))
    <form method="POST" action="{{ route('requests.saveItems', $supplyRequest) }}">
        @csrf
    @endif

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Item</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Status</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Qtd.</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Nº PC</th>
                    <th style="width:36px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($supplyRequest->items as $item)
                <tr data-item-row="{{ $item->id }}" @if($item->status === \App\Enums\ItemStatus::Cancelled) style="opacity:.55" @endif>
                    <td style="font-size:14px;font-weight:500">{{ $item->item->name }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="cs-badge {{ $item->status->badgeClass() }}" id="badge-{{ $item->id }}">{{ $item->status->label() }}</span>
                            @can('updateStatus', $item)
                            @if($item->status->nextStatus())
                            <button type="button"
                                    id="btn-advance-{{ $item->id }}"
                                    class="btn btn-sm btn-outline-secondary"
                                    style="font-size:11px;padding:2px 8px"
                                    onclick="stageAdvance({{ $item->id }}, {{ $item->status->value === 'quoting' ? 'true' : 'false' }}, @js($item->item->name), @js($item->status->nextStatus()->label()))">
                                <i class="bi bi-arrow-right me-1"></i>{{ $item->status->nextStatus()->label() }}
                            </button>
                            <button type="button"
                                    id="btn-unstage-{{ $item->id }}"
                                    class="btn btn-sm btn-outline-warning d-none"
                                    style="font-size:11px;padding:2px 8px"
                                    onclick="unstage({{ $item->id }})">
                                <i class="bi bi-x"></i>
                            </button>
                            @endif
                            @endcan
                        </div>
                        @if($item->cancel_reason)
                        <div style="font-size:11px;color:#94A3B8;margin-top:2px">{{ $item->cancel_reason }}</div>
                        @endif
                    </td>
                    <td style="font-size:13px;color:#374151">
                        @if(!in_array($supplyRequest->status->value, ['completed', 'cancelled']))
                        <div class="d-flex align-items-center gap-1">
                            <input type="number" step="0.001" min="0.001" required
                                   name="items[{{ $item->id }}][quantity]"
                                   value="{{ $item->quantity }}"
                                   class="form-control form-control-sm" style="width:80px;font-size:12px">
                            <input type="text" maxlength="20" placeholder="un."
                                   name="items[{{ $item->id }}][unit]"
                                   value="{{ $item->unit }}"
                                   class="form-control form-control-sm" style="width:55px;font-size:12px">
                            <input type="hidden"
                                   name="items[{{ $item->id }}][notes]"
                                   value="{{ $item->notes }}">
                        </div>
                        @else
                            {{ $item->formattedQuantity() }}
                            @if($item->unit)<span style="color:#94A3B8;font-size:12px"> {{ $item->unit }}</span>@endif
                        @endif
                    </td>
                    <td style="font-size:13px">
                        @if($item->order_number)
                        <span style="font-family:monospace;font-weight:600;color:#0369A1">
                            {{ $item->formattedOrderNumber() }}
                        </span>
                        @else
                        <span style="color:#CBD5E1">—</span>
                        @endif
                    </td>
                    <td>
                        <button type="button"
                                class="btn btn-sm btn-outline-secondary"
                                style="padding:3px 8px;font-size:12px"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#item-panel-{{ $item->id }}"
                                title="Ver detalhes">
                            <i class="bi bi-sliders2"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(!in_array($supplyRequest->status->value, ['completed', 'cancelled']))
    <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-check-lg me-1"></i>Salvar Itens
        </button>
    </div>
    </form>
    @endif

</div>

@push('modals')
{{-- Item offcanvases — fora do .cs-content para evitar stacking-context do transform da animação --}}
@foreach($supplyRequest->items as $item)
@php
    $delivered = (float) $item->delivered_quantity;
    $remaining = (float) $item->quantity - $delivered;
    $fmtQ = fn($n) => rtrim(rtrim(number_format((float)$n, 3, ',', ''), '0'), ',');
@endphp
<div class="offcanvas offcanvas-end" tabindex="-1"
     id="item-panel-{{ $item->id }}"
     data-bs-backdrop="false"
     data-bs-scroll="true"
     style="width:420px;max-width:100vw;top:60px;box-shadow:-4px 0 24px rgba(0,0,0,.1)"
     aria-labelledby="item-panel-label-{{ $item->id }}">

    <div class="offcanvas-header" style="border-bottom:1px solid #F1F5F9">
        <div>
            <h6 class="offcanvas-title mb-1" id="item-panel-label-{{ $item->id }}"
                style="font-size:15px;font-weight:700;color:#1E293B">
                {{ $item->item->name }}
            </h6>
            <span class="cs-badge {{ $item->status->badgeClass() }}">{{ $item->status->label() }}</span>
            @if($item->order_number)
            <span class="ms-1" style="font-family:monospace;font-size:11px;color:#0369A1;font-weight:600">
                {{ $item->formattedOrderNumber() }}
            </span>
            @endif
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body" style="font-size:13px">

        @if($item->cancel_reason)
        <div class="mb-3 px-3 py-2 rounded-2"
             style="background:#FEF2F2;border-left:3px solid #EF4444;font-size:12px;color:#7F1D1D">
            {{ $item->cancel_reason }}
        </div>
        @endif

        {{-- Quantidades --}}
        <div class="row g-2 mb-4">
            <div class="col-4">
                <div class="p-2 rounded-2 text-center" style="background:#F8FAFC">
                    <div style="font-size:10px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Pedida</div>
                    <div style="font-size:17px;font-weight:700;color:#1E293B;line-height:1.3">{{ $fmtQ($item->quantity) }}</div>
                    @if($item->unit)<div style="font-size:11px;color:#94A3B8">{{ $item->unit }}</div>@endif
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 rounded-2 text-center" style="background:{{ $delivered > 0 ? '#F0FDF4' : '#F8FAFC' }}">
                    <div style="font-size:10px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Entregue</div>
                    <div style="font-size:17px;font-weight:700;line-height:1.3;color:{{ $delivered > 0 ? '#166534' : '#CBD5E1' }}">
                        {{ $delivered > 0 ? $fmtQ($delivered) : '—' }}
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="p-2 rounded-2 text-center"
                     style="background:{{ $remaining > 0 && $item->status->value !== 'cancelled' ? '#FFFBEB' : '#F8FAFC' }}">
                    <div style="font-size:10px;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Restante</div>
                    <div style="font-size:17px;font-weight:700;line-height:1.3;color:{{ $remaining > 0 && $item->status->value !== 'cancelled' ? '#92400E' : '#CBD5E1' }}">
                        {{ $item->status->value !== 'cancelled' ? $fmtQ($remaining) : '—' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Fornecedor --}}
        <div class="mb-3">
            <div style="font-size:11px;color:#94A3B8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px">Fornecedor</div>
            @can('setSupplier', $item)
            <form method="POST" action="{{ route('requests.items.supplier', [$supplyRequest, $item]) }}">
                @csrf @method('PATCH')
                <select name="supplier_id" class="form-select form-select-sm" onchange="this.form.submit()" style="font-size:12px">
                    <option value="">— Nenhum —</option>
                    @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected($item->supplier_id == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </form>
            @else
            <span style="color:#374151">{{ $item->supplier?->name ?? '—' }}</span>
            @endcan
        </div>

        @if($item->notes)
        <div class="mb-3">
            <div style="font-size:11px;color:#94A3B8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:4px">Observações</div>
            <p style="margin:0;color:#374151">{{ $item->notes }}</p>
        </div>
        @endif

        {{-- Anexo --}}
        <div class="mb-4">
            <div style="font-size:11px;color:#94A3B8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Anexo</div>
            @if($item->attachment)
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span style="font-size:12px;color:#374151;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    {{ $item->attachment->original_name }}
                </span>
                <a href="{{ route('requests.items.attachment.view', [$supplyRequest, $item]) }}" target="_blank"
                   class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 8px">
                    <i class="bi bi-eye"></i> Ver
                </a>
                <a href="{{ route('requests.items.attachment.download', [$supplyRequest, $item]) }}"
                   class="btn btn-sm btn-outline-secondary" style="font-size:11px;padding:2px 8px">
                    <i class="bi bi-download"></i>
                </a>
                @can('delete', $item->attachment)
                <form method="POST" action="{{ route('requests.items.attachment.destroy', [$supplyRequest, $item]) }}"
                      onsubmit="return confirm('Remover este arquivo?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger" style="font-size:11px;padding:2px 6px">
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
                <label class="btn btn-sm btn-outline-secondary" style="font-size:12px;cursor:pointer;margin:0">
                    <i class="bi bi-paperclip me-1"></i>Anexar arquivo
                    <input type="file" name="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none" onchange="this.form.submit()">
                </label>
            </form>
            @else
            <span style="color:#CBD5E1">—</span>
            @endcan
            @endif
        </div>

        {{-- Ações --}}
        <div style="border-top:1px solid #F1F5F9;padding-top:14px;margin-bottom:16px">
            <div style="font-size:11px;color:#94A3B8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">Ações</div>
            <div class="d-flex flex-column gap-2">

                @can('updateStatus', $item)
                @if($item->status->nextStatus())
                <button type="button"
                        class="btn btn-sm btn-outline-secondary w-100"
                        style="font-size:12px;text-align:left;padding:7px 12px"
                        onclick="stageAdvance({{ $item->id }}, {{ $item->status->value === 'quoting' ? 'true' : 'false' }}, @js($item->item->name), @js($item->status->nextStatus()->label()))">
                    <i class="bi bi-arrow-right me-2"></i>Avançar para {{ $item->status->nextStatus()->label() }}
                </button>
                @endif
                @endcan

                @can('registerDelivery', $item)
                <form method="POST" action="{{ route('requests.items.deliver', [$supplyRequest, $item]) }}">
                    @csrf
                    <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:10px">
                        <div style="font-size:12px;font-weight:600;color:#1D4ED8;margin-bottom:8px">
                            <i class="bi bi-box-arrow-in-down me-1"></i>Registrar Entrega
                            <span style="font-weight:400;color:#6B7280">(restante: {{ $fmtQ($remaining) }}{{ $item->unit ? ' '.$item->unit : '' }})</span>
                        </div>
                        <div class="row g-2">
                            <div class="col-7">
                                <input type="number" name="quantity" class="form-control form-control-sm"
                                       step="0.001" min="0.001" max="{{ $remaining }}" required
                                       placeholder="Quantidade">
                            </div>
                            <div class="col-5">
                                <input type="text" name="notes" class="form-control form-control-sm"
                                       maxlength="500" placeholder="Obs. (opcional)">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm w-100" style="font-size:12px">
                                    <i class="bi bi-check me-1"></i>Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @endcan

                @can('approveCancellation', $item)
                <form method="POST" action="{{ route('requests.items.approveCancellation', [$supplyRequest, $item]) }}"
                      onsubmit="return confirm('Aprovar o cancelamento deste item?')">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger w-100"
                            style="font-size:12px;text-align:left;padding:7px 12px">
                        <i class="bi bi-check me-2"></i>Aprovar Cancelamento
                    </button>
                </form>
                @endcan

                @can('refuseCancellation', $item)
                <form method="POST" action="{{ route('requests.items.refuseCancellation', [$supplyRequest, $item]) }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100"
                            style="font-size:12px;text-align:left;padding:7px 12px">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>Recusar Cancelamento
                    </button>
                </form>
                @endcan

                @can('cancel', $item)
                <div style="background:#FFF1F2;border:1px solid #FECDD3;border-radius:8px;padding:10px">
                    <div style="font-size:12px;font-weight:600;color:#9F1239;margin-bottom:8px">
                        <i class="bi bi-x-circle me-1"></i>Cancelar Item
                    </div>
                    <form method="POST" action="{{ route('requests.items.cancel', [$supplyRequest, $item]) }}">
                        @csrf @method('DELETE')
                        <textarea name="cancel_reason" class="form-control form-control-sm mb-2"
                                  rows="2" maxlength="500" required
                                  placeholder="Motivo do cancelamento…"></textarea>
                        <button type="submit" class="btn btn-sm btn-danger w-100" style="font-size:12px">
                            Confirmar Cancelamento
                        </button>
                    </form>
                </div>
                @endcan

                @can('requestCancellation', $item)
                <div style="background:#FFF1F2;border:1px solid #FECDD3;border-radius:8px;padding:10px">
                    <div style="font-size:12px;font-weight:600;color:#9F1239;margin-bottom:8px">
                        <i class="bi bi-x-circle me-1"></i>Solicitar Cancelamento
                    </div>
                    <form method="POST" action="{{ route('requests.items.requestCancellation', [$supplyRequest, $item]) }}">
                        @csrf
                        <textarea name="cancel_reason" class="form-control form-control-sm mb-2"
                                  rows="2" maxlength="500" required
                                  placeholder="Motivo…"></textarea>
                        <button type="submit" class="btn btn-sm btn-danger w-100" style="font-size:12px">Enviar</button>
                    </form>
                </div>
                @endcan

            </div>
        </div>

        {{-- Forçar status (admin) --}}
        @can('jumpStatus', $item)
        <div class="mb-4" style="background:#FFFBEB;border:1.5px solid #FDE047;border-radius:8px;padding:10px">
            <div style="font-size:12px;font-weight:700;color:#92400E;margin-bottom:8px">
                <i class="bi bi-lightning-charge-fill me-1"></i>Forçar Status
            </div>
            <form method="POST" action="{{ route('requests.items.jumpStatus', [$supplyRequest, $item]) }}" class="d-flex gap-2">
                @csrf @method('PATCH')
                <select name="status" class="form-select form-select-sm"
                        style="font-size:12px;border-color:#FDE047;color:#92400E;background:#FFFBEB">
                    <option value="" disabled selected>Forçar para…</option>
                    @foreach(\App\Enums\ItemStatus::cases() as $s)
                    @if($s !== $item->status)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                    @endif
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm fw-semibold"
                        style="background:#FEF08A;border-color:#FDE047;color:#7C2D12;white-space:nowrap">
                    <i class="bi bi-lightning-charge-fill"></i>
                </button>
            </form>
        </div>
        @endcan

        {{-- Histórico de entregas --}}
        @if($item->deliveries->isNotEmpty())
        <div style="border-top:1px solid #F1F5F9;padding-top:14px">
            <div style="font-size:11px;color:#94A3B8;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">
                <i class="bi bi-clock-history me-1"></i>Histórico de Entregas
            </div>
            <div class="d-flex flex-column gap-2">
                @foreach($item->deliveries as $d)
                <div style="background:#F8FAFC;border-radius:6px;padding:8px 10px">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span style="font-weight:700;color:#166534;font-size:14px">
                            {{ $fmtQ($d->quantity) }}
                            @if($item->unit)<span style="font-weight:400;color:#94A3B8;font-size:11px"> {{ $item->unit }}</span>@endif
                        </span>
                        <span style="color:#94A3B8;font-size:11px">{{ $d->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div style="font-size:12px;color:#64748B">{{ $d->registeredBy->name }}</div>
                    @if($d->notes)
                    <div style="font-size:12px;color:#94A3B8;margin-top:3px">{{ $d->notes }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endforeach
@endpush

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
                            {{ str_pad((int) $eo->order_number, 4, '0', STR_PAD_LEFT) }}
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
        $isRequester  = auth()->user()->isRequester();
        $advanceLabel = $isCompleting
            ? ($isRequester ? 'Confirmar Recebimento' : 'Confirmar Conclusão')
            : 'Iniciar Atendimento';
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

{{-- Barra de confirmação de status em lote --}}
<div id="batch-bar" class="d-none"
     style="position:fixed;bottom:0;left:0;right:0;z-index:1040;
            background:#1E293B;padding:12px 24px;
            display:flex;align-items:center;justify-content:space-between;gap:12px;
            box-shadow:0 -4px 20px rgba(0,0,0,.25)">
    <span style="color:#fff;font-size:14px;font-weight:500">
        <i class="bi bi-clock-history me-2" style="color:#60A5FA"></i>
        <span id="batch-count">0</span> item(ns) com status pendente
    </span>
    <div class="d-flex gap-2">
        <button class="btn btn-sm" onclick="clearAllStaged()"
                style="color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.2)">
            Descartar
        </button>
        <button id="btn-confirm-batch" class="btn btn-sm btn-primary" onclick="confirmBatch()">
            <i class="bi bi-check me-1"></i>Confirmar alterações
        </button>
    </div>
</div>

@endsection


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

@push('modals')
{{-- Modal: número PC para avanço em lote --}}
<div class="modal fade" id="batchOrderModal" tabindex="-1" aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Número do Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-2" style="font-size:14px">
                    Item: <strong id="batchOrderItemName"></strong>
                </p>
                <label class="form-label fw-semibold">
                    Nº do Pedido <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text fw-semibold" style="font-family:monospace;color:#0369A1">PC-</span>
                    <input type="number" id="batchOrderInput" class="form-control"
                           min="1" max="9999" placeholder="0001" style="font-family:monospace">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="confirmBatchOrderModal()">Confirmar</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal para adicionar novo item --}}
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-modal="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('requests.addItem', $supplyRequest) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Adicionar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Item <span class="text-danger">*</span>
                        </label>
                        @include('supply-requests._item-picker', ['name' => 'item_id', 'label' => 'Selecionar item…', 'pickerId' => 'modal-item-picker'])
                        @error('item_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-7">
                            <label for="quantity" class="form-label fw-semibold">
                                Quantidade <span class="text-danger">*</span>
                            </label>
                            <input type="number" id="quantity" name="quantity"
                                   class="form-control form-control-sm @error('quantity') is-invalid @enderror"
                                   step="0.001" min="0.001" required
                                   placeholder="0,000"
                                   value="{{ old('quantity') }}">
                            @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-5">
                            <label for="unit" class="form-label fw-semibold">
                                Unidade
                            </label>
                            <input type="text" id="unit" name="unit"
                                   class="form-control form-control-sm @error('unit') is-invalid @enderror"
                                   maxlength="20" placeholder="un."
                                   value="{{ old('unit') }}">
                            @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-semibold">
                            Observações
                        </label>
                        <textarea id="notes" name="notes"
                                  class="form-control form-control-sm @error('notes') is-invalid @enderror"
                                  rows="3" maxlength="500" placeholder="Opcional…">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Item</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endpush

@include('supply-requests._request-form-script')

@push('scripts')
<script>
// ── Batch status staging ──────────────────────────────────────────────────────
const BATCH_URL   = @js(route('requests.items.batchStatus', $supplyRequest));
const BATCH_CSRF  = @js(csrf_token());

let staged    = {};          // { [itemId]: { order_number? } }
let batchItemId = null;      // item aguardando número PC no modal

function stageAdvance(id, isQuoting, itemName, nextLabel) {
    if (isQuoting) {
        batchItemId = id;
        document.getElementById('batchOrderItemName').textContent = itemName;
        document.getElementById('batchOrderInput').value = '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('batchOrderModal')).show();
    } else {
        commitStage(id, {});
    }
}

function confirmBatchOrderModal() {
    const val = parseInt(document.getElementById('batchOrderInput').value);
    if (!val || val < 1) {
        document.getElementById('batchOrderInput').classList.add('is-invalid');
        return;
    }
    bootstrap.Modal.getInstance(document.getElementById('batchOrderModal')).hide();
    commitStage(batchItemId, { order_number: val });
}

function commitStage(id, extra) {
    staged[id] = extra;
    document.getElementById(`btn-advance-${id}`)?.classList.add('d-none');
    document.getElementById(`btn-unstage-${id}`)?.classList.remove('d-none');
    document.querySelector(`tr[data-item-row="${id}"]`)?.classList.add('table-warning');
    updateBar();
}

function unstage(id) {
    delete staged[id];
    document.getElementById(`btn-advance-${id}`)?.classList.remove('d-none');
    document.getElementById(`btn-unstage-${id}`)?.classList.add('d-none');
    document.querySelector(`tr[data-item-row="${id}"]`)?.classList.remove('table-warning');
    updateBar();
}

function clearAllStaged() {
    Object.keys(staged).forEach(id => unstage(parseInt(id)));
}

function updateBar() {
    const count = Object.keys(staged).length;
    document.getElementById('batch-count').textContent = count;
    document.getElementById('batch-bar').classList.toggle('d-none', count === 0);
}

async function confirmBatch() {
    const items = Object.entries(staged).map(([id, data]) => ({ id: parseInt(id), ...data }));
    const btn   = document.getElementById('btn-confirm-batch');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Salvando…';

    try {
        const resp = await fetch(BATCH_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': BATCH_CSRF },
            body: JSON.stringify({ items }),
        });

        if (resp.ok) {
            window.location.reload();
        } else {
            const json = await resp.json().catch(() => ({}));
            alert(json.message || 'Erro ao salvar. Tente novamente.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check me-1"></i>Confirmar alterações';
        }
    } catch {
        alert('Erro de conexão. Tente novamente.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check me-1"></i>Confirmar alterações';
    }
}
// ─────────────────────────────────────────────────────────────────────────────

document.querySelectorAll('.btn-pdf-dl').forEach(function (item) {
    item.addEventListener('click', function () {
        var url = this.dataset.url;
        var mainBtn = document.getElementById('btn-pdf-main');
        var originalHtml = mainBtn.innerHTML;
        mainBtn.disabled = true;
        mainBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

        fetch(url)
            .then(function (res) {
                if (!res.ok) throw new Error('erro');
                var disp = res.headers.get('content-disposition') || '';
                var m = disp.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                var filename = m ? m[1].replace(/['"]/g, '') : 'itens.pdf';
                return res.blob().then(function (blob) { return { blob: blob, filename: filename }; });
            })
            .then(function (r) {
                var a = document.createElement('a');
                a.href = URL.createObjectURL(r.blob);
                a.download = r.filename;
                document.body.appendChild(a);
                a.click();
                setTimeout(function () { URL.revokeObjectURL(a.href); a.remove(); }, 100);
            })
            .catch(function () { alert('Erro ao gerar o PDF. Tente novamente.'); })
            .finally(function () { mainBtn.disabled = false; mainBtn.innerHTML = originalHtml; });
    });
});
</script>
@endpush
