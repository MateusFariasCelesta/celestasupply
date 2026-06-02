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

<div class="row g-4">
    {{-- Details --}}
    <div class="col-md-4">
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
                <dd class="mb-0">{{ $supplyRequest->notes }}</dd>
                @endif
            </dl>
        </div>
    </div>

    {{-- Items --}}
    <div class="col-md-8">
        <div class="cs-card">
            <h6 class="fw-semibold mb-3" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Itens</h6>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Item</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Qtd.</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Unidade</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Status</th>
                            <th style="font-size:12px;color:#64748B;font-weight:600">Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplyRequest->items as $item)
                        <tr>
                            <td style="font-size:14px;font-weight:500">{{ $item->item->name }}</td>
                            <td style="font-size:14px">{{ $item->quantity }}</td>
                            <td style="font-size:13px;color:#64748B">{{ $item->unit ?? '—' }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:11px">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td style="font-size:13px;color:#64748B">{{ $item->notes ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Actions --}}
<div class="d-flex gap-2 mt-4 flex-wrap">
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

    @can('cancelRequest', $supplyRequest)
    <form method="POST" action="{{ route('requests.cancelRequest', $supplyRequest) }}" class="d-inline"
          onsubmit="return confirm('Solicitar cancelamento desta solicitação?')">
        @csrf
        <button type="submit" class="btn btn-outline-danger">
            <i class="bi bi-x-circle me-1"></i>Solicitar Cancelamento
        </button>
    </form>
    @endcan
</div>
@endsection
