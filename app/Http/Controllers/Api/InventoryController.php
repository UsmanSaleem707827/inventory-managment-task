<?php

namespace App\Http\Controllers\Api;

use App\Models\AuditLog;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $items = Inventory::with('product')
            ->when($request->location, fn($q) => $q->where('location', $request->location))
            ->paginate(10);

        if ($items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No inventory items found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Inventory items retrieved successfully.',
            'data' => $items
        ], 200);
    }

    public function show($id)
    {
        $inventory = Inventory::with('product')->find($id);

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Inventory item retrieved successfully.',
            'data' => $inventory
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::find($id);

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory item not found.',
            ], 404);
        }
        
        $inventory->update($request->only('quantity'));
        AuditLog::create([
            'action' => 'inventory_update',
            'data' => $inventory
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Inventory item updated successfully.',
            'data' => $inventory
        ], 200);
    }
}
