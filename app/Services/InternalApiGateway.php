<?php

namespace App\Services;

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CustomerApiController; 
use App\Http\Controllers\Api\TransactionApiController;

class InternalApiGateway
{
    /**
     * Get products data by calling the API controller directly
     */
    public function getProducts()
    {
        $controller = new ProductController();
        $response = $controller->index();

        // Decode the response content to get the data
        $content = json_decode($response->getContent(), true);
        return $content['data'] ?? [];
    }

    /**
     * Get customers data by calling the API controller directly
     */
    public function getCustomers()
    {
        $controller = new CustomerApiController();
        $response = $controller->index();

        // Decode the response content to get the data
        $content = json_decode($response->getContent(), true);
        return $content['data'] ?? [];
    }

    /**
     * Get transactions data by calling the API controller directly
     */
    public function getTransactions()
    {
        $controller = new TransactionApiController();
        $response = $controller->index();

        // Decode the response content to get the data
        $content = json_decode($response->getContent(), true);
        
        // Handle both paginated (with 'data' wrapper) and non-paginated responses
        if (isset($content['data']) && is_array($content['data'])) {
            // Paginated response (like CustomerApiController)
            return $content['data'];
        } else {
            // Non-paginated response or direct array
            return $content ?? [];
        }
    }
}