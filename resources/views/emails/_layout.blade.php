<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body { margin:0; padding:0; background:#F1F5F9; font-family: Arial, sans-serif; color:#1E293B; }
    .wrap { max-width:560px; margin:32px auto; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:#1E3A5F; padding:24px 32px; }
    .header-title { color:#fff; font-size:13px; font-weight:600; letter-spacing:.04em; text-transform:uppercase; margin:0; opacity:.8; }
    .body { padding:28px 32px; }
    h2 { margin:0 0 6px; font-size:20px; font-weight:700; color:#1E293B; }
    .sub { color:#64748B; font-size:14px; margin:0 0 24px; }
    .badge { display:inline-block; padding:3px 10px; border-radius:4px; font-size:12px; font-weight:700; }
    .badge-pending         { background:#EFF6FF; color:#1D4ED8; }
    .badge-inProgress      { background:#FEFCE8; color:#A16207; }
    .badge-completed       { background:#F0FDF4; color:#166534; }
    .badge-cancelled       { background:#FEF2F2; color:#7F1D1D; }
    .badge-cancelRequested { background:#FFF1F2; color:#9F1239; }
    .badge-urg-high   { background:#FEF2F2; color:#9F1239; }
    .badge-urg-medium { background:#FFFBEB; color:#92400E; }
    .badge-urg-low    { background:#EFF6FF; color:#1E40AF; }
    .btn { display:inline-block; background:#1E3A5F; color:#fff !important; text-decoration:none; padding:12px 24px; border-radius:7px; font-weight:600; font-size:14px; margin:4px 0 20px; }
    .note { font-size:12px; color:#94A3B8; margin-top:8px; }
    .footer { background:#F8FAFC; padding:16px 32px; text-align:center; font-size:11px; color:#94A3B8; border-top:1px solid #E2E8F0; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">CelestaSupply &mdash; Notificação</p>
    </div>
    <div class="body">
        @yield('content')
        <a class="btn" href="{{ route('requests.show', $supplyRequest) }}">Ver Solicitação</a>
        <p class="note">Este é um email automático. Não responda a esta mensagem.</p>
    </div>
    <div class="footer">
        Celesta Mineração S.A. &mdash; CNPJ: 17.755.975/0001-22 &mdash; Curionópolis/PA
    </div>
</div>
</body>
</html>
