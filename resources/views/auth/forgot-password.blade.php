<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha — CelestaSupply</title>

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
        .brand p { font-size: 13px; color: rgba(255,255,255,.45); margin: 0; }
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
        .alert-success {
            background: #DCFCE7;
            border: none;
            border-left: 4px solid #22C55E;
            color: #166534;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 14px;
        }
        .alert-danger {
            background: #FEE2E2;
            border: none;
            border-left: 4px solid #EF4444;
            color: #991B1B;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 14px;
        }
        .link-back { font-size: 13px; color: #6B7280; text-decoration: none; }
        .link-back:hover { color: #3B82F6; }
    </style>
</head>
<body>

<div class="wrap">
    <div class="brand">
        <i class="bi bi-box-seam-fill"></i>
        <h1>CelestaSupply</h1>
    </div>

    <div class="card">
        <div class="card-title">Recuperar senha</div>
        <div class="card-subtitle">Informe seu e-mail e enviaremos um link de redefinição.</div>

        @if(session('status'))
            <div class="alert-success mb-4">
                <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert-danger mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input id="email" type="email" name="email"
                           class="form-control"
                           value="{{ old('email') }}"
                           placeholder="seu@email.com"
                           required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mb-3">
                <i class="bi bi-send me-2"></i>Enviar link de redefinição
            </button>

            <div class="text-center">
                <a href="{{ route('login') }}" class="link-back">
                    <i class="bi bi-arrow-left me-1"></i>Voltar ao login
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>