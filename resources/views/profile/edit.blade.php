@extends('layouts.app')

@section('content')
<div class="cs-page-title">Meu Perfil</div>

<div class="row g-4" style="max-width:760px">

    {{-- ── Informações da conta ── --}}
    <div class="col-12">
        <div class="cs-card">
            <h6 class="fw-semibold mb-1" style="color:#1E293B">Informações da conta</h6>
            <p class="mb-4" style="font-size:13px;color:#64748B">Atualize seu nome e endereço de e-mail.</p>

            @if(session('status') === 'profile-updated')
                <div class="alert alert-success py-2 px-3 mb-3" style="font-size:13px;border-radius:8px">
                    <i class="bi bi-check-circle-fill me-1"></i> Perfil atualizado com sucesso.
                </div>
            @endif

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input id="name" name="name" type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input id="email" name="email" type="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required autocomplete="email">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="whatsapp_phone" class="form-label">WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-whatsapp" style="color:#25D366"></i></span>
                        <input id="whatsapp_phone" name="whatsapp_phone" type="tel"
                               class="form-control @error('whatsapp_phone') is-invalid @enderror"
                               value="{{ old('whatsapp_phone', $user->whatsapp_phone) }}"
                               placeholder="5594999999999" autocomplete="tel"
                               oninput="phoneMask(this)">
                        @error('whatsapp_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-text">Com DDI. Ex: 5594999999999</div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-check-lg me-1"></i> Salvar alterações
                </button>
            </form>
        </div>
    </div>

    {{-- ── Alterar senha ── --}}
    <div class="col-12">
        <div class="cs-card">
            <h6 class="fw-semibold mb-1" style="color:#1E293B">Alterar senha</h6>
            <p class="mb-4" style="font-size:13px;color:#64748B">Use uma senha longa e aleatória para manter sua conta segura.</p>

            @if(session('status') === 'password-updated')
                <div class="alert alert-success py-2 px-3 mb-3" style="font-size:13px;border-radius:8px">
                    <i class="bi bi-check-circle-fill me-1"></i> Senha atualizada com sucesso.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="current_password" class="form-label">Senha atual</label>
                    <input id="current_password" name="current_password" type="password"
                           class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                           autocomplete="current-password">
                    @error('current_password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Nova senha</label>
                    <input id="password" name="password" type="password"
                           class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                           autocomplete="new-password">
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-shield-lock me-1"></i> Atualizar senha
                </button>
            </form>
        </div>
    </div>

    {{-- ── Excluir conta ── --}}
    <div class="col-12">
        <div class="cs-card" style="border-color:#FECACA">
            <h6 class="fw-semibold mb-1" style="color:#DC2626">Excluir conta</h6>
            <p class="mb-4" style="font-size:13px;color:#64748B">
                Após excluir a conta, todos os dados serão permanentemente removidos. Esta ação não pode ser desfeita.
            </p>

            <button type="button" class="btn btn-outline-danger btn-sm px-4"
                    data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                <i class="bi bi-trash me-1"></i> Excluir minha conta
            </button>
        </div>
    </div>

</div>

@endsection

@push('modals')
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:14px;border:none">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-semibold" id="deleteAccountModalLabel" style="color:#DC2626">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p style="font-size:14px;color:#475569">
                    Esta ação é permanente e irreversível. Confirme sua senha para continuar.
                </p>

                <form method="POST" action="{{ route('profile.destroy') }}" id="deleteAccountForm">
                    @csrf
                    @method('DELETE')

                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Senha</label>
                        <input id="delete_password" name="password" type="password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               placeholder="Digite sua senha" autocomplete="current-password">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="deleteAccountForm" class="btn btn-outline-danger btn-sm px-4">
                    <i class="bi bi-trash me-1"></i> Excluir conta
                </button>
            </div>
        </div>
    </div>
</div>

@if($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('deleteAccountModal')).show();
    });
</script>
@endif
@endpush
