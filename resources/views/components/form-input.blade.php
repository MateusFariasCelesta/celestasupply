{{-- Refined Form Input Component --}}
@php
  $id = $id ?? 'input-' . \Str::random(8);
  $type = $type ?? 'text';
  $error = $errors->first($name);
@endphp

<div class="form-group {{ $containerClass ?? '' }}">
  @if($label ?? null)
  <label for="{{ $id }}" class="form-label">
    {{ $label }}
    @if($required ?? false)
    <span class="required" aria-label="required">*</span>
    @endif
  </label>
  @endif

  <input
    id="{{ $id }}"
    type="{{ $type }}"
    name="{{ $name }}"
    class="form-input {{ $error ? 'is-invalid' : '' }} {{ $inputClass ?? '' }}"
    placeholder="{{ $placeholder ?? '' }}"
    @if($required ?? false) required @endif
    @if($disabled ?? false) disabled @endif
    @if($maxlength ?? null) maxlength="{{ $maxlength }}" @endif
    @if($minlength ?? null) minlength="{{ $minlength }}" @endif
    @if($pattern ?? null) pattern="{{ $pattern }}" @endif
    @if($readonly ?? false) readonly @endif
    value="{{ old($name, $value ?? '') }}"
    {{ $attributes->except(['class']) }}
  >

  @if($hint ?? null)
  <span class="form-hint">{{ $hint }}</span>
  @endif

  @if($error)
  <span class="form-error" role="alert">
    <i class="bi bi-exclamation-circle"></i>
    {{ $error }}
  </span>
  @endif
</div>

<style scoped>
.form-group {
  margin-bottom: var(--spacing-lg);
  display: flex;
  flex-direction: column;
}

.form-label {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-semibold);
  color: var(--color-text-secondary);
  margin-bottom: var(--spacing-xs);
  display: flex;
  align-items: center;
  gap: 4px;
}

.required {
  color: var(--color-error-500);
}

.form-input {
  width: 100%;
  padding: var(--spacing-sm) var(--spacing-md);
  font-size: 16px; /* Prevents zoom on iOS */
  font-family: var(--font-sans);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-md);
  background: var(--color-background);
  color: var(--color-text-primary);
  transition: all var(--transition-base);
  min-height: 44px;
}

.form-input:focus {
  outline: none;
  border-color: var(--color-primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input:disabled {
  background: var(--color-surface);
  color: var(--color-text-tertiary);
  cursor: not-allowed;
  opacity: 0.6;
}

.form-input.is-invalid {
  border-color: var(--color-error-500);
}

.form-input.is-invalid:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-hint {
  font-size: var(--font-size-xs);
  color: var(--color-text-tertiary);
  margin-top: var(--spacing-xs);
  display: block;
}

.form-error {
  font-size: var(--font-size-sm);
  color: var(--color-error-600);
  margin-top: var(--spacing-xs);
  display: flex;
  align-items: center;
  gap: 6px;
  animation: formErrorSlideDown 0.2s ease-out;
}

.form-error i {
  flex-shrink: 0;
  font-size: 16px;
}

/* Mobile adjustments */
@media (max-width: 639px) {
  .form-input {
    font-size: 16px;
    padding: var(--spacing-md);
  }
}
</style>
