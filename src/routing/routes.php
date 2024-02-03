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
use src\exceptions\AuthenticationFailureException;
use src\models\Post;
use src\models\Profile;

return [
    '' => Route::create('', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();

        // TODO: フォロワーのツイートを見られるようにする
        $postDao = DAOFactory::getPostDAO();
        $posts = $postDao->getTwentyPosts($user->getId(), 0);

        return new HTMLRenderer('page/top', ['posts'=> $posts]);
    })->setMiddleware([]),
    'guest' => Route::create('guest', function (): HTTPRenderer {
        return new HTMLRenderer('page/guest');
    })->setMiddleware([]),
    'signup' => Route::create('signup', function (): HTTPRenderer {
        return new HTMLRenderer('page/signup');
    })->setMiddleware(['guest']),
    'form/signup' => Route::create('form/signup', function (): HTTPRenderer {
        // TODO: エラーのtry-catch
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'account_name' => ValueType::STRING,
                'email' => ValueType::EMAIL,
                'password' => ValueType::PASSWORD,
                'confirm_password' => ValueType::PASSWORD,
            ];

            $userDao = DAOFactory::getUserDAO();

            // TODO: 厳格なバリデーションを作成
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);

            if ($validatedData['confirm_password'] !== $validatedData['password']) {
                FlashData::setFlashData('error', 'パスワードが一致しません');
                return new RedirectRenderer('signup');
            }

            // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
            if ($userDao->getByEmail($validatedData['email'])) {
                FlashData::setFlashData('error', '既に登録済みのEメールです');
                return new RedirectRenderer('signup');
            }

            // 新しいUserオブジェクトを作成します
            $user = new User(
                accountName: $validatedData['account_name'],
                email: $validatedData['email'],
                // 初期値としてランダムな文字列を割り当てる
                username:uniqid("")
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
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Invalid Data.');
            return new RedirectRenderer('signup');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'An error occurred.');
            return new RedirectRenderer('signup');
        }
    })->setMiddleware(['guest']),
    'verify/email' => Route::create('verify/email', function (): HTTPRenderer {
        $required_fields = [
            'id' => ValueType::INT,
            'user' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET, true);

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
        return new RedirectRenderer('profile/edit');
    }),
    'profile/edit' => Route::create('profile/edit', function() : HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();
        
        $profileDao = DAOFactory::getProfileDAO();
        $profile = $profileDao->getByUserId($user->getId());

        return new HTMLRenderer('page/profile_form', ['user'=>$user, 'profile'=> $profile]);

    })->setMiddleware(['auth']),
    'form/profile/edit' => Route::create('form/profile/edit', function() : HTTPRenderer {
        // TODO: エラーのtry-catch
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');
        
        // TODO: 厳格なバリデーション。usernameは一意かどうか確認する.英数字のみ
        $nullableFields = [
            'id' => ValueType::INT,
            'username' => ValueType::STRING,
            'age' => ValueType::INT,
            'location' => ValueType::STRING,
            'description' => ValueType::STRING
        ];

        $validatedData = ValidationHelper::validateFields($nullableFields, $_POST, false);

        $user = Authenticate::getAuthenticatedUser();
        // Profileオブジェクトを作成
        $profile = new Profile(
            userId: $user->getId(),
            id: $validatedData['id'],
            age: $validatedData['age'],
            location: $validatedData['location'],
            description: $validatedData['description']
        );

        $profileDao = DAOFactory::getProfileDAO();
        if(isset($validatedData['id'])) $success = $profileDao->update($profile);
        else $success = $profileDao->create($profile);

        if (!$success) throw new Exception('Database update failed!');

        $userDao = DAOFactory::getUserDAO();

        $user->setUsername($validatedData['username']);
        
        $updatedSuccess = $userDao->update($user);

        if (!$updatedSuccess) throw new Exception('Failed to update user!');

        return new RedirectRenderer('profile');
    }),
    'login' => Route::create('login', function (): HTTPRenderer {
        return new HTMLRenderer('page/login');
    })->setMiddleware(['guest']),
    'form/login' => Route::create('form/login', function (): HTTPRenderer {
        try {

            // TODO: Eメール認証していないとログインできないようにする
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'email' => ValueType::EMAIL,
                'password' => ValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);

            Authenticate::authenticate($validatedData['email'], $validatedData['password']);

            FlashData::setFlashData('success', 'ログインしました');
            return new RedirectRenderer('');
        } catch (AuthenticationFailureException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'Eメールもしくはパスワードが間違っています');
            return new RedirectRenderer('login');
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', '無効なデータです');
            return new RedirectRenderer('login');
        } catch (Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('login');
        }
    })->setMiddleware(['guest']),
    'logout' => Route::create('logout', function (): HTTPRenderer {
        Authenticate::logoutUser();
        FlashData::setFlashData('success', 'ログアウトしました');
        return new RedirectRenderer('login');
    })->setMiddleware(['auth']),
    'profile' => Route::create('profile', function (): HTTPRenderer {
        // TODO: 厳格なバリデーション
        $required_fields = [
            'username'=> ValueType::STRING
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET,true);

        $userDao = DAOFactory::getUserDAO();
        $user = $userDao->getByUsername($validatedData['username']);

        $profileDao = DAOFactory::getProfileDAO();
        $profile = $profileDao->getByUserId($user->getId());

        $postDao = DAOFactory::getPostDAO();
        $posts = $postDao->getTwentyPosts($user->getId(), 0);

        return new HTMLRenderer('page/profile',['user'=>$user, 'profile'=>$profile, 'posts'=>$posts]);
    })->setMiddleware(['auth']),
    'form/post' => Route::create('form/post', function() : HTTPRenderer {
        // TODO: try-catch文を書く

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        $required_fields = [
            'content' => ValueType::STRING,
        ];

        // TODO: 厳格なバリデーションを作成
        $validatedRequiredData = ValidationHelper::validateFields($required_fields,$_POST, true);

        $nullableFields = [
            'media_path' => ValueType::STRING,
            'scheduled_at' => ValueType::DATE
        ];

        $validatedNullableData = ValidationHelper::validateFields($nullableFields, $_POST, false);
        $validatedData = array_merge($validatedRequiredData, $validatedNullableData);

        // TODO: 画像アップロード時の振る舞いを追加

        $user = Authenticate::getAuthenticatedUser();

        $numberOfCharacters = 18;
        $randomString = bin2hex(random_bytes($numberOfCharacters / 2)); 

        $post = new Post(
            content: $validatedData['content'],
            url: $randomString,
            userId: $user->getId(),
            mediaPath: $validatedData['media_path'],
            scheduledAt: $validatedData['scheduled_at']
        );

        $postDao = DAOFactory::getPostDAO();
        $success = $postDao->create($post);

        if (!$success) throw new Exception('Failed to create a post!');

        return new RedirectRenderer('');
    })
];
