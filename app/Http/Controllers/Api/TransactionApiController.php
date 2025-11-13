<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Transaction::with(['customer', 'items.product'])->latest()->paginate(10)
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'        => 'nullable|exists:customers,id',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal += $product->price * $item['quantity'];
            }

            $discount = 0;
            if ($subtotal > 1000000) {
                $discount = $subtotal * 0.15;
            } elseif ($subtotal > 500000) {
                $discount = $subtotal * 0.10;
            }

            $total = $subtotal - $discount;

            $trx = Transaction::create([
                'user_id'     => auth()->id(),
                'customer_id' => $validated['customer_id'] ?? null,
                'invoice'     => 'INV' . date('YmdHis'),
                'subtotal'    => $subtotal,
                'discount'    => $discount,
                'total'       => $total,
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                TransactionItem::create([
                    'transaction_id' => $trx->id,
                    'product_id'     => $product->id,
                    'quantity'       => $item['quantity'],
                    'price'          => $product->price,
                    'subtotal'       => $product->price * $item['quantity'],
                ]);
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();
            return response()->json(['message' => 'Transaction success', 'data' => $trx->load('items.product')]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Transaction $transaction)
    {
        return response()->json($transaction->load('items.product', 'customer'));
    }
}
