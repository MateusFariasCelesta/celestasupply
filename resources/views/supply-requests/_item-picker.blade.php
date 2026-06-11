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

@push('scripts')
<script>
// Inicializar picker: {{ $pickerId }}
document.addEventListener('DOMContentLoaded', function() {
    (function() {
        const picker = document.querySelector('[data-picker-id="{{ $pickerId }}"]');
        if (!picker) return;

    const btn       = picker.querySelector('.js-btn');
    const label     = picker.querySelector('.js-label');
    const hid       = picker.querySelector('.js-id');
    const panel     = picker.querySelector('.js-panel');
    const search    = picker.querySelector('.js-search');
    const list      = picker.querySelector('.js-list');
    const createBtn = picker.querySelector('.js-create-btn');
    const createLabel = picker.querySelector('.js-create-label');

    function render() {
        const query = search.value.toLowerCase();
        list.innerHTML = '';
        const filtered = window.CATALOG.filter(c => c.name.toLowerCase().includes(query));

        filtered.forEach(c => {
            const li = document.createElement('div');
            li.style.cssText = 'padding:6px 10px;cursor:pointer;font-size:12px;color:#1E293B';
            li.textContent = c.name;
            li.addEventListener('mouseenter', () => li.style.background = '#F1F5F9');
            li.addEventListener('mouseleave', () => li.style.background = '');
            li.addEventListener('click', () => {
                hid.value = c.id;
                label.textContent = c.name;
                label.style.color = '#0F172A';
                panel.style.display = 'none';
            });
            list.appendChild(li);
        });

        if (filtered.length === 0 && query.length > 0) {
            createBtn.style.display = 'flex';
            createLabel.textContent = `Criar "${query}"`;
            createBtn.onclick = (e) => {
                e.preventDefault();
                const fd = new FormData();
                fd.append('name', query);
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                fetch(window.ADD_URL, {method: 'POST', body: fd})
                    .then(r => r.json())
                    .then(d => {
                        if (d.id) {
                            hid.value = d.id;
                            label.textContent = d.name;
                            label.style.color = '#0F172A';
                            search.value = '';
                            panel.style.display = 'none';
                            CATALOG.push({id: d.id, name: d.name});
                        }
                    });
            };
        } else {
            createBtn.style.display = 'none';
        }
    }

    search.addEventListener('input', function() {
        const q = search.value.trim();
        render();
        if (q && !window.CATALOG.find(i => i.name.toLowerCase() === q.toLowerCase())) {
            createBtn.style.display = 'flex';
            createLabel.textContent = `Criar "${q}"`;
        } else {
            createBtn.style.display = 'none';
        }
    });

    createBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const name = search.value.trim();
        if (!name) return;

        // Criar item no banco via API
        const fd = new FormData();
        fd.append('name', name);
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        fetch('{{ route('items.inline') }}', {method: 'POST', body: fd})
            .then(r => r.json())
            .then(data => {
                if (data.id && data.name) {
                    // Adiciona item criado ao CATALOG
                    if (!window.CATALOG.find(i => i.id === data.id)) {
                        window.CATALOG.push({ id: data.id, name: data.name });
                    }
                    // Seleciona o item criado
                    hid.value = data.id;
                    label.textContent = data.name;
                    label.style.color = '#0F172A';
                    panel.style.display = 'none';
                    search.value = '';
                }
            })
            .catch(err => console.error('Erro ao criar item:', err));
    });

    btn.addEventListener('click', () => {
        panel.style.display = panel.style.display === 'none' ? '' : 'none';
        if (panel.style.display !== 'none') {
            search.focus();
            render();
        }
    });
    btn.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            panel.style.display = panel.style.display === 'none' ? '' : 'none';
            if (panel.style.display !== 'none') search.focus();
        }
    });
    document.addEventListener('click', (e) => {
        if (!picker.contains(e.target)) panel.style.display = 'none';
    });
    })();
});
</script>
@endpush
