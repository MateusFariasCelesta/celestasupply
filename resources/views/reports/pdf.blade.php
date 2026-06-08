<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>{{ $reportTitle }}</title>
<style>
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1F2937; }

    /* ── Wrapper com margens reais ── */
    .page-wrap { padding: 20mm 18mm 26mm 18mm; }

    /* ── Rodapé fixo (relativo à página, respeitando margem lateral) ── */
    .footer {
        position: fixed;
        bottom: 6mm;
        left: 18mm; right: 18mm;
        font-size: 8px;
        color: #9CA3AF;
        text-align: center;
        border-top: 1px solid #E5E7EB;
        padding-top: 4px;
    }

    /* ── Cabeçalho ── */
    .header-top { width: 100%; margin-bottom: 8px; }
    .header-top td { vertical-align: middle; }
    .logo { height: 46px; }
    .header-date { font-size: 9px; color: #9CA3AF; text-align: right; }
    .header-divider { border-bottom: 3px solid #1E3A5F; margin-bottom: 10px; }
    .report-title { font-size: 20px; font-weight: 700; color: #1E3A5F; margin-bottom: 14px; }

    /* ── Linha de filtros ── */
    .filter-bar {
        font-size: 9px; color: #374151;
        margin-bottom: 14px; padding: 7px 12px;
        background: #F0F4FA;
        border-left: 4px solid #F5A623;
        border-radius: 0 4px 4px 0;
    }

    /* ── Tabela ── */
    table { width: 100%; border-collapse: collapse; }
    thead tr th {
        background: #1E3A5F; color: #fff;
        font-size: 9px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em;
        padding: 8px 9px; text-align: left;
    }
    tbody tr td {
        padding: 6px 9px;
        border-bottom: 1px solid #F3F4F6;
        font-size: 9.5px; vertical-align: middle;
    }
    tbody tr:nth-child(even) td { background: #F8FAFD; }

    /* ── Badges de status ── */
    .badge {
        display: inline-block; padding: 2px 7px;
        border-radius: 3px; font-size: 8px; font-weight: 700;
    }
    .badge-draft           { background: #F1F5F9; color: #475569; }
    .badge-pending         { background: #EFF6FF; color: #1D4ED8; }
    .badge-inProgress      { background: #FEFCE8; color: #A16207; }
    .badge-completed       { background: #F0FDF4; color: #166534; }
    .badge-cancelRequested { background: #FFF1F2; color: #9F1239; }
    .badge-cancelled       { background: #FEF2F2; color: #7F1D1D; }

    /* ── Urgência ── */
    .urg-high   { color: #9F1239; font-weight: 700; }
    .urg-medium { color: #92400E; }
    .urg-low    { color: #1E40AF; }

    .text-center { text-align: center; }

    /* ── Resumo ── */
    .summary-wrap { margin-top: 16px; width: 100%; border-collapse: collapse; }
    .summary-stripe { width: 5px; background: #1E3A5F; }
    .summary-body { background: #F0F4FA; padding: 8px 16px; }
    .summary-cols { width: 100%; border-collapse: collapse; }
    .summary-cols td { text-align: center; vertical-align: middle; padding: 2px 6px; border-left: 1px solid #CBD5E1; }
    .summary-cols td:first-child { border-left: none; }
    .summary-label { font-size: 8px; color: #6B7280; display: block; margin-bottom: 2px; }
    .summary-value { font-size: 15px; font-weight: 700; color: #1E3A5F; display: block; }
    .summary-total .summary-value { font-size: 18px; }
</style>
</head>
<body>

<div class="footer">
    Relatório gerado em {{ now()->format('d/m/Y \à\s H:i') }} &mdash; CelestaSupply
</div>

<div class="page-wrap">
{{-- Cabeçalho --}}
<table class="header-top">
    <tr>
        <td><img src="{{ $logoSrc }}" class="logo" alt="Celesta Mineração"></td>
        <td class="header-date">Gerado em {{ now()->format('d/m/Y H:i') }}</td>
    </tr>
</table>
<div class="header-divider"></div>
<div class="report-title">{{ $reportTitle }}</div>

{{-- Resumo dos filtros --}}
@php
    $filterParts = [];
    if (!empty($filters['from']) || !empty($filters['to'])) {
        $from = !empty($filters['from']) ? \Carbon\Carbon::parse($filters['from'])->format('d/m/Y') : null;
        $to   = !empty($filters['to'])   ? \Carbon\Carbon::parse($filters['to'])->format('d/m/Y')   : null;
        if ($from && $to) $filterParts[] = "{$from} a {$to}";
        elseif ($from) $filterParts[] = "a partir de {$from}";
        else $filterParts[] = "até {$to}";
    }
    if (!empty($filters['status']))
        $filterParts[] = 'Status: ' . collect($filters['status'])
            ->map(fn($s) => \App\Enums\RequestStatus::from($s)->label())->join(', ');
@endphp
<div class="filter-bar">
    {{ $filterParts ? implode('   ·   ', $filterParts) : 'Todos os registros (sem filtros de período ou status)' }}
</div>

{{-- Resumo --}}
@if($supplyRequests->isNotEmpty())
@php
    $colCount = 1 + ($showBreakdown ? $summary->count() : 0);
    $colPct   = round(100 / $colCount);
@endphp
<table class="summary-wrap">
    <tr>
        <td class="summary-stripe">&nbsp;</td>
        <td class="summary-body">
            <table class="summary-cols">
                <tr>
                    <td class="summary-total" style="width:{{ $colPct }}%">
                        <span class="summary-label">Total</span>
                        <span class="summary-value">{{ $supplyRequests->count() }}</span>
                    </td>
                    @if($showBreakdown)
                        @foreach($summary as $statusValue => $count)
                        <td style="width:{{ $colPct }}%">
                            <span class="summary-label">{{ \App\Enums\RequestStatus::from($statusValue)->label() }}</span>
                            <span class="summary-value">{{ $count }}</span>
                        </td>
                        @endforeach
                    @endif
                </tr>
            </table>
        </td>
    </tr>
</table>
@endif

{{-- Tabela --}}
<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Título</th>
            <th>Centro de Custo</th>
            <th>Solicitante</th>
            <th>Urgência</th>
            <th>Status</th>
            <th>Data</th>
            <th class="text-center">Itens</th>
        </tr>
    </thead>
    <tbody>
        @forelse($supplyRequests as $sr)
        <tr>
            <td><strong>{{ $sr->code }}</strong></td>
            <td>{{ $sr->title }}</td>
            <td>{{ $sr->costCenter->name }}</td>
            <td>{{ $sr->user->name }}</td>
            <td class="urg-{{ $sr->urgency->value }}">{{ $sr->urgency->label() }}</td>
            <td><span class="badge badge-{{ $sr->status->value }}">{{ $sr->status->label() }}</span></td>
            <td>{{ $sr->created_at->format('d/m/Y') }}</td>
            <td class="text-center">{{ $sr->items->count() }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="padding:20px;color:#9CA3AF">
                Nenhuma solicitação encontrada.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>


</div>{{-- .page-wrap --}}
</body>
</html>
