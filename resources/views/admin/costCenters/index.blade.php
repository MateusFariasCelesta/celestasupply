@extends('layouts.app')
@section('title', 'Centros de Custo — CelestaSupply')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Centros de Custo</h1>
    <a href="{{ route('admin.costCenters.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Novo Centro de Custo
    </a>
</div>

<div class="cs-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Código</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Nome</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($costCenters as $cc)
                <tr>
                    <td>
                        <span class="badge bg-light text-dark border" style="font-size:13px;font-weight:600;letter-spacing:.03em">
                            {{ $cc->id }}
                        </span>
                    </td>
                    <td style="font-size:14px;font-weight:500">{{ $cc->name }}</td>
                    <td>
                        @if($cc->isActive)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Ativo</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inativo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.costCenters.edit', $cc) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5" style="color:#94A3B8">
                        <i class="bi bi-building" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Nenhum centro de custo encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($costCenters->hasPages())
    <div class="mt-3">
        {{ $costCenters->links() }}
    </div>
    @endif
</div>
@endsection
