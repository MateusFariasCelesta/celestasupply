@extends('layouts.app')
@section('title', 'Editar Usuário — CelestaSupply')

@section('content')
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary" title="Voltar">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h1 class="cs-page-title mb-0">Editar Usuário</h1>
</div>

<div class="cs-card" style="max-width:600px">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">Nome</label>
            <input type="text" name="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:13px">E-mail</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold" style="font-size:13px">
                    Nova Senha <span class="text-muted fw-normal">(deixe em branco para não alterar)</span>
                </label>
                <input type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold" style="font-size:13px">Confirmar Nova Senha</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold" style="font-size:13px">Perfil</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="requester" {{ old('role', $user->role) === 'requester' ? 'selected' : '' }}>Solicitante</option>
                    <option value="buyer"     {{ old('role', $user->role) === 'buyer'     ? 'selected' : '' }}>Comprador</option>
                    <option value="admin"     {{ old('role', $user->role) === 'admin'     ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold" style="font-size:13px">
                    WhatsApp <span class="text-muted fw-normal">(opcional)</span>
                </label>
                <input type="text" name="whatsapp_phone"
                       class="form-control @error('whatsapp_phone') is-invalid @enderror"
                       value="{{ old('whatsapp_phone', $user->whatsapp_phone) }}"
                       placeholder="(11) 99999-9999 ou +1 555 0000"
                       oninput="phoneMask(this)"
                       inputmode="numeric">
                @error('whatsapp_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex gap-2 mt-1">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i>Salvar Alterações
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
