<?php

declare(strict_types = 1);

namespace InteractiveSolutions\HoneycombNewCore\Helpers;

use Illuminate\Http\JsonResponse;

class HCFrontendResponse
{
    /**
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function success(string $message, $data = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @param string $message
     * @param null $data
     * @return JsonResponse
     */
    public function error(string $message, $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
