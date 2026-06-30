<div class="cs-card mb-4">
    {{-- Cabeçalho: título + botão desktop --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <h6 class="fw-semibold mb-0" style="font-size:13px;text-transform:uppercase;letter-spacing:.05em;color:#64748B">Itens</h6>
            <a href="{{ route('items.create') }}" target="_blank"
               class="text-muted" style="font-size:12px" title="Abrir catálogo de itens em nova aba">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
        <button type="button" id="btn-add-row" class="btn btn-sm btn-outline-primary d-none d-md-inline-flex">
            <i class="bi bi-plus-lg me-1"></i>Adicionar Linha
        </button>
    </div>

    @error('items')<div class="alert alert-danger py-2 mb-3" style="font-size:13px">{{ $message }}</div>@enderror

    {{-- DESKTOP: Tabela (visível apenas em ≥768px) --}}
    <div class="d-none d-md-block" style="overflow: visible">
        <table class="table align-middle mb-0">
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

    {{-- MOBILE: Lista de cards (visível apenas em <768px) --}}
    <div class="d-block d-md-none" id="mobile-items-section">
        <div id="mobile-items-list">
            <p id="mobile-items-empty" style="font-size:13px;color:#94A3B8;text-align:center;padding:16px 0;margin:0">
                <i class="bi bi-inbox me-1"></i>Nenhum item adicionado.
            </p>
        </div>
        <button type="button" id="btn-add-item-mobile"
                data-bs-toggle="offcanvas" data-bs-target="#offcanvas-item-sheet"
                aria-controls="offcanvas-item-sheet"
                class="btn btn-outline-primary w-100 mt-3"
                style="border-style:dashed;border-radius:10px;padding:12px">
            <i class="bi bi-plus-lg me-2"></i>Adicionar Item
        </button>
    </div>
</div>

@push('styles')
<style>
/* ── Mobile item cards ── */
@media (max-width: 767px) {
    #mobile-items-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .mobile-item-card {
        background: #F8FAFC;
        border: 1px solid #E2E9F4;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        transition: background .15s;
    }

    .mobile-item-card:active {
        background: #F1F5F9;
    }

    .mobile-item-card-body {
        flex: 1;
        min-width: 0;
    }

    .mobile-item-card-name {
        font-size: 14px;
        font-weight: 600;
        color: #0F172A;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 3px;
    }

    .mobile-item-card-meta {
        font-size: 12px;
        color: #64748B;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mobile-item-card-actions {
        display: flex;
        gap: 6px;
        flex-shrink: 0;
        align-items: center;
    }

    .mobile-item-card-btn {
        background: none;
        border: 1px solid #E2E9F4;
        border-radius: 8px;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #64748B;
        font-size: 14px;
        transition: background .15s, color .15s, border-color .15s;
        flex-shrink: 0;
        padding: 0;
    }

    .mobile-item-card-btn:active {
        background: #F1F5F9;
    }

    .mobile-item-card-btn.edit:active {
        color: #3B82F6;
        border-color: #BFDBFE;
        background: #EFF6FF;
    }

    .mobile-item-card-btn.remove:active {
        color: #DC2626;
        border-color: #FECACA;
        background: #FEF2F2;
    }
}

/* Offcanvas z-index management */
.offcanvas-backdrop {
    z-index: 999 !important;
}

#offcanvas-item-sheet {
    z-index: 1050 !important;
}

#offcanvas-item-sheet {
    padding-bottom: max(env(safe-area-inset-bottom), 0px);
}
</style>
@endpush

