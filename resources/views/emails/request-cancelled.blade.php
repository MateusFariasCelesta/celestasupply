@extends('emails._layout')
@section('content')
<h2>Solicitação cancelada</h2>
<p class="sub">A solicitação <strong>{{ $supplyRequest->code }}</strong> foi cancelada.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:8px;margin-bottom:20px;font-size:13px">
    <tr>
        <td style="padding:12px 20px 0;border-bottom:1px solid #E2E8F0">
            <table width="100%" cellpadding="0" cellspacing="0"><tr>
                <td style="color:#64748B;font-weight:500;padding-bottom:12px">Código</td>
                <td align="right" style="color:#1E293B;font-weight:600;padding-bottom:12px">{{ $supplyRequest->code }}</td>
            </tr></table>
        </td>
    </tr>
    <tr>
        <td style="padding:12px 20px {{ $supplyRequest->cancellation_reason ? '0' : '' }};{{ $supplyRequest->cancellation_reason ? 'border-bottom:1px solid #E2E8F0' : '' }}">
            <table width="100%" cellpadding="0" cellspacing="0"><tr>
                <td style="color:#64748B;font-weight:500;padding-bottom:12px">Título</td>
                <td align="right" style="color:#1E293B;font-weight:600;padding-bottom:12px">{{ $supplyRequest->title }}</td>
            </tr></table>
        </td>
    </tr>
    @if($supplyRequest->cancellation_reason)
    <tr>
        <td style="padding:12px 20px">
            <table width="100%" cellpadding="0" cellspacing="0"><tr>
                <td style="color:#64748B;font-weight:500">Motivo</td>
                <td align="right" style="color:#1E293B;font-weight:600;max-width:300px">{{ $supplyRequest->cancellation_reason }}</td>
            </tr></table>
        </td>
    </tr>
    @endif
</table>
@endsection
