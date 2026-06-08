<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar E-mail — CelestaSupply</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #0F2044;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .wrap { width: 100%; max-width: 400px; padding: 24px; }
        .brand { text-align: center; margin-bottom: 32px; }
        .brand i { font-size: 36px; color: #3B82F6; }
        .brand h1 { font-size: 22px; font-weight: 700; color: #fff; margin: 10px 0 4px; }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            border: none;
        }
        .card-title { font-size: 16px; font-weight: 700; color: #0F172A; margin-bottom: 6px; }
        .card-subtitle { font-size: 13px; color: #6B7280; margin-bottom: 24px; line-height: 1.6; }
        .btn-primary {
            background: #0F2044;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: background .15s;
        }
        .btn-primary:hover { background: #3B82F6; }
        .alert-success {
            background: #DCFCE7;
            border: none;
            border-left: 4px solid #22C55E;
            color: #166534;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 14px;
            margin-bottom: 20px;
        }
        .link-logout {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #6B7280;
            text-decoration: none;
        }
        .link-logout:hover { color: #EF4444; }
    </style>
</head>
<body>

<div class="wrap">
    <div class="brand">
        <i class="bi bi-box-seam-fill"></i>
        <h1>CelestaSupply</h1>
    </div>

    <div class="card">
        <div class="card-title">Verifique seu e-mail</div>
        <div class="card-subtitle">
            Enviamos um link de verificação para o seu e-mail. 
            Clique no link para ativar sua conta.
        </div>

        @if(session('status') == 'verification-link-sent')
            <div class="alert-success">
                <i class="bi bi-check-circle me-2"></i>
                Novo link enviado com sucesso!
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-envelope me-2"></i>Reenviar link de verificação
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link-logout">
                <i class="bi bi-box-arrow-right me-1"></i>Sair da conta
            </button>
        </form>
    </div>
</div>

</body>
</html>