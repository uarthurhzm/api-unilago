<?php

namespace App\Infrastructure\Http;

use App\Shared\Enum\StatusCodeEnum;

class Response
{
    public static function send($data, string $message, int $statusCode = StatusCodeEnum::OK): void
    {
        http_response_code($statusCode);
        echo json_encode([
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);

        exit();
    }

    public static function success($data = null, string $message = 'Success'): void
    {
        self::send($data, $message, StatusCodeEnum::OK->value);
    }

    public static function error(string $message = 'Error'): void
    {
        self::send(null, $message, StatusCodeEnum::INTERNAL_SERVER_ERROR->value);
    }

    public static function notFound(string $message = 'Not Found'): void
    {
        self::send(null, $message, StatusCodeEnum::NOT_FOUND->value);
    }

    public static function badRequest(string $message = 'Bad Request'): void
    {
        self::send(null, $message, StatusCodeEnum::BAD_REQUEST->value);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::send(null, $message, StatusCodeEnum::UNAUTHORIZED->value);
    }

    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::send(null, $message, StatusCodeEnum::FORBIDDEN->value);
    }

    public static function conflict(string $message = 'conflict'): void
    {
        self::send(null, $message, StatusCodeEnum::CONFLICT->value);
    }

    public static function created($data = null, string $message = 'Created'): void
    {
        self::send($data, $message, StatusCodeEnum::CREATED->value);
    }

    public static function noContent(): void
    {
        http_response_code(StatusCodeEnum::NO_CONTENT->value);
        exit();
    }
}
