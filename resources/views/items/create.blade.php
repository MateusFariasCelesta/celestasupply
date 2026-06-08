@extends('layouts.app')
@section('title', 'Novo Item — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Novo Item</h1>
</div>

<div class="cs-card" style="max-width:480px">
    <form method="POST" action="{{ route('items.store') }}">
        @csrf

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Nome</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Ex: Papel A4 500 folhas"
                   required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Adicionar ao Catálogo
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
