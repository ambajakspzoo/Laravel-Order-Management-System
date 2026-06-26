<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

trait ApiResponses
{
    protected function jsonOk(mixed $data, int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $data], $status);
    }

    /** @param array<string, string>|null $errors */
    protected function jsonError(string $message, int $status = 400, ?array $errors = null): JsonResponse
    {
        $payload = ['success' => false, 'error' => $message];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
