<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CostCenter;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\RequestStatusHistory;
use App\Models\ItemDelivery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. USERS
        $adminUser = User::create([
            'name' => 'Admin CelestaSupply',
            'email' => 'admin@celestasupply.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'isActive' => true,
        ]);

        $buyerUser = User::create([
            'name' => 'Mateus Farias',
            'email' => 'mateus@celestasupply.com',
            'password' => Hash::make('buyer123'),
            'role' => 'buyer',
            'isActive' => true,
        ]);

        $requesterUser1 = User::create([
            'name' => 'João Silva',
            'email' => 'joao@celestasupply.com',
            'password' => Hash::make('requester123'),
            'role' => 'requester',
            'isActive' => true,
        ]);

        $requesterUser2 = User::create([
            'name' => 'Maria Santos',
            'email' => 'maria@celestasupply.com',
            'password' => Hash::make('requester123'),
            'role' => 'requester',
            'isActive' => true,
        ]);

        // 2. COST_CENTERS
        $cc1 = CostCenter::create([
            'id' => 'CC001',
            'name' => 'Mineração',
            'isActive' => true,
        ]);

        $cc2 = CostCenter::create([
            'id' => 'CC002',
            'name' => 'Manutenção',
            'isActive' => true,
        ]);

        $cc3 = CostCenter::create([
            'id' => 'CC003',
            'name' => 'Administrativo',
            'isActive' => true,
        ]);

        // 3. SUPPLIERS
        $supplier1 = Supplier::create([
            'name' => 'Fornecedora Industrial Ltda',
            'contact' => '(31) 98765-4321 - contato@fornecedora.com',
            'isActive' => true,
        ]);

        $supplier2 = Supplier::create([
            'name' => 'Distribuidor de Componentes SA',
            'contact' => '(11) 99999-8888 - vendas@distribuidor.com',
            'isActive' => true,
        ]);

        $supplier3 = Supplier::create([
            'name' => 'Indústria de Polímeros Brasil',
            'contact' => '(21) 3333-5555 - comercial@polimeros.com',
            'isActive' => true,
        ]);

        // 4. ITEMS
        $item1 = Item::create([
            'name' => 'Correia Transportadora Borracha 500mm',
            'isActive' => true,
        ]);

        $item2 = Item::create([
            'name' => 'Parafuso M16 Aço Carbono Rosca',
            'isActive' => true,
        ]);

        $item3 = Item::create([
            'name' => 'Óleo Hidráulico ISO 46',
            'isActive' => true,
        ]);

        $item4 = Item::create([
            'name' => 'Filtro de Ar Compressor',
            'isActive' => true,
        ]);

        $item5 = Item::create([
            'name' => 'Corrente Transmissão #60',
            'isActive' => true,
        ]);

        $item6 = Item::create([
            'name' => 'Rolamento 6205 2Z',
            'isActive' => true,
        ]);

        // 5. SUPPLY_REQUESTS
        $request1 = SupplyRequest::create([
            'title' => 'Requisição de Correia Transportadora',
            'cost_center_id' => 'CC001',
            'user_id' => $requesterUser1->id,
            'urgency' => 'high',
            'status' => 'pending',
            'notes' => 'Correia danificada na linha de produção. Necessário reposição urgente.',
        ]);

        $request2 = SupplyRequest::create([
            'title' => 'Requisição de Parafusos e Porcas',
            'cost_center_id' => 'CC002',
            'user_id' => $requesterUser2->id,
            'urgency' => 'medium',
            'status' => 'inProgress',
            'notes' => 'Reposição para manutenção preventiva mensal.',
        ]);

        $request3 = SupplyRequest::create([
            'title' => 'Requisição de Óleo Hidráulico',
            'cost_center_id' => 'CC001',
            'user_id' => $requesterUser1->id,
            'urgency' => 'low',
            'status' => 'draft',
            'notes' => 'Reabastecimento mensal do sistema hidráulico principal.',
        ]);

        $request4 = SupplyRequest::create([
            'title' => 'Manutenção de Filtros',
            'cost_center_id' => 'CC002',
            'user_id' => $requesterUser2->id,
            'urgency' => 'medium',
            'status' => 'completed',
            'notes' => 'Troca de filtros de ar dos compressores.',
        ]);

        $request5 = SupplyRequest::create([
            'title' => 'Peças para Corrente de Transmissão',
            'cost_center_id' => 'CC001',
            'user_id' => $requesterUser1->id,
            'urgency' => 'high',
            'status' => 'pending',
            'notes' => 'Substituição de corrente danificada na máquina de moagem.',
        ]);

        // 6. SUPPLY_REQUEST_ITEMS
        $requestItem1 = SupplyRequestItem::create([
            'supply_request_id' => $request1->id,
            'item_id' => $item1->id,
            'quantity' => 10.000,
            'unit' => 'metro',
            'status' => 'awaitingDelivery',
            'delivered_quantity' => 0,
        ]);

        $requestItem2 = SupplyRequestItem::create([
            'supply_request_id' => $request2->id,
            'item_id' => $item2->id,
            'quantity' => 500.000,
            'unit' => 'unidade',
            'status' => 'quoting',
            'delivered_quantity' => 0,
            'notes' => 'Solicitar orçamento com urgência',
        ]);

        $requestItem3 = SupplyRequestItem::create([
            'supply_request_id' => $request3->id,
            'item_id' => $item3->id,
            'quantity' => 200.000,
            'unit' => 'litro',
            'status' => 'pending',
            'delivered_quantity' => 0,
        ]);

        $requestItem4 = SupplyRequestItem::create([
            'supply_request_id' => $request4->id,
            'item_id' => $item4->id,
            'quantity' => 5.000,
            'unit' => 'unidade',
            'status' => 'received',
            'delivered_quantity' => 5.000,
        ]);

        $requestItem5 = SupplyRequestItem::create([
            'supply_request_id' => $request5->id,
            'item_id' => $item5->id,
            'quantity' => 3.000,
            'unit' => 'unidade',
            'status' => 'pending',
            'delivered_quantity' => 0,
        ]);

        $requestItem6 = SupplyRequestItem::create([
            'supply_request_id' => $request5->id,
            'item_id' => $item6->id,
            'quantity' => 12.000,
            'unit' => 'unidade',
            'status' => 'pending',
            'delivered_quantity' => 0,
        ]);

        // 7. REQUEST_STATUS_HISTORY
        RequestStatusHistory::create([
            'supply_request_id' => $request1->id,
            'from_status' => 'draft',
            'to_status' => 'pending',
            'changed_by' => $buyerUser->id,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $request2->id,
            'from_status' => 'draft',
            'to_status' => 'pending',
            'changed_by' => $buyerUser->id,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $request2->id,
            'from_status' => 'pending',
            'to_status' => 'inProgress',
            'changed_by' => $buyerUser->id,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $request4->id,
            'from_status' => 'draft',
            'to_status' => 'pending',
            'changed_by' => $buyerUser->id,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $request4->id,
            'from_status' => 'pending',
            'to_status' => 'inProgress',
            'changed_by' => $buyerUser->id,
        ]);

        RequestStatusHistory::create([
            'supply_request_id' => $request4->id,
            'from_status' => 'inProgress',
            'to_status' => 'completed',
            'changed_by' => $buyerUser->id,
        ]);

        // 8. ITEM_DELIVERIES
        ItemDelivery::create([
            'supply_request_item_id' => $requestItem4->id,
            'quantity' => 5.000,
            'notes' => 'Entrega completa em perfeito estado',
            'registered_by' => $requesterUser2->id,
        ]);
    }
}
