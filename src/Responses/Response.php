<?php

namespace Larafly\Apidoc\Responses;

use Illuminate\Contracts\Support\Responsable;
use Larafly\Apidoc\Exceptions\ApiException;

class Response implements Responsable
{
    public static string|array $message;
    public static int $code = 200;
    /**
     * @param  string|array
     * @param int $code return exception code
     *
     * @throws ApiException
     */
    final public static function error(string|array $message, int $code = 0): void
    {
        static::$message = $message;

    }

    public function toResponse($request)
    {
        return new JsonResponse();
    }
}
