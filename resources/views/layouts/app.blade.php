<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CelestaSupply')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --cs-primary:      #0F2044;
            --cs-accent:       #3B82F6;
            --cs-accent-dark:  #2563EB;
        }

        * { box-sizing: border-box; }

        body {
            background:
                radial-gradient(ellipse at 15% 0%, rgba(59,130,246,.08) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 100%, rgba(15,32,68,.06) 0%, transparent 55%),
                #F2F5FB;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Navbar ── */
        .cs-navbar {
            background: linear-gradient(135deg, #0A1628 0%, #0F2044 60%, #152848 100%);
            height: 60px;
            padding: 0 28px;
            display: flex;
            align-items: stretch;
            gap: 32px;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid rgba(59,130,246,.2);
            box-shadow: 0 1px 0 rgba(255,255,255,.05), 0 4px 24px rgba(0,0,0,.4);
        }

        .cs-brand, .cs-user { align-items: center; }

        .cs-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: .02em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .cs-brand i {
            font-size: 20px;
            color: #60A5FA;
            filter: drop-shadow(0 0 6px rgba(96,165,250,.5));
        }

        .cs-nav {
            display: flex;
            align-items: stretch;
            gap: 0;
            flex: 1;
        }

        .cs-nav-link {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 0 14px;
            border-bottom: 2px solid transparent;
            color: rgba(255,255,255,.5);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color .15s, background .15s, border-color .15s;
            white-space: nowrap;
        }

        .cs-nav-link:hover {
            color: rgba(255,255,255,.85);
            background: rgba(255,255,255,.05);
        }

        .cs-nav-link.active {
            color: #fff;
            border-bottom-color: #3B82F6;
            background: rgba(59,130,246,.07);
        }

        .cs-nav-link i { font-size: 14px; }

        /* Dropdown */
        .cs-dropdown { position: relative; }

        .cs-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            background: #1A2C4A;
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            min-width: 200px;
            padding: 6px;
            box-shadow: 0 8px 32px rgba(0,0,0,.4), 0 2px 8px rgba(0,0,0,.2);
        }

        .cs-dropdown-menu.open { display: block; }

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
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
            box-shadow: 0 0 0 2px rgba(59,130,246,.35), 0 2px 8px rgba(37,99,235,.4);
        }

        .cs-user-info { line-height: 1.3; }
        .cs-user-name { font-size: 13px; font-weight: 600; color: #fff; }
        .cs-user-role {
            font-size: 10.5px;
            color: rgba(255,255,255,.45);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .cs-logout {
            background: none;
            border: none;
            padding: 6px 10px;
            border-radius: 6px;
            color: rgba(255,255,255,.45);
            cursor: pointer;
            font-size: 17px;
            transition: color .15s, background .15s;
            display: flex;
            align-items: center;
        }

        .cs-logout:hover {
            background: rgba(239,68,68,.12);
            color: #F87171;
        }

        /* ── Content ── */
        .cs-content {
            padding: 32px 40px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .cs-page-title {
            font-size: 21px;
            font-weight: 700;
            color: #0F172A;
            letter-spacing: -.025em;
            margin-bottom: 20px;
            padding-left: 12px;
            border-left: 3px solid var(--cs-accent);
            line-height: 1.2;
        }

        /* ── Cards ── */
        .cs-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #E2E9F4;
            padding: 22px;
            box-shadow:
                0 1px 2px rgba(15,32,68,.04),
                0 4px 16px rgba(15,32,68,.06);
        }

        /* ── Buttons ── */
        .btn {
            font-family: inherit;
            font-weight: 500;
            letter-spacing: .01em;
            border-radius: 4px;
            transition: background .15s, color .15s, border-color .15s, box-shadow .15s;
        }

        .btn-primary {
            background: transparent;
            border: 1.5px solid var(--cs-accent);
            color: var(--cs-accent);
            font-weight: 600;
            box-shadow: none;
        }

        .btn-primary:hover, .btn-primary:focus-visible {
            background: var(--cs-accent);
            border-color: var(--cs-accent);
            color: #fff;
            box-shadow: 0 2px 10px rgba(37,99,235,.2);
        }

        .btn-primary:active {
            background: var(--cs-accent-dark);
            border-color: var(--cs-accent-dark);
            color: #fff;
            box-shadow: none;
        }

        .btn-outline-secondary {
            background: transparent;
            border-color: #CBD5E1;
            color: #64748B;
            border-radius: 4px;
        }

        .btn-outline-secondary:hover {
            background: #F1F5F9;
            border-color: #94A3B8;
            color: #334155;
        }

        .btn-outline-danger {
            background: transparent;
            border-color: #FCA5A5;
            color: #DC2626;
            border-radius: 4px;
        }

        .btn-outline-danger:hover {
            background: #FEF2F2;
            border-color: #EF4444;
            color: #B91C1C;
        }

        /* ── Form controls ── */
        .form-control, .form-select {
            font-family: inherit;
            border-color: #D1D9E6;
            color: #1E293B;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--cs-accent);
            box-shadow: 0 0 0 3px rgba(59,130,246,.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 13px;
            color: #374151;
        }

        /* ── Status badges ── */
        .cs-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 10.5px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .cs-badge-draft           { background: #F1F5F9; color: #475569;  border: 1px solid #CBD5E1; }
        .cs-badge-pending         { background: #FFFBEB; color: #92400E;  border: 1px solid #FDE68A; }
        .cs-badge-quoting         { background: #EFF6FF; color: #1E40AF;  border: 1px solid #BFDBFE; }
        .cs-badge-awaitingPayment { background: #FFF7ED; color: #9A3412;  border: 1px solid #FED7AA; }
        .cs-badge-awaitingPickup  { background: #F5F3FF; color: #5B21B6;  border: 1px solid #DDD6FE; }
        .cs-badge-review          { background: #F0FDFA; color: #065F46;  border: 1px solid #99F6E4; }
        .cs-badge-completed       { background: #F0FDF4; color: #166534;  border: 1px solid #BBF7D0; }
        .cs-badge-cancelRequested { background: #FFF1F2; color: #9F1239;  border: 1px solid #FECDD3; }
        .cs-badge-cancelled       { background: #FEF2F2; color: #7F1D1D;  border: 1px solid #FECACA; }

        .cs-badge-low    { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        .cs-badge-medium { background: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .cs-badge-high   { background: #FFF1F2; color: #9F1239; border: 1px solid #FECDD3; }

        /* ── Table ── */
        .table > thead > tr > th {
            background: #F8FAFD;
            border-bottom: 2px solid #E2E9F4 !important;
        }

        .table-hover > tbody > tr { transition: background .1s; }
        .table-hover > tbody > tr:hover > * { background-color: #F5F8FF; }

        /* ── Bootstrap badge override ── */
        .badge {
            font-family: inherit;
            font-weight: 600;
            letter-spacing: .04em;
            font-size: 10.5px;
            border-radius: 5px;
        }

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
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            min-width: 280px;
            box-shadow: 0 4px 24px rgba(0,0,0,.14), 0 1px 4px rgba(0,0,0,.08);
            animation: slideIn .2s ease;
        }

        .cs-toast-success { background: #F0FDF4; color: #166534; border: 1px solid #BBF7D0; border-left: 4px solid #22C55E; }
        .cs-toast-error   { background: #FFF1F2; color: #9F1239; border: 1px solid #FECDD3; border-left: 4px solid #EF4444; }

        @keyframes slideIn {
            from { transform: translateX(16px); opacity: 0; }
            to   { transform: translateX(0);    opacity: 1; }
        }

        /* ── Progress bar ── */
        #cs-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 2px;
            width: 0;
            background: linear-gradient(90deg, #3B82F6, #60A5FA, #93C5FD);
            z-index: 10000;
            pointer-events: none;
            opacity: 0;
            box-shadow: 0 0 10px rgba(59,130,246,.7);
        }

        /* ── Page fade-in ── */
        .cs-content {
            animation: cs-page-in .28s ease both;
        }

        @keyframes cs-page-in {
            from { opacity: 0; transform: translateY(7px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Hamburger ── */
        .cs-burger {
            display: none;
            background: none;
            border: none;
            color: rgba(255,255,255,.75);
            font-size: 23px;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 4px;
            align-items: center;
            line-height: 1;
            transition: color .15s;
        }
        .cs-burger:hover { color: #fff; }

        /* ── Mobile menu ── */
        .cs-mobile-menu {
            position: fixed;
            top: 60px;
            left: 0;
            right: 0;
            background: linear-gradient(160deg, #0A1628 0%, #0F2044 100%);
            border-bottom: 1px solid rgba(59,130,246,.2);
            box-shadow: 0 8px 32px rgba(0,0,0,.4);
            z-index: 997;
            padding: 10px 16px 20px;
            transform: translateY(-8px);
            opacity: 0;
            pointer-events: none;
            transition: transform .2s ease, opacity .2s ease;
        }
        .cs-mobile-menu.open {
            transform: translateY(0);
            opacity: 1;
            pointer-events: auto;
        }
        .cs-mobile-nav { display: flex; flex-direction: column; gap: 2px; }
        .cs-mobile-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 8px;
            color: rgba(255,255,255,.75);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: background .15s, color .15s;
        }
        .cs-mobile-nav-link:hover { background: rgba(255,255,255,.07); color: #fff; }
        .cs-mobile-nav-link.active {
            background: rgba(59,130,246,.18);
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(59,130,246,.25);
        }
        .cs-mobile-nav-link i { font-size: 16px; width: 20px; flex-shrink: 0; }
        .cs-mobile-divider { border-top: 1px solid rgba(255,255,255,.08); margin: 10px 0; }

        /* ── Responsive ── */
        @media (max-width: 767px) {
            .cs-navbar    { padding: 0 16px; gap: 8px; }
            .cs-nav       { display: none; }
            .cs-user-info { display: none; }
            .cs-burger    { display: flex; }
            .cs-content   { padding: 20px 16px; }
            .cs-card      { padding: 16px; border-radius: 10px; }
            .cs-page-title { font-size: 18px; }
        }
    </style>

    @stack('styles')
</head>
<body>

<div id="cs-bar" aria-hidden="true"></div>

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

        <a href="{{ route('requests.index') }}"
           class="cs-nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> Solicitações
        </a>

        @if(auth()->user()->isBuyerOrAdmin())
        <a href="{{ route('suppliers.index') }}"
           class="cs-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Fornecedores
        </a>
        @endif

        @if(auth()->user()->isBuyerOrAdmin())
        <a href="{{ route('items.index') }}"
           class="cs-nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
            <i class="bi bi-box"></i> Itens
        </a>
        <a href="{{ route('admin.costCenters.index') }}"
           class="cs-nav-link {{ request()->routeIs('admin.costCenters.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Centros de Custo
        </a>
        <a href="#"
           class="cs-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Relatórios
        </a>
        @endif

        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.users.index') }}"
           class="cs-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Usuários
        </a>
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
        <button id="cs-burger" class="cs-burger" aria-label="Abrir menu" aria-expanded="false">
            <i class="bi bi-list"></i>
        </button>
    </div>
</header>

{{-- ── Mobile Menu ── --}}
<div id="cs-mobile-menu" class="cs-mobile-menu" inert>
    <nav class="cs-mobile-nav">
        <a href="{{ route('dashboard') }}"
           class="cs-mobile-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('requests.index') }}"
           class="cs-mobile-nav-link {{ request()->routeIs('requests.*') ? 'active' : '' }}">
            <i class="bi bi-clipboard-check"></i> Solicitações
        </a>
        @if(auth()->user()->isBuyerOrAdmin())
        <a href="{{ route('suppliers.index') }}"
           class="cs-mobile-nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <i class="bi bi-truck"></i> Fornecedores
        </a>
        <a href="{{ route('items.index') }}"
           class="cs-mobile-nav-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
            <i class="bi bi-box"></i> Itens
        </a>
        <a href="{{ route('admin.costCenters.index') }}"
           class="cs-mobile-nav-link {{ request()->routeIs('admin.costCenters.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Centros de Custo
        </a>
        <a href="#" class="cs-mobile-nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Relatórios
        </a>
        @endif
        @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.users.index') }}"
           class="cs-mobile-nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Usuários
        </a>
        @endif
        <div class="cs-mobile-divider"></div>
        <div style="padding:4px 14px 0;font-size:11px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:.08em;font-weight:600">
            {{ auth()->user()->name }} &mdash; {{ auth()->user()->role }}
        </div>
    </nav>
</div>

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

    function phoneMask(input) {
        const raw = input.value;

        // Internacional: começa com + — deixa livre, só filtra caracteres inválidos
        if (raw.startsWith('+')) {
            input.value = raw.replace(/[^\d\s\-\+\(\)]/g, '').slice(0, 20);
            return;
        }

        // Brasil: aplica máscara automática
        let v = raw.replace(/\D/g, '').slice(0, 11);
        if (v.length <= 10) {
            v = v.replace(/^(\d{0,2})(\d{0,4})(\d{0,4})$/,
                (_, ddd, part1, part2) => {
                    if (!ddd)   return '';
                    if (!part1) return `(${ddd}`;
                    if (!part2) return `(${ddd}) ${part1}`;
                    return `(${ddd}) ${part1}-${part2}`;
                });
        } else {
            v = v.replace(/^(\d{2})(\d{5})(\d{0,4})$/,
                (_, ddd, part1, part2) => {
                    if (!part2) return `(${ddd}) ${part1}`;
                    return `(${ddd}) ${part1}-${part2}`;
                });
        }
        input.value = v;
    }

    document.addEventListener('submit', function (e) {
        const form = e.target;
        setTimeout(() => {
            form.querySelectorAll('input, select, textarea, button').forEach(el => {
                el.disabled = true;
            });
        }, 0);
    });

    // ── Barra de progresso de navegação ──
    (function () {
        const bar = document.getElementById('cs-bar');
        let trickle;

        function start() {
            clearInterval(trickle);
            bar.style.transition = 'none';
            bar.style.width = '0';
            bar.style.opacity = '1';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    bar.style.transition = 'width .5s cubic-bezier(.4,0,.2,1)';
                    bar.style.width = '60%';
                    trickle = setInterval(() => {
                        const w = parseFloat(bar.style.width);
                        if (w < 88) bar.style.width = (w + (88 - w) * .12) + '%';
                    }, 700);
                });
            });
        }

        function done() {
            clearInterval(trickle);
            bar.style.transition = 'width .18s ease';
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.transition = 'opacity .35s ease';
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.transition = 'none';
                    bar.style.width = '0';
                }, 350);
            }, 180);
        }

        document.addEventListener('click', function (e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || a.target === '_blank') return;
            start();
        });

        document.addEventListener('submit', start);
        window.addEventListener('pageshow', done);
    })();

    // ── Mobile menu ──
    (function () {
        const burger = document.getElementById('cs-burger');
        const menu   = document.getElementById('cs-mobile-menu');
        if (!burger || !menu) return;

        function open() {
            menu.classList.add('open');
            menu.removeAttribute('inert');
            burger.setAttribute('aria-expanded', 'true');
            burger.querySelector('i').className = 'bi bi-x-lg';
        }

        function close() {
            menu.classList.remove('open');
            menu.setAttribute('inert', '');
            burger.setAttribute('aria-expanded', 'false');
            burger.querySelector('i').className = 'bi bi-list';
        }

        burger.addEventListener('click', () => menu.classList.contains('open') ? close() : open());
        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', close));
        document.addEventListener('keydown', e => { if (e.key === 'Escape') close(); });
    })();

    // ── Prefetch on hover ──
    (function () {
        const prefetched = new Set();
        document.addEventListener('mouseover', function (e) {
            const a = e.target.closest('a[href]');
            if (!a) return;
            const href = a.href;
            if (!href || a.target === '_blank') return;
            try {
                const url = new URL(href);
                if (url.origin !== location.origin) return;
                if (url.pathname === location.pathname) return;
                if (prefetched.has(href)) return;
                prefetched.add(href);
                const link = document.createElement('link');
                link.rel  = 'prefetch';
                link.href = href;
                link.as   = 'document';
                document.head.appendChild(link);
            } catch (_) {}
        }, { passive: true });
    })();

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const apiFetch = (url, options = {}) => fetch(url, {
        credentials: 'same-origin',
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

@if(session('success') || session('error'))
<script>
    document.addEventListener('alpine:initialized', () => {
        @if(session('success'))
            toast(@json(session('success')));
        @endif
        @if(session('error'))
            toast(@json(session('error')), 'error');
        @endif
    });
</script>
@endif
</body>
</html>