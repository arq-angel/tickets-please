<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{

    public function ok($message, $data = []): JsonResponse
    {
        return $this->success($message, $data);
    }


    public function success($message, $data = [], $statusCode = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $statusCode
        ], $statusCode);
    }

    public function error($errors = [], $statusCode = null): JsonResponse
    {
        if (is_string($errors)) {
            return response()->json([
                'message' => $errors,
                'status' => $statusCode
            ], $statusCode);
        }

        return response()->json([
            'errors' => $errors,
        ]);
    }

    public function notAuthorized($message = 'Unauthorized'): JsonResponse
    {
        return $this->error([
            'status' => 401,
            'message' => $message,
            'source' => ''
        ]);
    }
}
