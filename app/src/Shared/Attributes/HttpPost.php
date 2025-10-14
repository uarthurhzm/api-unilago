<?php

namespace App\Shared\Attributes;

use App\Application\Middlewares\AuthMiddleware;
use App\Shared\Enum\MethodEnum;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class HttpPost extends Route
{
    public function __construct(
        string $path,
        array $middlewares = [AuthMiddleware::class]
    ) {
        parent::__construct($path, MethodEnum::POST->value, $middlewares);
    }
}
