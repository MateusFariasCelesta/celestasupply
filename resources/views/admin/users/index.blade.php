@extends('layouts.app')
@section('title', 'Usuários — CelestaSupply')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="cs-page-title mb-0">Usuários</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
        <i class="bi bi-plus-lg"></i> Novo Usuário
    </a>
</div>

<div class="cs-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Nome</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">E-mail</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Perfil</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">WhatsApp</th>
                    <th style="font-size:12px;font-weight:600;color:#64748B;text-transform:uppercase;letter-spacing:.04em">Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                @php
                    $roleMap = [
                        'requester' => ['Solicitante', 'secondary'],
                        'buyer'     => ['Comprador',   'primary'],
                        'admin'     => ['Admin',        'danger'],
                    ];
                    [$roleLabel, $roleColor] = $roleMap[$u->role] ?? [$u->role, 'secondary'];
                @endphp
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;border-radius:50%;background:#3B82F6;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span style="font-weight:500;font-size:14px">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:14px;color:#475569">{{ $u->email }}</td>
                    <td><span class="badge bg-{{ $roleColor }}">{{ $roleLabel }}</span></td>
                    <td style="font-size:14px;color:#475569">{{ $u->whatsapp_phone ?? '—' }}</td>
                    <td>
                        @if($u->isActive)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Ativo</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inativo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5" style="color:#94A3B8">
                        <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:8px"></i>
                        Nenhum usuário encontrado.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div class="mt-3">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
