<?php

namespace App\Http\Controllers;

use App\Enums\RequestStatus;
use App\Enums\Urgency;
use App\Models\SupplyRequest;
use Carbon\Carbon;
use Illuminate\Support\Collection;

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
        $byStatus = SupplyRequest::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $pendingCount         = (int) $byStatus->get(RequestStatus::Pending->value, 0);
        $cancelRequestedCount = (int) $byStatus->get(RequestStatus::CancelRequested->value, 0);
        $urgentOpenCount      = SupplyRequest::where('urgency', Urgency::High->value)
            ->whereNotIn('status', [RequestStatus::Completed->value, RequestStatus::Cancelled->value])
            ->count();

        [$chartLabels, $chartData] = $this->requestsByMonth();

        return view('dashboard.index', compact(
            'pendingCount', 'cancelRequestedCount', 'urgentOpenCount',
            'byStatus', 'chartLabels', 'chartData'
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

    private function requestsByMonth(): array
    {
        $months = collect(range(0, 11))->map(fn($i) => now()->subMonths(11 - $i)->format('Y-m'));

        $rows = SupplyRequest::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $data = $months->map(fn($m) => (int) $rows->get($m, 0))->toArray();

        return [$this->monthLabels($months), $data];
    }
}
