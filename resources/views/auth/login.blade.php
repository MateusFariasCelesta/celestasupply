<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CelestaSupply</title>

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

        .login-wrap {
            width: 100%;
            max-width: 400px;
            padding: 24px;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-brand i {
            font-size: 36px;
            color: #3B82F6;
        }

        .login-brand h1 {
            font-size: 22px;
            font-weight: 700;
            color: #fff;
            margin: 10px 0 4px;
        }

        .login-brand p {
            font-size: 13px;
            color: rgba(255,255,255,.45);
            margin: 0;
        }

        .login-card {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid #E5E7EB;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }

        .input-group-text {
            background: #F9FAFB;
            border: 1.5px solid #E5E7EB;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #9CA3AF;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .btn-login {
            background: #0F2044;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            width: 100%;
            transition: background .15s;
        }

        .btn-login:hover { background: #3B82F6; color: #fff; }

        .form-check-input:checked { background-color: #3B82F6; border-color: #3B82F6; }

        .alert-danger {
            background: #FEE2E2;
            border: none;
            border-left: 4px solid #EF4444;
            color: #991B1B;
            border-radius: 8px;
            font-size: 13px;
            padding: 10px 14px;
        }

        .link-forgot {
            font-size: 12px;
            color: #6B7280;
            text-decoration: none;
        }

        .link-forgot:hover { color: #3B82F6; }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: rgba(255,255,255,.3);
        }
    </style>
</head>
<body>

<div class="login-wrap">
    <div class="login-brand">
        <i class="bi bi-box-seam-fill"></i>
        <h1>CelestaSupply</h1>
        <p>Sistema de Solicitação de Suprimentos</p>
    </div>

    <div class="login-card">

        @if($errors->any())
            <div class="alert-danger mb-4">
                <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
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

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Senha</label>
                    @if(Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="link-forgot">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input id="password" type="password" name="password"
                           class="form-control"
                           placeholder="••••••••"
                           required>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember" style="font-size:13px; color:#6B7280">
                        Lembrar-me neste dispositivo
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
            </button>
        </form>
    </div>

    <div class="login-footer">
        Não tem conta? Solicite acesso ao administrador.
    </div>
</div>

</body>
</html>