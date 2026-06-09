# Backlog — CelestaSupply

> Última atualização: 2026-06-03
> Branch ativa: `dev`

---

## ÉPICO 1 — Setup & Infraestrutura ✅

- [x] Criar projeto Laravel 11
- [x] Configurar `.env` (DB, Mail, Queue, Storage)
- [x] Instalar e configurar Laravel Breeze
- [x] Configurar Laravel Queue (driver database)
- [x] Configurar Laravel Storage (disco `local`, root em `storage/app/private`)
- [x] Configurar Bootstrap 5.3.3 + Alpine.js no layout
- [x] Criar layout base Blade (`layouts/app.blade.php`) com navbar por role, toast system e progress bar de navegação
- [x] Configurar fuso horário `America/Sao_Paulo` e locale `pt_BR`

---

## ÉPICO 2 — Autenticação ✅

- [x] Personalizar migration `users`: adicionar `role`, `isActive`, `phone`
- [x] Atualizar Model `User`: `$fillable`, casts, helpers `isAdmin()`, `isBuyer()`, `isRequester()`, `isBuyerOrAdmin()`
- [x] Remover rota de auto-cadastro (register)
- [x] Blade: tela de login (`/login`) com checkbox "Lembrar-me"
- [x] Blade: tela de recuperação de senha
- [x] Blade: tela de redefinição de senha

---

## ÉPICO 3 — Admin: Usuários ✅

- [x] Policy: `UserPolicy` (somente admin)
- [x] Controller: `Admin\UserController` (index, create, store, edit, update, toggleActive)
- [x] Blade: `admin/users/index` — listagem com status ativo/inativo
- [x] Blade: `admin/users/create` — formulário (name, email, role, phone, password)
- [x] Blade: `admin/users/edit`
- [x] Rota: `PATCH /admin/users/{id}/toggleActive`

---

## ÉPICO 4 — Admin: Centros de Custo ✅

- [x] Migration: `cost_centers` (id manual UNIQUE, name, isActive, timestamps, softDeletes)
- [x] Model: `CostCenter` — PK sem auto-increment
- [x] Policy: `CostCenterPolicy` (somente admin)
- [x] Controller: `Admin\CostCenterController` (index, create, store, edit, update, toggleActive)
- [x] Blade: `admin/costCenters/index`, `create`, `edit`
- [x] Rota: `PATCH /admin/costCenters/{id}/toggleActive`

---

## ÉPICO 5 — Fornecedores ✅

- [x] Migration: `suppliers` (name, contact, isActive, timestamps, softDeletes)
- [x] Model: `Supplier`
- [x] Policy: `SupplierPolicy` (buyer e admin)
- [x] Controller: `SupplierController` (index, create, store, edit, update, toggleActive)
- [x] Blade: `suppliers/index`, `create`, `edit`
- [x] Rota: `PATCH /suppliers/{id}/toggleActive`

---

## ÉPICO 6 — Catálogo de Itens ✅

- [x] Migration: `items` (name unique, timestamps, softDeletes)
- [x] Model: `Item`
- [x] Policy: `ItemPolicy`
- [x] Controller: `ItemController` (search/autocomplete, store, update)
- [x] Rota: `GET /lookup/items?q=` — autocomplete (mín. 2 caracteres)
- [x] Rota: `POST /lookup/items` — criação inline
- [x] Lógica: `firstOrCreate` ao adicionar item à solicitação

---

## ÉPICO 7 — Solicitações: Fluxo do Solicitante ✅

- [x] Migration: `supply_requests` (todos os campos, softDeletes)
- [x] Migration: `supply_request_items` (todos os campos, softDeletes)
- [x] Model: `SupplyRequest` — relacionamentos, SoftDeletes, observer
- [x] Model: `SupplyRequestItem` — relacionamentos com `SupplyRequest`, `Item`, `Supplier`
- [x] Observer: `SupplyRequestObserver` — gera `name` no formato `SC-NNNN` ao criar
- [x] Enum: `RequestStatus` — `draft`, `pending`, `inProgress`, `completed`, `cancelRequested`, `cancelled`
- [x] Enum: `ItemStatus` — `pending`, `quoting`, `awaitingPayment`, `awaitingDelivery`, `received`, `cancelRequested`, `cancelled`
- [x] Enum: `Urgency` — `low`, `medium`, `high`
- [x] Policy: `SupplyRequestPolicy`
- [x] Controller: `SupplyRequestController` (index, create, store, show, edit, update, submit, cancelRequest)
- [x] Blade: `supply-requests/index` — listagem com busca full-text, filtros client-side (status multi-select, urgência, centro de custo, solicitante, período) e ordenação por coluna
- [x] Blade: `supply-requests/create` — formulário com header + itens dinâmicos (Alpine.js)
- [x] Blade: `supply-requests/show` — página de detalhe
- [x] Blade: `supply-requests/edit` — edição de rascunho

