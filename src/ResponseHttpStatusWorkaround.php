<?php

declare(strict_types=1);

namespace SoftInvest\Http\Responses;

use SoftInvest\Http\Responses\Traits\TAsJsonStandard;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Throwable;

class ResponseHttpStatusWorkaround extends AbstractResponse
{
    use TAsJsonStandard;

    protected $status = HttpFoundationResponse::HTTP_BAD_REQUEST;

    public function fromException(Throwable $exception, bool $hasTrace = false): static
    {
        $this->error = $exception->getMessage() . ($hasTrace ? $exception->getTraceAsString() : '');
        $this->status = (int)$exception->getCode();

        if (($this->status < 200) || ($this->status > 999)) {
            $this->status = HttpFoundationResponse::HTTP_BAD_REQUEST;
        }

        return $this;
    }

    public function setStatus(int $status): static
    {
        return $this;
    }
}
