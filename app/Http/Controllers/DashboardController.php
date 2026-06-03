<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\Urgency;
use App\Models\SupplyRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return $user->isBuyerOrAdmin()
            ? $this->buyerAdminView($user)
            : $this->requesterView($user);
    }

    private function requesterView($user)
    {
        $counts = SupplyRequest::where('user_id', $user->id)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recent = SupplyRequest::where('user_id', $user->id)
            ->with('costCenter')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('counts', 'recent'));
    }

    private function buyerAdminView($user)
    {
        $pendingCount         = SupplyRequest::where('status', RequestStatus::Pending->value)->count();
        $cancelRequestedCount = SupplyRequest::where('status', RequestStatus::CancelRequested->value)->count();
        $urgentOpenCount      = SupplyRequest::where('urgency', Urgency::High->value)
            ->whereNotIn('status', [RequestStatus::Completed->value, RequestStatus::Cancelled->value])
            ->count();

        $byStatus = SupplyRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        [$chartLabels, $chartData] = $this->requestsByMonth();

        $viewData = compact(
            'pendingCount', 'cancelRequestedCount', 'urgentOpenCount',
            'byStatus', 'chartLabels', 'chartData'
        );

        if ($user->isAdmin() && Schema::hasColumn('supply_request_items', 'total_price')) {
            [$valueLabels, $valueData] = $this->valueByMonth();
            $valueByCostCenter         = $this->valueByCostCenter();
            $viewData = array_merge($viewData, compact('valueLabels', 'valueData', 'valueByCostCenter'));
        }

        return view('dashboard.index', $viewData);
    }

    private function requestsByMonth(): array
    {
        $ptMonths = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        $months = collect(range(0, 11))->map(fn($i) => now()->subMonths(11 - $i)->format('Y-m'));

        $rows = SupplyRequest::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = $months->map(function ($m) use ($ptMonths) {
            $d = Carbon::createFromFormat('Y-m', $m);
            return $ptMonths[$d->month - 1] . '/' . $d->format('y');
        })->toArray();

        $data = $months->map(fn($m) => (int) $rows->get($m, 0))->toArray();

        return [$labels, $data];
    }

    private function valueByMonth(): array
    {
        $ptMonths = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        $months = collect(range(0, 11))->map(fn($i) => now()->subMonths(11 - $i)->format('Y-m'));

        $rows = DB::table('supply_request_items')
            ->join('supply_requests', 'supply_requests.id', '=', 'supply_request_items.supply_request_id')
            ->selectRaw("DATE_FORMAT(supply_requests.created_at, '%Y-%m') as month, SUM(supply_request_items.total_price) as total")
            ->where('supply_requests.created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->whereNotNull('supply_request_items.total_price')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = $months->map(function ($m) use ($ptMonths) {
            $d = Carbon::createFromFormat('Y-m', $m);
            return $ptMonths[$d->month - 1] . '/' . $d->format('y');
        })->toArray();

        $data = $months->map(fn($m) => (float) ($rows->get($m, 0) ?? 0))->toArray();

        return [$labels, $data];
    }

    private function valueByCostCenter()
    {
        return DB::table('supply_request_items')
            ->join('supply_requests', 'supply_requests.id', '=', 'supply_request_items.supply_request_id')
            ->join('cost_centers', 'cost_centers.id', '=', 'supply_requests.cost_center_id')
            ->selectRaw('cost_centers.name, SUM(supply_request_items.total_price) as total')
            ->whereNotNull('supply_request_items.total_price')
            ->groupBy('cost_centers.id', 'cost_centers.name')
            ->orderByDesc('total')
            ->get();
    }
}
