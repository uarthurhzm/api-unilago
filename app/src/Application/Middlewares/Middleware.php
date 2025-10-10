<?php

namespace App\Application\Middlewares;

abstract class Middleware
{
    abstract public function handle(...$params): array;
}
