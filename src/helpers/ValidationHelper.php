<?php

namespace src\helpers;

use finfo;
use src\types\GeneralValueType;
use src\types\PostValueType;
use src\types\UserValueType;

class ValidationHelper
{
    /**
     * データを検証する
     * 
     * @param array $fields フィールドの構造を指定 例:['content' => ValueType::STRING]
     * @param array $data 検証するデータ 例:['content' => 'test']
     * @return array 検証結果 例:['content' => 'test']
     */
    public static function validateFields(array $fields, array $data): array
    {

        /* ex:
            fields = [
                'content' => ValueType::STRING
            ];

            $data($_POST) = [
                'content' => 'test'
            ]
        */

        $validatedData = [];

        foreach ($fields as $field => $type) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new \InvalidArgumentException("フィールドが見つかりません: $field");
            }

            $value = $data[$field];

            $validatedValue = match ($type) {
                GeneralValueType::STRING => self::string($value),
                GeneralValueType::INT => self::integer($value),
                GeneralValueType::DATE => self::date($value),
                UserValueType::ACCOUNT_NAME => self::accountName($value),
                UserValueType::USERNAME => self::username($value),
                UserValueType::EMAIL => self::email($value),
                UserValueType::PASSWORD => self::password($value),
                UserValueType::AGE => self::age($value),
                UserValueType::LOCATION => self::location($value),
                UserValueType::DESCRIPTION => self::description($value),
                PostValueType::CONTENT => self::post($value),
                PostValueType::MEDIA => self::media($value),
                PostValueType::TYPE_REPLY_TO => self::postType($value),
                default => throw new \InvalidArgumentException(sprintf("フィールドに無効なタイプが指定されています: %s、タイプは%sです", $field, $type)),
            };

