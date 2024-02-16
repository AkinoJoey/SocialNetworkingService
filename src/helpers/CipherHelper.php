<?php 

namespace src\helpers;

use src\helpers\Settings;

class CipherHelper{
    private CONST CHAT_CIPHER_ALGO = 'aes-128-cbc';

    public static function encryptMessage(string $message): array
    {
        $iv = self::generateIv(self::CHAT_CIPHER_ALGO);
        $encrypted = openssl_encrypt($message, self::CHAT_CIPHER_ALGO, Settings::env('ENCRYPTION_PASSPHRASE'), OPENSSL_RAW_DATA, $iv);
        return ['iv' => $iv, 'encrypted' => $encrypted];
    }

    public static function generateIv($cipher): string
    {
        $ivlen = openssl_cipher_iv_length($cipher);
        return openssl_random_pseudo_bytes($ivlen);
    }

    public static function decryptMessage(string $encryptedMessage, string $iv): string
    {
        return openssl_decrypt($encryptedMessage, self::CHAT_CIPHER_ALGO, Settings::env('ENCRYPTION_PASSPHRASE'), OPENSSL_RAW_DATA, $iv);
    }


}