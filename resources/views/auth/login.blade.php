<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — CelestaSupply</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── Layout ─────────────────────────────── */
        .auth-container {
            display: flex;
            min-height: 100vh;
        }

        /* ─── Left Panel ──────────────────────────── */
        .auth-left {
            width: 44%;
            min-height: 100vh;
            background: linear-gradient(155deg, #071428 0%, #0D1E3D 35%, #102350 65%, #16315F 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 56px 48px;
            position: relative;
            overflow: hidden;
            animation: panelLeft .75s cubic-bezier(.22,1,.36,1) both;
        }

        /* Glow blobs */
        .auth-left .blob-1 {
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59,130,246,.18) 0%, transparent 65%);
            top: -160px; right: -140px;
            pointer-events: none;
            animation: float 8s ease-in-out infinite;
        }
        .auth-left .blob-2 {
            position: absolute;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(96,165,250,.12) 0%, transparent 65%);
            bottom: -100px; left: -80px;
            pointer-events: none;
            animation: float 10s ease-in-out infinite reverse;
        }
        .auth-left .blob-3 {
            position: absolute;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30,58,138,.35) 0%, transparent 70%);
            bottom: 30%; right: 10%;
            pointer-events: none;
            animation: float 6s ease-in-out infinite 2s;
        }

        /* Dot grid decoration */
        .dot-grid {
            position: absolute;
            bottom: 48px; right: 40px;
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 9px;
            opacity: .18;
            pointer-events: none;
        }
        .dot-grid span {
            width: 3px; height: 3px;
            border-radius: 50%;
            background: #93C5FD;
            display: block;
        }

        /* Left inner content */
        .auth-left-inner {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 300px;
            animation: fadeUp .7s .2s ease both;
        }

        /* Logo white card */
        .logo-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border-radius: 18px;
            padding: 18px 32px;
            box-shadow: 0 12px 40px rgba(0,0,0,.3), 0 2px 8px rgba(0,0,0,.2);
            margin-bottom: 40px;
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .logo-box:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 50px rgba(0,0,0,.35);
        }
        .logo-box img {
            height: 52px;
            width: auto;
            display: block;
        }

        .auth-left-title {
            font-size: 19px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.025em;
            line-height: 1.4;
            margin-bottom: 14px;
        }

        .auth-left-desc {
            font-size: 13.5px;
            color: rgba(255,255,255,.48);
            line-height: 1.7;
        }

        /* Bottom badge */

        /* ─── Right Panel ──────────────────────────── */
        .auth-right {
            flex: 1;
            background: #F6F9FE;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            animation: panelRight .75s cubic-bezier(.22,1,.36,1) both;
        }

        .auth-form-wrap {
            width: 100%;
            max-width: 390px;
        }

        .auth-heading {
            font-size: 26px;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: -.03em;
            margin-bottom: 6px;
            animation: fadeUp .5s .15s ease both;
        }

        .auth-subheading {
            font-size: 14px;
            color: #6B7280;
            margin-bottom: 36px;
            animation: fadeUp .5s .2s ease both;
        }

        /* ─── Form fields ──────────────────────────── */
        .field-wrap {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 7px;
        }

        .input-group-text {
            background: #EEF3FC;
            border: 1.5px solid #D1D9E6;
            border-right: none;
            border-radius: 11px 0 0 11px;
            color: #94A3B8;
            padding: 0 14px;
            transition: border-color .2s, background .2s, color .2s;
        }

        .form-control {
            border: 1.5px solid #D1D9E6;
            border-radius: 11px;
            padding: 12px 15px;
            font-size: 14px;
            color: #0F172A;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 11px 11px 0;
        }

        .form-control:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,.13);
            outline: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: #3B82F6;
            background: #EFF6FF;
            color: #3B82F6;
        }

        /* ─── Button ──────────────────────────────── */
        .btn-auth {
            position: relative;
            overflow: hidden;
            width: 100%;
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%);
            border: none;
            border-radius: 11px;
            padding: 14px;
            font-size: 14.5px;
            font-weight: 600;
            color: #fff;
            letter-spacing: .01em;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .2s ease;
        }
        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.18), transparent);
            transition: left .5s ease;
        }
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(37,99,235,.38);
            color: #fff;
        }
        .btn-auth:hover::before { left: 150%; }
        .btn-auth:active { transform: translateY(0); }

        /* ─── Other ───────────────────────────────── */
        .form-check-input:checked { background-color: #3B82F6; border-color: #3B82F6; }

        .link-forgot {
            font-size: 12px;
            color: #94A3B8;
            text-decoration: none;
            transition: color .15s;
        }
        .link-forgot:hover { color: #3B82F6; }

        .alert-auth {
            background: #FEF2F2;
            border-left: 4px solid #EF4444;
            color: #991B1B;
            border-radius: 10px;
            font-size: 13px;
            padding: 11px 15px;
            margin-bottom: 24px;
            animation: fadeUp .3s ease both;
        }

        .divider {
            border: none;
            border-top: 1px solid #E5EBF5;
            margin: 28px 0 22px;
        }

        .auth-note {
            text-align: center;
            font-size: 12.5px;
            color: #94A3B8;
        }

        /* ─── Staggered animations ────────────────── */
        .s1 { animation: fadeUp .5s .25s ease both; }
        .s2 { animation: fadeUp .5s .32s ease both; }
        .s3 { animation: fadeUp .5s .39s ease both; }
        .s4 { animation: fadeUp .5s .46s ease both; }
        .s5 { animation: fadeUp .5s .53s ease both; }

        @keyframes panelLeft {
            from { opacity: 0; transform: translateX(-48px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes panelRight {
            from { opacity: 0; transform: translateX(48px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-20px); }
        }

        /* ─── Mobile ──────────────────────────────── */
        @media (max-width: 768px) {
            body { overflow: auto; }
            .auth-container { flex-direction: column; }
            .auth-left {
                width: 100%;
                min-height: auto;
                padding: 28px 24px;
                animation: none;
            }
            .auth-left-inner { display: flex; align-items: center; gap: 18px; text-align: left; }
            .logo-box { margin-bottom: 0; padding: 12px 20px; }
            .logo-box img { height: 40px; }
            .auth-left-title { font-size: 15px; margin-bottom: 0; }
            .auth-left-desc, .dot-grid { display: none; }
            .auth-right { padding: 32px 20px; animation: none; min-height: auto; }
        }
    </style>
</head>
<body>

<div class="auth-container">

    {{-- ── Left Panel ── --}}
    <div class="auth-left">
        <div class="blob-1"></div>
        <div class="blob-2"></div>
        <div class="blob-3"></div>

        <div class="auth-left-inner">
            <div class="logo-box">
                <img src="{{ asset('images/celesta-mineracao-logo.png') }}" alt="Celesta Mineração">
            </div>
            <div class="auth-left-title">Sistema de Solicitação<br>de Suprimentos</div>
            <div class="auth-left-desc">Gerencie solicitações de compra com eficiência, rastreabilidade e controle total.</div>
        </div>

        <div class="dot-grid">
            @for($i = 0; $i < 30; $i++)<span></span>@endfor
        </div>

    </div>

    {{-- ── Right Panel ── --}}
    <div class="auth-right">
        <div class="auth-form-wrap">

            <div class="auth-heading">Bem-vindo de volta</div>
            <div class="auth-subheading">Acesse sua conta para continuar.</div>

            @if($errors->any())
                <div class="alert-auth">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="field-wrap s1">
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

                <div class="field-wrap s2">
                    <div class="d-flex justify-content-between align-items-center mb-1" style="margin-bottom:7px!important">
                        <label for="password" class="form-label mb-0">Senha</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="link-forgot">Esqueceu a senha?</a>
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

                <div class="field-wrap s3" style="margin-bottom:28px">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember"
                               style="font-size:13px;color:#6B7280;font-weight:400;text-transform:none;letter-spacing:0">
                            Lembrar-me neste dispositivo
                        </label>
                    </div>
                </div>

                <div class="s4">
                    <button type="submit" class="btn-auth">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Entrar na plataforma
                    </button>
                </div>
            </form>

            <hr class="divider">
            <div class="auth-note s5">
                Não tem acesso? Solicite ao administrador do sistema.
            </div>

        </div>
    </div>

</div>

</body>
</html>
