<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>{{ $supplyRequest->code }} — Itens</title>
<style>
    @page { margin: 0; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1F2937; }

    .page-wrap { padding: 20mm 18mm 26mm 18mm; }

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

    .header-top { width: 100%; margin-bottom: 8px; }
    .header-top td { vertical-align: middle; }
    .logo { height: 46px; }
    .header-date { font-size: 9px; color: #9CA3AF; text-align: right; }
    .header-divider { border-bottom: 3px solid #1E3A5F; margin-bottom: 10px; }
    .report-title { font-size: 18px; font-weight: 700; color: #1E3A5F; margin-bottom: 12px; }

    .filter-bar {
        font-size: 9px; color: #374151;
        margin-bottom: 14px; padding: 7px 12px;
        background: #F0F4FA;
        border-left: 4px solid #F5A623;
        border-radius: 0 4px 4px 0;
    }

    /* Info block */
    .info-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    .info-table td { padding: 5px 8px; vertical-align: top; font-size: 9.5px; }
    .info-label { font-size: 8px; color: #6B7280; text-transform: uppercase; letter-spacing: .04em; font-weight: 700; display: block; margin-bottom: 2px; }
    .info-value { color: #111827; font-weight: 500; }
    .info-block { background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 4px; padding: 10px 14px; margin-bottom: 16px; }

    /* Badges */
    .badge {
        display: inline-block; padding: 2px 7px;
        border-radius: 3px; font-size: 8px; font-weight: 700;
    }
    /* Request status */
    .badge-draft           { background: #F1F5F9; color: #475569; }
    .badge-pending         { background: #EFF6FF; color: #1D4ED8; }
    .badge-inProgress      { background: #FEFCE8; color: #A16207; }
    .badge-completed       { background: #F0FDF4; color: #166534; }
    .badge-cancelRequested { background: #FFF1F2; color: #9F1239; }
    .badge-cancelled       { background: #FEF2F2; color: #7F1D1D; }
    /* Item status */
    .badge-item-pending          { background: #EFF6FF; color: #1D4ED8; }
    .badge-item-quoting          { background: #F0F9FF; color: #0369A1; }
    .badge-item-awaitingPayment  { background: #FEFCE8; color: #A16207; }
    .badge-item-awaitingDelivery { background: #FFFBEB; color: #92400E; }
    .badge-item-received         { background: #F0FDF4; color: #166534; }
    .badge-item-cancelled        { background: #FEF2F2; color: #7F1D1D; }
    .badge-item-cancelRequested  { background: #FFF1F2; color: #9F1239; }

    /* Urgência */
    .urg-high   { color: #9F1239; font-weight: 700; }
    .urg-medium { color: #92400E; }
    .urg-low    { color: #1E40AF; }

    /* Items table */
    table.items-table { width: 100%; border-collapse: collapse; }
    table.items-table thead tr th {
        background: #1E3A5F; color: #fff;
        font-size: 9px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .04em;
        padding: 8px 9px; text-align: left;
    }
    table.items-table tbody tr td {
        padding: 7px 9px;
        border-bottom: 1px solid #F3F4F6;
        font-size: 9.5px; vertical-align: middle;
    }
    table.items-table tbody tr:nth-child(even) td { background: #F8FAFD; }

    .text-center { text-align: center; }
    .text-right  { text-align: right; }
    .mono { font-family: DejaVu Sans Mono, monospace; font-weight: 700; color: #0369A1; }

    /* Summary */
    .summary-wrap { margin-bottom: 16px; width: 100%; border-collapse: collapse; }
    .summary-stripe { width: 5px; background: #1E3A5F; }
    .summary-body { background: #F0F4FA; padding: 8px 16px; }
    .summary-cols { width: 100%; border-collapse: collapse; }
    .summary-cols td { text-align: center; vertical-align: middle; padding: 2px 6px; border-left: 1px solid #CBD5E1; }
    .summary-cols td:first-child { border-left: none; }
    .summary-label { font-size: 8px; color: #6B7280; display: block; margin-bottom: 2px; }
    .summary-value { font-size: 15px; font-weight: 700; color: #1E3A5F; display: block; }
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
        <td style="width:1%;white-space:nowrap">
            <img src="{{ $logoSrc }}" class="logo" alt="Celesta Mineração">
        </td>
        <td style="padding-left:12px;vertical-align:middle">
            <div style="font-size:11px;font-weight:700;color:#1E3A5F;line-height:1.4">CELESTA MINERAÇÃO S.A.</div>
            <div style="font-size:8.5px;color:#374151;line-height:1.6">
                CNPJ: 17.755.975/0001-22 &nbsp;|&nbsp; IE: 155214101<br>
                PA 275 - S/N - ZONA RURAL - Curionópolis - PA - 68523-000<br>
                Telefone: (94) 3346-5857 &nbsp;|&nbsp; E-mail: controladoria@celestamineracao.com.br
            </div>
        </td>
        <td class="header-date" style="white-space:nowrap">Gerado em {{ now()->format('d/m/Y H:i') }}</td>
    </tr>
</table>
<div class="header-divider"></div>
<div class="report-title">Itens da Solicitação {{ $supplyRequest->code }}</div>

{{-- Filtro aplicado --}}
<div class="filter-bar">
    @if($filter === 'pending')
        Somente itens pendentes &mdash; status: Pendente, Em Cotação ou Cancelamento Solicitado
    @else
        Todos os itens da solicitação
    @endif
</div>

{{-- Info da solicitação --}}
@php
    $reqStatusMap = [
        'draft'           => 'badge-draft',
        'pending'         => 'badge-pending',
        'inProgress'      => 'badge-inProgress',
        'completed'       => 'badge-completed',
        'cancelRequested' => 'badge-cancelRequested',
        'cancelled'       => 'badge-cancelled',
    ];
    $reqBadge = $reqStatusMap[$supplyRequest->status->value] ?? 'badge-draft';
@endphp
<div class="info-block">
    <table class="info-table">
        <tr>
            <td style="width:25%">
                <span class="info-label">Código</span>
                <span class="info-value mono" style="font-size:10px">{{ $supplyRequest->code }}</span>
            </td>
            <td style="width:45%">
                <span class="info-label">Título</span>
                <span class="info-value">{{ $supplyRequest->title }}</span>
            </td>
            <td style="width:15%">
                <span class="info-label">Status</span>
                <span class="badge {{ $reqBadge }}">{{ $supplyRequest->status->label() }}</span>
            </td>
            <td style="width:15%">
                <span class="info-label">Urgência</span>
                <span class="urg-{{ $supplyRequest->urgency->value }}">{{ $supplyRequest->urgency->label() }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="info-label">Centro de Custo</span>
                <span class="info-value">{{ $supplyRequest->costCenter->name }}</span>
            </td>
            <td colspan="2">
                <span class="info-label">Solicitante</span>
                <span class="info-value">{{ $supplyRequest->user->name }}</span>
            </td>
        </tr>
        @php
            $pcs = $supplyRequest->items
                ->pluck('order_number')
                ->filter()
                ->unique()
                ->sort()
                ->map(fn($num) => 'PC-' . str_pad($num, 4, '0', STR_PAD_LEFT))
                ->values()
                ->all();
        @endphp
        @if(count($pcs) > 0)
        <tr>
            <td colspan="4">
                <span class="info-label">Pedidos (PC)</span>
                <span class="info-value" style="display:flex;gap:6px;flex-wrap:wrap">
                    @foreach($pcs as $pc)
                    <span class="badge badge-draft">{{ $pc }}</span>
                    @endforeach
                </span>
            </td>
        </tr>
        @endif
        @if($supplyRequest->notes)
        <tr>
            <td colspan="4">
                <span class="info-label">Observações</span>
                <span class="info-value">{{ $supplyRequest->notes }}</span>
            </td>
        </tr>
        @endif
    </table>
</div>

{{-- Resumo --}}
<table class="summary-wrap">
    <tr>
        <td class="summary-stripe">&nbsp;</td>
        <td class="summary-body">
            <table class="summary-cols">
                <tr>
                    <td>
                        <span class="summary-label">Total de itens</span>
                        <span class="summary-value">{{ $supplyRequest->items->count() }}</span>
                    </td>
                    @if($filter === 'pending')
                    <td>
                        <span class="summary-label">Itens exportados (pendentes)</span>
                        <span class="summary-value">{{ $items->count() }}</span>
                    </td>
                    @endif
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Tabela de itens --}}
@php
    $itemBadgeMap = [
        'pending'          => 'badge-item-pending',
        'quoting'          => 'badge-item-quoting',
        'awaitingPayment'  => 'badge-item-awaitingPayment',
        'awaitingDelivery' => 'badge-item-awaitingDelivery',
        'received'         => 'badge-item-received',
        'cancelled'        => 'badge-item-cancelled',
        'cancelRequested'  => 'badge-item-cancelRequested',
    ];
@endphp
<table class="items-table">
    <thead>
        <tr>
            <th style="width:46%">Item</th>
            <th style="width:14%;text-align:right">Quantidade</th>
            <th style="width:12%">Unidade</th>
            <th style="width:18%">Status</th>
            <th style="width:10%">Nº PC</th>
        </tr>
    </thead>
    <tbody>
        @forelse($items as $item)
        <tr>
            <td style="font-weight:500">{{ $item->item->name }}</td>
            <td class="text-right">{{ $item->formattedQuantity() }}</td>
            <td style="color:#6B7280">{{ $item->unit ?? '—' }}</td>
            <td>
                <span class="badge {{ $itemBadgeMap[$item->status->value] ?? '' }}">
                    {{ $item->status->label() }}
                </span>
            </td>
            <td class="mono" style="font-size:9px">
                {{ $item->order_number ? $item->formattedOrderNumber() : '—' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center" style="padding:20px;color:#9CA3AF">
                Nenhum item encontrado com o filtro selecionado.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>{{-- .page-wrap --}}
</body>
</html>
