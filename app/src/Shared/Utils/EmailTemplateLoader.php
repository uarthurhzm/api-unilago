<?php

namespace App\Shared\Utils;

class EmailTemplateLoader
{
    public static function render(string $templateName, array $vars): string
    {
        $path = __DIR__ . "/../../Templates/Emails/{$templateName}.php";

        if (!file_exists($path)) {
            throw new \Exception("Template de email não encontrado: {$templateName}");
        }

        // Extrai variáveis (ex: ['user' => $user] vira $user)
        extract($vars);

        return include $path;
    }
}
