<?php

namespace App\Shared\Enum;

enum DotEnvKeysEnum: string
{
    case DB_HOST = 'DB_HOST';
    case DB_NAME = 'DB_NAME';
    case DB_USERNAME = 'DB_USERNAME';
    case DB_PASSWORD = 'DB_PASSWORD';

    case MAIL_HOST = 'MAIL_HOST';
    case MAIL_PORT = 'MAIL_PORT';
    case MAIL_USERNAME = 'MAIL_USERNAME';
    case MAIL_PASSWORD = 'MAIL_PASSWORD';
    case MAIL_FROM_NAME = 'MAIL_FROM_NAME';
    case MAIL_ENCRYPTION = 'MAIL_ENCRYPTION';

    case APP_ENV = 'APP_ENV';
    case APP_DEBUG = 'APP_DEBUG';
    case APP_URL = 'APP_URL';

    case JWT_ACCESS_SECRET = 'JWT_ACCESS_SECRET';
    case JWT_REFRESH_SECRET = 'JWT_REFRESH_SECRET';
    case JWT_ALGORITHM = 'JWT_ALGORITHM';
    case JWT_EXPIRATION = 'JWT_EXPIRATION';
    case JWT_REFRESH_EXPIRATION = 'JWT_REFRESH_EXPIRATION';
}
