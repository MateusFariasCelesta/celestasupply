@push('scripts')
<script>
// Expor globalmente para componentes reutilizáveis
window.CATALOG  = @json($items->map(fn($i) => ['id' => $i->id, 'name' => $i->name])->values());
window.ADD_URL  = '{{ route('items.inline') }}';

(function () {
    const CATALOG  = window.CATALOG;
    const ADD_URL  = window.ADD_URL;
    let rowCount = 0;

    function esc(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function buildAttCell(idx, data) {
        const name = data.existing_attachment_name || '';
        const id   = data.existing_item_id || '';
        if (name) {
            return `<td>
                <div class="js-att-cur" style="display:flex;align-items:center;gap:4px;flex-wrap:wrap">
                    <i class="bi bi-paperclip" style="font-size:11px;color:#94A3B8;flex-shrink:0"></i>
                    <span style="font-size:11px;color:#374151;max-width:95px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${esc(name)}">${esc(name)}</span>
                    <button type="button" class="js-att-swap" style="background:none;border:none;padding:0;cursor:pointer;font-size:11px;color:#3B82F6;white-space:nowrap">trocar</button>
                </div>
                <label class="js-att-pick btn btn-sm btn-outline-secondary" style="display:none;font-size:12px;cursor:pointer;padding:2px 8px;margin:0">
                    <i class="bi bi-paperclip me-1"></i>Escolher
                    <input type="file" name="items[${idx}][attachment]" class="js-att-input" accept=".pdf,.jpg,.jpeg,.png" style="display:none">
                </label>
                <span class="js-att-show" style="display:none;font-size:11px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:110px"></span>
                <input type="hidden" name="items[${idx}][existing_item_id]" value="${esc(id)}">
            </td>`;
        }
        return `<td>
            <label class="btn btn-sm btn-outline-secondary" style="font-size:12px;cursor:pointer;padding:2px 8px;margin:0">
                <i class="bi bi-paperclip me-1"></i>Anexar
                <input type="file" name="items[${idx}][attachment]" class="js-att-input" accept=".pdf,.jpg,.jpeg,.png" style="display:none">
            </label>
            <span class="js-att-show" style="font-size:11px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;display:block;max-width:110px"></span>
            <input type="hidden" name="items[${idx}][existing_item_id]" value="">
        </td>`;
    }

    function buildRow(idx, data = {}) {
        const hasItem   = !!(data.item_id);
        const labelText = data.item_name || 'Selecionar item...';
        const labelColor = hasItem ? '#0F172A' : '#94A3B8';

        const tr = document.createElement('tr');
        tr.dataset.rowIdx = idx;
        tr.innerHTML = `
            <td>
                <div class="js-picker" style="position:relative">
                    <div class="js-btn form-control form-control-sm d-flex align-items-center justify-content-between"
                         style="cursor:pointer;user-select:none" tabindex="0">
                        <span class="js-label" style="color:${labelColor};flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(labelText)}</span>
                        <i class="bi bi-chevron-down js-chevron" style="font-size:11px;color:#94A3B8;flex-shrink:0;margin-left:6px"></i>
                    </div>
                    <input type="hidden" name="items[${idx}][item_id]" class="js-id" value="${esc(data.item_id || '')}">

                    <div class="js-panel" style="display:none;position:absolute;top:calc(100% + 2px);left:0;z-index:300;background:#fff;border:1px solid #E2E8F0;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.13);width:100%;min-width:240px">
                        <div style="padding:6px 8px">
                            <input type="text" class="form-control form-control-sm js-search" placeholder="Filtrar..." autocomplete="off">
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
            </td>
            <td>
                <div style="position:relative">
                    <div class="js-qty-err" style="display:none;position:absolute;bottom:calc(100% + 3px);left:0;background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;font-size:11px;padding:2px 7px;border-radius:4px;white-space:nowrap;z-index:50">
                        <i class="bi bi-exclamation-triangle-fill"></i> Quantidade inválida
                    </div>
                    <input type="number" name="items[${idx}][quantity]"
                           class="form-control form-control-sm js-qty" min="0" step="any"
                           value="${esc(data.quantity || 1)}" required>
                </div>
            </td>
            <td>
                <div style="position:relative">
                    <div class="js-unit-err" style="display:none;position:absolute;bottom:calc(100% + 3px);left:0;background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;font-size:11px;padding:2px 7px;border-radius:4px;white-space:nowrap;z-index:50">
                        <i class="bi bi-exclamation-triangle-fill"></i> Unidade obrigatória
                    </div>
                    <input type="text" name="items[${idx}][unit]"
                           class="form-control form-control-sm js-unit" placeholder="un, kg, L…"
                           value="${esc(data.unit || '')}">
                </div>
            </td>
            <td><input type="text" name="items[${idx}][notes]"
                       class="form-control form-control-sm" placeholder="Opcional"
                       value="${esc(data.notes || '')}"></td>
            ${buildAttCell(idx, data)}
            <td><button type="button" class="btn btn-sm btn-outline-danger js-rm" title="Remover">
                    <i class="bi bi-trash"></i>
                </button></td>
        `;

        wirePicker(tr);
        wireAttachment(tr);

        tr.querySelector('.js-rm').addEventListener('click', function () {
            if (document.querySelectorAll('#items-tbody tr').length > 1) {
                tr.remove();
                syncRemoveBtns();
            }
        });

        return tr;
    }

    function wireAttachment(tr) {
        const attInput = tr.querySelector('.js-att-input');
        if (!attInput) return;
        attInput.addEventListener('change', function () {
            const show = tr.querySelector('.js-att-show');
            if (show) {
                show.textContent = this.files[0] ? this.files[0].name : '';
                show.style.display = this.files[0] ? 'block' : 'none';
            }
        });
        const swapBtn = tr.querySelector('.js-att-swap');
        if (swapBtn) {
            swapBtn.addEventListener('click', function () {
                tr.querySelector('.js-att-cur').style.display = 'none';
                tr.querySelector('.js-att-pick').style.display = '';
            });
        }
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

    function wirePicker(tr) {
        const btn       = tr.querySelector('.js-btn');
        const label     = tr.querySelector('.js-label');
        const hid       = tr.querySelector('.js-id');
        const panel     = tr.querySelector('.js-panel');
        const search    = tr.querySelector('.js-search');
        const list      = tr.querySelector('.js-list');
        const createBtn = tr.querySelector('.js-create-btn');
        const createLbl = tr.querySelector('.js-create-label');

        function open() {
            renderList('');
            panel.style.display = 'block';
            search.value = '';
            setTimeout(() => search.focus(), 0);
        }

        function close() {
            panel.style.display = 'none';
        }

        function createNew(name) {
            name = name.trim();
            if (!name) return;
            if (!CATALOG.find(i => i.name.toLowerCase() === name.toLowerCase()))
                CATALOG.push({ id: 'new:' + name, name });
            selectItem({ id: 'new:' + name, name });
        }

        function selectItem(item) {
            hid.value         = String(item.id);
            label.textContent = item.name;
            label.style.color = '#0F172A';
            close();
            addRow();
            const rows = document.querySelectorAll('#items-tbody tr');
            setTimeout(() => rows[rows.length - 1]?.querySelector('.js-btn')?.focus(), 0);
        }

        function renderList(q) {
            const terms = q.trim().toLowerCase().split(/\s+/).filter(Boolean);
            const hits  = terms.length
                ? CATALOG.filter(i => terms.every(t => i.name.toLowerCase().includes(t))).slice(0, 25)
                : CATALOG.slice(0, 25);

            list.innerHTML = '';

            if (!hits.length) {
                list.innerHTML = '<div style="padding:8px 12px;font-size:13px;color:#94A3B8">Nenhum resultado.</div>';
                return;
            }

            hits.forEach(function (item) {
                const d = document.createElement('div');
                d.tabIndex = 0;
                d.style.cssText = 'padding:8px 12px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:8px;outline:none';
                d.innerHTML = `<i class="bi bi-box" style="color:#94A3B8;font-size:12px;flex-shrink:0"></i>${esc(item.name)}`;
                const hi = () => d.style.background = '#F1F5F9';
                const lo = () => d.style.background = '';
                d.addEventListener('mouseenter', hi); d.addEventListener('focus', hi);
                d.addEventListener('mouseleave', lo); d.addEventListener('blur',  lo);
                d.addEventListener('mousedown', e => { e.preventDefault(); selectItem(item); });
                d.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') { e.preventDefault(); selectItem(item); return; }
                    if (e.key === 'Escape') { close(); btn.focus(); return; }
                    const all = Array.from(list.querySelectorAll('[tabindex="0"]'));
                    const idx = all.indexOf(d);
                    if (e.key === 'ArrowDown' || (e.key === 'Tab' && !e.shiftKey)) {
                        e.preventDefault();
                        idx < all.length - 1 ? all[idx + 1].focus() : search.focus();
                    }
                    if (e.key === 'ArrowUp' || (e.key === 'Tab' && e.shiftKey)) {
                        e.preventDefault();
                        idx > 0 ? all[idx - 1].focus() : search.focus();
                    }
                });
                list.appendChild(d);
            });
        }

        // Abre/fecha ao clicar no botão
        btn.addEventListener('click', () => panel.style.display === 'none' ? open() : close());
        btn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
            if (e.key === 'Escape') close();
        });

        // Filtra ao digitar na busca
        search.addEventListener('input', function () {
            const q = search.value.trim();
            renderList(search.value);
            if (q) {
                createLbl.textContent = `Criar "${q}"`;
                createBtn.style.display = 'flex';
            } else {
                createBtn.style.display = 'none';
            }
        });
        search.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { close(); btn.focus(); return; }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const first = list.querySelector('[tabindex="0"]');
                if (first) first.focus();
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                const first = list.querySelector('[tabindex="0"]');
                if (first) first.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                else createNew(search.value);
            }
        });
        createBtn.addEventListener('mousedown', function (e) {
            e.preventDefault();
            createNew(search.value);
        });

        // Fecha ao clicar fora
        document.addEventListener('click', e => { if (!tr.contains(e.target)) close(); });
    }

    // Função pública para inicializar picker em modal (sem estar em uma tabela)
    window.wireModalPicker = function(element) {
        const btn       = element.querySelector('.js-btn');
        const label     = element.querySelector('.js-label');
        const hid       = element.querySelector('.js-id');
        const panel     = element.querySelector('.js-panel');
        const search    = element.querySelector('.js-search');
        const list      = element.querySelector('.js-list');
        const createBtn = element.querySelector('.js-create-btn');
        const createLbl = element.querySelector('.js-create-label');

        function renderList(q) {
            const terms = q.trim().toLowerCase().split(/\s+/).filter(Boolean);
            const hits  = terms.length
                ? CATALOG.filter(i => terms.every(t => i.name.toLowerCase().includes(t))).slice(0, 25)
                : CATALOG.slice(0, 25);

            list.innerHTML = '';

            if (!hits.length) {
                list.innerHTML = '<div style="padding:8px 12px;font-size:13px;color:#94A3B8">Nenhum resultado.</div>';
                return;
            }

            hits.forEach(function (item) {
                const d = document.createElement('div');
                d.style.cssText = 'padding:8px 12px;cursor:pointer;font-size:13px;display:flex;align-items:center;gap:8px';
                d.innerHTML = `<i class="bi bi-box" style="color:#94A3B8;font-size:12px;flex-shrink:0"></i>${esc(item.name)}`;
                d.addEventListener('mouseenter', () => d.style.background = '#F1F5F9');
                d.addEventListener('mouseleave', () => d.style.background = '');
                d.addEventListener('click', () => {
                    hid.value = item.id;
                    label.textContent = item.name;
                    label.style.color = '#0F172A';
                    panel.style.display = 'none';
                });
                list.appendChild(d);
            });
        }

        function open() {
            renderList('');
            panel.style.display = 'block';
            search.value = '';
            setTimeout(() => search.focus(), 0);
        }

        function close() {
            panel.style.display = 'none';
        }

        btn.addEventListener('click', () => panel.style.display === 'none' ? open() : close());
        btn.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); open(); }
            if (e.key === 'Escape') close();
        });

        search.addEventListener('input', function () {
            const q = search.value.trim();
            renderList(search.value);
            if (q && !CATALOG.find(i => i.name.toLowerCase() === q.toLowerCase())) {
                createLbl.textContent = `Criar "${q}"`;
                createBtn.style.display = 'flex';
            } else {
                createBtn.style.display = 'none';
            }
        });
        search.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { close(); btn.focus(); return; }
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const first = list.querySelector('[tabindex="0"]');
                if (first) first.focus();
                return;
            }
            if (e.key === 'Enter') {
                e.preventDefault();
                const name = search.value.trim();
                if (!name) return;
                const fd = new FormData();
                fd.append('name', name);
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                fetch(ADD_URL, {method: 'POST', body: fd})
                    .then(r => r.json())
                    .then(data => {
                        if (data.id && data.name) {
                            if (!CATALOG.find(i => i.id === data.id)) {
                                CATALOG.push({id: data.id, name: data.name});
                            }
                            hid.value = data.id;
                            label.textContent = data.name;
                            label.style.color = '#0F172A';
                            search.value = '';
                            panel.style.display = 'none';
                        }
                    })
                    .catch(err => console.error('Erro ao criar item:', err));
            }
        });

        createBtn.addEventListener('mousedown', function (e) {
            e.preventDefault();
            const name = search.value.trim();
            if (!name) return;
            const fd = new FormData();
            fd.append('name', name);
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            fetch(ADD_URL, {method: 'POST', body: fd})
                .then(r => r.json())
                .then(data => {
                    if (data.id && data.name) {
                        if (!CATALOG.find(i => i.id === data.id)) {
                            CATALOG.push({id: data.id, name: data.name});
                        }
                        hid.value = data.id;
                        label.textContent = data.name;
                        label.style.color = '#0F172A';
                        search.value = '';
                        panel.style.display = 'none';
                    }
                })
                .catch(err => console.error('Erro ao criar item:', err));
        });

        document.addEventListener('click', e => { if (!element.contains(e.target)) close(); });
    };

    document.addEventListener('DOMContentLoaded', function () {
        // Valida e limpa antes de submeter
        document.getElementById('items-tbody')?.closest('form')?.addEventListener('submit', function (e) {
            // Limpa alertas anteriores
            document.querySelectorAll('#items-tbody .js-qty-err, #items-tbody .js-unit-err').forEach(el => el.style.display = 'none');
            document.querySelectorAll('#items-tbody .js-qty, #items-tbody .js-unit').forEach(el => el.classList.remove('is-invalid'));

            let hasError = false;
            document.querySelectorAll('#items-tbody tr').forEach(function (row) {
                const hasItem = !!row.querySelector('.js-id')?.value;
                if (!hasItem) return;

                const qtyInp  = row.querySelector('.js-qty');
                const unitInp = row.querySelector('.js-unit');
                const qty     = parseFloat(qtyInp?.value);

                if (isNaN(qty) || qty <= 0) {
                    qtyInp.classList.add('is-invalid');
                    row.querySelector('.js-qty-err').style.display = 'block';
                    hasError = true;
                }
                if (!unitInp?.value.trim()) {
                    unitInp.classList.add('is-invalid');
                    row.querySelector('.js-unit-err').style.display = 'block';
                    hasError = true;
                }
            });

            if (hasError) {
                e.preventDefault();
                e.stopImmediatePropagation(); // impede o handler global de desabilitar os botões
                return;
            }

            // Remove linhas sem item selecionado
            document.querySelectorAll('#items-tbody tr').forEach(function (row) {
                if (!row.querySelector('.js-id')?.value) row.remove();
            });
        });

        document.getElementById('btn-add-row').addEventListener('click', () => addRow());

        const rows = window.__initialItemRows || [];
        rows.length ? rows.forEach(addRow) : addRow();
    });
})();
</script>
@endpush