            $validatedData[$field] = $validatedValue;
        }

        return $validatedData;
    }

    public static function integer($value, float $min = -INF, float $max = INF): int
    {
        $value = filter_var($value, FILTER_VALIDATE_INT, ["min_range" => (int) $min, "max_range" => (int) $max]);

        if ($value === false) throw new \InvalidArgumentException("有効な整数ではありません");

        return $value;
    }

    public static function string($value): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException("有効な文字列ではありません");
        }

        return $value;
    }

    public static function email(string $value): string
    {
        $value = filter_var($value, FILTER_VALIDATE_EMAIL);

        if (!$value) {
            throw new \InvalidArgumentException("有効なEメールではありません");
        }

        return $value;
    }


    public static function date(string $date, string $format = 'Y-m-d'): string
    {
        $d = \DateTime::createFromFormat($format, $date);
        if ($d && $d->format($format) === $date) {
            return $date;
        }

        throw new \InvalidArgumentException(sprintf("%s の日付形式が無効です。 必要な形式: %s", $date, $format));
    }

    public static function password(string $password): string
    {
        $isValid = is_string($password) && strlen($password) >= 8 && // Minimum 8 characters
            strlen($password) <= 30 && // Max 30 characters
            preg_match('/[A-Z]/', $password) && // 少なくとも1文字の大文字
            preg_match('/[a-z]/', $password) && // 少なくとも1文字の小文字
            preg_match('/\d/', $password) && // 少なくとも1桁
            preg_match('/[\W_]/', $password); // 少なくとも1つの特殊文字（アルファベット以外の文字）

        if (!$isValid) {
            throw new \InvalidArgumentException("有効なパスワードではありません");
        }

        return $password;
    }


    public static function accountName(string $accountName): string
    {

        self::unicodeString($accountName);

        $minAccountName = 1;
        $maximumAccountName = 50;

        if (mb_strlen($accountName, "UTF-8") < $minAccountName || mb_strlen($accountName, "UTF-8") > $maximumAccountName) {
            throw new \LengthException("アカウント名は1文字以上、50文字以内です");
        }

        return $accountName;
    }

    public static function unicodeString(string $value): bool
    {
        // UTF−８かどうか、置換文字（U+FFFD）が含まれているかどうかをチェック
        if (!mb_check_encoding($value, 'UTF-8') || strpos($value, "\xEF\xBF\xBD") !== false) {
            throw new \InvalidArgumentException("無効な文字が含まれています");
        }
        return true;
    }

    public static function username(string $username): string
    {

        self::unicodeString($username);

        $minAccountName = 5;
        $maxAccountName = 50;

        if (mb_strlen($username, "UTF-8") > $maxAccountName || mb_strlen($username, "UTF-8") < $minAccountName) {
            throw new \LengthException("ユーザー名の有効な文字数は5文字以上、15文字以内です");
        }

        return $username;
    }

    public static function age(int $age): int
    {
        $minAge = 1;
        $maxAge = 150;

        if ($age < $minAge || $age > $maxAge) {
            throw new \LengthException("有効な年齢が入力されていません");
        }

        return $age;
    }

    public static function location(string $location): string
    {
        self::unicodeString($location);

        $maxLen = 30;

        if(mb_strlen($location) > $maxLen){
            throw new \LengthException("場所に入力できる文字数は30文字までです");
        }

        return $location;
    }

    public static function description(string $description): string
    {
        self::unicodeString($description);

        $maxLen = 160;

        if (mb_strlen($description, "UTF-8") > $maxLen) {
            throw new \LengthException("プロフィールの最大文字数は160文字です");
        }

        return $description;
    }

    public static function post(string $content): string
    {
        self::unicodeString($content);

        $maxContentSize = 140;

        if (mb_strlen($content, "UTF-8") > $maxContentSize) {
            throw new \LengthException("最大文字数は140文字です");
        }

        return $content;
    }

    public static function media(string $path): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        if (str_starts_with($mime, 'image/')) {
            self::image($path);
        } else if (str_starts_with($mime, 'video/')) {
            self::video($path);
        }

        return $path;
    }

    public static function image(string $path, string $type = 'post'): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);
        $byteSize = filesize($path);

        if($type === 'post'){
            $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/jpg',  'image/gif', 'image/webp'];
        }else if($type === 'avatar'){
            $allowedMimeTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
        }
        $maxImageSize =  5 * 1024 * 1024;

        if (!in_array($mime, $allowedMimeTypes) || $byteSize > $maxImageSize) {
            throw new \InvalidArgumentException("画像は5MB以内かつ、jpg, png, gif, webp形式のみ対応しています");
        }
        return $path;
    }

    public static function video(string $path): string
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);
        $byteSize = filesize($path);

        $allowedMimeTypes = ['video/mp4', 'video/quicktime'];
        $maxVideoSize =  40 * 1024 * 1024;

        if (!in_array($mime, $allowedMimeTypes) || $byteSize > $maxVideoSize) {
            throw new \InvalidArgumentException("動画は40MB以内かつ、mp4, movの拡張式のみ対応しています");
        }

        // 秒数の検証
        // FFmpeg コマンドを実行して動画の情報を取得
        $ffmpegOutput = shell_exec(sprintf("ffmpeg -i %s 2>&1", $path));

        // 正規表現を使って動画の長さを抽出
        if (preg_match('/Duration: (\d{2}):(\d{2}):(\d{2})/', $ffmpegOutput, $matches)) {
            $hours = (int)$matches[1];
            $minutes = (int)$matches[2];
            $seconds = (int)$matches[3];

            // 動画の総秒数を計算
            $totalSeconds = $hours * 3600 + $minutes * 60 + $seconds;

            // 動画の長さが30秒より長いかどうかを確認
            if ($totalSeconds > 30) {
                throw new \InvalidArgumentException("30秒より長い動画はアップロードできません。");
            }
        } else {
            // 動画の情報を取得できない場合はエラーとして処理
            throw new \Exception("動画の情報を取得できませんでした。");
        }


        return $path;
    }

    public static function postType(string $type): string
    {
        $allowedMimeTypes = ['post', 'comment'];

        if (!in_array($type, $allowedMimeTypes)) {
            throw new \InvalidArgumentException("無効なタイプが入力されました。");
        }

        return $type;
    }
}
