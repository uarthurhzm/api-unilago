<?php

// Carrega as variáveis de ambiente do arquivo .env
require_once dirname(__DIR__, 2) . '/app/src/Shared/Utils/DotEnv.php';

use App\Shared\Utils\DotEnv;

$envPath = dirname(__DIR__, 2) . '/.env';
if (file_exists($envPath)) {
    DotEnv::load($envPath);
}

spl_autoload_register(function ($class) {
    $file = str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $class) . '.php';
    
    $projectRoot = dirname(__DIR__, 2);
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($projectRoot, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $path) {
        if ($path->isFile() && basename($path) === basename($file)) {
            require_once $path->getPathname();
            return true;
        }
    }

    return false;
});
