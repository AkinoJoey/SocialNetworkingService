<?php

namespace src\response;

class FlashData
{
    public static function setFlashData(string $name, $data): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$name] = $data;
    }

    public static function getFlashData(string $name): mixed
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_SESSION['flash'][$name])) {
            $message = $_SESSION['flash'][$name];
            unset($_SESSION['flash'][$name]);
            return $message;
        }

        return null;
    }
}
