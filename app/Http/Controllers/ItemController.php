<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Item::class);

        $items = Item::orderBy('name')->paginate(25);

        return view('items.index', compact('items'));
    }

    public function create(): View
    {
        $this->authorize('create', Item::class);

        return view('items.create');
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $this->authorize('create', Item::class);

        Item::create($request->validated());

        return redirect()->route('items.index')
            ->with('success', 'Item adicionado ao catálogo.');
    }

    public function edit(Item $item): View
    {
        $this->authorize('update', $item);

        return view('items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        $this->authorize('update', $item);

        $item->update($request->validated());

        return redirect()->route('items.index')
            ->with('success', 'Item atualizado com sucesso.');
    }

    // ── JSON API (usada no formulário de solicitações) ──────

    public function search(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $items = Item::where('isActive', true)
            ->where('name', 'like', '%' . $q . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($items);
    }

    public function apiStore(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);

        $item = Item::firstOrCreate(
            ['name' => $request->name],
            ['isActive' => true]
        );

        return response()->json($item, 201);
    }
}
