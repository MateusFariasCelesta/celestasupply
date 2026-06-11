{{-- Item picker dropdown com busca e criação inline --}}
<div class="js-picker" data-picker-id="{{ $pickerId ?? 'picker_' . uniqid() }}" style="position:relative">
    <div class="js-btn form-control form-control-sm d-flex align-items-center justify-content-between"
         style="cursor:pointer;user-select:none" tabindex="0">
        <span class="js-label" style="color:#94A3B8;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $label ?? 'Selecionar item…' }}</span>
        <i class="bi bi-chevron-down js-chevron" style="font-size:11px;color:#94A3B8;flex-shrink:0;margin-left:6px"></i>
    </div>
    <input type="hidden" name="{{ $name ?? 'item_id' }}" class="js-id" value="">

    <div class="js-panel" style="display:none;position:absolute;top:calc(100% + 2px);left:0;z-index:3000;background:#fff;border:1px solid #E2E8F0;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.13);width:100%;min-width:240px">
        <div style="padding:6px 8px">
            <input type="text" class="form-control form-control-sm js-search" placeholder="Filtrar…" autocomplete="off">
        </div>
        <div class="js-list" style="max-height:180px;overflow-y:auto"></div>
        <div style="border-top:1px solid #E2E8F0;padding:6px 10px">
            <button type="button" class="js-create-btn"
                    style="display:none;background:none;border:none;padding:0;cursor:pointer;font-size:12px;color:#3B82F6;align-items:center;gap:5px">
                <i class="bi bi-plus-circle"></i>
                <span class="js-create-label"></span>
            </button>
        </div>
    </div>
</div>

{{-- Script de inicialização será chamado manualmente quando necessário --}}
