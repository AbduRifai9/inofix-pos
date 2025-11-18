<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        [$token, $apiClient] = $this->getApiTokenAndClient($request);
        
        if (!$token || !$apiClient) {
            return redirect()->route('login');
        }

        try {
            $products = $apiClient->getProducts();
            $customers = $apiClient->getCustomers();
            $transactions = $apiClient->getTransactions();
            
            $productCount = count($products);
            $customerCount = count($customers);
            $transactionCount = count($transactions);
        } catch (\Exception $e) {
            $productCount = 0;
            $customerCount = 0;
            $transactionCount = 0;
        }

        return view('dashboard', [
            'api_token' => $token,
            'product_count' => $productCount,
            'customer_count' => $customerCount,
            'transaction_count' => $transactionCount,
            'recent_transactions' => $transactions // All transactions for pagination
        ]);
    }

    public function products(Request $request)
    {
        [$token, $apiClient] = $this->getApiTokenAndClient($request);
        
        if (!$token || !$apiClient) {
            return redirect()->route('login');
        }

        try {
            $products = $apiClient->getProducts();
        } catch (\Exception $e) {
            $products = [];
        }

        return view('products.index', [
            'api_token' => $token,
            'products' => $products
        ]);
    }

    public function customers(Request $request)
    {
        [$token, $apiClient] = $this->getApiTokenAndClient($request);
        
        if (!$token || !$apiClient) {
            return redirect()->route('login');
        }

        try {
            $customers = $apiClient->getCustomers();
        } catch (\Exception $e) {
            $customers = [];
        }

        return view('customers.index', [
            'api_token' => $token,
            'customers' => $customers
        ]);
    }

    public function transactions(Request $request)
    {
        [$token, $apiClient] = $this->getApiTokenAndClient($request);
        
        if (!$token || !$apiClient) {
            return redirect()->route('login');
        }

        try {
            $transactions = $apiClient->getTransactions();
        } catch (\Exception $e) {
            $transactions = [];
        }

        return view('transactions.index', [
            'api_token' => $token,
            'transactions' => $transactions
        ]);
    }
}