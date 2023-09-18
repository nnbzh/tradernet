<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponder
{
    public static function success(mixed $data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    public static function error(string $message = 'Something went wrong', int $code = 500, mixed $data = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}
