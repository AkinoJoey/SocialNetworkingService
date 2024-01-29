<?php

namespace src\helpers;

use src\database\data_access\DAOFactory;
use src\models\User;
use src\exceptions\AuthenticationFailureException;
use src\helpers\Settings;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Authenticate
{
    // 認証されたユーザーの状態をこのクラス変数に保持します
    private static ?User $authenticatedUser = null;
    private const USER_ID_SESSION_KEY = 'user_id';

    public static function loginAsUser(User $user): bool
    {
        if ($user->getId() === null) throw new \Exception('Cannot login a user with no ID.');
        if (isset($_SESSION[self::USER_ID_SESSION_KEY])) throw new \Exception('User is already logged in. Logout before continuing.');

        $_SESSION[self::USER_ID_SESSION_KEY] = $user->getId();
        return true;
    }

    public static function logoutUser(): bool
    {
        if (isset($_SESSION[self::USER_ID_SESSION_KEY])) {
            unset($_SESSION[self::USER_ID_SESSION_KEY]);
            self::$authenticatedUser = null;
            return true;
        } else throw new \Exception('No user to logout.');
    }

    private static function retrieveAuthenticatedUser(): void
    {
        if (!isset($_SESSION[self::USER_ID_SESSION_KEY])) return;
        $userDao = DAOFactory::getUserDAO();
        self::$authenticatedUser = $userDao->getById($_SESSION[self::USER_ID_SESSION_KEY]);
    }

    public static function isLoggedIn(): bool
    {
        self::retrieveAuthenticatedUser();
        return self::$authenticatedUser !== null;
    }

    public static function isEmailVerified(): bool
    {
        if (self::$authenticatedUser === null) return false;
        return self::$authenticatedUser->getEmailVerified();
    }

    public static function getAuthenticatedUser(): ?User
    {
        self::retrieveAuthenticatedUser();
        return self::$authenticatedUser;
    }

    /**
     * @throws AuthenticationFailureException
     */
    public static function authenticate(string $email, string $password): User
    {
        $userDAO = DAOFactory::getUserDAO();
        self::$authenticatedUser = $userDAO->getByEmail($email);

        // ユーザーが見つからない場合はnullを返します
        if (self::$authenticatedUser === null) throw new AuthenticationFailureException("Could not retrieve user by specified email %s " . $email);

        // データベースからハッシュ化されたパスワードを取得します
        $hashedPassword = $userDAO->getHashedPasswordById(self::$authenticatedUser->getId());

        if (password_verify($password, $hashedPassword)) {
            self::loginAsUser(self::$authenticatedUser);
            return self::$authenticatedUser;
        } else throw new AuthenticationFailureException("Invalid password.");
    }

    public static function sendVerificationEmail(User $user, string $url): bool
    {
        // 例外を有効にして PHPMailer を起動します。
        $mail = new PHPMailer(true);

        try {
            // サーバの設定
            $mail->isSMTP();                                      // SMTPを使用するようにメーラーを設定します。
            $mail->Host       = Settings::env('MAIL_HOST');                 // GmailのSMTPサーバ
            $mail->SMTPAuth   = true;                             // SMTP認証を有効にします。
            $mail->Username   = Settings::env('MAIL_USER');       // SMTPユーザー名
            $mail->Password   = Settings::env('MAIL_PASSWORD');   // SMTPパスワード
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // 必要に応じてTLS暗号化を有効にします。
            $mail->Port       = 587;                              // 接続先のTCPポート
            $mail->CharSet = 'UTF-8';

            // 受信者
            $mail->setFrom($mail->Username, 'ten'); // 送信者設定
            $mail->addAddress($user->getEmail(), 'My User');          // 受信者を追加します。

            $mail->Subject = "メールアドレスを確認してください";

            // HTMLコンテンツ
            $mail->isHTML(); // メール形式をHTMLに設定します。
            $body = <<<MAIL
            <h2>メールアドレスを確認してください</h2>

            <p> tenアカウントの作成に必要なステップとして、このメールアドレスが正しいことを確認しましょう。新しいアカウントにこのアドレスを使うことを確認してください。</p>

            <p>tenを使い始めるには、以下のURLをクリックしてださい。</p>

            <a href="http://{$url}">http://{$url}</a>

            <p>もしURLをクリックしてもリンクが開かない場合は、URLをコピーしてブラウザに貼り付けてくてださい。</p>

            <p>このメールの有効期間は30分です。</p>

            <p>アカウントの登録ありがとうございます。</p>

            <p>ten</p>

            MAIL;
            $mail->Body =  $body;

            // 本文は、相手のメールプロバイダーがHTMLをサポートしていない場合に備えて、シンプルなテキストで構成されています。

            $altBody = <<<MAIL
            メールアドレスを確認してください

            tenアカウントの作成に必要なステップとして、このメールアドレスが正しいことを確認しましょう。新しいアカウントにこのアドレスを使うことを確認してください。

            tenを使い始めるには、以下のURLをクリックしてださい。

            http://{$url}

            もしURLをクリックしてもリンクが開かない場合は、URLをコピーしてブラウザに貼り付けてくてださい。
            
            このメールの有効期間は30分です。

            アカウントの登録ありがとうございます。

            ten
            MAIL;

            $mail->AltBody = $altBody;

            $success =  $mail->send();

            return $success;
        } catch (Exception $e) {
            throw new Exception("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }
}