---

## ÉPICO 8 — Solicitações: Gestão do Comprador/Admin ✅

- [x] Serviço: `RequestStatusService` — centraliza lógica de transição e registra em `request_status_history`
- [x] Controller: `RequestManagementController` (advanceStatus, jumpStatus, cancelDirect, approveCancellation, refuseCancellation)
- [x] Rota: `POST /requests/{id}/advance-status`
- [x] Rota: `POST /requests/{id}/jump-status`
- [x] Rota: `POST /requests/{id}/cancel`
- [x] Rota: `POST /requests/{id}/approve-cancellation`
- [x] Rota: `POST /requests/{id}/refuse-cancellation`
- [x] Fluxo de status: `draft → pending → inProgress → completed`
- [x] Fluxo de cancelamento: `cancelRequested → cancelled` (aprovado) ou volta ao status anterior (recusado)

---

## ÉPICO 9 — Itens: Ações do Comprador ✅

- [x] Policy: `RequestItemPolicy` (updateStatus, registerDelivery, setSupplier, cancel, requestCancellation, approveCancellation, refuseCancellation)
- [x] Controller: `RequestItemController`
- [x] Rota: `PATCH /requests/{id}/items/{itemId}/status` — avançar status do item
- [x] Rota: `PATCH /requests/{id}/items/{itemId}/jump-status` — pular status do item
- [x] Rota: `PATCH /requests/{id}/items/{itemId}/supplier` — vincular fornecedor
- [x] Rota: `DELETE /requests/{id}/items/{itemId}` — cancelar item (cancelReason obrigatório)
- [x] Rota: `POST /requests/{id}/items/{itemId}/request-cancellation`
- [x] Rota: `POST /requests/{id}/items/{itemId}/approve-cancellation`
- [x] Rota: `POST /requests/{id}/items/{itemId}/refuse-cancellation`
- [x] Lógica de entrega: `delivered_quantity` acumulativo, saldo calculado na view

---

## ÉPICO 10 — Arquivos ✅

- [x] Migration: `item_attachments` (`supply_request_item_id` UNIQUE, sem softDeletes)
- [x] Migration: `request_attachments` (type enum: quote, invoice, receipt, other — sem softDeletes)
- [x] Migration: `external_orders` (order_number unsignedInteger exibido como `0001`, sem softDeletes)
- [x] Model: `ItemAttachment`, `RequestAttachment`, `ExternalOrder`
- [x] Enum: `AttachmentType` — `quote`, `invoice`, `receipt`, `other`
- [x] Policy: `ItemAttachmentPolicy`, `RequestAttachmentPolicy`, `ExternalOrderPolicy`
- [x] Serviço: `FileUploadService` — valida tipo (PDF/JPG/PNG), tamanho (máx 10 MB), salva em `storage/app/private/`
- [x] Controller: `ItemAttachmentController` — store (substitui existente), destroy, download, view (inline)
- [x] Controller: `RequestAttachmentController` — store, destroy, download, view
- [x] Controller: `ExternalOrderController` — store, destroy, download, view
- [x] Arquivos salvos em: `attachments/request-items/{id}`, `attachments/requests/{id}`, `attachments/external-orders/{id}`
- [x] Hard delete (sem SoftDeletes) — arquivo físico removido junto com o registro
- [x] Visualização inline via `response()->file()` (Content-Disposition: inline)
- [x] Seção "Pedidos" e "Anexos da Solicitação" na página de detalhe

---

## ÉPICO 11 — Timeline & Histórico ✅

- [x] Migration: `request_status_history` (sem softDeletes — log imutável)
- [x] Model: `RequestStatusHistory`
- [x] Registro automático via `RequestStatusService` a cada mudança de status
- [x] Timestamp sempre reflete a **última** vez que o status foi atingido (não a primeira)
- [x] Componente Blade: `<x-status-timeline :supply-request="$sr">` — stepper horizontal com 4 etapas (draft → pending → inProgress → completed)
- [x] Indicador visual de cancelamento (banner vermelho com motivo, responsável e data)
- [x] Log de histórico abaixo do stepper (de → para, quem alterou, quando)

