<?php

use App\Shared\Enum\MethodEnum;
use App\Shared\Enum\StatusCodeEnum;
use App\Shared\Utils\Server;

header("ngrok-skip-browser-warning: any");
header("Access-Control-Expose-Headers: ngrok-skip-browser-warning");

$originsAllowed = [
    'http://192.168.0.11:5173',
    'https://192.168.0.11:5173',
    'http://192.168.0.11:4173',
    'https://192.168.0.11:4173',
    'https://uncomplimentary-osmically-kolten.ngrok-free.dev',
    'https://nova-area-aluno.netlify.app'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin) {
    $isAllowed = false;

    // verifica origens exatas
    if (in_array($origin, $originsAllowed, true)) {
        $isAllowed = true;
    } else if (
        strpos($origin, 'https://') === 0 &&
        (strpos($origin, '.ngrok-free.dev') !== false ||
            strpos($origin, '.netlify.app') !== false)
    ) {
        $isAllowed = true;
    }

    if ($isAllowed) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Vary: Origin");
    } else {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Credentials: false");
    }
} else {
    // fallback se o navegador não enviar Origin
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: false");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, ngrok-skip-browser-warning");
header("Content-Type: application/json");

if (Server::REQUEST_METHOD() === MethodEnum::OPTIONS->value) {
    http_response_code(StatusCodeEnum::OK->value);
    exit();
}
