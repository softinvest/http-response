<?php

declare(strict_types=1);

namespace SoftInvest\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

abstract class AbstractResponse
{
    /**
     * @var string
     */
    protected string $error = '';

    /**
     * @var bool
     */
    protected bool $success = false;

    /**
     * @var int
     */
    protected $status = 0;

    /**
     * @var mixed|null
     */
    protected mixed $data = null;


    /**
     * @param mixed|null $data
     */
    public function __construct(mixed $data = null)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setData(mixed $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return JsonResponse
     */
    abstract function asJSON(): JsonResponse;

    /**
     * @param string $json
     * @return $this
     */
    public function fromJSON(string $json): static
    {
        $arr = json_decode($json, true);
        if (null === $arr) {
            return $this;
        }
        $this->error = $arr['error'] ?? null;
        $this->success = $arr['success'] ?? null;
        $this->data = $arr['data'] ?? null;
        return $this;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function asPlainText(): \Illuminate\Http\Response
    {
        return Response::make($this->data, $this->status);
    }
}
