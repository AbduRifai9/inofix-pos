<?php

namespace App\Http\Controllers;

use App\Services\ApiClientService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    
    protected function getApiTokenAndClient(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return [null, null];
        }

        // Get or create API token for this session
        $token = $user->currentAccessToken();
        if (!$token) {
            $token = $user->createToken('web-session')->plainTextToken;
        } else {
            $token = $token->plainTextToken;
        }

        $apiClient = new ApiClientService();
        $apiClient->setToken($token);

        return [$token, $apiClient];
    }
}
