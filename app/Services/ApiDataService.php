<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Customer;

class ApiDataService
{
    /**
     * Get products data in API format (matching ProductController@index response structure)
     */
    public function getProductsData()
    {
        try {
            $products = Product::all()->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            })->toArray();

            return [
                'success' => true,
                'message' => 'Daftar produk berhasil diambil',
                'data' => $products
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil daftar produk',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get customers data in API format (matching CustomerApiController@index response structure)
     */
    public function getCustomersData()
    {
        try {
            $customers = Customer::all();

            // Format the same as CustomerApiController@index which returns Customer::paginate(10)
            // But since we want all customers for the POS view, we convert to the same structure
            return [
                'data' => $customers->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                        'email' => $customer->email,
                        'created_at' => $customer->created_at,
                        'updated_at' => $customer->updated_at,
                    ];
                })->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Error fetching customers: ' . $e->getMessage()
            ];
        }
    }
}