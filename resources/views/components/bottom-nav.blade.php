{{-- Mobile Bottom Navigation --}}
<nav class="bottom-nav d-md-none">
  <a href="{{ route('dashboard') }}"
     class="bottom-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
     aria-label="Dashboard">
    <i class="bi bi-speedometer2"></i>
    <span class="label">Dashboard</span>
  </a>

  <a href="{{ route('requests.index') }}"
     class="bottom-nav-item {{ request()->routeIs('requests.*') ? 'active' : '' }}"
     aria-label="Solicitações">
    <i class="bi bi-clipboard-list"></i>
    <span class="label">Solicitações</span>
  </a>

  @if(auth()->user()->isBuyerOrAdmin())
  <a href="{{ route('items.index') }}"
     class="bottom-nav-item {{ request()->routeIs('items.*') ? 'active' : '' }}"
     aria-label="Itens">
    <i class="bi bi-box-seam"></i>
    <span class="label">Itens</span>
  </a>
  @endif

  <button class="bottom-nav-item menu-toggle"
          type="button"
          aria-label="Menu"
          aria-expanded="false"
          aria-controls="mobile-menu">
    <i class="bi bi-list"></i>
    <span class="label">Menu</span>
  </button>
</nav>

<style scoped>
.bottom-nav {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60px;
  background: white;
  border-top: 1px solid var(--color-border);
  display: flex;
  justify-content: space-around;
  align-items: stretch;
  box-shadow: var(--shadow-lg);
  z-index: var(--z-fixed);
  padding-bottom: max(env(safe-area-inset-bottom), 0);
}

.bottom-nav-item {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  color: var(--color-text-secondary);
  text-decoration: none;
  background: none;
  border: none;
  cursor: pointer;
  transition: all var(--transition-fast);
  min-height: 44px;
  position: relative;
}

.bottom-nav-item i {
  font-size: 20px;
}

.bottom-nav-item .label {
  font-size: 11px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.bottom-nav-item:hover,
.bottom-nav-item.active {
  color: var(--color-primary-600);
  background-color: var(--color-primary-50);
}

.bottom-nav-item.active::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: var(--color-primary-500);
  border-radius: 0 0 2px 2px;
}
</style>
