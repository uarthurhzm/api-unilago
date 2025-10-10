<?php

namespace App\Shared\Utils;

class DotEnv
{
    protected static array $variables = [];

    /**
     * Carrega as variáveis do arquivo .env
     * 
     * @param string $path Caminho para o arquivo .env
     * @return void
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("Arquivo .env não encontrado: {$path}");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Parse da linha
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                
                $name = trim($name);
                $value = trim($value);
                
                // Remove aspas do valor se existirem
                if (preg_match('/^(["\'])(.*)\\1$/', $value, $matches)) {
                    $value = $matches[2];
                }
                
                // Armazena no array interno
                self::$variables[$name] = $value;
                
                // Define no ambiente do PHP
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                    putenv("{$name}={$value}");
                }
            }
        }
    }

    /**
     * Obtém o valor de uma variável de ambiente
     * 
     * @param string $key Nome da variável
     * @param mixed $default Valor padrão caso não exista
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (isset(self::$variables[$key])) {
            return self::$variables[$key];
        }
        
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }

    /**
     * Verifica se uma variável existe
     * 
     * @param string $key Nome da variável
     * @return bool
     */
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    /**
     * Define uma variável de ambiente (útil para testes)
     * 
     * @param string $key Nome da variável
     * @param mixed $value Valor da variável
     * @return void
     */
    public static function set(string $key, $value): void
    {
        self::$variables[$key] = $value;
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }

    /**
     * Remove todas as variáveis carregadas
     * 
     * @return void
     */
    public static function clear(): void
    {
        foreach (self::$variables as $key => $value) {
            unset($_ENV[$key]);
            putenv($key);
        }
        
        self::$variables = [];
    }
}
