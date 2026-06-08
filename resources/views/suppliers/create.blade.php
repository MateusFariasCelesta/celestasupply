@extends('layouts.app')
@section('title', 'Novo Fornecedor — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Novo Fornecedor</h1>
</div>

<div class="cs-card" style="max-width:480px">
    <form method="POST" action="{{ route('suppliers.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Nome</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Ex: Distribuidora ABC Ltda"
                   required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">
                Contato <span class="text-muted fw-normal">(opcional)</span>
            </label>
            <input type="text" name="contact"
                   class="form-control @error('contact') is-invalid @enderror"
                   value="{{ old('contact') }}"
                   placeholder="Ex: João Silva — (11) 99999-0000">
            @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Criar Fornecedor
            </button>
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
