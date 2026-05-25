# HTTP Responses Library

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-8892BF.svg?style=flat-square)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/laravel-8%2F9%2F10%2F11-FF2D20.svg?style=flat-square)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/softinvest/http-response.svg?style=flat-square)](https://packagist.org/packages/softinvest/http-response)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg?style=flat-square)]()


A flexible, Laravel‑friendly HTTP response library that standardizes API responses, exception handling, and output formats (plain text, JSON).  
Provides a base controller with helper methods for consistent error handling, resource transformation, and IP detection.

## Features

- **Unified response structure** via `AbstractResponse` and concrete classes (`ResponseSuccess`, `ResponseFailure`, `ResponseCreated`, `ResponseOk`).
- **JSON & plain text** output formats.
- **Exception handling** with optional stack trace inclusion.
- **Laravel resource support** – single resources and collections.
- **CloudFlare compatible** – retrieves real client IP from `HTTP_CF_CONNECTING_IP`.
- **Extensible** – create your own response types by extending `AbstractResponse`.

## Requirements

- PHP 8.0 or higher
- Laravel 8+ (or any Illuminate HTTP component)

## Installation

```bash
composer require softinvest/http-response
```

## Basic Usage

### 1. Extend the base controller

Make your controllers extend `HttpResponseController`:

```php
use SoftInvest\Http\Controllers\HttpResponseController;

class UserController extends HttpResponseController
{
    // Your methods...
}
```

### 2. Simple success response (plain text or JSON)

```php
public function index()
{
    $data = "Hello, world!";
    
    // Plain text response
    return $this->renderSuccessResponse($data, self::FORMAT_PLAIN);
    
    // JSON response
    return $this->renderSuccessResponse($data, self::FORMAT_JSON);
}
```

### 3. Using the `response` helper (recommended)

Wrap your business logic in a callback – any exception will be automatically converted into a `ResponseFailure` JSON response.

```php
public function show($id)
{
    return $this->response(function () use ($id) {
        return User::findOrFail($id);
    });
}
```

### 4. Using Laravel Resources

```php
use App\Http\Resources\UserResource;

public function show($id)
{
    return $this->responseWithResource(
        UserResource::class,
        function () use ($id) {
            return User::findOrFail($id);
        }
    );
}

public function index()
{
    return $this->responseWithResourceCollection(
        UserResource::class,
        function () {
            return User::all();
        }
    );
}
```

### 5. Custom success/failure classes

You can inject different response classes (must extend `AbstractResponse`):

```php
$this->response(
    callback: fn() => $someData,
    successResponseCassName: CustomSuccess::class,
    failureResponseCassName: CustomFailure::class
);
```

### 6. Enabling stack traces in error responses

Set `$hasTrace = true` in your controller to include the exception trace in failure responses.

```php
class MyController extends HttpResponseController
{
    protected bool $hasTrace = true;
}
```

### 7. Getting client IP (with CloudFlare support)

```php
$ip = $this->getUserIP(); // Automatically checks HTTP_CF_CONNECTING_IP first
```

## Response Classes

| Class                        | Description                                                      | Default HTTP status |
|------------------------------|------------------------------------------------------------------|---------------------|
| `ResponseSuccess`            | Standard success response with `success: true`                  | 200 OK              |
| `ResponseCreated`            | Success response for resource creation                          | 201 Created         |
| `ResponseOk`                 | Minimal JSON `{"status":"ok"}`                                  | 200 OK              |
| `ResponseFailure`            | Error response with message and optional trace                  | 400 Bad Request     |
| `ResponseHttpStatusWorkaround` | Same as `ResponseFailure` but `setStatus()` does nothing (legacy compatibility) | 400 Bad Request |

All responses extend `AbstractResponse` and provide:

- `asJSON()` – returns `Illuminate\Http\JsonResponse`
- `asPlainText()` – returns `Illuminate\Http\Response` (plain text)
- `setData()`, `setStatus()`, `getData()`, etc.

## JSON Response Structure

The `TAsJsonStandard` trait (used in `ResponseSuccess` and `ResponseFailure`) produces the following JSON shape:

```json
{
    "success": true,
    "data": { ... },
    "error": null,
    "status": 200
}
```

For failures:

```json
{
    "success": false,
    "data": null,
    "error": "Something went wrong",
    "status": 400
}
```

*You can override the trait or implement your own `asJSON()` method in custom responses.*

## Exception Handling in `response()` methods

When an exception is caught:

- Message (and optional stack trace) is placed into the `error` field.
- HTTP status code is taken from the exception’s `getCode()` (if between 200–999), otherwise defaults to `400`.
- The response is always returned as JSON.

## Extending

### Create a custom response class

```php
use SoftInvest\Http\Responses\AbstractResponse;
use Illuminate\Http\JsonResponse;

class MyCustomResponse extends AbstractResponse
{
    protected ?bool $success = true;
    protected $status = 200;

    public function asJSON(): JsonResponse
    {
        return new JsonResponse([
            'custom' => $this->data,
            'version' => '1.0'
        ], $this->status);
    }
}
```

Then use it with `$this->response(..., successResponseCassName: MyCustomResponse::class)`.

## Advanced: Direct usage without controller

You can instantiate responses directly:

```php
use SoftInvest\Http\Responses\ResponseSuccess;
use SoftInvest\Http\Responses\ResponseFailure;

$success = (new ResponseSuccess())->setData(['id' => 1])->asJSON();
$failure = (new ResponseFailure('Not found', 404))->asJSON();
```

## License

GPLv2