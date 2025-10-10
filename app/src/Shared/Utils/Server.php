<?php

namespace App\Shared\Utils;

class Server
{
    public static function REQUEST_METHOD(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function REQUEST_URI(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function REMOTE_IP(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}
