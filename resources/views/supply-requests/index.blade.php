@extends('layouts.app')
@section('title', 'Solicitações — CelestaSupply')

@section('content')
@php $isBuyerOrAdmin = auth()->user()->isBuyerOrAdmin(); @endphp
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Solicitações</h1>
    <div class="d-flex gap-2">
        @if($isBuyerOrAdmin)
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
                    <ul class="dropdown-menu py-1 shadow-sm" style="min-width:280px;max-width:100vw;max-height:260px;overflow-y:auto">
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
                <div class="dropdown">
                    <button type="button" id="f-urgency-btn"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            class="btn btn-sm w-100 text-start d-flex align-items-center justify-content-between"
                            style="border:1px solid #DEE2E6;background:#fff;font-size:.875rem;padding:.25rem .5rem;color:#212529;border-radius:.25rem">
                        <span id="f-urgency-label">Todas</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.5"></i>
                    </button>
                    <ul class="dropdown-menu py-1 shadow-sm" style="min-width:280px;max-width:100vw">
                        <li><a class="dropdown-item" href="#" data-value="">Todas</a></li>
                        @foreach(\App\Enums\Urgency::cases() as $u)
                        <li><a class="dropdown-item f-urgency-opt" href="#" data-value="{{ $u->value }}">{{ $u->label() }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <input type="hidden" id="f-urgency" class="f-urgency-input">
            </div>
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Centro de Custo</label>
                <div class="dropdown">
                    <button type="button" id="f-cc-btn"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            class="btn btn-sm w-100 text-start d-flex align-items-center justify-content-between"
                            style="border:1px solid #DEE2E6;background:#fff;font-size:.875rem;padding:.25rem .5rem;color:#212529;border-radius:.25rem">
                        <span id="f-cc-label">Todos</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.5"></i>
                    </button>
                    <ul class="dropdown-menu py-1 shadow-sm" style="min-width:280px;max-width:100vw">
                        <li><a class="dropdown-item" href="#" data-value="">Todos</a></li>
                        @foreach($costCenters as $cc)
                        <li><a class="dropdown-item f-cc-opt" href="#" data-value="{{ $cc->id }}">{{ $cc->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <input type="hidden" id="f-cc" class="f-cc-input">
            </div>
            @if($isBuyerOrAdmin)
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Solicitante</label>
                <div class="dropdown">
                    <button type="button" id="f-user-btn"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside"
                            class="btn btn-sm w-100 text-start d-flex align-items-center justify-content-between"
                            style="border:1px solid #DEE2E6;background:#fff;font-size:.875rem;padding:.25rem .5rem;color:#212529;border-radius:.25rem">
                        <span id="f-user-label">Todos</span>
                        <i class="bi bi-chevron-down" style="font-size:10px;opacity:.5"></i>
                    </button>
                    <ul class="dropdown-menu py-1 shadow-sm" style="min-width:280px;max-width:100vw">
                        <li><a class="dropdown-item" href="#" data-value="">Todos</a></li>
                        @foreach($requesters as $u)
                        <li><a class="dropdown-item f-user-opt" href="#" data-value="{{ $u->id }}">{{ $u->name }}</a></li>
                        @endforeach
                    </ul>
                </div>
                <input type="hidden" id="f-user" class="f-user-input">
            </div>
            @endif
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">De</label>
                <div class="date-picker-wrapper" style="position:relative">
                    <input type="text" id="f-from" class="form-control form-control-sm date-picker-input" placeholder="Selecione a data" style="font-size:.875rem">
                    <div class="date-picker-menu" style="display:none;position:absolute;top:100%;left:0;background:white;border:1px solid #DEE2E6;border-radius:.25rem;box-shadow:0 2px 8px rgba(0,0,0,.1);z-index:100;width:320px;padding:12px;margin-top:4px">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                            <button type="button" class="date-picker-prev" style="background:none;border:none;cursor:pointer;padding:4px 8px"><i class="bi bi-chevron-left" style="font-size:16px"></i></button>
                            <span class="date-picker-title" style="font-weight:600;font-size:14px">Janeiro 2026</span>
                            <button type="button" class="date-picker-next" style="background:none;border:none;cursor:pointer;padding:4px 8px"><i class="bi bi-chevron-right" style="font-size:16px"></i></button>
                        </div>
                        <div class="date-picker-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md">
                <label class="form-label fw-semibold" style="font-size:12px">Até</label>
                <div class="date-picker-wrapper" style="position:relative">
                    <input type="text" id="f-to" class="form-control form-control-sm date-picker-input" placeholder="Selecione a data" style="font-size:.875rem">
                    <div class="date-picker-menu" style="display:none;position:absolute;top:100%;left:0;background:white;border:1px solid #DEE2E6;border-radius:.25rem;box-shadow:0 2px 8px rgba(0,0,0,.1);z-index:100;width:320px;padding:12px;margin-top:4px">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                            <button type="button" class="date-picker-prev" style="background:none;border:none;cursor:pointer;padding:4px 8px"><i class="bi bi-chevron-left" style="font-size:16px"></i></button>
                            <span class="date-picker-title" style="font-weight:600;font-size:14px">Janeiro 2026</span>
                            <button type="button" class="date-picker-next" style="background:none;border:none;cursor:pointer;padding:4px 8px"><i class="bi bi-chevron-right" style="font-size:16px"></i></button>
                        </div>
                        <div class="date-picker-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px"></div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-auto">
                <label class="form-label fw-semibold" style="font-size:12px">Filtro</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="f-has-pending" value="true">
                    <label class="form-check-label" for="f-has-pending" style="font-size:13px;font-weight:500;cursor:pointer;user-select:none">
                        Apenas com pendências
                    </label>
                </div>
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
                    @if($isBuyerOrAdmin)
                    <th data-sort-col="userName" style="{{ $thStyle }}">Solicitante <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    @endif
                    <th data-sort-col="urgencyOrder" style="{{ $thStyle }}">Urgência <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th data-sort-col="statusLabel" style="{{ $thStyle }}">Status <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th data-sort-col="date" style="{{ $thStyle }}">Data <i class="sort-icon bi bi-chevron-expand ms-1" style="opacity:.3;font-size:10px"></i></th>
                    <th style="{{ $thStyle }}">Nº Pedido</th>
                    <th style="{{ $thStyle }}">Pendências</th>
                </tr>
            </thead>
            <tbody id="requests-tbody">
                @forelse($supplyRequests as $sr)
                @php
                    $itemNames    = $sr->items->map(fn($i) => $i->item?->name ?? '')->implode(' ');
                    $orderNumbers = $sr->items
                        ->pluck('order_number')
                        ->filter()
                        ->unique()
                        ->sort()
                        ->map(fn($num) => 'PC-' . str_pad($num, 4, '0', STR_PAD_LEFT))
                        ->values();
                    $searchData   = strtolower(implode(' ', [
                        $sr->code ?? '',
                        $sr->title,
                        $sr->costCenter->name,
                        $sr->user->name,
                        $itemNames,
                        $orderNumbers->implode(' '),
                    ]));
                    $urgencyOrder = $sr->urgency->sortOrder();
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
                    data-status-label="{{ $sr->status->label() }}"
                    data-orders="{{ $orderNumbers->implode(' ') }}"
                    data-pending-count="{{ $sr->getPendingItemsCount() }}">
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:12px;font-weight:600">
                            {{ $sr->code ?? '—' }}
                        </span>
                    </td>
                    <td style="font-size:14px;font-weight:500">{{ $sr->title }}</td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->costCenter->name }}</td>
                    @if($isBuyerOrAdmin)
                    <td style="font-size:13px;color:#64748B">{{ $sr->user->name }}</td>
                    @endif
                    <td><span class="cs-badge {{ $sr->urgency->badgeClass() }}">{{ $sr->urgency->label() }}</span></td>
                    <td><span class="cs-badge {{ $sr->status->badgeClass() }}">{{ $sr->status->label() }}</span></td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center" onclick="event.stopPropagation()">
                        @if($orderNumbers->isNotEmpty())
                            <div style="display:flex;gap:4px;flex-wrap:wrap;justify-content:center">
                                @foreach($orderNumbers as $pc)
                                <span class="badge bg-light text-dark border" style="font-size:11px;font-weight:600;font-family:monospace">{{ $pc }}</span>
                                @endforeach
                            </div>
                        @else
                            <span style="color:#D1D9E6;font-size:12px">—</span>
                        @endif
                    </td>
                    <td style="text-align:center" onclick="event.stopPropagation()">
                        @if($sr->hasPendingItems())
                            <a href="{{ route('requests.show', $sr) }}?highlight=pending"
                               class="cs-badge"
                               style="background:#F59E0B;color:white;padding:4px 8px;border-radius:4px;font-size:11px;font-weight:600;text-decoration:none;cursor:pointer;display:inline-block"
                               title="{{ $sr->getPendingItemsCount() }} item(ns) aguardando ação (Pendente ou Em Cotação)">
                                ⚠ {{ $sr->getPendingItemsCount() }}
                            </a>
                        @else
                            <span style="color:#D1D9E6;font-size:12px">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr id="empty-row">
                    <td colspan="9" class="text-center py-5" style="color:#94A3B8">
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
        q:         document.getElementById('f-q'),
        urgency:   document.getElementById('f-urgency'),
        cc:        document.getElementById('f-cc'),
        user:      document.getElementById('f-user'),
        from:      document.getElementById('f-from'),
        to:        document.getElementById('f-to'),
        hasPending: document.getElementById('f-has-pending'),
    };

    // URL query params management
    function getFilterParams() {
        const params = new URLSearchParams(window.location.search);
        return {
            q: params.get('q') || '',
            urgency: params.get('urgency') || '',
            cc: params.get('cost_center_id') || '',
            user: params.get('user_id') || '',
            from: params.get('from') || '',
            to: params.get('to') || '',
            status: (params.getAll('status[]') || []),
            hasPending: params.get('has_pending') === 'true'
        };
    }

    function updateUrlParams() {
        const params = new URLSearchParams();
        if (inputs.q.value) params.append('q', inputs.q.value);
        if (inputs.urgency.value) params.append('urgency', inputs.urgency.value);
        if (inputs.cc.value) params.append('cost_center_id', inputs.cc.value);
        if (inputs.user.value) params.append('user_id', inputs.user.value);
        if (inputs.from.value) params.append('from', inputs.from.value);
        if (inputs.to.value) params.append('to', inputs.to.value);
        if (inputs.hasPending.checked) params.append('has_pending', 'true');
        document.querySelectorAll('.f-status-cb:checked').forEach(function(cb) {
            params.append('status[]', cb.value);
        });

        const queryString = params.toString();
        window.history.replaceState(null, '', queryString ? '?' + queryString : window.location.pathname);
    }

    // Restore filters from URL params on page load
    function restoreFiltersFromUrl() {
        const params = getFilterParams();
        if (params.q) inputs.q.value = params.q;

        if (params.urgency) {
            inputs.urgency.value = params.urgency;
            const urgencyLabel = document.querySelector('.f-urgency-opt[data-value="' + params.urgency + '"]');
            if (urgencyLabel) {
                document.getElementById('f-urgency-label').textContent = urgencyLabel.textContent.trim();
            }
        }

        if (params.cc) {
            inputs.cc.value = params.cc;
            const ccLabel = document.querySelector('.f-cc-opt[data-value="' + params.cc + '"]');
            if (ccLabel) {
                document.getElementById('f-cc-label').textContent = ccLabel.textContent.trim();
            }
        }

        if (params.user) {
            inputs.user.value = params.user;
            const userLabel = document.querySelector('.f-user-opt[data-value="' + params.user + '"]');
            if (userLabel) {
                document.getElementById('f-user-label').textContent = userLabel.textContent.trim();
            }
        }

        if (params.from) inputs.from.value = params.from;
        if (params.to) inputs.to.value = params.to;
        if (params.hasPending) inputs.hasPending.checked = true;

        // Restore status checkboxes
        if (params.status.length) {
            params.status.forEach(function(status) {
                const cb = document.querySelector('.f-status-cb[value="' + status + '"]');
                if (cb) cb.checked = true;
            });
        }
    }

    // Restore on page load
    restoreFiltersFromUrl();
    updateStatusLabel();
    filter();

    // Handle urgency dropdown
    var urgencyBtn = document.getElementById('f-urgency-btn');
    if (urgencyBtn) {
        urgencyBtn.parentElement.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const value = this.dataset.value;
                const label = this.textContent.trim();
                inputs.urgency.value = value;
                document.getElementById('f-urgency-label').textContent = label;
                filter();
            });
        });
    }

    // Handle cc dropdown
    var ccBtn = document.getElementById('f-cc-btn');
    if (ccBtn) {
        ccBtn.parentElement.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const value = this.dataset.value;
                const label = this.textContent.trim();
                inputs.cc.value = value;
                document.getElementById('f-cc-label').textContent = label;
                filter();
            });
        });
    }

    // Handle user dropdown
    var userBtn = document.getElementById('f-user-btn');
    if (userBtn) {
        userBtn.parentElement.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const value = this.dataset.value;
                const label = this.textContent.trim();
                inputs.user.value = value;
                document.getElementById('f-user-label').textContent = label;
                filter();
            });
        });
    }

    // Handle date input formatting (DD/MM/YYYY to YYYY-MM-DD)
    inputs.from?.addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '');
        if (val.length > 8) val = val.slice(0, 8);
        if (val.length >= 2) val = val.slice(0, 2) + '/' + val.slice(2);
        if (val.length >= 5) val = val.slice(0, 5) + '/' + val.slice(5);
        this.value = val;
    });

    inputs.from?.addEventListener('blur', function () {
        if (this.value) {
            const [day, month, year] = this.value.split('/');
            if (day && month && year && day.length === 2 && month.length === 2 && year.length === 4) {
                this.value = year + '-' + month + '-' + day;
                filter();
            } else {
                this.value = '';
                filter();
            }
        }
    });

    inputs.to?.addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '');
        if (val.length > 8) val = val.slice(0, 8);
        if (val.length >= 2) val = val.slice(0, 2) + '/' + val.slice(2);
        if (val.length >= 5) val = val.slice(0, 5) + '/' + val.slice(5);
        this.value = val;
    });

    inputs.to?.addEventListener('blur', function () {
        if (this.value) {
            const [day, month, year] = this.value.split('/');
            if (day && month && year && day.length === 2 && month.length === 2 && year.length === 4) {
                this.value = year + '-' + month + '-' + day;
                filter();
            } else {
                this.value = '';
                filter();
            }
        }
    });

    // Date picker calendar
    function initDatePicker(inputEl) {
        if (!inputEl) return;
        const wrapper = inputEl.closest('.date-picker-wrapper');
        if (!wrapper) return;
        const menu = wrapper.querySelector('.date-picker-menu');
        if (!menu) return;
        const grid = menu.querySelector('.date-picker-grid');
        const title = menu.querySelector('.date-picker-title');
        const prevBtn = menu.querySelector('.date-picker-prev');
        const nextBtn = menu.querySelector('.date-picker-next');
        if (!grid || !title || !prevBtn || !nextBtn) return;

        let currentDate = new Date();

        const months = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];

        function renderCalendar() {
            grid.innerHTML = '';
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            title.textContent = months[month] + ' ' + year;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                grid.innerHTML += '<div style="text-align:center;padding:4px;color:#ccc">—</div>';
            }

            // Days of month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayBtn = document.createElement('button');
                dayBtn.type = 'button';
                dayBtn.textContent = day;
                dayBtn.style.cssText = 'background:none;border:1px solid #e0e0e0;border-radius:4px;padding:6px;cursor:pointer;font-size:12px';
                dayBtn.addEventListener('mouseenter', function() { this.style.background = '#f0f0f0'; });
                dayBtn.addEventListener('mouseleave', function() { this.style.background = 'none'; });
                dayBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dateStr = year + '-' + String(month+1).padStart(2,'0') + '-' + String(day).padStart(2,'0');
                    inputEl.value = dateStr;
                    menu.style.display = 'none';
                    inputEl.dispatchEvent(new Event('change'));
                    filter();
                });
                grid.appendChild(dayBtn);
            }
        }

        inputEl.addEventListener('focus', function() {
            menu.style.display = 'block';
            renderCalendar();
        });

        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) menu.style.display = 'none';
        });

        prevBtn.addEventListener('click', function(e) {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
    }

    document.querySelectorAll('.date-picker-input')?.forEach?.(initDatePicker);

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
        const hasPending  = inputs.hasPending?.checked || false;

        const hasFilter = q || selStatuses.length || urgency || cc || user || from || to || hasPending;
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
            const matchPending = !hasPending || parseInt(row.dataset.pendingCount || 0) > 0;

            const show = matchQ && matchStatus && matchUrgency && matchCc && matchUser && matchFrom && matchTo && matchPending;
            row.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        noRes.style.display = (rows.length > 0 && visible === 0) ? 'block' : 'none';
        updateUrlParams();
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

    // Checkbox "Apenas com pendências"
    inputs.hasPending?.addEventListener('change', filter);

    // Limpar
    clearBtn?.addEventListener('click', function () {
        Object.values(inputs).forEach(el => {
            if (el) {
                if (el.type === 'checkbox') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
            }
        });
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
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Gerando…';

        fetch(buildExportUrl(pdfBase))
            .then(function (res) {
                if (!res.ok) throw new Error('erro');
                var disp = res.headers.get('content-disposition') || '';
                var m = disp.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                var filename = m ? m[1].replace(/['"]/g, '') : 'relatorio.pdf';
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
            .finally(function () { btn.disabled = false; btn.innerHTML = originalHtml; });
    });
})();
</script>
@endpush
