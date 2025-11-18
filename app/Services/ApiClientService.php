<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiClientService
{
    protected $baseUrl;
    protected $token;
    protected $internalGateway;

    public function __construct()
    {
        $this->baseUrl = config('app.url');
        $this->internalGateway = new InternalApiGateway();
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Get products from API
     */
    public function getProducts()
    {
        // First try using internal gateway to avoid HTTP request issues
        try {
            return $this->internalGateway->getProducts();
        } catch (\Exception $e) {
            // Fallback to HTTP request if internal gateway fails
            if (!$this->token) {
                throw new \Exception('API token is required');
            }

            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/api/products");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        }
    }

    /**
     * Get customers from API
     */
    public function getCustomers()
    {
        // First try using internal gateway to avoid HTTP request issues
        try {
            return $this->internalGateway->getCustomers();
        } catch (\Exception $e) {
            // Fallback to HTTP request if internal gateway fails
            if (!$this->token) {
                throw new \Exception('API token is required');
            }

            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/api/customers");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        }
    }
    
    /**
     * Get transactions from API
     */
    public function getTransactions()
    {
        // First try using internal gateway to avoid HTTP request issues
        try {
            return $this->internalGateway->getTransactions();
        } catch (\Exception $e) {
            // Fallback to HTTP request if internal gateway fails
            if (!$this->token) {
                throw new \Exception('API token is required');
            }

            $response = Http::withToken($this->token)
                ->get("{$this->baseUrl}/api/transactions");

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            return [];
        }
    }
}