{{-- Refined Card Component --}}
<div class="card {{ $class ?? '' }}"
     {{ $attributes->merge(['role' => 'article']) }}>

  @if($header ?? null)
  <div class="card-header">
    {{ $header }}
  </div>
  @endif

  <div class="card-body">
    {{ $slot }}
  </div>

  @if($footer ?? null)
  <div class="card-footer">
    {{ $footer }}
  </div>
  @endif
</div>

<style scoped>
.card {
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  overflow: hidden;
  transition: all var(--transition-base);
  box-shadow: var(--shadow-sm);
}

.card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

.card-header {
  padding: var(--spacing-lg);
  border-bottom: 1px solid var(--color-border);
  background: var(--color-surface);
}

.card-header h3,
.card-header h4,
.card-header h5,
.card-header h6 {
  margin: 0;
  font-size: var(--font-size-lg);
  font-weight: var(--font-weight-semibold);
}

.card-body {
  padding: var(--spacing-lg);
}

.card-body > :last-child {
  margin-bottom: 0;
}

.card-footer {
  padding: var(--spacing-lg);
  border-top: 1px solid var(--color-border);
  background: var(--color-surface);
  display: flex;
  gap: var(--spacing-md);
  justify-content: flex-end;
}

/* Mobile adjustments */
@media (max-width: 639px) {
  .card {
    border-radius: var(--radius-md);
  }

  .card-header,
  .card-body,
  .card-footer {
    padding: var(--spacing-md);
  }
}
</style>
