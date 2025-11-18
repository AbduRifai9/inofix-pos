<?php

namespace App\Http\Controllers;

use App\Services\ApiClientService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index(Request $request)
    {
        // Get API token for authenticated user
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get the API token for this session
        $token = $user->currentAccessToken();
        if (!$token) {
            // If no current token exists, create a new one
            $token = $user->createToken('pos-session')->plainTextToken;
        } else {
            // Get the plain text token from the access token
            $token = $token->plainTextToken;
        }

        // Use the API client service to fetch data using the token
        $apiClient = new ApiClientService();
        $apiClient->setToken($token);

        try {
            // Get products and customers using API calls with token
            $products = $apiClient->getProducts();
            $customers = $apiClient->getCustomers();
        } catch (\Exception $e) {
            // Fallback if API call fails
            $products = [];
            $customers = [];
        }

        // Pass data and token to the view
        return view('pos', [
            'products' => $products,
            'customers' => $customers,
            'api_token' => $token
        ]);
    }
}