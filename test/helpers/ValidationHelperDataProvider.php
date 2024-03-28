<?php

namespace test\helpers;

use src\types\GeneralValueType;
use src\types\UserValueType;

class ValidationHelperDataProvider
{
    public static function validFieldsProvider(): array
    {
        return [
            // 有効なフィールドとデータ
            [
                ['content' => GeneralValueType::STRING],
                ['content' => 'This is a valid content'],
            ],
            [
                ['age' => UserValueType::AGE],
                ['age' => 25],
            ],
        ];
    }

    public static function invalidFieldsProvider(): array
    {
        return [
            // フィールドが欠けている場合
            [
                ['content' => GeneralValueType::STRING],
                [],
                "フィールドが見つかりません: content",
            ],
            // 無効なフィールドの型が指定された場合
            [
                ['content' => 'invalid_type'],
                ['content' => 'This is a valid content'],
                "フィールドに無効なタイプが指定されています: content、タイプはinvalid_typeです",
            ],
        ];
    }

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
            [str_repeat('1Aa@!', 6)] // 30文字のパスワード
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
            [str_repeat('1Aa!', 8)] //30文字を越しているパスワード
        ];
    }

    public static function validAccountNameProvider(): array
    {
        return [
            [str_repeat("あ",50)], // 最大文字数（50文字以下）
            ['s'], // 最小文字数のアカウント名
            ["🌝"]
        ];
    }

    public static function invalidAccountNameProvider(): array
    {
        return [
            [''], // 最小文字より少ない
            [str_repeat("a", 51)], // 最大文字数を超えるアカウント名
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

    public static function validUsernameProvider(): array
    {
        return [
            [str_repeat('a', 4)], // 最小文字数のユーザー名
            ['1234'], // 有効なユーザー名
            ['user_name'], // 有効なユーザー名
            [str_repeat('a', 15)], // 最大文字数のユーザー名
        ];
    }

    public static function invalidUsernameProvider(): array
    {
        return [
            ["🌝🌝🌝🌝🌝", \InvalidArgumentException::class ,"ユーザー名は半角英数字とアンダースコアのみを含む必要があります"],
            ["あいうえお", \InvalidArgumentException::class ,"ユーザー名は半角英数字とアンダースコアのみを含む必要があります"],
            ['123', \LengthException::class ,"ユーザー名の有効な文字数は4文字以上、15文字以内です"], // 最小文字数未満のユーザー名
            [str_repeat('a', 16), \LengthException::class, "ユーザー名の有効な文字数は4文字以上、15文字以内です"], // 最大文字数を超えるユーザー名
        ];
    }

    public static function validAgeProvider(): array
    {
        return [
            [1], // 最小年齢
            [18], // 有効な年齢
            [100], // 有効な年齢
            [150], // 最大年齢
        ];
    }

    public static function invalidAgeProvider(): array
    {
        return [
            [0], // 最小年齢未満
            [151], // 最大年齢を超える
            [-1], // 負の年齢
            [1000], // 無効な年齢
        ];
    }

    public static function validLocationProvider(): array
    {
        return [
            // 有効な場所
            ['東京'],
            ['New York'],
            ['London'],
            ['Paris'],
            ['Berlin'],
            [''],
            [' '],
            [str_repeat('あ',30)]
        ];
    }

    public static function invalidLocationProvider(): array
    {
        return [
            // 31文字の無効な場所
            [str_repeat('a', 31)],
        ];
    }


    public static function validDescriptionProvider(): array
    {
        return [
            [''],
            [' '], // 空白
            [str_repeat('a', 160)], // 最大文字数の説明
            ['description'], // 最大文字数以内の説明
        ];
    }

    public static function invalidDescriptionProvider(): array
    {
        return [
            [str_repeat('a', 161)], // 最大文字数を超える説明
        ];
    }

    public static function validPostProvider(): array
    {
        return [
            [""], // 有効な投稿
            [str_repeat('a', 140)], // 有効な投稿
            [str_repeat('🌝', 140)], // 有効な投稿
        ];
    }

    public static function invalidPostProvider(): array
    {
        return [
            [str_repeat('a', 141)], // 140文字を超える投稿
            [str_repeat('🌝', 141)], 

        ];
    }

    public static function validMediaProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/images/4.8MB.jpg'], // 有効な画像
            [__DIR__ . '/../fixtures/videos/3.8MB-30sec.mp4'], // 有効な動画
        ];
    }

    public static function invalidMediaProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/videos/1.4MB-31sec.mp4', "30秒より長い動画はアップロードできません。"], // 30秒より長い動画
            [__DIR__ . '/../fixtures/images/1KB.svg', "画像は5MB以内かつ、jpg, png, gif, webp形式のみ対応しています"], // 対応していない形式の画像

        ];
    }

    public static function validImageProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/images/4.8MB.jpg', 'post'], 
            [__DIR__ . '/../fixtures/images/208KB.jpg', 'post'], 
            [__DIR__ . '/../fixtures/images/832KB.jpeg', 'post'], 
            [__DIR__ . '/../fixtures/images/1.7MB.png', 'post'], 
            [__DIR__ . '/../fixtures/images/175KB.gif', 'post'],
            [__DIR__ . '/../fixtures/images/10KB.webp', 'post'],
            [__DIR__ . '/../fixtures/images/1.7MB.png', 'avatar'],
            [__DIR__ . '/../fixtures/images/10KB.webp', 'avatar'], 
        ];
    }

    public static function invalidImageProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/images/6MB.jpg', 'post'], // 5MB以上の画像
            [__DIR__ . '/../fixtures/images/1KB.svg', 'post'], // 対応していない形式の画像
            [__DIR__ . '/../fixtures/images/175KB.gif', 'avatar'],
        ];
    }

    public static function validVideoProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/videos/3.8MB-30sec.mp4'], 
            [__DIR__ . '/../fixtures/videos/818KB-14sec.mov'], 
        ];
    }

    public static function invalidVideoProvider(): array
    {
        return [
            [__DIR__ . '/../fixtures/videos/162KB-3sec.avi', "動画は40MB以内かつ、mp4, movの拡張式のみ対応しています"], // 対応していない形式
            [__DIR__ . '/../fixtures/videos/52.2MB-3sec.mp4', "動画は40MB以内かつ、mp4, movの拡張式のみ対応しています"], // 40MB以上の動画
            [__DIR__ . '/../fixtures/videos/1.4MB-31sec.mp4', "30秒より長い動画はアップロードできません。"], // 30秒より長い動画
            
        ];
    }

    public static function validPostTypeProvider() : array {
        return [
            ["post"],
            ["comment"]
        ];
    }

    public static function invalidPostTypeProvider() : array {
        return[
            [""],
            ["/post"],
            ["/comment"],
            ['posts'],
            ['comments']
        ];
    }

    public static function validChatTypeProvider(): array
    {
        return [
            ["join"],
            ["message"]
        ];
    }

    public static function invalidChatTypeProvider(): array
    {
        return [
            [""],
            ["joins"],
            ["messages"],
            [' '],
        ];
    }
}
