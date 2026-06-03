@extends('layouts.app')
@section('title', 'Solicitações — CelestaSupply')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Solicitações</h1>
    <div class="d-flex gap-2">
        @if(auth()->user()->isBuyerOrAdmin())
        <button id="btn-export-excel" class="btn btn-sm fw-semibold" style="background:#217346;color:#fff;border:none;border-radius:6px">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </button>
        <button id="btn-export-pdf" class="btn btn-sm fw-semibold" style="background:#DC2626;color:#fff;border:none;border-radius:6px">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </button>
        @endif
        <a href="{{ route('requests.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Nova Solicitação
        </a>
    </div>
</div>

{{-- Busca --}}
<div style="max-width:600px;margin:0 auto 24px">
    <div style="position:relative">
        <i class="bi bi-search" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#94A3B8;font-size:16px;pointer-events:none"></i>
        <input type="text" id="f-q" class="form-control"
               placeholder="Buscar por código, título, item, solicitante…"
               autocomplete="off"
               style="padding:13px 16px 13px 44px;font-size:15px;border-radius:8px;border:1.5px solid #D1D9E6;box-shadow:0 2px 12px rgba(15,32,68,.07)">
    </div>
</div>

{{-- Filtros client-side --}}
<div class="cs-card mb-4" id="f-filters-card">
    {{-- Botão toggle (só mobile) --}}
    <button class="btn btn-sm btn-outline-secondary d-flex d-md-none align-items-center gap-1 mb-2 collapsed"
            id="f-toggle-btn" type="button"
            data-bs-toggle="collapse" data-bs-target="#f-filters-body"
            aria-expanded="false" aria-controls="f-filters-body">
        <i class="bi bi-sliders2"></i> Filtros
        <i class="bi bi-chevron-down f-chevron" style="font-size:10px"></i>
    </button>

    {{-- Filtros --}}
    <div class="collapse d-md-block" id="f-filters-body">
        <div class="row g-2 align-items-end">
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Status</label>
                <div class="dropdown">
                    <button type="button" id="f-status-btn"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            class="btn btn-sm w-100 text-start d-flex align-items-center justify-content-between"
                            style="border:1px solid #DEE2E6;background:#fff;font-size:.875rem;padding:.25rem .5rem;color:#212529;border-radius:.25rem">
                        <span id="f-status-label">Todos</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.5"></i>
                    </button>
                    <ul class="dropdown-menu w-100 py-1 shadow-sm" style="min-width:0;max-height:260px;overflow-y:auto">
                        @foreach(\App\Enums\RequestStatus::cases() as $s)
                        <li>
                            <label class="dropdown-item d-flex align-items-center gap-2 py-2" style="cursor:pointer;font-size:.875rem">
                                <input type="checkbox" class="form-check-input flex-shrink-0 f-status-cb" value="{{ $s->value }}">
                                {{ $s->label() }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Urgência</label>
                <select id="f-urgency" class="form-select form-select-sm">
                    <option value="">Todas</option>
                    @foreach(\App\Enums\Urgency::cases() as $u)
                    <option value="{{ $u->value }}">{{ $u->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Centro de Custo</label>
                <select id="f-cc" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($costCenters as $cc)
                    <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isBuyerOrAdmin())
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Solicitante</label>
                <select id="f-user" class="form-select form-select-sm">
                    <option value="">Todos</option>
                    @foreach($requesters as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">De</label>
                <input type="date" id="f-from" class="form-control form-control-sm">
            </div>
            <div class="col-6 col-md">
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
</div>

<div class="cs-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    @php $thStyle = 'font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em;cursor:pointer;user-select:none;white-space:nowrap'; @endphp
                    <th data-sort-col="code" style="{{ $thStyle }}">Código <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th data-sort-col="title" style="{{ $thStyle }}">Título <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th data-sort-col="ccName" style="{{ $thStyle }}">Centro de Custo <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    @if(auth()->user()->isBuyerOrAdmin())
                    <th data-sort-col="userName" style="{{ $thStyle }}">Solicitante <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    @endif
                    <th data-sort-col="urgencyOrder" style="{{ $thStyle }}">Urgência <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th data-sort-col="statusLabel" style="{{ $thStyle }}">Status <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th data-sort-col="date" style="{{ $thStyle }}">Data <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                </tr>
            </thead>
            <tbody id="requests-tbody">
                @forelse($supplyRequests as $sr)
                @php
                    $itemNames    = $sr->items->map(fn($i) => $i->item?->name ?? '')->implode(' ');
                    $searchData   = strtolower(implode(' ', [
                        $sr->code ?? '',
                        $sr->title,
                        $sr->costCenter->name,
                        $sr->user->name,
                        $itemNames,
                    ]));
                    $urgencyOrder = match($sr->urgency->value) { 'high' => 3, 'medium' => 2, default => 1 };
                @endphp
                <tr style="cursor:pointer"
                    onclick="window.location='{{ route('requests.show', $sr) }}'"
                    data-search="{{ $searchData }}"
                    data-status="{{ $sr->status->value }}"
                    data-urgency="{{ $sr->urgency->value }}"
                    data-cc="{{ $sr->cost_center_id }}"
                    data-user="{{ $sr->user_id }}"
                    data-date="{{ $sr->created_at->format('Y-m-d') }}"
                    data-code="{{ $sr->code ?? $sr->name }}"
                    data-title="{{ $sr->title }}"
                    data-cc-name="{{ $sr->costCenter->name }}"
                    data-user-name="{{ $sr->user->name }}"
                    data-urgency-order="{{ $urgencyOrder }}"
                    data-status-label="{{ $sr->status->label() }}">
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

@push('styles')
<style>
    .f-chevron { transition: transform .2s ease; }
    #f-toggle-btn:not(.collapsed) .f-chevron { transform: rotate(-180deg); }
    @media (max-width: 767px) {
        #f-filters-card { background: none; border: none; box-shadow: none; padding: 0; }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const tbody    = document.getElementById('requests-tbody');
    const noRes    = document.getElementById('no-results');
    const clearBtn = document.getElementById('f-clear');
    const rows     = Array.from(tbody.querySelectorAll('tr[data-search]'));

    const inputs = {
        q:       document.getElementById('f-q'),
        urgency: document.getElementById('f-urgency'),
        cc:      document.getElementById('f-cc'),
        user:    document.getElementById('f-user'),
        from:    document.getElementById('f-from'),
        to:      document.getElementById('f-to'),
    };

    function getSelectedStatuses() {
        return Array.from(document.querySelectorAll('.f-status-cb:checked')).map(cb => cb.value);
    }

    function updateStatusLabel() {
        const checked = document.querySelectorAll('.f-status-cb:checked');
        const label   = document.getElementById('f-status-label');
        if (!label) return;
        label.textContent = checked.length === 0
            ? 'Todos'
            : checked.length === 1
                ? checked[0].closest('label').textContent.trim()
                : checked.length + ' selecionados';
    }

    function filter() {
        const q           = inputs.q?.value.trim().toLowerCase() || '';
        const terms       = q.split(/\s+/).filter(Boolean);
        const selStatuses = getSelectedStatuses();
        const urgency     = inputs.urgency?.value || '';
        const cc          = inputs.cc?.value      || '';
        const user        = inputs.user?.value    || '';
        const from        = inputs.from?.value    || '';
        const to          = inputs.to?.value      || '';

        const hasFilter = q || selStatuses.length || urgency || cc || user || from || to;
        clearBtn.style.display = hasFilter ? '' : 'none';

        let visible = 0;
        rows.forEach(function (row) {
            const matchQ       = !terms.length || terms.every(t => row.dataset.search.includes(t));
            const matchStatus  = !selStatuses.length || selStatuses.includes(row.dataset.status);
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

    // Status: checkboxes no dropdown
    document.querySelectorAll('.f-status-cb').forEach(function (cb) {
        cb.addEventListener('change', function () { updateStatusLabel(); filter(); });
    });

    // Busca: instantânea
    inputs.q?.addEventListener('input', filter);

    // Selects e datas: ao mudar
    ['urgency', 'cc', 'user', 'from', 'to'].forEach(function (key) {
        inputs[key]?.addEventListener('change', filter);
    });

    // Limpar
    clearBtn?.addEventListener('click', function () {
        Object.values(inputs).forEach(el => { if (el) el.value = ''; });
        document.querySelectorAll('.f-status-cb').forEach(cb => cb.checked = false);
        updateStatusLabel();
        filter();
        inputs.q?.focus();
    });

    // Ordenação por coluna
    let sortCol = null;
    let sortDir = 'asc';

    document.querySelectorAll('th[data-sort-col]').forEach(function (th) {
        th.addEventListener('click', function () {
            const col = th.dataset.sortCol;
            sortDir   = (sortCol === col && sortDir === 'asc') ? 'desc' : 'asc';
            sortCol   = col;

            document.querySelectorAll('th[data-sort-col]').forEach(function (h) {
                const icon = h.querySelector('.sort-icon');
                if (!icon) return;
                if (h.dataset.sortCol === sortCol) {
                    icon.className = 'sort-icon bi bi-chevron-' + (sortDir === 'asc' ? 'up' : 'down') + ' ms-1';
                    icon.style.opacity = '1';
                    icon.style.color   = '#6366F1';
                } else {
                    icon.className     = 'sort-icon bi bi-chevron-expand ms-1';
                    icon.style.opacity = '.3';
                    icon.style.color   = '';
                }
            });

            const numericCols = ['urgencyOrder'];
            rows.sort(function (a, b) {
                const aVal = a.dataset[col] || '';
                const bVal = b.dataset[col] || '';
                const cmp  = numericCols.includes(col)
                    ? parseFloat(aVal || 0) - parseFloat(bVal || 0)
                    : aVal.localeCompare(bVal, 'pt-BR', { numeric: true, sensitivity: 'base' });
                return sortDir === 'asc' ? cmp : -cmp;
            });

            rows.forEach(function (row) { tbody.appendChild(row); });
            filter();
        });
    });

    // ── Export com filtros actuais ──
    function buildExportUrl(base) {
        const params = new URLSearchParams();

        const q = document.getElementById('f-q')?.value.trim();
        if (q) params.append('q', q);

        document.querySelectorAll('.f-status-cb:checked').forEach(function (cb) {
            params.append('status[]', cb.value);
        });

        const urgency = document.getElementById('f-urgency')?.value;
        if (urgency) params.append('urgency', urgency);

        const cc = document.getElementById('f-cc')?.value;
        if (cc) params.append('cost_center_id', cc);

        const user = document.getElementById('f-user')?.value;
        if (user) params.append('user_id', user);

        const from = document.getElementById('f-from')?.value;
        if (from) params.append('from', from);

        const to = document.getElementById('f-to')?.value;
        if (to) params.append('to', to);

        return base + '?' + params.toString();
    }

    const excelBase = '{{ route("reports.export.excel") }}';
    const pdfBase   = '{{ route("reports.export.pdf") }}';

    document.getElementById('btn-export-excel')?.addEventListener('click', function () {
        window.open(buildExportUrl(excelBase), '_blank');
    });
    document.getElementById('btn-export-pdf')?.addEventListener('click', function () {
        window.open(buildExportUrl(pdfBase), '_blank');
    });
})();
</script>
@endpush