---

## ÉPICO 12 — Notificações por E-mail

> Sem notificações in-app. Todas via e-mail usando `Mail::queue()`.

- [ ] Criar Mailable: `RequestSubmittedMail` — destinatários: todos os buyers e admins
- [ ] Criar Mailable: `RequestStatusAdvancedMail` — destinatário: solicitante
- [ ] Criar Mailable: `CancellationRequestedMail` — destinatários: todos os buyers e admins
- [ ] Criar Mailable: `CancellationDecidedMail` — destinatário: solicitante
- [ ] Criar Mailable: `RequestCompletedMail` — destinatário: solicitante
- [ ] Criar Mailable: `DeliveryRegisteredMail` — destinatário: solicitante
- [ ] Disparar cada Mailable via `Mail::queue()` dentro do `RequestStatusService` e controllers correspondentes
- [ ] Configurar `.env`: dados SMTP + `QUEUE_CONNECTION=database`
- [ ] Garantir que falha no envio não bloqueia a requisição (queue isola o erro)

---

## ÉPICO 13 — Dashboard ✅

- [x] Controller: `DashboardController` — dados condicionais por `role`
- [x] Blade: `dashboard/index.blade.php` com renderização por role

**Solicitante:**
- [x] Cards de contagem por status (Pendente, Em Andamento, Concluído, Cancel. Solicitado, Cancelado, Rascunho)
- [x] Tabela das últimas 10 solicitações com link para detalhe

**Comprador / Admin:**
- [x] Card: Pendentes de Ação (azul)
- [x] Card: Cancelamentos Pendentes (vermelho)
- [x] Card: Urgentes em Aberto (vermelho)
- [x] Visão geral por status com barras de progresso proporcionais
- [x] Gráfico de barras: solicitações por mês — últimos 12 meses (Chart.js)

**Admin:**
- [x] Gráfico de linha: valor por mês (exibido apenas se coluna `total_price` existir)
- [x] Tabela: valor por centro de custo

---

## ÉPICO 14 — Relatórios (Admin e Comprador) ✅

- [x] Botões Excel e PDF diretamente na página de solicitações (sem página separada)
- [x] Export respeita filtros ativos: busca, status, urgência, centro de custo, solicitante, período
- [x] Rascunhos excluídos de todos os exports
- [x] Excel: logo, separador azul, título dinâmico, barra de filtros, resumo mesclado, tabela com AutoFilter e zebra
- [x] PDF: logo, título dinâmico, barra de filtros, resumo com colunas equidistantes, tabela, rodapé
- [x] Título adaptado ao filtro (ex.: "Relatório de Solicitações Concluídas — 01/05/2026 a 31/05/2026")
- [x] Instalar `maatwebsite/excel` + `barryvdh/laravel-dompdf`
- [x] Rotas: `GET /reports/export/excel` e `GET /reports/export/pdf`

---

## ÉPICO 15 — Página de Detalhe (`/requests/{id}`) ✅

> Agrega componentes de vários épicos.

- [x] Cabeçalho: código SC-NNNN, título, centro de custo, solicitante, data, urgência, status
- [x] Componente `<x-status-timeline>` (Épico 11)
- [x] Seção "Pedidos" — lista com número (formato 0001), notas e botões ver/download (Épico 10)
- [x] Seção "Anexos da Solicitação" — tipo (badge), tamanho, ver/download (Épico 10)
- [x] Tabela de itens: status individual, fornecedor, quantidade pedida, quantidade entregue, restante, anexo
- [x] Histórico de entregas por item (expansível inline — ícone relógio no nome do item)
- [x] Ações do solicitante: botão "Solicitar Cancelamento" com modal + motivo obrigatório
- [x] Ações do solicitante: botão "Confirmar Recebimento" visível quando `status = inProgress` e todos os itens resolvidos
- [x] Ações do comprador: avançar status da solicitação ("Iniciar Atendimento" / "Confirmar Conclusão")
- [x] Ações do comprador: aprovar / recusar cancelamento
- [x] Ações do comprador: registrar entrega por item (modal com quantidade + observações, auto-avança para Recebido ao completar)
- [x] Ações do comprador: vincular fornecedor ao item
- [x] Ações do comprador: cancelar item individualmente (com motivo)
- [x] Toast de feedback após cada ação (sistema já implementado no layout)
