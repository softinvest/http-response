<?php

namespace SoftInvest\Http\Controllers;

use SoftInvest\Http\Responses\AbstractResponse;
use SoftInvest\Http\Responses\ResponseFailure;
use SoftInvest\Http\Responses\ResponseSuccess;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Throwable;


class HttpResponseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public const FORMAT_PLAIN = 0;
    public const FORMAT_JSON = 1;
    public const FORMAT_XML = 2;

    protected bool $hasTrace = false;

    /**
     * @param string $responseContent
     * @param int    $responseFormatType
     *
     * @return Response|JsonResponse|null
     */
    public function renderSuccessResponse(
        string $responseContent,
        int    $responseFormatType = self::FORMAT_PLAIN
    ): Response|JsonResponse|null {
        $response = (new ResponseSuccess())->setData(data: $responseContent);
        return $this->renderResponse($response, $responseFormatType);
    }

    /**
     * @param AbstractResponse $response
     * @param int              $responseFormatType
     *
     * @return Response|JsonResponse|null
     */
    public function renderResponse(AbstractResponse $response,
                                   int              $responseFormatType = self::FORMAT_PLAIN
    ): Response|JsonResponse|null {
        return match ($responseFormatType) {
            self::FORMAT_PLAIN => $response->asPlainText(),
            self::FORMAT_JSON => $response->asJSON(),
            default => null
        };
    }

    /**
     * @param callable $callback
     * @param bool     $hasTrace
     *
     * @return JsonResponse
     */
    public function response(callable $callback, string $successResponseCassName = ResponseSuccess::class): JsonResponse
    {
        try {
            $result = $callback();
        } catch (Throwable $e) {
            return (new ResponseFailure())
                ->fromException(exception: $e, hasTrace: $this->hasTrace)
                ->setStatus(status: HttpFoundationResponse::HTTP_BAD_REQUEST)
                ->asJSON();
        }
        return (new $successResponseCassName)
            ->setData(data: $result)
            ->asJSON();
    }

    public function responseWithResource(
        string $resourceClassName,
        callable $callback,
        string $successResponseCassName = ResponseSuccess::class,
        string $failureResponseCassName = ResponseFailure::class
    ): JsonResponse {
        try {
            $result = (static function() use ($callback, $resourceClassName) {
                $result = $callback();

                return $resourceClassName::make($result);
            })();
        } catch (Throwable $e) {
            return (new $failureResponseCassName())
                ->fromException(exception: $e, hasTrace: $this->hasTrace)
                ->setStatus(status: HttpFoundationResponse::HTTP_BAD_REQUEST)
                ->asJSON();
        }
        return (new $successResponseCassName)
            ->setData(data: $result)
            ->asJSON();
    }

    public function responseWithResourceCollection(
        string $resourceClassName,
        callable $callback,
        string $successResponseCassName = ResponseSuccess::class,
        string $failureResponseCassName = ResponseFailure::class
    ): JsonResponse {
        try {
            $result = (static function() use ($callback, $resourceClassName) {
                $result = $callback();

                return $resourceClassName::collection($result);
            })();
        } catch (Throwable $e) {
            return (new $failureResponseCassName())
                ->fromException(exception: $e, hasTrace: $this->hasTrace)
                ->setStatus(status: HttpFoundationResponse::HTTP_BAD_REQUEST)
                ->asJSON();
        }
        return (new $successResponseCassName)
            ->setData(data: $result)
            ->asJSON();
    }

    /**
     * @return ?string
     */
    public function getUserIP(): ?string
    {
        return request()?->server('HTTP_CF_CONNECTING_IP') ?: request()?->getClientIp();
    }
}
