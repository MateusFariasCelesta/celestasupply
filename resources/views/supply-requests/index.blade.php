@extends('layouts.app')
@section('title', 'Solicitações — CelestaSupply')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Solicitações</h1>
    <a href="{{ route('requests.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Nova Solicitação
    </a>
</div>

{{-- Filtros client-side --}}
<div class="cs-card mb-4">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-semibold" style="font-size:12px">Buscar</label>
            <input type="text" id="f-q" class="form-control form-control-sm"
                   placeholder="Código, título, centro de custo, solicitante, item…" autocomplete="off">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold" style="font-size:12px">Status</label>
            <select id="f-status" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach(\App\Enums\RequestStatus::cases() as $s)
                <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold" style="font-size:12px">Urgência</label>
            <select id="f-urgency" class="form-select form-select-sm">
                <option value="">Todas</option>
                @foreach(\App\Enums\Urgency::cases() as $u)
                <option value="{{ $u->value }}">{{ $u->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size:12px">Centro de Custo</label>
            <select id="f-cc" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($costCenters as $cc)
                <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                @endforeach
            </select>
        </div>
        @if(auth()->user()->isBuyerOrAdmin())
        <div class="col-md-3">
            <label class="form-label fw-semibold" style="font-size:12px">Solicitante</label>
            <select id="f-user" class="form-select form-select-sm">
                <option value="">Todos</option>
                @foreach($requesters as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="col-md-2">
            <label class="form-label fw-semibold" style="font-size:12px">De</label>
            <input type="date" id="f-from" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold" style="font-size:12px">Até</label>
            <input type="date" id="f-to" class="form-control form-control-sm">
        </div>
        <div class="col-auto">
            <button type="button" id="f-clear" class="btn btn-sm btn-outline-secondary" style="display:none">
                <i class="bi bi-x me-1"></i>Limpar
            </button>
        </div>
    </div>
</div>

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
                </tr>
            </thead>
            <tbody id="requests-tbody">
                @forelse($supplyRequests as $sr)
                @php
                    $itemNames = $sr->items->map(fn($i) => $i->item?->name ?? '')->implode(' ');
                    $searchData = strtolower(implode(' ', [
                        $sr->code ?? '',
                        $sr->title,
                        $sr->costCenter->name,
                        $sr->user->name,
                        $itemNames,
                    ]));
                @endphp
                <tr style="cursor:pointer"
                    onclick="window.location='{{ route('requests.show', $sr) }}'"
                    data-search="{{ $searchData }}"
                    data-status="{{ $sr->status->value }}"
                    data-urgency="{{ $sr->urgency->value }}"
                    data-cc="{{ $sr->cost_center_id }}"
                    data-user="{{ $sr->user_id }}"
                    data-date="{{ $sr->created_at->format('Y-m-d') }}">
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
                    <td><span class="cs-badge {{ $sr->urgency->badgeClass() }}">{{ $sr->urgency->label() }}</span></td>
                    <td><span class="cs-badge {{ $sr->status->badgeClass() }}">{{ $sr->status->label() }}</span></td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr id="empty-row">
                    <td colspan="8" class="text-center py-5" style="color:#94A3B8">
                        <i class="bi bi-clipboard-check" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Nenhuma solicitação encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div id="no-results" class="text-center py-5" style="display:none;color:#94A3B8">
        <i class="bi bi-search" style="font-size:32px;display:block;margin-bottom:8px"></i>
        Nenhuma solicitação corresponde aos filtros.
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const tbody    = document.getElementById('requests-tbody');
    const noRes    = document.getElementById('no-results');
    const clearBtn = document.getElementById('f-clear');
    const rows     = Array.from(tbody.querySelectorAll('tr[data-search]'));

    const inputs = {
        q:       document.getElementById('f-q'),
        status:  document.getElementById('f-status'),
        urgency: document.getElementById('f-urgency'),
        cc:      document.getElementById('f-cc'),
        user:    document.getElementById('f-user'),
        from:    document.getElementById('f-from'),
        to:      document.getElementById('f-to'),
    };

    function filter() {
        const q       = inputs.q?.value.trim().toLowerCase() || '';
        const terms   = q.split(/\s+/).filter(Boolean);
        const status  = inputs.status?.value  || '';
        const urgency = inputs.urgency?.value || '';
        const cc      = inputs.cc?.value      || '';
        const user    = inputs.user?.value    || '';
        const from    = inputs.from?.value    || '';
        const to      = inputs.to?.value      || '';

        const hasFilter = q || status || urgency || cc || user || from || to;
        clearBtn.style.display = hasFilter ? '' : 'none';

        let visible = 0;
        rows.forEach(function (row) {
            const matchQ       = !terms.length || terms.every(t => row.dataset.search.includes(t));
            const matchStatus  = !status  || row.dataset.status  === status;
            const matchUrgency = !urgency || row.dataset.urgency === urgency;
            const matchCc      = !cc      || row.dataset.cc      === cc;
            const matchUser    = !user    || row.dataset.user    === user;
            const matchFrom    = !from    || row.dataset.date    >= from;
            const matchTo      = !to      || row.dataset.date    <= to;

            const show = matchQ && matchStatus && matchUrgency && matchCc && matchUser && matchFrom && matchTo;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        noRes.style.display = (rows.length > 0 && visible === 0) ? 'block' : 'none';
    }

    // Busca: instantânea
    inputs.q?.addEventListener('input', filter);

    // Selects e datas: ao mudar
    ['status', 'urgency', 'cc', 'user', 'from', 'to'].forEach(function (key) {
        inputs[key]?.addEventListener('change', filter);
    });

    // Limpar
    clearBtn?.addEventListener('click', function () {
        Object.values(inputs).forEach(el => { if (el) el.value = ''; });
        filter();
        inputs.q?.focus();
    });
})();
</script>
@endpush
