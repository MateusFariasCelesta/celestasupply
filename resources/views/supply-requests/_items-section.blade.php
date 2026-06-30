<div class="cs-card mb-4">
    {{-- Cabeçalho --}}
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

    {{-- Items Container - Tabela em desktop, cards em mobile --}}
    <div class="table-responsive" style="overflow: visible">
        <table class="table align-middle mb-0" id="items-table">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;color:#64748B;font-weight:600;min-width:260px">Item</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600;width:110px">Quantidade</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600;width:120px">Unidade</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600">Observação</th>
                    <th style="font-size:12px;color:#64748B;font-weight:600;width:130px">Anexo</th>
                    <th style="width:48px"></th>
                </tr>
            </thead>
            <tbody id="items-tbody"></tbody>
        </table>
    </div>

    <p id="items-empty" style="font-size:13px;color:#94A3B8;text-align:center;padding:16px 0;margin:0;display:none">
        <i class="bi bi-inbox me-1"></i>Nenhum item adicionado.
    </p>
</div>

@push('styles')
<style>
/* Mobile: Converter tabela em cards sem scroll horizontal */
@media (max-width: 767px) {
    .table-responsive {
        overflow: visible !important;
    }

    #items-table {
        display: block !important;
        border: none;
        width: 100%;
    }

    #items-table thead {
        display: none !important;
    }

    #items-table tbody {
        display: block !important;
    }

    #items-table tr {
        display: grid;
        grid-template-columns: 1fr 70px 70px 34px;
        border: 1px solid #E2E9F4;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #F8FAFC;
        width: 100%;
        gap: 8px;
        align-items: flex-start;
    }

    #items-table td {
        display: grid;
        grid-template-columns: auto 1fr;
        padding: 0 !important;
        border: none !important;
        margin: 0 !important;
        min-height: auto;
        word-break: break-word;
        gap: 4px;
        align-items: center;
    }

    /* Item: linha 1, full width */
    #items-table td:nth-child(1) {
        grid-column: 1 / -1;
        grid-template-columns: auto 1fr;
    }

    /* Quantidade: linha 2, coluna 1 */
    #items-table td:nth-child(2) {
        grid-column: 1;
        grid-row: 2;
        grid-template-columns: auto 1fr;
    }

    /* Unidade: linha 2, coluna 2 */
    #items-table td:nth-child(3) {
        grid-column: 2;
        grid-row: 2;
        grid-template-columns: auto 1fr;
    }

    /* Observação: linha 3, full width */
    #items-table td:nth-child(4) {
        grid-column: 1 / -1;
    }

    /* Anexo: linha 4, full width */
    #items-table td:nth-child(5) {
        grid-column: 1 / -1;
    }

    /* Botão remove: linha 2, coluna 4 (pequeno) */
    #items-table td:nth-child(6) {
        grid-column: 4;
        grid-row: 2;
        justify-content: flex-start;
    }

    #items-table td:nth-child(6) button {
        width: auto !important;
        padding: 4px 6px !important;
        font-size: 12px !important;
        min-width: 34px;
        height: 34px;
    }

    #items-table td:before {
        content: attr(data-label);
        display: block;
        font-weight: 600;
        font-size: 10px;
        text-transform: uppercase;
        color: #64748B;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    #items-table .form-control,
    #items-table .form-select {
        font-size: 13px !important;
        padding: 6px 8px !important;
        width: 100% !important;
        max-width: none !important;
    }

    #items-table .btn {
        width: 100%;
    }

    #items-table .d-flex {
        flex-direction: column !important;
        gap: 4px !important;
    }

    #items-table .d-flex > * {
        width: 100% !important;
    }
}
</style>
@endpush
