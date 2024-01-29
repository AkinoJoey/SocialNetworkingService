<?php

use src\response\HTTPRenderer;
use src\response\render\HTMLRenderer;
use src\types\ValueType;
use src\helpers\ValidationHelper;
use src\response\FlashData;
use src\response\render\RedirectRenderer;
use src\database\data_access\DAOFactory;
use src\models\User;
use src\helpers\Authenticate;
use src\routing\Route;

return [
    '' => Route::create('', function (): HTTPRenderer {
        return new HTMLRenderer('page/top');
    })->setMiddleware([]),
    'guest' => Route::create('guest', function (): HTTPRenderer {
        return new HTMLRenderer('page/guest');
    })->setMiddleware([]),
    'signup' => Route::create('signup', function (): HTTPRenderer {
        return new HTMLRenderer('page/signup');
    })->setMiddleware(['guest']),
    'form/signup' => Route::create('form/signup', function (): HTTPRenderer {
        // TODO: エラーのtry-catch

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        $required_fields = [
            'account_name' => ValueType::STRING,
            'email' => ValueType::EMAIL,
            'password' => ValueType::PASSWORD,
            'confirm_password' => ValueType::PASSWORD,
        ];

        $userDao = DAOFactory::getUserDAO();

        // シンプルな検証
        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

        if ($validatedData['confirm_password'] !== $validatedData['password']) {
            FlashData::setFlashData('error', 'Invalid Password!');
            return new RedirectRenderer('signup');
        }

        // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
        if ($userDao->getByEmail($validatedData['email'])) {
            FlashData::setFlashData('error', 'Email is already in use!');
            return new RedirectRenderer('signup');
        }

        // 新しいUserオブジェクトを作成します
        $user = new User(
            accountName: $validatedData['account_name'],
            email: $validatedData['email'],
        );

        // データベースにユーザーを作成しようとします
        $success = $userDao->create($user, $validatedData['password']);

        if (!$success) throw new Exception('Failed to create new user!');

        // ユーザーログイン
        Authenticate::loginAsUser($user);

        // 期限を30分に設定
        $lasts = 1 * 60 * 30;
        $param = [
            'id' => $user->getId(),
            'user' => hash('sha256', $user->getEmail()),
            'expiration' => time() + $lasts
        ];

        $signedUrl = Route::create('verify/email', function () {
        })->getSignedURL($param);
        Authenticate::sendVerificationEmail($user, $signedUrl);

        FlashData::setFlashData('success', 'Account successfully created. Please check your email!');
        return new RedirectRenderer('login');
    })->setMiddleware(['guest']),
    'verify/email' => Route::create('verify/email', function (): HTTPRenderer {
        $required_fields = [
            'id' => ValueType::INT,
            'user' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $user = Authenticate::getAuthenticatedUser();

        // ユーザーの詳細とパラメーターが一致しているか確認
        if ($user === null || $user->getId() !== $validatedData['id'] || hash('sha256', $user->getEmail()) !== $validatedData['user']) {
            FlashData::setFlashData('error', '無効なURLです。');
            return new RedirectRenderer('signup');
        }

        // email_verifiedを更新
        $user->setEmailVerified(true);
        $userDao = DAOFactory::getUserDAO();
        $userDao->update($user);

        FlashData::setFlashData('success', 'Eメールの確認に成功しました。');
        return new RedirectRenderer('profile');
    }),
    'login' => Route::create('login', function (): HTTPRenderer {
        return new HTMLRenderer('page/login');
    })->setMiddleware(['guest']),
    'profile' => Route::create('profile', function (): HTTPRenderer {
        return new HTMLRenderer('page/profile');
    })->setMiddleware(['auth']),
];
