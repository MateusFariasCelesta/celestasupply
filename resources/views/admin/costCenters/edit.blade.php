@extends('layouts.app')
@section('title', 'Editar Centro de Custo — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.costCenters.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Editar Centro de Custo</h1>
</div>

<div class="cs-card" style="max-width:480px">
    <form method="POST" action="{{ route('admin.costCenters.update', $costCenter) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Código</label>
            <input type="text" class="form-control bg-light" value="{{ $costCenter->id }}" disabled>
            <div class="form-text">O código não pode ser alterado após a criação.</div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Nome</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $costCenter->name) }}"
                   required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Status</label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="isActive" value="1" id="isActive"
                       {{ old('isActive', $costCenter->isActive) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive" style="font-size:14px">Centro de custo ativo</label>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Salvar Alterações
            </button>
            <a href="{{ route('admin.costCenters.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
