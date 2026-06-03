@extends('layouts.app')
@section('title', 'Dashboard — CelestaSupply')

@section('content')

@php $user = auth()->user(); @endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Dashboard</h1>
    <span class="text-muted small">Bem-vindo, {{ $user->name }}</span>
</div>

{{-- ===================== BUYER / ADMIN ===================== --}}
@if($user->isBuyerOrAdmin())

{{-- KPI cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="cs-kpi-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Pendentes de Ação</div>
                    <div class="fs-2 fw-bold lh-1 mt-1">{{ $pendingCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="cs-kpi-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Cancelamentos Pendentes</div>
                    <div class="fs-2 fw-bold lh-1 mt-1">{{ $cancelRequestedCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="cs-kpi-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-exclamation-lg"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Urgentes em Aberto</div>
                    <div class="fs-2 fw-bold lh-1 mt-1">{{ $urgentOpenCount }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Status overview + Requests-by-month chart --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-1">Visão Geral por Status</h6>
                @php
                    $statuses = \App\Enums\RequestStatus::cases();
                    $statusColors = [
                        'draft'           => '#94A3B8',
                        'pending'         => '#3B82F6',
                        'inProgress'      => '#F59E0B',
                        'completed'       => '#22C55E',
                        'cancelRequested' => '#F43F5E',
                        'cancelled'       => '#EF4444',
                    ];
                    $total = $byStatus->sum() ?: 1;
                @endphp
                <div class="text-muted small mb-3">{{ $byStatus->sum() }} no total</div>
                @foreach($statuses as $status)
                @php
                    $count = (int) $byStatus->get($status->value, 0);
                    $pct   = round($count / $total * 100);
                    $color = $statusColors[$status->value] ?? '#94A3B8';
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <span style="width:9px;height:9px;border-radius:50%;background:{{ $color }};flex-shrink:0;display:inline-block"></span>
                            <span class="small text-dark">{{ $status->label() }}</span>
                        </div>
                        <span class="fw-semibold small">{{ $count }}</span>
                    </div>
                    <div style="height:4px;border-radius:2px;background:#F1F5F9">
                        <div style="width:{{ $pct }}%;height:4px;border-radius:2px;background:{{ $color }};transition:width .4s ease"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Solicitações por Mês</h6>
                <canvas id="requestsByMonthChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Admin: value charts --}}
@if($user->isAdmin() && isset($valueLabels))
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Valor Comprado por Mês</h6>
                <canvas id="valueByMonthChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Valor por Centro de Custo</h6>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Centro de Custo</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($valueByCostCenter as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td class="text-end fw-semibold">R$ {{ number_format($row->total, 2, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-3">Nenhum dado de preço registrado.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===================== REQUESTER ===================== --}}
@else

@php
$statusCards = [
    [\App\Enums\RequestStatus::Pending,         'bi-clock',          'text-warning'],
    [\App\Enums\RequestStatus::InProgress,      'bi-arrow-repeat',   'text-primary'],
    [\App\Enums\RequestStatus::Completed,       'bi-check-circle',   'text-success'],
    [\App\Enums\RequestStatus::CancelRequested, 'bi-question-circle','text-warning'],
    [\App\Enums\RequestStatus::Cancelled,       'bi-x-circle',       'text-danger'],
    [\App\Enums\RequestStatus::Draft,           'bi-file-earmark',   'text-secondary'],
];
@endphp

<div class="row g-3 mb-4">
    @foreach($statusCards as [$status, $icon, $color])
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card border-0 shadow-sm text-center h-100">
            <div class="card-body py-3 px-2">
                <i class="bi {{ $icon }} {{ $color }} fs-2"></i>
                <div class="fs-3 fw-bold mt-1">{{ $counts->get($status->value, 0) }}</div>
                <div class="text-muted" style="font-size:.75rem;line-height:1.2">{{ $status->label() }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-semibold">Minhas Últimas Solicitações</h6>
    <a href="{{ route('requests.index') }}" class="btn btn-outline-primary btn-sm">
        Ver Todas <i class="bi bi-arrow-right ms-1"></i>
    </a>
</div>

<div class="cs-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Código</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Título</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Centro de Custo</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Status</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Data</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $sr)
                <tr style="cursor:pointer" onclick="window.location='{{ route('requests.show', $sr) }}'">
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:12px;font-weight:600">
                            {{ $sr->code }}
                        </span>
                    </td>
                    <td style="font-size:14px;font-weight:500">{{ $sr->title }}</td>
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
(function () {
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.color       = '#6B7280';

    new Chart(document.getElementById('requestsByMonthChart'), {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Solicitações',
                data: @json($chartData),
                backgroundColor: 'rgba(99, 102, 241, 0.75)',
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, precision: 0 }, grid: { color: '#F3F4F6' } },
                x: { grid: { display: false } }
            }
        }
    });

    @if($user->isAdmin() && isset($valueLabels))
    new Chart(document.getElementById('valueByMonthChart'), {
        type: 'line',
        data: {
            labels: @json($valueLabels),
            datasets: [{
                label: 'Valor (R$)',
                data: @json($valueData),
                borderColor: 'rgba(16, 185, 129, 1)',
                backgroundColor: 'rgba(16, 185, 129, 0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#F3F4F6' },
                    ticks: {
                        callback: v => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                    }
                },
                x: { grid: { display: false } }
            }
        }
    });
    @endif
})();
</script>
@endif
@endpush
