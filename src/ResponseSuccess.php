<?php

declare(strict_types=1);

namespace SoftInvest\Http\Responses;

use SoftInvest\Http\Responses\Traits\TAsJsonStandard;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ResponseSuccess extends AbstractResponse
{
    use TAsJsonStandard;

    protected ?bool $success = true;
    protected $status = HttpFoundationResponse::HTTP_OK;

}
