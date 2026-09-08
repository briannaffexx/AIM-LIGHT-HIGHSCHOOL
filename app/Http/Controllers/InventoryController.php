<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $items      = $query->orderBy('name')->paginate(25)->appends($request->query());
        $categories = Inventory::distinct()->pluck('category');

        return view('inventory.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'category'         => 'required|string',
            'total_quantity'   => 'required|integer|min:1',
            'assigned_quantity'=> 'required|integer|min:0|lte:total_quantity',
            'status'           => 'required|in:good,damaged,need_replacement',
            'condition_notes'  => 'nullable|string',
        ]);

        Inventory::create($request->only([
            'name', 'category', 'total_quantity',
            'assigned_quantity', 'status', 'condition_notes',
        ]));

        return redirect()->route('inventory.index')->with('success', 'Asset registered successfully.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'status'          => 'required|in:good,damaged,need_replacement',
            'condition_notes' => 'nullable|string',
        ]);

        Inventory::findOrFail($id)->update($request->only(['status', 'condition_notes']));

        return redirect()->route('inventory.index')->with('success', 'Asset updated successfully.');
    }
}
