<div class="cs-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <h6 class="fw-semibold mb-0" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Itens</h6>
            <a href="{{ route('items.create') }}" target="_blank"
               class="text-muted" style="font-size:12px" title="Abrir catálogo de itens em nova aba">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
        <button type="button" id="btn-add-row" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-plus-lg me-1"></i>Adicionar Linha
        </button>
    </div>

    @error('items')<div class="alert alert-danger py-2 mb-3" style="font-size:13px">{{ $message }}</div>@enderror

    {{-- overflow:visible garante que o dropdown não seja cortado pela tabela --}}
    <div style="overflow: visible">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;color:#64748B;font-weight:600;min-width:260px">Item</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600;width:110px">Quantidade</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600;width:120px">Unidade</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Observação</th>
                    <th style="width:48px"></th>
                </tr>
            </thead>
            <tbody id="items-tbody"></tbody>
        </table>
    </div>
</div>
