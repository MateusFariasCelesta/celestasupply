@extends('layouts.app')
@section('title', 'Catálogo de Itens — CelestaSupply')

@section('content')
<div x-data="itemsSearch()" x-init="init()" class="w-100">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="cs-page-title mb-0">Catálogo de Itens</h1>
        <a href="{{ route('items.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Novo Item
        </a>
    </div>

    <div class="cs-card mb-3">
        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-search"></i>
            </span>
            <input
                type="text"
                class="form-control"
                placeholder="Pesquisar itens..."
                x-model="searchQuery"
                @input="updateUrl()"
            >
            <button
                class="btn btn-outline-secondary"
                type="button"
                @click="clearSearch()"
                x-show="searchQuery"
            >
                Limpar
            </button>
        </div>
    </div>

    <div class="cs-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">#</th>
                        <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Nome</th>
                        <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="4" class="text-center py-5" style="color:#94A3B8">
                                <i class="bi bi-box" style="font-size:32px;display:block;margin-bottom:8px"></i>
                                <span x-text="allItems.length === 0 ? 'Nenhum item no catálogo ainda.' : 'Nenhum item encontrado.'"></span>
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in paginatedItems" :key="item.id">
                        <tr>
                            <td style="font-size:13px;color:#94A3B8;width:60px" x-html="getHighlightedId(item)"></td>
                            <td style="font-size:14px;font-weight:500" x-html="getHighlightedName(item)"></td>
                            <td>
                                <span
                                    :class="item.isActive ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle'"
                                    class="badge"
                                    x-text="item.isActive ? 'Ativo' : 'Desativado'"
                                ></span>
                            </td>
                            <td class="text-end">
                                <a :href="`${baseUrl}/${item.id}/edit`" class="btn btn-sm btn-outline-secondary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <template x-if="totalPages > 1">
            <div class="mt-3">
                <nav aria-label="Paginação">
                    <ul class="pagination mb-0">
                        <li :class="{ 'disabled': currentPage === 1 }" class="page-item">
                            <button
                                class="page-link"
                                @click="goToPage(1)"
                                :disabled="currentPage === 1"
                            >
                                ← Primeira
                            </button>
                        </li>
                        <li :class="{ 'disabled': currentPage === 1 }" class="page-item">
                            <button
                                class="page-link"
                                @click="goToPage(currentPage - 1)"
                                :disabled="currentPage === 1"
                            >
                                ← Anterior
                            </button>
                        </li>

                        <template x-for="page in pageNumbers" :key="page">
                            <li :class="{ 'active': currentPage === page }" class="page-item">
                                <button
                                    class="page-link"
                                    @click="goToPage(page)"
                                    x-text="page"
                                ></button>
                            </li>
                        </template>

                        <li :class="{ 'disabled': currentPage === totalPages }" class="page-item">
                            <button
                                class="page-link"
                                @click="goToPage(currentPage + 1)"
                                :disabled="currentPage === totalPages"
                            >
                                Próxima →
                            </button>
                        </li>
                        <li :class="{ 'disabled': currentPage === totalPages }" class="page-item">
                            <button
                                class="page-link"
                                @click="goToPage(totalPages)"
                                :disabled="currentPage === totalPages"
                            >
                                Última →
                            </button>
                        </li>
                    </ul>
                </nav>
                <div class="text-center mt-2" style="color:#94A3B8;font-size:13px">
                    Página <span x-text="currentPage"></span> de <span x-text="totalPages"></span>
                    (<span x-text="filteredItems.length"></span> resultados)
                </div>
            </div>
        </template>
    </div>
</div>

