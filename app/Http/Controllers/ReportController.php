<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Exports\SupplyRequestsExport;
use App\Models\SupplyRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function authorizeAccess(): void
    {
        abort_if(!auth()->user()->isBuyerOrAdmin(), 403);
    }

    public function exportExcel(Request $request)
    {
        $this->authorizeAccess();
        $supplyRequests   = $this->buildQuery($request)->get();
        $selectedStatuses = array_values(array_filter((array) $request->status));
        $showBreakdown    = count($selectedStatuses) !== 1;
        $summary          = $showBreakdown
            ? $supplyRequests->groupBy(fn($sr) => $sr->status->value)->map->count()
            : collect();

        return Excel::download(
            new SupplyRequestsExport(
                $supplyRequests,
                $this->buildTitle($request),
                $this->buildFilterDesc($request),
                $summary,
                $showBreakdown,
            ),
            'relatorio-celesta-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $this->authorizeAccess();
        $supplyRequests   = $this->buildQuery($request)->get();
        $filters          = $request->only(['from', 'to', 'status', 'cost_center_id', 'user_id']);
        $reportTitle      = $this->buildTitle($request);
        $selectedStatuses = array_values(array_filter((array) $request->status));
        $showBreakdown    = count($selectedStatuses) !== 1;
        $summary          = $showBreakdown
            ? $supplyRequests->groupBy(fn($sr) => $sr->status->value)->map->count()
            : collect();

        $logoBase64 = base64_encode(file_get_contents(public_path('images/celesta-mineracao-logo.png')));
        $logoSrc    = 'data:image/png;base64,' . $logoBase64;

        $pdf = Pdf::loadView('reports.pdf', compact(
            'supplyRequests', 'filters', 'logoSrc',
            'reportTitle', 'showBreakdown', 'summary'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-celesta-' . now()->format('Y-m-d') . '.pdf');
    }

    private function buildQuery(Request $request)
    {
        return SupplyRequest::with(['costCenter', 'user', 'items'])
            ->where('status', '!=', RequestStatus::Draft->value)
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->q;
                $q->where(function ($sub) use ($term) {
                    $sub->where('title', 'like', "%{$term}%")
                        ->orWhereHas('costCenter', fn($q) => $q->where('name', 'like', "%{$term}%"))
                        ->orWhereHas('user',       fn($q) => $q->where('name', 'like', "%{$term}%"));
                });
            })
            ->when($request->filled('from'),           fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->filled('to'),             fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->filled('status'),         fn($q) => $q->whereIn('status', (array) $request->status))
            ->when($request->filled('urgency'),        fn($q) => $q->where('urgency', $request->urgency))
            ->when($request->filled('cost_center_id'), fn($q) => $q->where('cost_center_id', $request->cost_center_id))
            ->when($request->filled('user_id'),        fn($q) => $q->where('user_id', $request->user_id))
            ->when(!auth()->user()->isBuyerOrAdmin(),  fn($q) => $q->where('user_id', auth()->id()))
            ->orderByDesc('created_at');
    }

    private function buildFilterDesc(Request $request): string
    {
        $parts = [];

        if ($request->filled('from') || $request->filled('to')) {
            $from   = $request->filled('from') ? Carbon::parse($request->from)->format('d/m/Y') : null;
            $to     = $request->filled('to')   ? Carbon::parse($request->to)->format('d/m/Y')   : null;
            $parts[] = match(true) {
                $from && $to => "{$from} a {$to}",
                (bool) $from => "a partir de {$from}",
                default      => "até {$to}",
            };
        }

        if ($request->filled('status'))
            $parts[] = 'Status: ' . collect((array) $request->status)
                ->map(fn($s) => RequestStatus::from($s)->label())->join(', ');

        if ($request->filled('urgency'))
            $parts[] = 'Urgência: ' . \App\Enums\Urgency::from($request->urgency)->label();

        if ($request->filled('q'))
            $parts[] = 'Busca: "' . $request->q . '"';

        return $parts ? implode('   ·   ', $parts) : 'Todos os registros (sem filtros)';
    }

    private function buildTitle(Request $request): string
    {
        $sel = array_values(array_filter((array) $request->status));

        $statusDesc = count($sel) === 1 ? match($sel[0]) {
            'completed'       => 'Solicitações Concluídas',
            'cancelled'       => 'Solicitações Canceladas',
            'pending'         => 'Solicitações Pendentes',
            'inProgress'      => 'Solicitações em Andamento',
            'cancelRequested' => 'Cancelamentos Solicitados',
            'draft'           => 'Rascunhos',
            default           => 'Solicitações',
        } : 'Solicitações';

        $from   = $request->filled('from') ? Carbon::parse($request->from)->format('d/m/Y') : null;
        $to     = $request->filled('to')   ? Carbon::parse($request->to)->format('d/m/Y')   : null;
        $period = match(true) {
            $from && $to  => "{$from} a {$to}",
            (bool) $from  => "a partir de {$from}",
            (bool) $to    => "até {$to}",
            default       => '',
        };

        return 'Relatório de ' . $statusDesc . ($period ? ' — ' . $period : '');
    }
}
