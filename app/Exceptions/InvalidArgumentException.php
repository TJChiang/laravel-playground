<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvalidArgumentException extends Exception
{
    public function render(Request $request): Response|JsonResponse
    {
        return response()->json([
            'msg' => 'error',
            'error' => $this->getMessage(),
        ], 422);
    }
}
