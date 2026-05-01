<?php

class CsrfToken
{
    private const SESSION_KEY = '_csrf_token';

    public static function get(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function verify(): bool
    {
        $token = $_POST['_csrf_token'] ?? '';
        return !empty($token) && hash_equals(self::get(), $token);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::get()) . '">';
    }
}
