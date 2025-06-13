<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\AuditLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

   public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|integer|exists:products,id',
        'quantity' => 'required|integer|min:1',
        'type' => 'sometimes|string|in:sale,restock'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $productId = $request->product_id;
    $quantity = $request->quantity;
    $type = $request->type ?? 'sale';

    try {
        DB::beginTransaction();

        $product = Product::with('inventory', 'pricingRules')->find($productId);
        $inventory = $product->inventory;

        if (!$inventory) {
            throw new \Exception('No inventory found for this product');
        }

        if ($type === 'sale' && $inventory->quantity < $quantity) {
            throw new \Exception('Insufficient stock');
        }

        $pricePerUnit = $this->calculatePrice($product, $quantity);

        $transaction = Transaction::create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'type' => $type,
            'price_per_unit' => $pricePerUnit
        ]);

        $beforeQty = $inventory->quantity;

        if ($type === 'sale') {
            $inventory->quantity -= $quantity;
        } else {
            $inventory->quantity += $quantity;
        }

        $inventory->save();

        AuditLog::create([
            'action' => 'transaction_created',
            'data' => [
                'transaction_id' => $transaction->id,
                'product_id' => $productId,
                'quantity' => $quantity,
                'type' => $type,
                'price_per_unit' => $pricePerUnit,
                'inventory_before' => $beforeQty,
                'inventory_after' => $inventory->quantity
            ]
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Transaction completed successfully.',
            'data' => [
                'transaction' => $transaction,
                'remaining_stock' => $inventory->quantity
            ]
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'message' => 'Transaction failed: ' . $e->getMessage()
        ], 400);
    }
}

private function calculatePrice($product, $qty)
{
    $basePrice = $product->base_price;
    $finalPrice = $basePrice;
    $now = now();
    $currentDay = $now->format('D');

    foreach ($product->pricingRules->sortBy('precedence') as $rule) {
        if ($rule->type === 'quantity' && $qty >= $rule->min_quantity) {
            $finalPrice -= ($finalPrice * $rule->discount / 100);
        }
        if ($rule->type === 'time' && $rule->days_of_week) {
            $days = explode(',', $rule->days_of_week);
            if (in_array($currentDay, $days)) {
                $startTime = $now->copy()->setTimeFromTimeString($rule->start_time);
                $endTime = $now->copy()->setTimeFromTimeString($rule->end_time);

                if ($now->between($startTime, $endTime)) {
                    $finalPrice -= ($finalPrice * $rule->discount / 100);
                }
            }
        }
    }

    return round($finalPrice, 2);
}
}