<script>
function itemsSearch() {
    const itemsPerPage = 25;
    const baseUrl = '{{ url("/items") }}';

    return {
        allItems: [],
        searchQuery: '',
        currentPage: 1,
        itemsPerPage: itemsPerPage,
        baseUrl: baseUrl,

        get filteredItems() {
            if (!this.searchQuery.trim()) {
                return this.allItems;
            }

            return this.fuzzyFilter(this.allItems, this.searchQuery.trim());
        },

        parseQuery(query) {
            // Divide a query em tokens (palavras e números)
            const tokens = query.trim().toLowerCase().split(/\s+/).filter(t => t.length > 0);
            const textTokens = [];
            const numberTokens = [];

            tokens.forEach(token => {
                if (/^\d+$/.test(token)) {
                    numberTokens.push(token);
                } else {
                    textTokens.push(token);
                }
            });

            return { textTokens, numberTokens };
        },

        get totalPages() {
            return Math.ceil(this.filteredItems.length / this.itemsPerPage) || 1;
        },

        get paginatedItems() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredItems.slice(start, end);
        },

        get pageNumbers() {
            const pages = [];
            const maxVisible = 5;
            let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let end = Math.min(this.totalPages, start + maxVisible - 1);

            if (end - start + 1 < maxVisible) {
                start = Math.max(1, end - maxVisible + 1);
            }

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },

        fuzzyFilter(items, query) {
            const { textTokens, numberTokens } = this.parseQuery(query);

            const results = items.filter(item => {
                const name = item.name.toLowerCase();
                const id = String(item.id);

                // Verifica se todos os tokens de texto estão no nome
                for (const token of textTokens) {
                    if (!name.includes(token)) {
                        // Tenta fuzzy match se não encontrar substring exata
                        let tokenIndex = 0;
                        for (let i = 0; i < name.length && tokenIndex < token.length; i++) {
                            if (name[i] === token[tokenIndex]) {
                                tokenIndex++;
                            }
                        }
                        if (tokenIndex !== token.length) {
                            return false;
                        }
                    }
                }

                // Verifica se todos os números estão no nome ou ID
                for (const numToken of numberTokens) {
                    const foundInName = name.includes(numToken);
                    const foundInId = id.includes(numToken);

                    if (!foundInName && !foundInId) {
                        return false;
                    }
                }

                return true;
            });

            // Ordena por relevância
            return results.sort((a, b) => {
                const aName = a.name.toLowerCase();
                const bName = b.name.toLowerCase();
                const aId = String(a.id);
                const bId = String(b.id);

                // Prioriza quanto mais tokens encontrados no início
                let aScore = 0;
                let bScore = 0;

                textTokens.forEach(token => {
                    if (aName.startsWith(token)) aScore += 100;
                    if (aName.indexOf(token) !== -1) aScore += 50;
                    if (bName.startsWith(token)) bScore += 100;
                    if (bName.indexOf(token) !== -1) bScore += 50;
                });

                numberTokens.forEach(token => {
                    if (aId.startsWith(token)) aScore += 100;
                    if (aId.includes(token)) aScore += 50;
                    if (aName.includes(token)) aScore += 30;
                    if (bId.startsWith(token)) bScore += 100;
                    if (bId.includes(token)) bScore += 50;
                    if (bName.includes(token)) bScore += 30;
                });

                if (bScore !== aScore) {
                    return bScore - aScore;
                }

                return aName.localeCompare(bName);
            });
        },

        async init() {
            try {
                const response = await fetch('{{ route("items.all") }}');
                if (!response.ok) throw new Error('Erro ao carregar itens');
                this.allItems = await response.json();

                const params = new URLSearchParams(window.location.search);
                this.searchQuery = params.get('search')?.trim() || '';
                const page = parseInt(params.get('page'));
                this.currentPage = !isNaN(page) && page > 0 ? page : 1;
            } catch (error) {
                console.error('Erro ao carregar itens:', error);
                this.allItems = [];
                this.searchQuery = '';
                this.currentPage = 1;
            }
        },

        updateUrl() {
            this.currentPage = 1;
            const params = new URLSearchParams();

            if (this.searchQuery.trim()) {
                params.set('search', this.searchQuery.trim());
            }
            params.set('page', this.currentPage);

            const newUrl = params.toString()
                ? `{{ route('items.index') }}?${params.toString()}`
                : '{{ route("items.index") }}';

            window.history.replaceState({}, '', newUrl);
        },

        goToPage(page) {
            const validPage = parseInt(page);
            if (!isNaN(validPage) && validPage >= 1 && validPage <= this.totalPages) {
                this.currentPage = validPage;
                const params = new URLSearchParams();

                if (this.searchQuery?.trim()) {
                    params.set('search', this.searchQuery.trim());
                }
                params.set('page', this.currentPage);

                const newUrl = params.toString()
                    ? `{{ route('items.index') }}?${params.toString()}`
                    : '{{ route("items.index") }}';

                window.history.replaceState({}, '', newUrl);
                window.scrollTo(0, 0);
            }
        },

        clearSearch() {
            this.searchQuery = '';
            this.currentPage = 1;
            this.updateUrl();
        },

        highlightTokens(text, tokens) {
            if (!tokens || tokens.length === 0) return text;

            let result = text;
            tokens.forEach(token => {
                const regex = new RegExp(`(${token})`, 'gi');
                result = result.replace(regex, '<mark>$1</mark>');
            });
            return result;
        },

        getHighlightedName(item) {
            const query = this.searchQuery.trim();
            if (!query) return item.name;

            const { textTokens, numberTokens } = this.parseQuery(query);
            const allTokens = [...textTokens, ...numberTokens];

            return this.highlightTokens(item.name, allTokens);
        },

        getHighlightedId(item) {
            const query = this.searchQuery.trim();
            if (!query) return String(item.id);

            const { numberTokens } = this.parseQuery(query);

            return this.highlightTokens(String(item.id), numberTokens);
        }
    };
}
</script>

<style>
mark {
    background-color: #fef3c7;
    color: #b45309;
    padding: 2px 4px;
    border-radius: 2px;
    font-weight: 600;
}
</style>
@endsection
