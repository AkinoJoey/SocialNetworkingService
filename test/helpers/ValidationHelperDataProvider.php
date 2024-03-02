<?php

namespace test\helpers;

class ValidationHelperDataProvider
{
    public static function validIntegerProvider(): array
    {
        return [
            [5, -INF, INF], // 通常の整数
            [10, 0, 20], // 最小値と最大値の間の整数
            [-100, -200, -50], // 負の整数
            [PHP_INT_MAX, -INF, INF], // PHPの最大整数
        ];
    }

    public static function invalidIntegerProvider(): array
    {
        return [
            ["not_an_integer", -INF, INF], // 整数ではない値
            [3.14, -INF, INF], // 浮動小数点数
            [-INF, -INF, INF], // 範囲外の整数
            [INF, -INF, INF]
        ];
    }

    public static function validStringProvider(): array
    {
        return [
            ["hello"],
            ["world"],
            ["123"],
            ["1.5"],
            ["true"],
            ["false"],
            ["null"],
        ];
    }

    public static function invalidStringProvider(): array
    {
        return [
            [123],
            [1.5],
            [true],
            [false],
            [null],
            [[]],
            [new \stdClass()],
        ];
    }

    public static function validEmailProvider(): array
    {
        return [
            ['test@example.com'], // 有効なEメール
            ['user@mail.co.jp'], // 日本のドメインを含むEメール
            ['1234@test.org'], // 数字を含むEメール
        ];
    }

    public static function invalidEmailProvider(): array
    {
        return [
            ['not_an_email'], // 有効なEメールではない
            ['user@'], // 不完全なEメールアドレス
            ['user@example'], // ドメインが不完全なEメールアドレス
            ['user@example.'], // ドットの後に何も続かないEメールアドレス
            ['user@ex ample.com'], // スペースを含むEメールアドレス
        ];
    }

    public static function validDateProvider(): array
    {
        return [
            ['2023-12-31', 'Y-m-d'], // 正しい日付形式
            ['31-12-2023', 'd-m-Y'], // 別のフォーマットの日付
            ['2023-02-28', 'Y-m-d'], // うるう年以外の日付
        ];
    }

    public static function invalidDateProvider(): array
    {
        return [
            ['2023-02-31', 'Y-m-d'], // 存在しない日付
            ['31-02-2023', 'd-m-Y'], // 存在しない日付（別のフォーマット）
            ['2023-13-31', 'Y-m-d'], // 不正な月
            ['2023-12-32', 'Y-m-d'], // 不正な日
        ];
    }

    public static function validPasswordProvider(): array
    {
        return [
            ['Password1!'], // 大文字、小文字、数字、特殊文字を含む8文字以上のパスワード
            ['MySecurePassword!123'], // さまざまな文字を含む12文字のパスワード
            ['SecurePass123!'], // 数字、大文字、小文字、特殊文字を含む10文字のパスワード
        ];
    }

    public static function invalidPasswordProvider(): array
    {
        return [
            ['pass'], // 8文字未満のパスワード
            ['password'], // 大文字、小文字、数字、特殊文字が含まれていないパスワード
            ['12345678'], // 数字のみのパスワード
            ['PASSWORD'], // 小文字が含まれていないパスワード
            ['Password'], // 数字が含まれていないパスワード
            ['Password!'], // 数字が含まれていないパスワード
            ['Password123'], // 特殊文字が含まれていないパスワード
        ];
    }

    public static function validAccountNameProvider(): array
    {
        return [
            [bin2hex(random_bytes(25))], // 最大文字数（50文字以下）
            ['s'], // 最小文字数のアカウント名
        ];
    }

    public static function invalidAccountNameProvider(): array
    {
        return [
            [''], // 最小文字より少ない
            [bin2hex(random_bytes(26))], // 最大文字数を超えるアカウント名
        ];
    }

    public static function validUnicodeStringProvider(): array
    {
        return [
            ['Hello, こんにちは, नमस्ते'], // Unicode文字列
            ['12345'], // ASCII文字列
            ['😊'], // 絵文字
            ['Привет, 你好'], // サロゲートペアを含む文字列

        ];
    }

    public static function invalidUnicodeStringProvider(): array
    {
        return [
            ["\xEF\xBF\xBD"], // 置換文字
            ["\xC0\xAF"], // 不正なUTF-8バイト列
            ["\x80\x80"], // 不正なUTF-8バイト列
            ["\xF0\x9F\x98\x8D\xFF"], // 不正なUTF-8バイト列
        ];
    }
}
