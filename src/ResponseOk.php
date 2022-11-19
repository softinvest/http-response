<?php

declare(strict_types=1);

namespace SoftInvest\Http\Responses;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ResponseOk extends AbstractResponse
{
    protected ?bool $success = true;
    protected $status = HttpFoundationResponse::HTTP_OK;

    /**
     * @return JsonResponse
     */
    public function asJSON(): JsonResponse
    {
        return new JsonResponse(
            [
                'status' => 'ok',
            ], $this->status
        );
    }
}
