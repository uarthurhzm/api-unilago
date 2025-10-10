<?php

namespace App\Application\Controllers;

use App\Infrastructure\Http\Request;

abstract class ControllerBase
{
    protected function getRequestData(Request $request): array
    {
        return $request->getData();
    }

    protected function getParam(Request $request, string $key, $default = null)
    {
        return $request->getParams()[$key] ?? $default;
    }
}
