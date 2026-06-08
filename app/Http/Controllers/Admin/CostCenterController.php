<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCostCenterRequest;
use App\Http\Requests\Admin\UpdateCostCenterRequest;
use App\Models\CostCenter;

class CostCenterController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CostCenter::class);

        $costCenters = CostCenter::orderBy('id')->paginate(20);

        return view('admin.costCenters.index', compact('costCenters'));
    }

    public function create()
    {
        $this->authorize('create', CostCenter::class);

        return view('admin.costCenters.create');
    }

    public function store(StoreCostCenterRequest $request)
    {
        CostCenter::create($request->validated());

        return redirect()->route('admin.costCenters.index')
            ->with('success', 'Centro de custo criado com sucesso.');
    }

    public function edit(CostCenter $costCenter)
    {
        $this->authorize('update', $costCenter);

        return view('admin.costCenters.edit', compact('costCenter'));
    }

    public function update(UpdateCostCenterRequest $request, CostCenter $costCenter)
    {
        $costCenter->update($request->validated());

        return redirect()->route('admin.costCenters.index')
            ->with('success', 'Centro de custo atualizado com sucesso.');
    }

}
