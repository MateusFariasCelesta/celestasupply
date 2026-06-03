@extends('layouts.app')
@section('title', 'Novo Centro de Custo — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.costCenters.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Novo Centro de Custo</h1>
</div>

<div class="cs-card" style="max-width:480px">
    <form method="POST" action="{{ route('admin.costCenters.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Código</label>
            <input type="text" name="id"
                   class="form-control @error('id') is-invalid @enderror"
                   value="{{ old('id') }}"
                   required autofocus>
            @error('id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">Nome</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Ex: Tecnologia da Informação"
                   required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Criar Centro de Custo

            </button>
            <a href="{{ route('admin.costCenters.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
