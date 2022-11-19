<?php

declare(strict_types=1);

namespace SoftInvest\Http\Responses;

use SoftInvest\Http\Responses\Traits\TAsJsonStandard;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Throwable;

class ResponseFailure extends AbstractResponse
{
    use TAsJsonStandard;

    protected ?bool $success = false;
    protected $status = HttpFoundationResponse::HTTP_BAD_REQUEST;

    public function __construct(string $error = '', int $status = HttpFoundationResponse::HTTP_BAD_REQUEST, mixed $data = null)
    {
        $this->error = $error;
        $this->status = $status;
        $this->data = $data;
    }

    public function fromException(Throwable $exception, bool $hasTrace = false): static
    {
        $this->error = $exception->getMessage() . ($hasTrace ? $exception->getTraceAsString() : '');
        $this->status = (int)$exception->getCode();
        if (($this->status < 200) || ($this->status > 999)) {
            $this->status = HttpFoundationResponse::HTTP_BAD_REQUEST;
        }
        return $this;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function asPlainText(): \Illuminate\Http\Response
    {
        return Response::make($this->error, $this->status);
    }
}
