@extends('layouts.app')
@section('title', 'Editar Item — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Editar Item</h1>
</div>

<div class="cs-card" style="max-width:480px">
    <form method="POST" action="{{ route('items.update', $item) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Nome</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $item->name) }}"
                   required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Status</label>
            <div class="form-check form-switch">
                <input type="hidden" name="isActive" value="0">
                <input class="form-check-input" type="checkbox" name="isActive" value="1" id="isActive"
                       {{ old('isActive', $item->isActive) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive" style="font-size:14px">Item ativo</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Salvar Alterações
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
