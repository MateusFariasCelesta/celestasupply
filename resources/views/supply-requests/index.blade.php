@extends('layouts.app')
@section('title', 'Solicitações — CelestaSupply')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Solicitações</h1>
    <a href="{{ route('requests.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Nova Solicitação
    </a>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('requests.index') }}" class="cs-card mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size:12px">Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(\App\Enums\RequestStatus::cases() as $s)
                <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>
                    {{ $s->label() }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold" style="font-size:12px">Urgência</label>
            <select name="urgency" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach(\App\Enums\Urgency::cases() as $u)
                <option value="{{ $u->value }}" {{ request('urgency') === $u->value ? 'selected' : '' }}>
                    {{ $u->label() }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size:12px">Centro de Custo</label>
            <select name="cost_center_id" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($costCenters as $cc)
                <option value="{{ $cc->id }}" {{ request('cost_center_id') == $cc->id ? 'selected' : '' }}>
                    {{ $cc->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                <i class="bi bi-funnel me-1"></i>Filtrar
            </button>
        </div>
        @if(request()->hasAny(['status','urgency','cost_center_id']))
        <div class="col-md-2">
            <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-secondary w-100">Limpar</a>
        </div>
        @endif
    </div>
</form>

<div class="cs-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Código</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Título</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Centro de Custo</th>
                    @if(auth()->user()->isBuyerOrAdmin())
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Solicitante</th>
                    @endif
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Urgência</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Status</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplyRequests as $sr)
                <tr>
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:12px;font-weight:600">
                            {{ $sr->code ?? '—' }}
                        </span>
                    </td>
                    <td style="font-size:14px;font-weight:500">{{ $sr->title }}</td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->costCenter->name }}</td>
                    @if(auth()->user()->isBuyerOrAdmin())
                    <td style="font-size:13px;color:#64748B">{{ $sr->user->name }}</td>
                    @endif
                    <td>
                        <span class="cs-badge {{ $sr->urgency->badgeClass() }}">
                            {{ $sr->urgency->label() }}
                        </span>
                    </td>
                    <td>
                        <span class="cs-badge {{ $sr->status->badgeClass() }}">
                            {{ $sr->status->label() }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('requests.show', $sr) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5" style="color:#94A3B8">
                        <i class="bi bi-clipboard-check" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Nenhuma solicitação encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($supplyRequests->hasPages())
    <div class="mt-3">{{ $supplyRequests->links() }}</div>
    @endif
</div>
@endsection
