<?php

namespace App\Exceptions\Currency;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotFoundException extends Exception
{
    public function render(Request $request): Response|JsonResponse
    {
        return response()->json([
            'msg' => 'error',
            'error' => $this->getMessage(),
        ], 404);
    }
}
