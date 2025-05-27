<?php

namespace SoftInvest\Http\Responses\Traits;

use Illuminate\Http\Request;
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
                'data' => (is_object($this->data) && method_exists($this->data,'toArray'))
                    ?
                    $this->data->toArray(Request::capture())
                    : $this->data
            ], $this->status
        );
    }
}
