<?php

namespace src\helpers;

class CrossSiteForgeryProtection
{
    public static function getToken()
    {
        return $_SESSION['csrf_token'];
    }
}