@push('modals')
<div class="offcanvas offcanvas-bottom"
     tabindex="-1"
     id="offcanvas-item-sheet"
     aria-labelledby="offcanvas-item-sheet-label"
     style="height: auto; max-height: 92vh; border-radius: 16px 16px 0 0; border-top: 1px solid #E2E9F4;">

    <div class="offcanvas-header" style="border-bottom: 1px solid #F1F5F9; padding: 16px 20px 12px; position: relative;">
        <div style="position:absolute;top:8px;left:50%;transform:translateX(-50%);width:36px;height:4px;background:#E2E8F0;border-radius:4px"></div>
        <h5 class="offcanvas-title fw-semibold"
            id="offcanvas-item-sheet-label"
            style="font-size:15px;color:#0F172A">
            Adicionar Item
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>

    <div class="offcanvas-body" style="padding: 20px; overflow-y: auto;">

        <!-- Item picker -->
        <div class="mb-3" id="mobile-picker-wrapper">
            <label class="form-label fw-semibold" style="font-size:13px">
                Item <span class="text-danger">*</span>
            </label>
            <div class="js-picker" id="mobile-item-picker" style="position:relative">
                <div class="js-btn form-control d-flex align-items-center justify-content-between"
                     style="cursor:pointer;user-select:none;padding: 10px 14px" tabindex="0">
                    <span class="js-label"
                          id="mobile-picker-label"
                          style="color:#94A3B8;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                        <i class="bi bi-search me-2" style="font-size:13px"></i>Pesquisar item...
                    </span>
                    <i class="bi bi-chevron-down js-chevron" style="font-size:11px;color:#94A3B8;flex-shrink:0;margin-left:6px"></i>
                </div>
                <input type="hidden" class="js-id" id="mobile-picker-id" value="">

                <div class="js-panel"
                     style="display:none;position:absolute;top:calc(100% + 2px);left:0;right:0;
                            z-index:1060;background:#fff;border:1px solid #E2E8F0;
                            border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.18)">
                    <div style="padding:8px">
                        <input type="text"
                               class="form-control js-search"
                               id="mobile-picker-search"
                               placeholder="Filtrar..."
                               autocomplete="off">
                    </div>
                    <div class="js-list" style="max-height:200px;overflow-y:auto"></div>
                    <div style="border-top:1px solid #E2E8F0;padding:8px 10px">
                        <button type="button" class="js-create-btn"
                                style="display:none;background:none;border:none;padding:0;
                                       cursor:pointer;font-size:13px;color:#3B82F6;
                                       align-items:center;gap:6px">
                            <i class="bi bi-plus-circle"></i>
                            <span class="js-create-label"></span>
                        </button>
                    </div>
                </div>
            </div>
            <div id="mobile-picker-error"
                 style="display:none;font-size:12px;color:#DC2626;margin-top:4px">
                <i class="bi bi-exclamation-triangle-fill"></i> Selecione um item
            </div>
        </div>

        <!-- Qtd + Unidade -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label fw-semibold" style="font-size:13px">
                    Qtd <span class="text-danger">*</span>
                </label>
                <input type="number"
                       id="mobile-qty"
                       class="form-control"
                       min="0.001" step="any"
                       value="1"
                       placeholder="1">
                <div id="mobile-qty-error"
                     style="display:none;font-size:12px;color:#DC2626;margin-top:4px">
                    <i class="bi bi-exclamation-triangle-fill"></i> Quantidade inválida
                </div>
            </div>
            <div class="col-6">
                <label class="form-label fw-semibold" style="font-size:13px">
                    Unidade <span class="text-danger">*</span>
                </label>
                <input type="text"
                       id="mobile-unit"
                       class="form-control"
                       placeholder="un, kg, L…"
                       maxlength="50">
                <div id="mobile-unit-error"
                     style="display:none;font-size:12px;color:#DC2626;margin-top:4px">
                    <i class="bi bi-exclamation-triangle-fill"></i> Obrigatório
                </div>
            </div>
        </div>

        <!-- Observações -->
        <div class="mb-4">
            <label class="form-label fw-semibold" style="font-size:13px">
                Observações <span class="text-muted fw-normal">(opcional)</span>
            </label>
            <input type="text"
                   id="mobile-notes"
                   class="form-control"
                   placeholder="Ex: para reunião de diretoria"
                   maxlength="255">
        </div>

        <!-- Hidden state -->
        <input type="hidden" id="mobile-editing-idx" value="">

        <!-- Confirmar button -->
        <button type="button"
                id="btn-mobile-confirm-item"
                class="btn btn-primary w-100"
                style="padding:13px;font-size:15px;font-weight:600;border-radius:10px">
            <i class="bi bi-check-lg me-2"></i>
            <span id="btn-mobile-confirm-label">Adicionar</span>
        </button>
    </div>
</div>
@endpush
