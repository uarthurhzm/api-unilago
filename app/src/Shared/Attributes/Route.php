<?php

namespace App\Shared\Attributes;

use App\Application\Middlewares\AuthMiddleware;
use App\Shared\Enum\MethodEnum;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public function __construct(
        public string $path,
        public string $method = MethodEnum::GET->value,
        public array $middlewares = [AuthMiddleware::class]
    ) {}
}
