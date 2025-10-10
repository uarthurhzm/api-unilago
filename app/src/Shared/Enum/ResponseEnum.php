<?php

namespace App\Shared\Enum;

enum ResponseEnum: string
{
    case SEND = 'send';
    case SUCCESS = 'success';
    case ERROR = 'error';
    case NOT_FOUND = 'notFound';
    case BAD_REQUEST = 'badRequest';
    case UNAUTHORIZED = 'unauthorized';
    case FORBIDDEN = 'forbidden';
    case CONFLICT = 'conflict';
    case CREATED = 'created';
    case NO_CONTENT = 'noContent';
}
