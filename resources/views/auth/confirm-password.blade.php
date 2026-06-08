<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Senha — CelestaSupply</title>

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
        .card-subtitle { font-size: 13px; color: #6B7280; margin-bottom: 24px; }
        .form-label { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-control {
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
        }
        .form-control:focus { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
        .input-group-text {
            background: #F9FAFB;
            border: 1.5px solid #E5E7EB;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #9CA3AF;
        }
        .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; }
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
        .alert-danger {
            background: #FEE2E2;
            border: none;
            border-left: 4px solid #EF4444;
            color: #991B1B;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 14px;
        }
    </style>
</head>
<body>

<div class="wrap">
    <div class="brand">
        <i class="bi bi-box-seam-fill"></i>
        <h1>CelestaSupply</h1>
    </div>

    <div class="card">
        <div class="card-title">Confirmar senha</div>
        <div class="card-subtitle">Por segurança, confirme sua senha antes de continuar.</div>

        @if($errors->any())
            <div class="alert-danger mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-4">
                <label for="password" class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" name="password"
                           class="form-control"
                           placeholder="••••••••"
                           required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-shield-check me-2"></i>Confirmar
            </button>
        </form>
    </div>
</div>

</body>
</html>