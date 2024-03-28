<?php

namespace test\helpers;

use PHPUnit\Framework\TestCase;
use src\helpers\ValidationHelper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use test\helpers\ValidationHelperDataProvider;

class ValidationHelperTest extends TestCase
{
    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validFieldsProvider')]
    public function testValidFields(array $fields, array $data): void
    {
        $validatedData = ValidationHelper::validateFields($fields, $data);
        $this->assertSame($data, $validatedData);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidFieldsProvider')]
    public function testInvalidFields(array $fields, array $data, string $expectedExceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        ValidationHelper::validateFields($fields, $data);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validIntegerProvider')]
    public function testValidInteger($value, $min, $max): void
    {
        $this->assertSame($value, ValidationHelper::integer($value, $min, $max));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidIntegerProvider')]
    public function testInvalidInteger($value, $min, $max): void
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("有効な整数ではありません");
        ValidationHelper::integer($value, $min, $max);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validStringProvider')]
    public function testValidString($value): void
    {
        $this->assertSame($value, ValidationHelper::string($value));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidStringProvider')]
    public function testInvalidString($value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("有効な文字列ではありません");
        ValidationHelper::string($value);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validEmailProvider')]
    public function testValidEmail(string $value): void
    {
        $this->assertSame($value, ValidationHelper::email($value));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidEmailProvider')]
    public function testInvalidEmail(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("有効なEメールではありません");
        ValidationHelper::email($value);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validDateProvider')]
    public function testValidDate(string $date, string $format): void
    {
        $this->assertSame($date, ValidationHelper::date($date, $format));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidDateProvider')]
    public function testInvalidDate(string $date, string $format): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf("%s の日付形式が無効です。 必要な形式: %s", $date, $format));
        ValidationHelper::date($date, $format);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validPasswordProvider')]
    public function testValidPassword(string $password): void
    {
        $this->assertSame($password, ValidationHelper::password($password));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidPasswordProvider')]
    public function testInvalidPassword(string $password): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("有効なパスワードではありません");
        ValidationHelper::password($password);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validAccountNameProvider')]
    public function testValidAccountName(string $accountName): void
    {
        $this->assertSame($accountName, ValidationHelper::accountName($accountName));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidAccountNameProvider')]
    public function testInvalidAccountName(string $accountName): void
    {
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage("アカウント名は1文字以上、50文字以内です");
        ValidationHelper::accountName($accountName);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validUnicodeStringProvider')]
    public function testValidUnicodeString(string $value): void
    {
        $this->assertTrue(ValidationHelper::unicodeString($value));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidUnicodeStringProvider')]
    public function testInvalidUnicodeString(string $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("無効な文字が含まれています");
        ValidationHelper::unicodeString($value);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validUsernameProvider')]
    public function testValidUsername(string $username): void
    {
        $this->assertSame($username, ValidationHelper::username($username));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidUsernameProvider')]
    public function testInvalidUsername(string $username, string $expectedException,  string $expectedExceptionMessage): void
    {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);
        ValidationHelper::username($username);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validAgeProvider')]
    public function testValidAge(int $age): void
    {
        $this->assertSame($age, ValidationHelper::age($age));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidAgeProvider')]
    public function testInvalidAge(int $age): void
    {
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage("有効な年齢が入力されていません");
        ValidationHelper::age($age);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validLocationProvider')]
    public function testValidLocation(string $location): void
    {
        $this->assertSame($location, ValidationHelper::location($location));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidLocationProvider')]
    public function testInvalidLocation(string $location): void
    {
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage("場所に入力できる文字数は30文字までです");
        ValidationHelper::location($location);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validDescriptionProvider')]
    public function testValidDescription(string $description): void
    {
        $this->assertSame($description, ValidationHelper::description($description));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidDescriptionProvider')]
    public function testInvalidDescription(string $description): void
    {
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage("プロフィールの最大文字数は160文字です");
        ValidationHelper::description($description);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validPostProvider')]
    public function testValidPost(string $content): void
    {
        $this->assertSame($content, ValidationHelper::post($content));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidPostProvider')]
    public function testInvalidPost(string $content): void
    {
        $this->expectException(\LengthException::class);
        $this->expectExceptionMessage("最大文字数は140文字です");
        ValidationHelper::post($content);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validMediaProvider')]
    public function testValidMedia(string $path): void
    {
        $this->assertSame($path, ValidationHelper::media($path));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidMediaProvider')]
    public function testInvalidMedia(string $path, string $expectedExceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        ValidationHelper::media($path);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validImageProvider')]
    public function testValidImage(string $path, $type): void
    {
        $this->assertSame($path, ValidationHelper::image($path, $type));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidImageProvider')]
    public function testInvalidImage(string $path, $type): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("画像は5MB以内かつ、jpg, png, gif, webp形式のみ対応しています");
        ValidationHelper::image($path,$type);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validVideoProvider')]
    public function testValidVideo(string $path): void
    {
        $this->assertSame($path, ValidationHelper::video($path));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidVideoProvider')]
    public function testInvalidVideo(string $path, string $expectedExceptionMessage): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);
        ValidationHelper::video($path);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validPostTypeProvider')]
    public function testValidPostType(string $type) : void {
        $this->assertSame($type, ValidationHelper::postType($type));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidPostTypeProvider')]
    public function testInvalidPostType(string $type) : void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("無効なタイプが入力されました。");
        ValidationHelper::postType($type);
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'validChatTypeProvider')]
    public function testValidChatType(String $type) : void {
        $this->assertSame($type, ValidationHelper::ChatType($type));
    }

    #[DataProviderExternal(ValidationHelperDataProvider::class, 'invalidChatTypeProvider')]
    public function testInvalidChatType(string $type): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("無効なタイプが入力されました。");
        ValidationHelper::ChatType($type);
    }
}
