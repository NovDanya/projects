<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * @param mixed|null $data
     * @param string $message
     * @param int $code
     *
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => 'ok',
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    /**
     * @param mixed $paginator
     * @param string $message
     * @param int $code
     *
     * @return JsonResponse
     */
    public static function paginated($paginator, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => 'ok',
            'message' => $message,
            'data' => $paginator->items(),
            'pagination_data' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'code' => $code,
        ], $code);
    }

    /**
     * @param string $message
     * @param int $code
     *
     * @return JsonResponse
     */
    public static function error(string $message = 'Error', int $code = 400): JsonResponse
    {
        return response()->json([
            'success' => 'error',
            'message' => $message,
            'data' => null,
            'code' => $code,
        ], $code);
    }
}
