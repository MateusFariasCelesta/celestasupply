@extends('layouts.app')
@section('title', 'Nova Solicitação — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('requests.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Nova Solicitação</h1>
</div>

<form method="POST" action="{{ route('requests.store') }}" enctype="multipart/form-data">
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

    {{-- Anexos --}}
    <div class="cs-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">
                Anexos <span class="fw-normal text-muted normal-case">(opcional)</span>
            </h6>
            <button type="button" id="btn-add-att" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-paperclip me-1"></i>Adicionar Arquivo
            </button>
        </div>
        @error('files.*')
        <div class="alert alert-danger py-2 mb-3 small">{{ $message }}</div>
        @enderror
        <div id="att-container"></div>
        <p id="att-empty" class="text-muted small mb-0"><i class="bi bi-paperclip me-1"></i>Nenhum arquivo adicionado.</p>
    </div>

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

@push('scripts')
<script>
(function () {
    var typeOptions = `<option value="other">Outro</option><option value="quote">Orçamento</option><option value="invoice">Nota Fiscal</option><option value="receipt">Comprovante</option>`;

    function updateEmpty() {
        var empty = document.getElementById('att-container').children.length === 0;
        document.getElementById('att-empty').style.display = empty ? '' : 'none';
    }

    document.getElementById('btn-add-att').addEventListener('click', function () {
        var row = document.createElement('div');
        row.className = 'd-flex gap-2 align-items-center mb-2';
        row.innerHTML = `<select name="file_types[]" class="form-select form-select-sm" style="max-width:160px">${typeOptions}</select>
            <input type="file" name="files[]" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.webp">
            <button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest('div').remove();updateEmpty()"><i class="bi bi-x"></i></button>`;
        document.getElementById('att-container').appendChild(row);
        updateEmpty();
    });

    updateEmpty();
})();
</script>
@endpush
