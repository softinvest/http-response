<?php

declare(strict_types=1);

namespace SoftInvest\Http\Responses;

use SoftInvest\Http\Responses\Traits\TAsJsonStandard;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ResponseCreated extends ResponseSuccess
{
    protected $status = HttpFoundationResponse::HTTP_CREATED;
}
