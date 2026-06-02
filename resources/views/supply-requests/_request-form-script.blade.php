@push('scripts')
<script>
(function () {
    // Catálogo carregado pelo PHP — busca client-side, sem fetch
    const CATALOG = @json($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name])->values());

    let rowCount = 0;

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildRow(idx, data = {}) {
        const tr = document.createElement('tr');
        tr.dataset.rowIdx = idx;
        tr.innerHTML = `
            <td>
                <div style="position:relative">
                    <input type="text"
                           class="form-control form-control-sm js-search"
                           placeholder="Buscar item..."
                           autocomplete="off"
                           value="${esc(data.item_name || '')}">
                    <input type="hidden" name="items[${idx}][item_id]" class="js-id" value="${esc(data.item_id || '')}">
                    <div class="js-drop" style="display:none;position:absolute;top:calc(100% + 2px);left:0;right:0;z-index:200;background:#fff;border:1px solid #E2E8F0;border-radius:8px;box-shadow:0 4px 16px rgba(0,0,0,.12);max-height:220px;overflow-y:auto;min-width:220px"></div>
                </div>
            </td>
            <td><input type="number" name="items[${idx}][quantity]" class="form-control form-control-sm" min="1" value="${esc(data.quantity || 1)}" required></td>
            <td><input type="text"   name="items[${idx}][unit]"     class="form-control form-control-sm" placeholder="un, kg, L…" value="${esc(data.unit  || '')}"></td>
            <td><input type="text"   name="items[${idx}][notes]"    class="form-control form-control-sm" placeholder="Opcional"   value="${esc(data.notes || '')}"></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger js-rm" title="Remover"><i class="bi bi-trash"></i></button></td>
        `;

        wireAuto(tr);

        tr.querySelector('.js-rm').addEventListener('click', function () {
            if (document.querySelectorAll('#items-tbody tr').length > 1) {
                tr.remove();
                syncRemoveBtns();
            }
        });

        return tr;
    }

    function addRow(data = {}) {
        rowCount++;
        document.getElementById('items-tbody').appendChild(buildRow(rowCount, data));
        syncRemoveBtns();
    }

    function syncRemoveBtns() {
        const btns = document.querySelectorAll('#items-tbody .js-rm');
        btns.forEach(b => b.style.display = btns.length > 1 ? '' : 'none');
    }

    function wireAuto(tr) {
        const inp  = tr.querySelector('.js-search');
        const hid  = tr.querySelector('.js-id');
        const drop = tr.querySelector('.js-drop');

        inp.addEventListener('input',   () => { hid.value = ''; render(inp.value); });
        inp.addEventListener('focus',   () => { if (inp.value.trim().length >= 2 && !hid.value) render(inp.value); });
        inp.addEventListener('keydown', e => { if (e.key === 'Escape') hide(); });
        document.addEventListener('click', e => { if (!tr.contains(e.target)) hide(); });

        function hide() { drop.style.display = 'none'; drop.innerHTML = ''; }

        function render(raw) {
            const q = raw.trim();
            if (q.length < 2) { hide(); return; }

            const hits = CATALOG.filter(i => i.name.toLowerCase().includes(q.toLowerCase())).slice(0, 10);
            drop.innerHTML = '';

            if (hits.length) {
                hits.forEach(item => {
                    const d = document.createElement('div');
                    d.style.cssText = 'padding:8px 12px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:8px';
                    d.innerHTML = `<i class="bi bi-box" style="color:#94A3B8;font-size:12px"></i>${esc(item.name)}`;
                    d.addEventListener('mouseenter', () => d.style.background = '#F1F5F9');
                    d.addEventListener('mouseleave', () => d.style.background = '');
                    d.addEventListener('mousedown', e => {
                        e.preventDefault();
                        hid.value = item.id;
                        inp.value = item.name;
                        hide();
                    });
                    drop.appendChild(d);
                });
            } else {
                const d = document.createElement('div');
                d.style.cssText = 'padding:8px 12px;display:flex;align-items:center;gap:8px;flex-wrap:wrap';
                d.innerHTML = `<span style="font-size:13px;color:#64748B">Nenhum resultado.</span>
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size:12px">
                        <i class="bi bi-plus"></i> Adicionar "${esc(q)}"
                    </button>`;
                d.querySelector('button').addEventListener('mousedown', e => {
                    e.preventDefault();
                    // Prefixo "new:" indica ao servidor para criar via firstOrCreate
                    hid.value = 'new:' + q;
                    inp.value = q;
                    // Adiciona ao catálogo local para não aparecer como "não encontrado" se buscar de novo
                    if (!CATALOG.find(i => i.name.toLowerCase() === q.toLowerCase())) {
                        CATALOG.push({ id: 'new:' + q, name: q });
                    }
                    hide();
                });
                drop.appendChild(d);
            }

            drop.style.display = 'block';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('btn-add-row').addEventListener('click', () => addRow());
        const rows = window.__initialItemRows || [];
        rows.length ? rows.forEach(r => addRow(r)) : addRow();
    });
})();
</script>
@endpush
