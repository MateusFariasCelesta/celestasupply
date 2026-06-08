@extends('emails._layout')
@section('content')
@if($isBuyer)
<h2>Nova solicitação para atendimento</h2>
<p class="sub">{{ $supplyRequest->user->name }} enviou uma nova solicitação.</p>
@else
<h2>Solicitação enviada!</h2>
<p class="sub">Sua solicitação foi registrada e está aguardando atendimento.</p>
@endif

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:8px;margin-bottom:20px;font-size:13px">
    <tr>
        <td style="padding:12px 20px 0;border-bottom:1px solid #E2E8F0">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#64748B;font-weight:500;padding-bottom:12px">Código</td>
                    <td align="right" style="color:#1E293B;font-weight:600;padding-bottom:12px">{{ $supplyRequest->code }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:12px 20px 0;border-bottom:1px solid #E2E8F0">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#64748B;font-weight:500;padding-bottom:12px">Título</td>
                    <td align="right" style="color:#1E293B;font-weight:600;padding-bottom:12px">{{ $supplyRequest->title }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:12px 20px 0;border-bottom:1px solid #E2E8F0">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#64748B;font-weight:500;padding-bottom:12px">Centro de Custo</td>
                    <td align="right" style="color:#1E293B;font-weight:600;padding-bottom:12px">{{ $supplyRequest->costCenter->name }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:12px 20px 0;border-bottom:1px solid #E2E8F0">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#64748B;font-weight:500;padding-bottom:12px">Urgência</td>
                    <td align="right" style="padding-bottom:12px">
                        <span class="badge badge-urg-{{ $supplyRequest->urgency->value }}">{{ $supplyRequest->urgency->label() }}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:12px 20px">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#64748B;font-weight:500">Itens</td>
                    <td align="right" style="color:#1E293B;font-weight:600">{{ $supplyRequest->items->count() }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
