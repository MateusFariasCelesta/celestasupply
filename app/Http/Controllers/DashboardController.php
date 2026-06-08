<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\Urgency;
use App\Models\CostCenter;
use App\Models\SupplyRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return $user->isBuyerOrAdmin()
            ? $this->buyerAdminView()
            : $this->requesterView($user);
    }

    private function requesterView(\App\Models\User $user)
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

    private function buyerAdminView()
    {
        $byStatus = SupplyRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingCount         = (int) $byStatus->get(RequestStatus::Pending->value, 0);
        $cancelRequestedCount = (int) $byStatus->get(RequestStatus::CancelRequested->value, 0);
        $urgentOpenCount      = SupplyRequest::where('urgency', Urgency::High->value)
            ->whereNotIn('status', [RequestStatus::Completed->value, RequestStatus::Cancelled->value])
            ->count();
        $completedThisMonth   = SupplyRequest::where('status', RequestStatus::Completed->value)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        $costCenters = CostCenter::where('isActive', true)->orderBy('name')->get();

        // Build 12-month labels once; one dataset per cost center + "all"
        $months      = collect(range(0, 11))->map(fn($i) => now()->subMonths(11 - $i)->format('Y-m'));
        $chartLabels = $this->monthLabels($months);

        $chartDatasets = ['' => $this->monthlyData($months)];
        foreach ($costCenters as $cc) {
            $chartDatasets[$cc->id] = $this->monthlyData($months, $cc->id);
        }

        return view('dashboard.index', compact(
            'pendingCount', 'cancelRequestedCount', 'urgentOpenCount',
            'completedThisMonth', 'byStatus', 'chartLabels', 'chartDatasets', 'costCenters'
        ));
    }

    private function monthLabels(Collection $months): array
    {
        $ptMonths = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];

        return $months->map(function ($m) use ($ptMonths) {
            $d = Carbon::createFromFormat('Y-m', $m);
            return $ptMonths[$d->month - 1] . '/' . $d->format('y');
        })->toArray();
    }

    private function monthlyData(Collection $months, ?string $costCenterId = null): array
    {
        $query = SupplyRequest::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->where('status', '!=', RequestStatus::Draft->value)
            ->groupBy('month')
            ->orderBy('month');

        if ($costCenterId) {
            $query->where('cost_center_id', $costCenterId);
        }

        $rows = $query->pluck('total', 'month');

        return $months->map(fn($m) => (int) $rows->get($m, 0))->toArray();
    }
}
