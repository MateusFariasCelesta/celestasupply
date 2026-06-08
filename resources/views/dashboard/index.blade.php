@extends('layouts.app')
@section('title', 'Dashboard — CelestaSupply')

@push('styles')
<style>
    /* ─── KPI Cards ─────────────────────────────── */
    .kpi-card {
        background: #fff;
        border: 1px solid #E2E9F4;
        border-left: 4px solid transparent;
        border-radius: 12px;
        padding: 22px 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 1px 2px rgba(15,32,68,.04), 0 4px 16px rgba(15,32,68,.06);
        transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .22s;
        height: 100%;
    }
    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 32px rgba(15,32,68,.11);
    }
    .kpi-blue  { border-left-color: #3B82F6; }
    .kpi-red   { border-left-color: #EF4444; }
    .kpi-amber { border-left-color: #F59E0B; }
    .kpi-green { border-left-color: #22C55E; }

    .kpi-icon {
        width: 52px; height: 52px;
        border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        transition: transform .2s cubic-bezier(.34,1.56,.64,1);
    }
    .kpi-card:hover .kpi-icon { transform: scale(1.12); }
    .kpi-icon-blue  { background: #EFF6FF; color: #3B82F6; }
    .kpi-icon-red   { background: #FEF2F2; color: #EF4444; }
    .kpi-icon-amber { background: #FFFBEB; color: #F59E0B; }
    .kpi-icon-green { background: #F0FDF4; color: #22C55E; }

    .kpi-label { font-size: 12px; color: #6B7280; font-weight: 500; margin-bottom: 5px; letter-spacing: .01em; }
    .kpi-value { font-size: 34px; font-weight: 800; color: #0F172A; line-height: 1; letter-spacing: -.04em; }

    /* ─── Section title ──────────────────────────── */
    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #0F172A;
        letter-spacing: -.01em;
    }

    /* ─── Status bars ────────────────────────────── */
    .status-row {
        padding: 9px 0;
        border-bottom: 1px solid #F1F5F9;
    }
    .status-row:last-child { border-bottom: none; padding-bottom: 0; }
    .status-row:first-child { padding-top: 0; }

    /* ─── Chart filter controls ──────────────────── */
    .period-btn {
        background: #F1F5F9;
        border: 1.5px solid transparent;
        border-radius: 7px;
        padding: 5px 14px;
        font-size: 12.5px;
        font-weight: 600;
        color: #64748B;
        cursor: pointer;
        transition: background .15s, border-color .15s, color .15s, transform .15s;
        line-height: 1.5;
    }
    .period-btn:hover { background: #E2E8F0; color: #334155; transform: translateY(-1px); }
    .period-btn.active {
        background: #EFF6FF;
        border-color: #93C5FD;
        color: #1D4ED8;
    }

    .cc-select {
        border: 1.5px solid #D1D9E6;
        border-radius: 7px;
        padding: 5px 30px 5px 11px;
        font-size: 13px;
        color: #374151;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z' fill='%2394A3B8'/%3E%3C/svg%3E") no-repeat right 10px center;
        appearance: none;
        cursor: pointer;
        transition: border-color .15s;
        font-family: inherit;
        max-width: 180px;
    }
    .cc-select:focus { outline: none; border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); }

    /* ─── Requester status cards ─────────────────── */
    .req-stat-card {
        background: #fff;
        border: 1px solid #E2E9F4;
        border-radius: 12px;
        padding: 18px 16px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(15,32,68,.04), 0 4px 12px rgba(15,32,68,.05);
        transition: transform .2s cubic-bezier(.34,1.56,.64,1), box-shadow .2s;
        height: 100%;
    }
    .req-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 24px rgba(15,32,68,.10);
    }
    .req-stat-icon { font-size: 26px; margin-bottom: 6px; display: block; }
    .req-stat-value { font-size: 28px; font-weight: 800; color: #0F172A; line-height: 1; letter-spacing: -.03em; margin-bottom: 4px; }
    .req-stat-label { font-size: 11.5px; color: #64748B; font-weight: 500; line-height: 1.3; }

    /* ─── Loading spinner ────────────────────────── */
    @keyframes spin { to { transform: rotate(360deg); } }
    .spin { animation: spin .7s linear infinite; display: inline-block; }

    /* ─── Stagger entrance ───────────────────────── */
    .dash-fade { animation: dashIn .4s ease both; }
    @keyframes dashIn {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .d1 { animation-delay: .05s; }
    .d2 { animation-delay: .12s; }
    .d3 { animation-delay: .19s; }
    .d4 { animation-delay: .26s; }
    .d5 { animation-delay: .33s; }
    .d6 { animation-delay: .40s; }
</style>
@endpush

@section('content')
@php $user = auth()->user(); @endphp

{{-- ── Header ── --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Dashboard</h1>
    <div class="text-end">
        <div style="font-size:13.5px;font-weight:600;color:#0F172A">{{ $user->name }}</div>
        <div style="font-size:12px;color:#94A3B8">{{ now()->format('d/m/Y') }}</div>
    </div>
</div>

{{-- ══════════════════ BUYER / ADMIN ══════════════════ --}}
@if($user->isBuyerOrAdmin())

{{-- KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3 dash-fade d1">
        <div class="kpi-card kpi-blue">
            <div class="kpi-icon kpi-icon-blue"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="kpi-label">Pendentes de Ação</div>
                <div class="kpi-value">{{ $pendingCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 dash-fade d2">
        <div class="kpi-card kpi-red">
            <div class="kpi-icon kpi-icon-red"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="kpi-label">Urgentes em Aberto</div>
                <div class="kpi-value">{{ $urgentOpenCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 dash-fade d3">
        <div class="kpi-card kpi-amber">
            <div class="kpi-icon kpi-icon-amber"><i class="bi bi-x-circle"></i></div>
            <div>
                <div class="kpi-label">Cancelamentos Pendentes</div>
                <div class="kpi-value">{{ $cancelRequestedCount }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 dash-fade d4">
        <div class="kpi-card kpi-green">
            <div class="kpi-icon kpi-icon-green"><i class="bi bi-check-circle"></i></div>
            <div>
                <div class="kpi-label">Concluídas este Mês</div>
                <div class="kpi-value">{{ $completedThisMonth }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Status Overview + Chart --}}
<div class="row g-3">

    {{-- Status Overview --}}
    <div class="col-12 col-lg-4 dash-fade d5">
        <div class="cs-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="section-title">Visão por Status</div>
                <span style="font-size:12px;color:#94A3B8;font-weight:500">{{ $byStatus->sum() }} total</span>
            </div>
            @php
                $statuses = \App\Enums\RequestStatus::cases();
                $total    = $byStatus->sum() ?: 1;
            @endphp
            @foreach($statuses as $status)
            @php
                $count = (int) $byStatus->get($status->value, 0);
                $pct   = round($count / $total * 100);
                $color = $status->chartColor();
            @endphp
            <div class="status-row">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-2">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $color }};flex-shrink:0;display:inline-block"></span>
                        <span style="font-size:13px;color:#374151;font-weight:500">{{ $status->label() }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span style="font-size:11.5px;color:#94A3B8">{{ $pct }}%</span>
                        <span style="font-size:13px;font-weight:700;color:#0F172A;min-width:18px;text-align:right">{{ $count }}</span>
                    </div>
                </div>
                <div style="height:5px;border-radius:3px;background:#F1F5F9;overflow:hidden">
                    <div style="width:{{ $pct }}%;height:5px;border-radius:3px;background:{{ $color }};transition:width .7s cubic-bezier(.4,0,.2,1)"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Chart with filters --}}
    <div class="col-12 col-lg-8 dash-fade d6">
        <div class="cs-card h-100" x-data="chartFilter()">

            {{-- Chart header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <div class="section-title">Solicitações por Mês</div>
                    <div style="font-size:12px;color:#94A3B8;margin-top:2px" x-text="filterDesc"></div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Stack toggle --}}
                    <button class="period-btn" :class="stacked?'active':''" @click="toggleStacked()"
                            title="Dividir por centro de custo">
                        <i class="bi bi-bar-chart-steps"></i>
                    </button>

                    {{-- Period toggle --}}
                    <div class="d-flex gap-1">
                        <button class="period-btn" :class="period===3?'active':''"  @click="setPeriod(3)">3M</button>
                        <button class="period-btn" :class="period===6?'active':''"  @click="setPeriod(6)">6M</button>
                        <button class="period-btn" :class="period===12?'active':''" @click="setPeriod(12)">12M</button>
                    </div>

                    {{-- Cost center (hidden when stacked) --}}
                    <select class="cc-select" x-model="costCenter" @change="updateChart()" x-show="!stacked">
                        <option value="">Todos os centros</option>
                        @foreach($costCenters as $cc)
                        <option value="{{ $cc->id }}">{{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <canvas id="requestsByMonthChart"></canvas>

        </div>
    </div>
</div>

{{-- ══════════════════ REQUESTER ══════════════════ --}}
@else

@php
$statusCards = [
    [\App\Enums\RequestStatus::Pending,         'bi-clock',           'text-primary',   '#3B82F6'],
    [\App\Enums\RequestStatus::InProgress,      'bi-arrow-repeat',    'text-warning',   '#F59E0B'],
    [\App\Enums\RequestStatus::Completed,       'bi-check-circle',    'text-success',   '#22C55E'],
    [\App\Enums\RequestStatus::CancelRequested, 'bi-question-circle', 'text-danger',    '#F43F5E'],
    [\App\Enums\RequestStatus::Cancelled,       'bi-x-circle',        'text-secondary', '#94A3B8'],
    [\App\Enums\RequestStatus::Draft,           'bi-file-earmark',    'text-muted',     '#CBD5E1'],
];
@endphp

<div class="row g-3 mb-4">
    @foreach($statusCards as [$status, $icon, $color, $hex])
    <div class="col-6 col-md-4 col-lg-2 dash-fade" style="animation-delay:{{ $loop->index * 0.07 }}s">
        <div class="req-stat-card">
            <i class="bi {{ $icon }} req-stat-icon {{ $color }}"></i>
            <div class="req-stat-value">{{ $counts->get($status->value, 0) }}</div>
            <div class="req-stat-label">{{ $status->label() }}</div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="section-title">Minhas Últimas Solicitações</div>
    <a href="{{ route('requests.index') }}" class="btn btn-primary btn-sm">
        Ver todas <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<div class="cs-card dash-fade d4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="font-size:11.5px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em">Código</th>
                    <th style="font-size:11.5px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em">Título</th>
                    <th style="font-size:11.5px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em">Centro de Custo</th>
                    <th style="font-size:11.5px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em">Status</th>
                    <th style="font-size:11.5px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.05em">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $sr)
                <tr style="cursor:pointer" onclick="window.location='{{ route('requests.show', $sr) }}'">
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:12px;font-weight:600">{{ $sr->code }}</span>
                    </td>
                    <td style="font-size:14px;font-weight:500;color:#0F172A">{{ $sr->title }}</td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->costCenter?->name ?? '—' }}</td>
                    <td><span class="cs-badge {{ $sr->status->badgeClass() }}">{{ $sr->status->label() }}</span></td>
                    <td style="font-size:13px;color:#64748B">{{ $sr->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5" style="color:#94A3B8">
                        <i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Nenhuma solicitação encontrada.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endif
@endsection

@push('scripts')
@if($user->isBuyerOrAdmin())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.color       = '#6B7280';

    const _allLabels   = @json($chartLabels);
    const _allDatasets = @json($chartDatasets);
    const _ccNames     = { @foreach($costCenters as $cc)'{{ $cc->id }}': '{{ addslashes($cc->name) }}', @endforeach };
    let   _chart       = null;

    const CC_PALETTE = [
        [59,130,246],[16,185,129],[245,158,11],[239,68,68],
        [139,92,246],[14,165,233],[236,72,153],[20,184,166],
        [251,146,60],[132,204,22],[234,179,8],[6,182,212],
        [168,85,247],[34,197,94],[249,115,22],[244,63,94],
        [99,102,241],[2,132,199],[217,70,239],[20,184,166],
        [251,191,36],[74,222,128],[248,113,113],[129,140,248],
        [52,211,153],[253,186,116],[167,139,250],[94,234,212],
        [110,231,183],[253,224,71],[252,165,165],[196,181,253],
    ];

    function chartFilter() {
        return {
            period:     12,
            costCenter: '',
            stacked:    false,

            get currentLabels() {
                return _allLabels.slice(-this.period);
            },
            get currentData() {
                const ds = _allDatasets[this.costCenter] ?? _allDatasets[''];
                return ds.slice(-this.period);
            },
            get filterDesc() {
                if (this.stacked) return `Últimos ${this.period} meses · por centro de custo`;
                const p  = `Últimos ${this.period} meses`;
                const el = this.$el.querySelector('.cc-select');
                const cc = el ? el.options[el.selectedIndex].text : '';
                return (cc && cc !== 'Todos os centros') ? `${p} · ${cc}` : p;
            },

            init() {
                const canvas = document.getElementById('requestsByMonthChart');

                _chart = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels:   this.currentLabels,
                        datasets: this.buildSingleDataset(),
                    },
                    options: {
                        responsive: true,
                        animation: { duration: 450, easing: 'easeInOutQuart' },
                        plugins: {
                            legend: {
                                display: false,
                                position: 'bottom',
                                labels: { font: { size: 11.5 }, padding: 14, boxWidth: 11, boxHeight: 11 },
                            },
                            tooltip: {
                                backgroundColor: '#0F172A',
                                titleColor:      '#94A3B8',
                                bodyColor:       '#fff',
                                bodyFont:        { size: 13, weight: '700' },
                                padding:         12,
                                cornerRadius:    8,
                                callbacks: {
                                    label: c => ` ${c.dataset.label}: ${c.parsed.y}`,
                                }
                            }
                        },
                        scales: {
                            y: { beginAtZero: true, stacked: false, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#F1F5F9' } },
                            x: { stacked: false, grid: { display: false } },
                        }
                    }
                });

                setTimeout(() => this.applyGradient(), 80);
            },

            buildSingleDataset() {
                return [{
                    label: 'Solicitações',
                    data:  this.currentData,
                    backgroundColor:      'rgba(59,130,246,.7)',
                    hoverBackgroundColor: 'rgba(37,99,235,.88)',
                    borderRadius: 6,
                    borderSkipped: false,
                }];
            },

            buildStackedDatasets() {
                return Object.keys(_allDatasets)
                    .filter(k => k !== '')
                    .map((id, i) => {
                        const [r,g,b] = CC_PALETTE[i % CC_PALETTE.length];
                        return {
                            label: _ccNames[id] || id,
                            data:  _allDatasets[id].slice(-this.period),
                            backgroundColor:      `rgba(${r},${g},${b},.75)`,
                            hoverBackgroundColor: `rgba(${r},${g},${b},.92)`,
                            borderRadius: 0,
                            borderSkipped: false,
                        };
                    });
            },

            applyGradient() {
                if (this.stacked) return;
                const area = _chart.chartArea;
                if (!area) return;
                const canvas = document.getElementById('requestsByMonthChart');
                const grad   = canvas.getContext('2d').createLinearGradient(0, area.top, 0, area.bottom);
                grad.addColorStop(0, 'rgba(59,130,246,.82)');
                grad.addColorStop(1, 'rgba(59,130,246,.18)');
                _chart.data.datasets[0].backgroundColor = grad;
                _chart.update('none');
            },

            toggleStacked() {
                this.stacked = !this.stacked;
                this.rebuildChart();
            },

            setPeriod(p) {
                this.period = p;
                this.rebuildChart();
            },

            updateChart() {
                this.rebuildChart();
            },

            rebuildChart() {
                _chart.data.labels   = this.currentLabels;
                _chart.data.datasets = this.stacked
                    ? this.buildStackedDatasets()
                    : this.buildSingleDataset();
                _chart.options.scales.x.stacked              = this.stacked;
                _chart.options.scales.y.stacked              = this.stacked;
                _chart.options.plugins.legend.display        = this.stacked;
                _chart.update('active');
                if (!this.stacked) setTimeout(() => this.applyGradient(), 500);
            },
        };
    }
</script>
@endif
@endpush
