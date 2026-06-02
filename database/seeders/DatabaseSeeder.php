<?php

namespace Database\Seeders;

use App\Enums\ItemStatus;
use App\Enums\RequestStatus;
use App\Enums\Urgency;
use App\Models\CostCenter;
use App\Models\Item;
use App\Models\RequestStatusHistory;
use App\Models\Supplier;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuários ──────────────────────────────────────────────
        $admin = User::create([
            'name'           => 'Carlos Admin',
            'email'          => 'admin@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'admin',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0001',
        ]);

        $buyer1 = User::create([
            'name'           => 'Ana Compradora',
            'email'          => 'buyer1@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'buyer',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0002',
        ]);

        $buyer2 = User::create([
            'name'           => 'Rafael Compras',
            'email'          => 'buyer2@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'buyer',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0003',
        ]);

        $req1 = User::create([
            'name'           => 'Fernanda Silva',
            'email'          => 'req1@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'requester',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0004',
        ]);

        $req2 = User::create([
            'name'           => 'Bruno Oliveira',
            'email'          => 'req2@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'requester',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0005',
        ]);

        $req3 = User::create([
            'name'           => 'Juliana Costa',
            'email'          => 'req3@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'requester',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0006',
        ]);

        $req4 = User::create([
            'name'           => 'Marcos Pereira',
            'email'          => 'req4@celesta.com',
            'password'       => Hash::make('password'),
            'role'           => 'requester',
            'isActive'       => true,
            'whatsapp_phone' => '(11) 99001-0007',
        ]);

        // ── Centros de Custo ──────────────────────────────────────
        $ccAdm = CostCenter::create(['id' => 'ADM', 'name' => 'Administrativo', 'isActive' => true]);
        $ccTi  = CostCenter::create(['id' => 'TI',  'name' => 'Tecnologia',     'isActive' => true]);
        $ccOp  = CostCenter::create(['id' => 'OP',  'name' => 'Operações',      'isActive' => true]);
        $ccFin = CostCenter::create(['id' => 'FIN', 'name' => 'Financeiro',     'isActive' => true]);
        $ccMkt = CostCenter::create(['id' => 'MKT', 'name' => 'Marketing',      'isActive' => true]);

        // ── Fornecedores ──────────────────────────────────────────
        $sup1 = Supplier::create(['name' => 'Papelaria Central',     'contact' => '(11) 3001-1001', 'isActive' => true]);
        $sup2 = Supplier::create(['name' => 'TechStore Informática', 'contact' => '(11) 3001-1002', 'isActive' => true]);
        $sup3 = Supplier::create(['name' => 'Móveis & Cia',          'contact' => '(11) 3001-1003', 'isActive' => true]);
        $sup4 = Supplier::create(['name' => 'Limpeza Total',         'contact' => '(11) 3001-1004', 'isActive' => true]);
        $sup5 = Supplier::create(['name' => 'InfoPrint Solutions',   'contact' => '(11) 3001-1005', 'isActive' => true]);
        $sup6 = Supplier::create(['name' => 'Distribuidora Sul',     'contact' => '(11) 3001-1006', 'isActive' => true]);
        $sup7 = Supplier::create(['name' => 'Equipamentos Pro',      'contact' => '(11) 3001-1007', 'isActive' => true]);
        $sup8 = Supplier::create(['name' => 'Global Office',         'contact' => '(11) 3001-1008', 'isActive' => true]);

        // ── Itens ─────────────────────────────────────────────────
        $items = collect([
            'Resma de papel A4',
            'Caneta esferográfica azul',
            'Caneta esferográfica preta',
            'Pasta arquivo suspensa',
            'Grampeador',
            'Grampos 26/6',
            'Toner impressora HP LaserJet',
            'Toner impressora Brother',
            'Mouse sem fio',
            'Teclado USB',
            'Monitor 24"',
            'Headset USB',
            'Cadeira ergonômica',
            'Suporte para monitor',
            'Produto de limpeza multiuso',
            'Papel toalha',
            'Sabonete líquido',
            'Copo descartável 200ml',
            'Café em pó 500g',
            'Filtro de papel para café',
        ])->map(fn($name) => Item::create(['name' => $name, 'isActive' => true]));

        $now = Carbon::now();

        // 1. Rascunho
        $this->makeRequest($req1, $ccTi, Urgency::Medium, RequestStatus::Draft, null, null, $now->copy()->subDays(2), [
            ['item' => $items[8],  'qty' => 3,   'unit' => 'un'],
            ['item' => $items[9],  'qty' => 3,   'unit' => 'un'],
        ], []);

        // 2. Rascunho
        $this->makeRequest($req3, $ccMkt, Urgency::Low, RequestStatus::Draft, null, null, $now->copy()->subDays(1), [
            ['item' => $items[0],  'qty' => 10,  'unit' => 'resma'],
            ['item' => $items[1],  'qty' => 50,  'unit' => 'un'],
        ], []);

        // 3. Pendente
        $this->makeRequest($req2, $ccAdm, Urgency::High, RequestStatus::Pending, null, null, $now->copy()->subDays(5), [
            ['item' => $items[14], 'qty' => 5,   'unit' => 'un'],
            ['item' => $items[15], 'qty' => 10,  'unit' => 'pct'],
            ['item' => $items[16], 'qty' => 4,   'unit' => 'un'],
        ], [
            ['from' => null,                  'to' => RequestStatus::Draft,   'by' => $req2,  'at' => $now->copy()->subDays(5)],
            ['from' => RequestStatus::Draft,  'to' => RequestStatus::Pending, 'by' => $req2,  'at' => $now->copy()->subDays(5)->addMinutes(10)],
        ]);

        // 4. Pendente
        $this->makeRequest($req4, $ccFin, Urgency::Medium, RequestStatus::Pending, null, null, $now->copy()->subDays(3), [
            ['item' => $items[17], 'qty' => 200, 'unit' => 'pct'],
            ['item' => $items[18], 'qty' => 6,   'unit' => 'pct'],
        ], [
            ['from' => null,                  'to' => RequestStatus::Draft,   'by' => $req4,  'at' => $now->copy()->subDays(3)],
            ['from' => RequestStatus::Draft,  'to' => RequestStatus::Pending, 'by' => $req4,  'at' => $now->copy()->subDays(3)->addMinutes(5)],
        ]);

        // 5. Em Andamento — itens em cotação
        $this->makeRequest($req1, $ccOp, Urgency::High, RequestStatus::InProgress, null, null, $now->copy()->subDays(10), [
            ['item' => $items[6],  'qty' => 2,   'unit' => 'un',  'status' => ItemStatus::Quoting,  'supplier' => $sup5],
            ['item' => $items[7],  'qty' => 1,   'unit' => 'un',  'status' => ItemStatus::Quoting,  'supplier' => $sup5],
        ], [
            ['from' => null,                   'to' => RequestStatus::Draft,      'by' => $req1,   'at' => $now->copy()->subDays(10)],
            ['from' => RequestStatus::Draft,   'to' => RequestStatus::Pending,    'by' => $req1,   'at' => $now->copy()->subDays(10)->addMinutes(8)],
            ['from' => RequestStatus::Pending, 'to' => RequestStatus::InProgress, 'by' => $buyer1, 'at' => $now->copy()->subDays(9)],
        ]);

        // 6. Em Andamento — itens em estágios variados
        $this->makeRequest($req3, $ccTi, Urgency::Medium, RequestStatus::InProgress, null, null, $now->copy()->subDays(8), [
            ['item' => $items[10], 'qty' => 1,   'unit' => 'un',  'status' => ItemStatus::AwaitingDelivery, 'supplier' => $sup2, 'order_number' => 'PO-2026-001'],
            ['item' => $items[11], 'qty' => 2,   'unit' => 'un',  'status' => ItemStatus::AwaitingPayment,  'supplier' => $sup2, 'order_number' => 'PO-2026-002'],
            ['item' => $items[13], 'qty' => 1,   'unit' => 'un',  'status' => ItemStatus::Quoting,          'supplier' => null],
        ], [
            ['from' => null,                   'to' => RequestStatus::Draft,      'by' => $req3,   'at' => $now->copy()->subDays(8)],
            ['from' => RequestStatus::Draft,   'to' => RequestStatus::Pending,    'by' => $req3,   'at' => $now->copy()->subDays(8)->addMinutes(15)],
            ['from' => RequestStatus::Pending, 'to' => RequestStatus::InProgress, 'by' => $buyer2, 'at' => $now->copy()->subDays(7)],
        ]);

        // 7. Em Andamento — itens aguardando pagamento
        $this->makeRequest($req2, $ccAdm, Urgency::Low, RequestStatus::InProgress, null, null, $now->copy()->subDays(15), [
            ['item' => $items[4],  'qty' => 4,   'unit' => 'un',  'status' => ItemStatus::AwaitingPayment, 'supplier' => $sup1, 'order_number' => 'PO-2026-003'],
            ['item' => $items[5],  'qty' => 10,  'unit' => 'cx',  'status' => ItemStatus::Quoting,         'supplier' => $sup1],
        ], [
            ['from' => null,                   'to' => RequestStatus::Draft,      'by' => $req2,   'at' => $now->copy()->subDays(15)],
            ['from' => RequestStatus::Draft,   'to' => RequestStatus::Pending,    'by' => $req2,   'at' => $now->copy()->subDays(15)->addMinutes(20)],
            ['from' => RequestStatus::Pending, 'to' => RequestStatus::InProgress, 'by' => $buyer1, 'at' => $now->copy()->subDays(14)],
        ]);

        // 8. Em Andamento — entrega parcial
        $this->makeRequest($req4, $ccMkt, Urgency::High, RequestStatus::InProgress, null, null, $now->copy()->subDays(12), [
            ['item' => $items[0],  'qty' => 20,  'unit' => 'resma', 'status' => ItemStatus::AwaitingDelivery, 'supplier' => $sup8, 'order_number' => 'PO-2026-004'],
            ['item' => $items[2],  'qty' => 100, 'unit' => 'un',    'status' => ItemStatus::Received,          'supplier' => $sup8, 'order_number' => 'PO-2026-004', 'delivered_qty' => 100],
            ['item' => $items[3],  'qty' => 30,  'unit' => 'un',    'status' => ItemStatus::AwaitingDelivery,  'supplier' => $sup8, 'order_number' => 'PO-2026-005'],
        ], [
            ['from' => null,                   'to' => RequestStatus::Draft,      'by' => $req4,   'at' => $now->copy()->subDays(12)],
            ['from' => RequestStatus::Draft,   'to' => RequestStatus::Pending,    'by' => $req4,   'at' => $now->copy()->subDays(12)->addMinutes(5)],
            ['from' => RequestStatus::Pending, 'to' => RequestStatus::InProgress, 'by' => $buyer2, 'at' => $now->copy()->subDays(11)],
        ]);

        // 9. Concluída
        $this->makeRequest($req2, $ccTi, Urgency::Low, RequestStatus::Completed, null, null, $now->copy()->subDays(40), [
            ['item' => $items[6],  'qty' => 1,   'unit' => 'un',  'status' => ItemStatus::Received, 'supplier' => $sup5, 'order_number' => 'PO-2025-088', 'delivered_qty' => 1],
            ['item' => $items[7],  'qty' => 2,   'unit' => 'un',  'status' => ItemStatus::Received, 'supplier' => $sup5, 'order_number' => 'PO-2025-088', 'delivered_qty' => 2],
        ], [
            ['from' => null,                    'to' => RequestStatus::Draft,      'by' => $req2,   'at' => $now->copy()->subDays(40)],
            ['from' => RequestStatus::Draft,    'to' => RequestStatus::Pending,    'by' => $req2,   'at' => $now->copy()->subDays(40)->addHours(1)],
            ['from' => RequestStatus::Pending,  'to' => RequestStatus::InProgress, 'by' => $buyer1, 'at' => $now->copy()->subDays(39)],
            ['from' => RequestStatus::InProgress,'to' => RequestStatus::Completed, 'by' => $req2,   'at' => $now->copy()->subDays(32)],
        ]);

        // 10. Concluída
        $this->makeRequest($req4, $ccAdm, Urgency::Medium, RequestStatus::Completed, null, null, $now->copy()->subDays(50), [
            ['item' => $items[14], 'qty' => 10,  'unit' => 'un',  'status' => ItemStatus::Received, 'supplier' => $sup4, 'order_number' => 'PO-2025-071', 'delivered_qty' => 10],
            ['item' => $items[15], 'qty' => 20,  'unit' => 'pct', 'status' => ItemStatus::Received, 'supplier' => $sup4, 'order_number' => 'PO-2025-071', 'delivered_qty' => 20],
        ], [
            ['from' => null,                    'to' => RequestStatus::Draft,      'by' => $req4,   'at' => $now->copy()->subDays(50)],
            ['from' => RequestStatus::Draft,    'to' => RequestStatus::Pending,    'by' => $req4,   'at' => $now->copy()->subDays(50)->addMinutes(45)],
            ['from' => RequestStatus::Pending,  'to' => RequestStatus::InProgress, 'by' => $buyer2, 'at' => $now->copy()->subDays(49)],
            ['from' => RequestStatus::InProgress,'to' => RequestStatus::Completed, 'by' => $req4,   'at' => $now->copy()->subDays(41)],
        ]);

        // 11. Cancelamento solicitado
        $this->makeRequest($req1, $ccMkt, Urgency::Low, RequestStatus::CancelRequested, 'inProgress', 'Fornecedor não tem o produto disponível', $now->copy()->subDays(6), [
            ['item' => $items[10], 'qty' => 2,   'unit' => 'un',  'status' => ItemStatus::Quoting],
        ], [
            ['from' => null,                   'to' => RequestStatus::Draft,          'by' => $req1,   'at' => $now->copy()->subDays(6)],
            ['from' => RequestStatus::Draft,   'to' => RequestStatus::Pending,        'by' => $req1,   'at' => $now->copy()->subDays(6)->addMinutes(20)],
            ['from' => RequestStatus::Pending, 'to' => RequestStatus::InProgress,     'by' => $buyer1, 'at' => $now->copy()->subDays(5)],
            ['from' => RequestStatus::InProgress,'to' => RequestStatus::CancelRequested,'by' => $req1, 'at' => $now->copy()->subDays(4)],
        ]);

        // 12. Cancelada
        $this->makeRequest($req3, $ccFin, Urgency::Medium, RequestStatus::Cancelled, 'pending', null, $now->copy()->subDays(30), [
            ['item' => $items[17], 'qty' => 500, 'unit' => 'pct', 'status' => ItemStatus::Cancelled],
            ['item' => $items[18], 'qty' => 12,  'unit' => 'pct', 'status' => ItemStatus::Cancelled],
        ], [
            ['from' => null,                     'to' => RequestStatus::Draft,          'by' => $req3,   'at' => $now->copy()->subDays(30)],
            ['from' => RequestStatus::Draft,     'to' => RequestStatus::Pending,        'by' => $req3,   'at' => $now->copy()->subDays(30)->addMinutes(10)],
            ['from' => RequestStatus::Pending,   'to' => RequestStatus::CancelRequested,'by' => $req3,   'at' => $now->copy()->subDays(29)],
            ['from' => RequestStatus::CancelRequested,'to' => RequestStatus::Cancelled, 'by' => $buyer2, 'at' => $now->copy()->subDays(28)],
        ], 'Compra não aprovada pelo financeiro');

        // 13. Urgente pendente (criada hoje)
        $this->makeRequest($req2, $ccOp, Urgency::High, RequestStatus::Pending, null, null, $now->copy()->subHours(3), [
            ['item' => $items[12], 'qty' => 1,   'unit' => 'un'],
        ], [
            ['from' => null,                 'to' => RequestStatus::Draft,   'by' => $req2, 'at' => $now->copy()->subHours(3)],
            ['from' => RequestStatus::Draft, 'to' => RequestStatus::Pending, 'by' => $req2, 'at' => $now->copy()->subHours(3)->addMinutes(2)],
        ]);
    }

    private function makeRequest(
        User $requester,
        CostCenter $costCenter,
        Urgency $urgency,
        RequestStatus $status,
        ?string $previousStatus,
        ?string $cancellationReason,
        Carbon $createdAt,
        array $itemRows,
        array $historyRows,
        ?string $notes = null,
    ): SupplyRequest {
        $sr = SupplyRequest::create([
            'title'               => $this->nextTitle(),
            'cost_center_id'      => $costCenter->id,
            'user_id'             => $requester->id,
            'urgency'             => $urgency,
            'status'              => $status,
            'previous_status'     => $previousStatus,
            'cancellation_reason' => $cancellationReason,
            'notes'               => $notes,
        ]);

        $sr->created_at = $createdAt;
        $sr->updated_at = $createdAt;
        $sr->saveQuietly();

        foreach ($itemRows as $row) {
            SupplyRequestItem::create([
                'supply_request_id' => $sr->id,
                'item_id'           => $row['item']->id,
                'quantity'          => $row['qty'],
                'unit'              => $row['unit'] ?? null,
                'status'            => $row['status'] ?? ItemStatus::Pending,
                'supplier_id'       => $row['supplier']?->id ?? null,
                'order_number'      => $row['order_number'] ?? null,
                'delivered_quantity' => $row['delivered_qty'] ?? 0,
                'notes'             => $row['notes'] ?? null,
            ]);
        }

        foreach ($historyRows as $h) {
            $entry = RequestStatusHistory::create([
                'supply_request_id' => $sr->id,
                'from_status'       => $h['from']?->value,
                'to_status'         => $h['to']->value,
                'changed_by'        => $h['by']->id,
            ]);
            $entry->created_at = $h['at'];
            $entry->updated_at = $h['at'];
            $entry->saveQuietly();
        }

        return $sr;
    }

    private function nextTitle(): string
    {
        static $titles = [
            'Reposição de material de escritório',
            'Equipamentos para novo colaborador',
            'Material de limpeza mensal',
            'Suprimentos de impressão',
            'Mobiliário para sala de reuniões',
            'Itens de copa e cozinha',
            'Equipamentos de TI',
            'Material de higiene',
            'Suprimentos administrativos',
            'Aquisição de periféricos',
            'Reposição de consumíveis',
            'Compra emergencial de insumos',
        ];
        static $i = 0;
        return $titles[$i++ % count($titles)];
    }
}
