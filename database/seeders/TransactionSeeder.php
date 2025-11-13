<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Customer;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing records
        $user = User::first();
        $customer = Customer::first();
        $products = Product::all();

        // Create sample transactions
        for ($i = 0; $i < 5; $i++) {
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'invoice' => 'INV' . date('YmdHis', strtotime("+$i days")),
                'subtotal' => 0,
                'discount' => 0,
                'total' => 0,
            ]);

            // Add items to transaction and calculate totals
            $items = $products->random(rand(2, 4))->take(rand(2, 4));
            $subtotal = 0;
            
            foreach ($items as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $itemSubtotal = $price * $quantity;
                
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ]);
                
                $subtotal += $itemSubtotal;
            }

            // Calculate discount based on subtotal
            $discount = 0;
            if ($subtotal > 1000000) {
                $discount = $subtotal * 0.15; // 15% discount
            } elseif ($subtotal > 500000) {
                $discount = $subtotal * 0.10; // 10% discount
            }

            $total = $subtotal - $discount;

            // Update transaction with calculated values
            $transaction->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
            ]);
        }
    }
}