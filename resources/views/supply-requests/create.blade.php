@extends('layouts.app')
@section('title', 'Nova Solicitação — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Nova Solicitação</h1>
</div>

<form method="POST" action="{{ route('requests.store') }}">
    @csrf

    <div class="cs-card mb-4">
        <h6 class="fw-semibold mb-3" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Informações Gerais</h6>

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Título</label>
            <input type="text" name="title"
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}"
                   placeholder="Ex: Materiais de escritório Q3"
                   required autofocus>
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="font-size:13px">Centro de Custo</label>
                <select name="cost_center_id" class="form-select @error('cost_center_id') is-invalid @enderror" required>
                    <option value="">Selecionar...</option>
                    @foreach($costCenters as $cc)
                    <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>
                        {{ $cc->id }} — {{ $cc->name }}
                    </option>
                    @endforeach
                </select>
                @error('cost_center_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold" style="font-size:13px">Urgência</label>
                <select name="urgency" class="form-select @error('urgency') is-invalid @enderror" required>
                    @foreach(\App\Enums\Urgency::cases() as $u)
                    <option value="{{ $u->value }}" {{ old('urgency', 'low') === $u->value ? 'selected' : '' }}>
                        {{ $u->label() }}
                    </option>
                    @endforeach
                </select>
                @error('urgency')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mt-3">
            <label class="form-label fw-semibold" style="font-size:13px">
                Observações <span class="text-muted fw-normal">(opcional)</span>
            </label>
            <textarea name="notes" class="form-control" rows="2"
                      placeholder="Informações adicionais...">{{ old('notes') }}</textarea>
        </div>
    </div>

    @include('supply-requests._items-section')

    <div class="d-flex gap-2">
        <button type="submit" name="action" value="draft" class="btn btn-outline-secondary">
            <i class="bi bi-floppy me-1"></i>Salvar Rascunho
        </button>
        <button type="submit" name="action" value="submit" class="btn btn-primary">
            <i class="bi bi-send me-1"></i>Enviar Solicitação
        </button>
        <a href="{{ route('requests.index') }}" class="btn btn-link text-muted ms-1">Cancelar</a>
    </div>
</form>
@endsection

@include('supply-requests._request-form-script')
