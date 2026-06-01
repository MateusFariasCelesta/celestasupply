<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CelestaSupply')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --cs-primary: #0F2044;
            --cs-accent:  #3B82F6;
        }

        * { box-sizing: border-box; }

        body {
            background: #F1F5F9;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .cs-navbar {
            background: var(--cs-primary);
            height: 60px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 32px;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0,0,0,.25);
        }

        .cs-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: .03em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cs-brand i {
            font-size: 20px;
            color: var(--cs-accent);
        }

        .cs-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            flex: 1;
        }

        .cs-nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            color: rgba(255,255,255,.65);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background .15s, color .15s;
            white-space: nowrap;
        }

        .cs-nav-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .cs-nav-link.active {
            background: rgba(59,130,246,.2);
            color: #fff;
        }

        .cs-nav-link i { font-size: 15px; }

        /* Dropdown */
        .cs-dropdown { position: relative; }

        .cs-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 10px);
            left: 0;
            background: #1E2D45;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 8px;
            min-width: 200px;
            padding: 6px;
            box-shadow: 0 8px 24px rgba(0,0,0,.3);
        }

        .cs-dropdown:hover .cs-dropdown-menu { display: block; }

        .cs-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 13.5px;
            transition: background .15s, color .15s;
        }

        .cs-dropdown-item:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .cs-dropdown-divider {
            border-top: 1px solid rgba(255,255,255,.08);
            margin: 4px 0;
        }

        /* User area */
        .cs-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
            flex-shrink: 0;
        }

        .cs-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--cs-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .cs-user-info { line-height: 1.3; }
        .cs-user-name { font-size: 13px; font-weight: 600; color: #fff; }
        .cs-user-role {
            font-size: 11px;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .cs-logout {
            background: none;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            color: rgba(255,255,255,.5);
            cursor: pointer;
            font-size: 18px;
            transition: color .15s, background .15s;
            display: flex;
            align-items: center;
        }

        .cs-logout:hover {
            background: rgba(255,255,255,.08);
            color: #F87171;
        }

        /* ── Content ── */
        .cs-content {
            padding: 28px 32px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .cs-page-title {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 20px;
        }

        /* ── Cards ── */
        .cs-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid #E2E8F0;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        /* ── Status badges ── */
        .cs-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .cs-badge-draft           { background: #F1F5F9; color: #64748B; }
        .cs-badge-pending         { background: #FEF9C3; color: #854D0E; }
        .cs-badge-quoting         { background: #DBEAFE; color: #1E40AF; }
        .cs-badge-awaitingPayment { background: #FFEDD5; color: #9A3412; }
        .cs-badge-awaitingPickup  { background: #EDE9FE; color: #5B21B6; }
        .cs-badge-review          { background: #CCFBF1; color: #065F46; }
        .cs-badge-completed       { background: #DCFCE7; color: #166534; }
        .cs-badge-cancelRequested { background: #FEE2E2; color: #991B1B; }
        .cs-badge-cancelled       { background: #FEE2E2; color: #7F1D1D; }

        .cs-badge-low    { background: #DBEAFE; color: #1E40AF; }
        .cs-badge-medium { background: #FEF9C3; color: #854D0E; }
        .cs-badge-high   { background: #FEE2E2; color: #991B1B; }

        /* ── Toast ── */
        .cs-toast-wrap {
            position: fixed;
            top: 72px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .cs-toast {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            min-width: 260px;
            box-shadow: 0 4px 16px rgba(0,0,0,.12);
            animation: slideIn .2s ease;
        }

        .cs-toast-success { background: #DCFCE7; color: #166534; border-left: 4px solid #22C55E; }
        .cs-toast-error   { background: #FEE2E2; color: #991B1B; border-left: 4px solid #EF4444; }

        @keyframes slideIn {
            from { transform: translateX(20px); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Navbar ── --}}
<header class="cs-navbar">
    <a href="{{ route('dashboard') }}" class="cs-brand">
        <i class="bi bi-box-seam-fill"></i>
        CelestaSupply
    </a>

    <nav class="cs-nav">
        <a href="{{ route('dashboard') }}"
           class="cs-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="#"
           class="cs-nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> Solicitações
        </a>

        @if(auth()->user()->isBuyerOrAdmin())
        <a href="#"
           class="cs-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Fornecedores
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <div class="cs-dropdown">
            <span class="cs-nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" style="cursor:pointer">
                <i class="bi bi-gear"></i> Admin <i class="bi bi-chevron-down" style="font-size:11px"></i>
            </span>
            <div class="cs-dropdown-menu">
                <a href="#" class="cs-dropdown-item">
                    <i class="bi bi-people"></i> Usuários
                </a>
                <a href="#" class="cs-dropdown-item">
                    <i class="bi bi-building"></i> Centros de Custo
                </a>
                <div class="cs-dropdown-divider"></div>
                <a href="#" class="cs-dropdown-item">
                    <i class="bi bi-bar-chart"></i> Relatórios
                </a>
            </div>
        </div>
        @endif
    </nav>

    <div class="cs-user">
        <div class="cs-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="cs-user-info">
            <div class="cs-user-name">{{ auth()->user()->name }}</div>
            <div class="cs-user-role">{{ auth()->user()->role }}</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="cs-logout" title="Sair">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</header>

{{-- ── Toasts ── --}}
<div x-data="toastManager()" @toast.window="add($event.detail)" class="cs-toast-wrap">
    <template x-for="t in toasts" :key="t.id">
        <div class="cs-toast" :class="t.type === 'success' ? 'cs-toast-success' : 'cs-toast-error'">
            <i class="bi" :class="t.type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'"></i>
            <span x-text="t.message"></span>
        </div>
    </template>
</div>

{{-- ── Content ── --}}
<main class="cs-content">
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js" defer></script>

<script>
    function toastManager() {
        return {
            toasts: [],
            add({ message, type = 'success' }) {
                const id = Date.now();
                this.toasts.push({ id, message, type });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) {
                this.toasts = this.toasts.filter(t => t.id !== id);
            },
        };
    }

    function toast(message, type = 'success') {
        window.dispatchEvent(new CustomEvent('toast', { detail: { message, type } }));
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const apiFetch = (url, options = {}) => fetch(url, {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            ...options.headers,
        },
        ...options,
    });
</script>

@stack('scripts')
</body>
</html>