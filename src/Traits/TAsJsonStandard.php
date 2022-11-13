<?php

namespace App\Http\Responses\Traits;

use Illuminate\Http\JsonResponse;

trait TAsJsonStandard
{
    /**
     * @return JsonResponse
     */
    public function asJSON(): JsonResponse
    {
        return new JsonResponse(
            [
                'error' => $this->error,
                'success' => $this->success,
                'data' => $this->data
            ], $this->status
        );
    }
}
