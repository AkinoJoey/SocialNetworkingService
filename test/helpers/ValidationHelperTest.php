<?php

namespace test\helpers;

use PHPUnit\Framework\TestCase;
use src\helpers\ValidationHelper;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use test\helpers\ValidationHelperDataProvider;

class ValidationHelperTest extends TestCase
{
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
}
