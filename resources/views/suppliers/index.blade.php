@extends('layouts.app')
@section('title', 'Fornecedores — CelestaSupply')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Fornecedores</h1>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Novo Fornecedor
    </a>
</div>

<div class="cs-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Nome</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Contato</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td style="font-size:14px;font-weight:500">{{ $supplier->name }}</td>
                    <td style="font-size:14px;color:#64748B">{{ $supplier->contact ?? '—' }}</td>
                    <td>
                        @if($supplier->isActive)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Ativo</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inativo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5" style="color:#94A3B8">
                        <i class="bi bi-truck" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Nenhum fornecedor encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($suppliers->hasPages())
    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>
    @endif
</div>
@endsection
