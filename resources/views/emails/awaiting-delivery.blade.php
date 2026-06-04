@extends('emails._layout')
@section('content')
<h2>Itens aguardando entrega</h2>
<p class="sub">Os itens abaixo da solicitação <strong>{{ $supplyRequest->code }}</strong> estão prontos e aguardando entrega.</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F8FAFC;border-radius:8px;margin-bottom:20px;font-size:13px">
    @foreach($awaitingItems as $item)
    <tr>
        <td style="padding:12px 20px {{ !$loop->last ? '0' : '' }};{{ !$loop->last ? 'border-bottom:1px solid #E2E8F0' : '' }}">
            <table width="100%" cellpadding="0" cellspacing="0"><tr>
                <td style="color:#1E293B;font-weight:500;padding-bottom:{{ !$loop->last ? '12' : '0' }}px">{{ $item->item->name }}</td>
                <td align="right" style="color:#64748B;padding-bottom:{{ !$loop->last ? '12' : '0' }}px">{{ $item->formattedQuantity() }}{{ $item->unit ? ' '.$item->unit : '' }}</td>
            </tr></table>
        </td>
    </tr>
    @endforeach
</table>
@endsection
