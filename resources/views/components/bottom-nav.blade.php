{{-- Mobile Bottom Navigation --}}
<nav class="cs-bottom-nav" id="cs-bottom-nav">
  <a href="{{ route('dashboard') }}"
     class="cs-bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
     aria-label="Dashboard">
    <i class="bi bi-speedometer2"></i>
    <span class="label">Dashboard</span>
  </a>

  <a href="{{ route('requests.index') }}"
     class="cs-bottom-nav-item {{ request()->routeIs('requests.*') ? 'active' : '' }}"
     aria-label="Solicitações">
    <i class="bi bi-clipboard-check"></i>
    <span class="label">Solicitações</span>
  </a>

  @if(auth()->user()->isBuyerOrAdmin())
  <a href="{{ route('items.index') }}"
     class="cs-bottom-nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}"
     aria-label="Itens">
    <i class="bi bi-box-seam"></i>
    <span class="label">Itens</span>
  </a>
  @endif

  <button class="cs-bottom-nav-item cs-bottom-menu-toggle"
          type="button"
          id="cs-bottom-menu-toggle"
          aria-label="Menu adicional"
          aria-expanded="false"
          aria-controls="cs-bottom-menu-overlay">
    <i class="bi bi-list"></i>
    <span class="label">Menu</span>
  </button>
</nav>

{{-- Bottom Menu Overlay --}}
<div class="cs-bottom-menu-overlay" id="cs-bottom-menu-overlay" inert>
  <div class="cs-bottom-menu-content">
    @if(auth()->user()->isBuyerOrAdmin())
    <a href="{{ route('suppliers.index') }}"
       class="cs-bottom-menu-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
      <i class="bi bi-truck"></i> Fornecedores
    </a>
    <a href="{{ route('admin.costCenters.index') }}"
       class="cs-bottom-menu-item {{ request()->routeIs('admin.costCenters.*') ? 'active' : '' }}">
      <i class="bi bi-building"></i> Centros de Custo
    </a>
    @endif

    @if(auth()->user()->isAdmin())
    <a href="{{ route('admin.users.index') }}"
       class="cs-bottom-menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i class="bi bi-people"></i> Usuários
    </a>
    @endif

    <a href="{{ route('profile.edit') }}"
       class="cs-bottom-menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
      <i class="bi bi-person-circle"></i> Meu Perfil
    </a>

    <div class="cs-bottom-menu-divider"></div>

    <form method="POST" action="{{ route('logout') }}" class="w-100">
      @csrf
      <button type="submit" class="cs-bottom-menu-item cs-logout-item">
        <i class="bi bi-box-arrow-right"></i> Sair
      </button>
    </form>
  </div>
</div>

<style>
  /* Bottom Navigation Bar */
  .cs-bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 70px;
    background: linear-gradient(135deg, #0A1628 0%, #0F2044 60%, #152848 100%);
    border-top: 1px solid rgba(59, 130, 246, 0.2);
    display: grid;
    grid-auto-flow: column;
    grid-auto-columns: 1fr;
    align-items: stretch;
    z-index: 998;
    box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.3);
    padding-bottom: max(env(safe-area-inset-bottom), 0);
  }

  .cs-bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    padding: 10px 0;
    min-width: 0;
    flex: 1;
    height: 100%;
  }

  .cs-bottom-nav-item:active {
    transform: scale(0.95);
  }

  .cs-bottom-nav-item i {
    font-size: 20px;
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    line-height: 1;
  }

  .cs-bottom-nav-item .label {
    font-size: 10px;
    font-weight: 600;
    color: inherit;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    line-height: 1.2;
  }

  /* Active state */
  .cs-bottom-nav-item.active {
    color: #fff;
  }

  .cs-bottom-nav-item.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #3B82F6, #60A5FA);
    border-radius: 0 0 2px 2px;
  }

  .cs-bottom-nav-item.active i {
    color: #60A5FA;
    transform: scale(1.15);
  }

  /* Hover state */
  .cs-bottom-nav-item:hover {
    color: rgba(255, 255, 255, 0.9);
  }

  .cs-bottom-nav-item:hover i {
    transform: scale(1.12) translateY(-2px);
  }

  /* Menu toggle special styling */
  .cs-bottom-menu-toggle {
    font-weight: 600;
  }

  /* Bottom Menu Overlay */
  .cs-bottom-menu-overlay {
    position: fixed;
    bottom: 64px;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 997;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
    padding-bottom: max(env(safe-area-inset-bottom), 0);
  }

  .cs-bottom-menu-overlay.open {
    opacity: 1;
    visibility: visible;
  }

  .cs-bottom-menu-content {
    background: linear-gradient(160deg, #0A1628 0%, #0F2044 100%);
    border-top: 1px solid rgba(59, 130, 246, 0.2);
    box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.4);
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 10px 16px 16px;
    max-height: 70vh;
    overflow-y: auto;
  }

  .cs-bottom-menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.75);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    background: none;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
    width: 100%;
    text-align: left;
  }

  .cs-bottom-menu-item:active {
    transform: scale(0.98);
  }

  .cs-bottom-menu-item:hover,
  .cs-bottom-menu-item.active {
    background: rgba(59, 130, 246, 0.18);
    color: #fff;
    box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.25);
  }

  .cs-bottom-menu-item i {
    width: 20px;
    flex-shrink: 0;
    font-size: 16px;
  }

  .cs-logout-item {
    color: rgba(248, 113, 113, 0.75);
    margin-top: 4px;
  }

  .cs-logout-item:hover,
  .cs-logout-item:active {
    background: rgba(239, 68, 68, 0.12);
    color: #F87171;
  }

  .cs-bottom-menu-divider {
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    margin: 6px 0;
  }

  /* Desktop: hide bottom nav */
  @media (min-width: 768px) {
    .cs-bottom-nav,
    .cs-bottom-menu-overlay {
      display: none;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('cs-bottom-menu-toggle');
    const overlay = document.getElementById('cs-bottom-menu-overlay');
    const nav = document.getElementById('cs-bottom-nav');

    if (!toggle || !overlay || !nav) return;

    function open() {
      overlay.classList.add('open');
      overlay.removeAttribute('inert');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.querySelector('i').className = 'bi bi-x-lg';
    }

    function close() {
      overlay.classList.remove('open');
      overlay.setAttribute('inert', '');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.querySelector('i').className = 'bi bi-list';
    }

    toggle.addEventListener('click', () => {
      overlay.classList.contains('open') ? close() : open();
    });

    overlay.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', close);
    });

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') close();
    });

    // Close when clicking outside menu
    overlay.addEventListener('click', e => {
      if (e.target === overlay) close();
    });
  });
</script>
