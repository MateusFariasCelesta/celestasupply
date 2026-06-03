@props(['supplyRequest'])

@php
use App\Enums\RequestStatus;

$mainFlow = [
    RequestStatus::Draft,
    RequestStatus::Pending,
    RequestStatus::InProgress,
    RequestStatus::Completed,
];

$n        = count($mainFlow);
$current  = $supplyRequest->status;
$history  = $supplyRequest->statusHistory;

// Map status value → last history entry where it was reached (always overwrite)
$lastReached = [];
foreach ($history as $entry) {
    $lastReached[$entry->to_status->value] = $entry;
}

// Index of each main-flow step
$mainFlowMap = [];
foreach ($mainFlow as $idx => $s) {
    $mainFlowMap[$s->value] = $idx;
}

$isCancelled     = in_array($current, [RequestStatus::Cancelled, RequestStatus::CancelRequested]);
$currentMainIdx  = $mainFlowMap[$current->value] ?? null;

if ($currentMainIdx === null) {
    // Cancelled/CancelRequested: find highest main-flow step reached
    $currentMainIdx = 0;
    foreach ($mainFlow as $idx => $s) {
        if (isset($lastReached[$s->value]) || $mainFlowMap[$s->value] === 0) {
            $currentMainIdx = $idx;
        }
    }
}

// Progress line width: percentage from left edge to active circle
// Each step occupies 1/(n-1) of the total line width
$progressPct = $n > 1 ? round($currentMainIdx / ($n - 1) * 100) : 0;

// Step config: visual properties per status
$stepConfig = [
    RequestStatus::Draft->value      => ['icon' => 'bi-pencil',        'color' => '#64748B', 'bg' => '#F1F5F9', 'ring' => '#E2E9F4'],
    RequestStatus::Pending->value    => ['icon' => 'bi-clock',         'color' => '#1D4ED8', 'bg' => '#EFF6FF', 'ring' => '#BFDBFE'],
    RequestStatus::InProgress->value => ['icon' => 'bi-gear-fill',     'color' => '#92400E', 'bg' => '#FFFBEB', 'ring' => '#FDE68A'],
    RequestStatus::Completed->value  => ['icon' => 'bi-check-circle',  'color' => '#166534', 'bg' => '#F0FDF4', 'ring' => '#BBF7D0'],
];
@endphp

<div class="cs-card mb-4">
    <h6 class="fw-semibold mb-4" style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">
        Linha do Tempo
    </h6>

    {{-- Step bar --}}
    <div class="position-relative" style="padding:0 0 4px">

        {{-- Base line (gray) --}}
        <div class="position-absolute"
             style="top:16px;left:calc(100% / {{ $n * 2 }});right:calc(100% / {{ $n * 2 }});height:2px;background:#E2E9F4;z-index:0;border-radius:2px"></div>

        {{-- Progress line (colored) --}}
        @if($progressPct > 0)
        <div class="position-absolute"
             style="top:16px;left:calc(100% / {{ $n * 2 }});width:calc({{ $progressPct }}% * (1 - 1/{{ $n }}));height:2px;background:{{ $isCancelled ? '#FCA5A5' : '#10B981' }};z-index:1;border-radius:2px;transition:width .4s ease"></div>
        @endif

        {{-- Steps --}}
        <div class="d-flex" style="position:relative;z-index:2">
            @foreach($mainFlow as $idx => $status)
            @php
                $cfg = $stepConfig[$status->value];

                if ($isCancelled) {
                    $state = $idx <= $currentMainIdx ? 'done' : 'pending';
                } else {
                    $state = match(true) {
                        $idx < $currentMainIdx  => 'done',
                        $idx === $currentMainIdx => 'active',
                        default                  => 'pending',
                    };
                }

                $entry    = $lastReached[$status->value] ?? null;
                $hasEntry = $entry !== null;
            @endphp
            <div class="d-flex flex-column align-items-center text-center flex-fill" style="min-width:0">

                {{-- Circle --}}
                @if($state === 'done')
                <div style="width:32px;height:32px;border-radius:50%;background:#10B981;color:#fff;display:flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 0 0 4px #D1FAE5">
                    <i class="bi bi-check-lg"></i>
                </div>
                @elseif($state === 'active')
                <div style="width:32px;height:32px;border-radius:50%;background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};border:2px solid {{ $cfg['color'] }};display:flex;align-items:center;justify-content:center;font-size:13px;box-shadow:0 0 0 4px {{ $cfg['ring'] }}">
                    <i class="bi {{ $cfg['icon'] }}"></i>
                </div>
                @else
                <div style="width:32px;height:32px;border-radius:50%;background:#F8FAFC;color:#CBD5E1;border:2px solid #E2E9F4;display:flex;align-items:center;justify-content:center;font-size:13px">
                    <i class="bi {{ $cfg['icon'] }}"></i>
                </div>
                @endif

                {{-- Label --}}
                <div style="margin-top:7px;font-size:11px;font-weight:{{ $state === 'active' ? '700' : '500' }};color:{{ $state === 'pending' ? '#CBD5E1' : ($state === 'active' ? $cfg['color'] : '#374151') }};white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:80px">
                    {{ $status->label() }}
                </div>

                {{-- Date from history --}}
                @if($hasEntry)
                <div style="font-size:10px;color:#94A3B8;margin-top:2px;white-space:nowrap">
                    {{ $entry->created_at->format('d/m H:i') }}
                </div>
                @else
                <div style="font-size:10px;color:transparent;margin-top:2px">—</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Cancelled / CancelRequested indicator --}}
    @if($isCancelled)
    @php $cancelEntry = $lastReached[$current->value] ?? null; @endphp
    <div class="d-flex align-items-center gap-2 mt-4 px-3 py-2 rounded-2"
         style="background:#FEF2F2;border:1px solid #FECACA">
        <i class="bi {{ $current === RequestStatus::CancelRequested ? 'bi-clock-history' : 'bi-x-circle-fill' }}"
           style="color:#DC2626;font-size:15px"></i>
        <span style="font-size:13px;font-weight:600;color:#7F1D1D">{{ $current->label() }}</span>
        @if($cancelEntry)
        <span style="font-size:12px;color:#94A3B8;margin-left:4px">
            · {{ $cancelEntry->changedBy->name }}, {{ $cancelEntry->created_at->format('d/m/Y \à\s H:i') }}
        </span>
        @endif
    </div>
    @endif

    {{-- History log --}}
    @if($history->count() > 0)
    <div style="border-top:1px solid #F1F5F9;margin-top:16px;padding-top:12px">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:.05em;color:#94A3B8;font-weight:600;margin-bottom:8px">
            Histórico
        </div>
        <div class="d-flex flex-column gap-2">
            @foreach($history as $entry)
            <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size:12px">
                <span style="color:#94A3B8;min-width:72px;font-variant-numeric:tabular-nums">
                    {{ $entry->created_at->format('d/m H:i') }}
                </span>
                <span style="color:#475569;font-weight:500">{{ $entry->changedBy->name }}</span>
                @if($entry->from_status)
                <span class="cs-badge {{ $entry->from_status->badgeClass() }}">{{ $entry->from_status->label() }}</span>
                <i class="bi bi-arrow-right" style="color:#CBD5E1"></i>
                @endif
                <span class="cs-badge {{ $entry->to_status->badgeClass() }}">{{ $entry->to_status->label() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
