<?php

namespace App\Shared\Helpers;

class Validators
{
    public static function isEmpty($value): bool
    {
        return !isset($value) || trim($value) === '';
    }

    public static function verifyParameters(array $parameters, array $data): bool
    {
        foreach ($parameters as $param) {
            if (!array_key_exists($param, $data)) {
                // var_dump($param);
                return false;
            }
        }
        return true;
    }

    public static function isNumber($value): bool
    {
        return is_numeric($value);
    }

    public static function isDate($date, $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function isFile($file): bool
    {
        return isset($file) && isset($file['tmp_name']) && is_uploaded_file($file['tmp_name']);
    }

    public static function inOptions($value, array $options): bool
    {
        return in_array($value, $options);
    }

    public static function isPhone($phone): bool
    {
        $cleaned = preg_replace('/\D/', '', $phone);

        // Verifica se o número tem 10 ou 11 dígitos (padrão brasileiro)
        return preg_match('/^\d{10,11}$/', $cleaned) === 1;
    }

    public static function isEmail($email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
