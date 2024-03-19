<?php

use src\response\HTTPRenderer;
use src\response\render\HTMLRenderer;
use src\types\GeneralValueType;
use src\helpers\ValidationHelper;
use src\response\FlashData;
use src\response\render\RedirectRenderer;
use src\database\data_access\DAOFactory;
use src\models\User;
use src\helpers\Authenticate;
use src\routing\Route;
use src\exceptions\AuthenticationFailureException;
use src\helpers\MediaHelper;
use src\models\Post;
use src\models\Profile;
use src\response\render\JSONRenderer;
use src\models\Comment;
use src\models\PostLike;
use src\models\CommentLike;
use src\models\DmMessage;
use src\models\DmThread;
use src\models\Follow;
use src\models\Notification;
use src\types\NotificationType;
use src\types\PostStatusType;
use src\types\UserValueType;
use src\types\PostValueType;
use Symfony\Component\HttpFoundation\JsonResponse;

return [
    '' => Route::create('', function (): HTTPRenderer {
        // TODO: ゲスト用とユーザー用で振り分ける
        $user = Authenticate::getAuthenticatedUser();

        // ゲストの場合
        if ($user === null) {
            return new HTMLRenderer('page/guest');
        }

        $followDao =  DAOFactory::getFollowDAO();
        $followingUserIdList = $followDao->getFollowingUserIdList($user->getId());

        if (count($followingUserIdList) === 0) {
            return new HTMLRenderer('page/top', ['user' => $user, 'tabActive' => 'trend']);
        } else {

            return new HTMLRenderer('page/top', ['user' => $user, 'tabActive' => 'following']);
        }
    })->setMiddleware([]),
    'timeline/following' => Route::create('timeline/following', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();
        $followDao =  DAOFactory::getFollowDAO();
        $followingUserIdList = $followDao->getFollowingUserIdList($user->getId());

        $required_fields = [
            'offset' => GeneralValueType::INT
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $postDao = DAOFactory::getPostDAO();
        $postsByFollowedUsers = $postDao->getPostsByFollowedUsers($followingUserIdList, $user->getId(), $validatedData['offset'], 3);

        $htmlString = "";

        foreach ($postsByFollowedUsers as $post) {
            ob_start();
            $user;
            include(__DIR__ . '/../views/components/post_card.php');
            $postCardHtml = ob_get_clean();
            $htmlString .= $postCardHtml;
        }
        return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
    })->setMiddleware(['auth']),
    'timeline/trend' => Route::create('timeline/trend', function (): HTTPRenderer {
        $required_fields = [
            'offset' => GeneralValueType::INT
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $user = Authenticate::getAuthenticatedUser();
        $postDao = DAOFactory::getPostDAO();
        $trendPosts = $postDao->getTrendPosts($user->getId(), $validatedData['offset'], 3);

        $htmlString = "";
        foreach ($trendPosts as $post) {
            ob_start();
            include(__DIR__ . '/../views/components/post_card.php');
            $postCardHtml = ob_get_clean();
            $htmlString .= $postCardHtml;
        }
        return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
    })->setMiddleware(['auth']),
    'timeline/guest' => Route::create("timeline/guest", function (): HTTPRenderer {
        $required_fields = [
            'offset' => GeneralValueType::INT
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $postDao = DAOFactory::getPostDAO();
        $posts = $postDao->getTrendPostsForGuest($validatedData['offset'], 3); //TODO: 20にする

        $htmlString = "";
        foreach ($posts as $post) {
            ob_start();
            include(__DIR__ . '/../views/components/post_card_for_guest.php');
            $postCardHtml = ob_get_clean();
            $htmlString .= $postCardHtml;
        }

        return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
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
                'account_name' => UserValueType::ACCOUNT_NAME,
                'email' => UserValueType::EMAIL,
                'password' => UserValueType::PASSWORD,
                'confirm_password' => UserValueType::PASSWORD,
            ];

            $userDao = DAOFactory::getUserDAO();

            // TODO: 厳格なバリデーションを作成
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

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
                username: uniqid("")
            );

            // データベースにユーザーを作成しようとします
            $success = $userDao->create($user, $validatedData['password']);

            // 空のプロフィールを作成しておく
            if ($success) {
                $profileDao = DAOFactory::getProfileDAO();
                $profile = new Profile($user->getId());
                $profileDao->create($profile);
            } else {
                throw new Exception('Failed to create new user!');
            }
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
            'id' => GeneralValueType::INT,
            'user' => GeneralValueType::STRING,
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
        return new RedirectRenderer('profile/edit');
    }),
    'profile/edit' => Route::create('profile/edit', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();

        $profileDao = DAOFactory::getProfileDAO();
        $profile = $profileDao->getByUserId($user->getId());

        return new HTMLRenderer('page/profile_form', ['user' => $user, 'profile' => $profile]);
    })->setMiddleware(['auth']),
    'form/profile/edit' => Route::create('form/profile/edit', function (): HTTPRenderer {
        // TODO: エラーのtry-catch
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'id' => GeneralValueType::INT,
                'username' => UserValueType::USERNAME
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            $profileDao = DAOFactory::getProfileDAO();
            $profile = $profileDao->getById($validatedData['id']);

            if (isset($_POST['age']) && $_POST['age'] !== '') {
                $age = ValidationHelper::age($_POST['age']);
                $profile->setAge($age);
            }

            if (isset($_POST['location']) && $_POST['location'] !== '') {
                $location = ValidationHelper::location($_POST['location']);
                $profile->setLocation($location);
            }

            if (isset($_POST['description']) && $_POST['description'] !== '') {
                $description = ValidationHelper::description($_POST['description']);
                $profile->setDescription($description);
            }

            if ($_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $media = ValidationHelper::image($_FILES['media']['tmp_name'], 'avatar');
                $validatedData['media'] = $media;
            }


            if (isset($validatedData['media'])) {
                $tmpPath = $validatedData['media'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($tmpPath);
                $extension = '.' . explode('/', $mime)[1];
                //メディアのファイル名の長さ 
                $numberOfCharacters = 18;
                $basename = bin2hex(random_bytes($numberOfCharacters / 2));
                $filename = $basename . $extension;
                $uploadDir =  __DIR__ .  "/../../public/uploads/";
                $subdirectory = substr($filename, 0, 2) . "/";
                $mediaPath = $uploadDir .  $subdirectory . $filename;

                $uploadSuccess = MediaHelper::uploadMedia($mediaPath, $tmpPath);
                if (!$uploadSuccess) throw new Exception("メディアのアップロードに失敗しました。");

                // 画像を編集
                if (str_starts_with($mime, 'image/')) {
                    $thumbnailPath = $uploadDir .  $subdirectory . explode(".", $filename)[0] . "_thumb" . $extension;

                    $success = MediaHelper::createThumbnail($mediaPath, $thumbnailPath, "400x400");
                    if ($success) {
                        //　名前をオリジナルに変更
                        rename($thumbnailPath, $mediaPath);

                        // 元のアバターを削除
                        $oldAvatarPath = $profile->getProfileImagePath();
                        if (isset($oldAvatarPath)) {
                            unlink($uploadDir . substr($oldAvatarPath, 0, 2) . '/' .  $oldAvatarPath . $profile->getExtension());
                        }
                    }
                    if (!$success) throw new Exception("エラーが発生しました");
                }
                // インスタンスに保存
                $profile->setProfileImagePath($basename);
                $profile->setExtension($extension);
            }

            $profileDao->update($profile);
            if (!$success) throw new Exception('Database update failed!');

            $userDao = DAOFactory::getUserDAO();
            $user = Authenticate::getAuthenticatedUser();
            $user->setUsername($validatedData['username']);

            $updatedSuccess = $userDao->update($user);

            if (!$updatedSuccess) throw new Exception('Failed to update user!');

            FlashData::setFlashData('success', 'プロフィールを更新しました');
            return new JSONRenderer(['status' => 'success']);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        }
    })->setMiddleware(['auth']),
    'login' => Route::create('login', function (): HTTPRenderer {
        return new HTMLRenderer('page/login');
    })->setMiddleware(['guest']),
    'form/login' => Route::create('form/login', function (): HTTPRenderer {
        try {

            // TODO: Eメール認証していないとログインできないようにする
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            $required_fields = [
                'email' => UserValueType::EMAIL,
                'password' => UserValueType::PASSWORD,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

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
            'username' => UserValueType::USERNAME
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $userDao = DAOFactory::getUserDAO();
        $user = $userDao->getByUsername($validatedData['username']);
        $authenticatedUser = Authenticate::getAuthenticatedUser();

        $profileDao = DAOFactory::getProfileDAO();
        $profile = $profileDao->getByUserId($user->getId());

        $followDao = DAOFactory::getFollowDAO();
        $followingList = $followDao->getFollowingUserIdList($user->getId());
        $followingCount = $followingList !== null ? count($followingList) : 0;
        $followerList = $followDao->getFollowerUserIdList($user->getId());
        $followerCount = $followerList !== null ? count($followerList) : 0;

        // 自分のプロフィールを見る場合
        if ($user->getId() === $authenticatedUser->getId()) {
            return new HTMLRenderer('page/profile', ['user' => $user, 'profile' => $profile, 'authenticatedUser' => $authenticatedUser, 'followingCount' => $followingCount, 'followerCount' => $followerCount]);
        } else {
            $dmThreadDao = DAOFactory::getDmThreadDAO();

            // dm用のURLが作成されていない場合は作成する
            $dmThread = $dmThreadDao->getByUserIds($user->getId(), $authenticatedUser->getId());

            if ($dmThread === null) {
                $numberOfCharacters = 18;
                $randomString = bin2hex(random_bytes($numberOfCharacters / 2));
                $dmThread = new DmThread(
                    url: $randomString,
                    userId1: $user->getId(),
                    userId2: $authenticatedUser->getId()
                );

                $success = $dmThreadDao->create($dmThread);
                if (!$success) throw new Exception('Failed to create a dm thread!');
            }

            $isFollow = $followDao->isFollow($authenticatedUser->getId(), $user->getId());

            return new HTMLRenderer('page/profile', ['user' => $user, 'profile' => $profile, 'authenticatedUser' => $authenticatedUser, 'followingCount' => $followingCount, 'followerCount' => $followerCount, 'isFollow' => $isFollow, 'dmUrl' => $dmThread->getUrl()]);
        }
    })->setMiddleware(['auth']),
    'profile/posts' => Route::create('profile/posts', function (): HTTPRenderer {
        $required_fields = [
            'user_id' => GeneralValueType::INT,
            'offset' => GeneralValueType::INT
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $postDao = DAOFactory::getPostDAO();
        $posts = $postDao->getPostsByFollowedUsers([], $validatedData['user_id'], $validatedData['offset'], 3); //TODO: 20に変更する

        $user = Authenticate::getAuthenticatedUser();
        $htmlString = "";

        foreach ($posts as $post) {
            ob_start();
            $user;
            include(__DIR__ . '/../views/components/post_card.php');
            $postCardHtml = ob_get_clean();
            $htmlString .= $postCardHtml;
        }

        return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
    })->setMiddleware(['auth']),
    'form/new' => Route::create('form/new', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

            // 文字が空かつ、メディアがない場合はアラートを出す
            if ($_POST['content'] === "" && $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
                return new JSONRenderer(['status' => 'error', "message" => "投稿にはテキスト、あるいはメディアのどちらかが必要です"]);
            }

            $user = Authenticate::getAuthenticatedUser();

            //投稿のURLとメディアのファイル名の長さ 
            $numberOfCharacters = 18;

            $post = new Post(
                url: bin2hex(random_bytes($numberOfCharacters / 2)),
                userId: $user->getId(),
            );

            if ($_POST['content'] !== "") {
                $fields = [
                    'content' => PostValueType::CONTENT
                ];
                $validatedData = ValidationHelper::validateFields($fields, $_POST);
                $post->setContent($validatedData['content']);
            }

            if ($_FILES['media']['error'] === UPLOAD_ERR_OK) {
                $fields = [
                    'media' => PostValueType::MEDIA
                ];
                $validatedData = ValidationHelper::validateFields($fields, ['media' => $_FILES['media']['tmp_name']]);
            }


            if (isset($validatedData['media'])) {
                $tmpPath = $validatedData['media'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($tmpPath);
                $extension = explode('/', $mime)[1] === "quicktime" ? '.mov' : '.' . explode('/', $mime)[1];
                $basename = bin2hex(random_bytes($numberOfCharacters / 2));
                $filename = $basename . $extension;
                $uploadDir =  __DIR__ .  "/../../public/uploads/";
                $subdirectory = substr($filename, 0, 2) . "/";
                $mediaPath = $uploadDir .  $subdirectory . $filename;

                $uploadSuccess = MediaHelper::uploadMedia($mediaPath, $tmpPath);
                if (!$uploadSuccess) throw new Exception("メディアのアップロードに失敗しました。");
                error_log($uploadSuccess);

                // インスタンスに保存
                $post->setMediaPath($basename);
                $post->setExtension($extension);

                // gif以外はサムネを作成する
                if (str_starts_with($mime, 'image/') && $extension !== ".gif") {
                    $thumbnailPath = $uploadDir .  $subdirectory . explode(".", $filename)[0] . "_thumb" . $extension;

                    $success = MediaHelper::createThumbnail($mediaPath, $thumbnailPath, "720x720");
                    if (!$success) throw new Exception("エラーが発生しました");
                } else if (str_starts_with($mime, 'video/')) {
                    $success = MediaHelper::convertAndCompressToMp4Video($mediaPath);
                    if (!$success) throw new Exception("エラーが発生しました");

                    $post->setExtension(".mp4");
                }
            }

            $postDao = DAOFactory::getPostDAO();

            if ($_POST['scheduled_at'] !== "") {
                $validatedDatetime = ValidationHelper::date($_POST['scheduled_at'], 'Y-m-d H:i:s');

                $maxScheduledPosts = 20; // 最大20件まで予約投稿ができる
                if ($postDao->countScheduledPosts($user->getId()) < $maxScheduledPosts) {
                    $post->setScheduledAt(new DateTime($validatedDatetime));
                    $post->setStatus(PostStatusType::SCHEDULED->value);
                } else {
                    return new JSONRenderer(['status' => 'error', 'message' => '予約投稿ができる件数は最大20件です']);
                }
            }

            $success = $postDao->create($post);

            if (!$success) throw new Exception('Failed to create a post!');

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        } catch (\LengthException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        }
    })->setMiddleware(['auth']),
    'posts' => Route::create('posts', function (): HTTPRenderer {
        try {
            // TODO: 厳格なバリデーション
            // TODO: データベースにないURLのクエリだった場合は404を出す
            $required_fields = [
                'url' => GeneralValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $user = Authenticate::getAuthenticatedUser();
            $postDao = DAOFactory::getPostDAO();
            $post = $postDao->getByUrl($validatedData['url'], $user->getId());

            return new HTMLRenderer('page/posts', ['post' => $post, 'user' => $user, 'path' => 'posts']);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
    })->setMiddleware(['auth']),
    'posts/comments' => Route::create('posts/comments', function (): HTTPRenderer {
        $required_fields = [
            'type_reply_to' => PostValueType::TYPE_REPLY_TO,
            'post_id' => GeneralValueType::INT,
            'offset' => GeneralValueType::INT
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

        $user = Authenticate::getAuthenticatedUser();

        $commentDao = DAOFactory::getCommentDAO();

        if ($validatedData['type_reply_to'] === 'post') {
            $comments = $commentDao->getCommentsToPost($validatedData['post_id'], $user->getId(), $validatedData['offset'], 3); //TODO: 20にする
        } else if ($validatedData['type_reply_to'] === 'comment') {
            $comments = $commentDao->getChildComments($validatedData['post_id'], $user->getId(), $validatedData['offset'], 3); //TODO: 20にする
        }


        $htmlString = "";

        foreach ($comments as $comment) {
            ob_start();
            $user;
            include(__DIR__ . '/../views/components/comment.php');
            $commentHtml = ob_get_clean();
            $htmlString .= $commentHtml;
        }

        return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
    })->setMiddleware(['auth']),
    'delete/post' => Route::create('delete/post', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception(
            'Invalid request method!'
        );

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => GeneralValueType::INT,
        ];

        $user = Authenticate::getAuthenticatedUser();
        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $postDao = DAOFactory::getPostDAO();
        $success = $postDao->delete($validatedData['post_id'], $user->getId());

        if (!$success) throw new Exception('Failed to delete a comment!');

        FlashData::setFlashData('success', "投稿を削除しました");
        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/reply' => Route::create('form/reply', function (): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        error_log(print_r($_POST, true));
        error_log(print_r($_FILES, true));

        // 文字が空かつ、メディアがない場合はアラートを出す
        if ($_POST['content'] === "" && $_FILES['media']['error'] !== UPLOAD_ERR_OK) {
            return new JSONRenderer(['status' => 'error', "message" => "投稿にはテキスト、あるいはメディアのどちらかが必要です"]);
        }

        $user = Authenticate::getAuthenticatedUser();
        //投稿のURLとメディアのファイル名の長さ 
        $numberOfCharacters = 18;
        $url = bin2hex(random_bytes($numberOfCharacters / 2));

        $comment = new Comment(
            url: $url,
            userId: $user->getId()
        );

        if ($_POST['content'] !== "") {
            $fields = [
                'content' => PostValueType::CONTENT
            ];
            $validatedData = ValidationHelper::validateFields($fields, $_POST);
            $comment->setContent($validatedData['content']);
        }

        if ($_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $fields = [
                'media' => PostValueType::MEDIA
            ];
            $validatedData = ValidationHelper::validateFields($fields, ['media' => $_FILES['media']['tmp_name']]);
        }


        if (isset($validatedData['media'])) {
            $tmpPath = $validatedData['media'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpPath);
            $extension = explode('/', $mime)[1] === "quicktime" ? '.mov' : '.' . explode('/', $mime)[1];
            $basename = bin2hex(random_bytes($numberOfCharacters / 2));
            $filename = $basename . $extension;
            $uploadDir =  __DIR__ .  "/../../public/uploads/";
            $subdirectory = substr($filename, 0, 2) . "/";
            $mediaPath = $uploadDir .  $subdirectory . $filename;

            $uploadSuccess = MediaHelper::uploadMedia($mediaPath, $tmpPath);
            if (!$uploadSuccess) throw new Exception("メディアのアップロードに失敗しました。");

            // インスタンスに保存
            $comment->setMediaPath($basename);
            $comment->setExtension($extension);

            if (str_starts_with($mime, 'image/') && $extension !== ".gif") {
                $thumbnailPath = $uploadDir .  $subdirectory . explode(".", $filename)[0] . "_thumb" . $extension;

                $success = MediaHelper::createThumbnail($mediaPath, $thumbnailPath, "720x720");

                if (!$success) throw new Exception("エラーが発生しました");
            } else if (str_starts_with($mime, 'video/')) {
                $success = MediaHelper::convertAndCompressToMp4Video($mediaPath);
                if (!$success) throw new Exception("エラーが発生しました");

                // 動画はすべてmp4形式
                $comment->setExtension(".mp4");
            }
        }

        $required_fields = [
            'post_id' => GeneralValueType::INT,
            'type_reply_to' => PostValueType::TYPE_REPLY_TO
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

        $commentDao = DAOFactory::getCommentDAO();

        // 投稿への返信の場合
        if ($validatedData['type_reply_to'] === 'post') {
            $comment->setPostId($validatedData['post_id']);
            $success = $commentDao->create($comment);

            if (!$success) throw new Exception('Failed to create a comment!');

            // 自分の投稿に対して自分がコメントしていない場合は通知する
            $postDao = DAOFactory::getPostDAO();
            $currentPost = $postDao->getById($validatedData['post_id']);

            if ($currentPost->getUserId() !== $user->getId()) {
                $notification = new Notification(
                    userId: $currentPost->getUserId(),
                    sourceId: $user->getId(),
                    notificationType: NotificationType::COMMENT->value,
                    commentId: $comment->getId()
                );

                $notificationDao = DAOFactory::getNotificationDAO();
                $success = $notificationDao->create($notification);

                if (!$success) throw new Exception('Failed to create a notification!');
            }
        } else {
            // コメントへの返信の場合
            $comment->setParentCommentId($validatedData['post_id']);
            $success = $commentDao->create($comment);
            if (!$success) throw new Exception('Failed to create a comment!');

            // TODO: 余裕があったらコメントに対する返信の通知を作成

        }

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'comments' => Route::create('comments', function (): HTTPRenderer {
        // TODO: 厳格なバリデーション
        $required_fields = [
            'url' => GeneralValueType::STRING
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET);
        $user = Authenticate::getAuthenticatedUser();

        $commentDao = DAOFactory::getCommentDAO();
        $parentComment = $commentDao->getByUrl($validatedData['url'], $user->getId());

        return new HTMLRenderer('page/posts', ['post' => $parentComment, 'user' => $user, 'path' => 'comments']);
    })->setMiddleware(['auth']),
    'delete/comment' => Route::create('delete/comment', function (): HTTPRenderer {
        // TODO: 投稿者だけが削除できるようにする
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $commentDao = DAOFactory::getCommentDAO();

        $user = Authenticate::getAuthenticatedUser();
        $success = $commentDao->delete($validatedData['post_id'], $user->getId());
        if (!$success) throw new Exception('Failed to delete a comment!');

        FlashData::setFlashData('success', "コメントを削除しました");
        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/like-post' => Route::create('form/like-post', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $user = Authenticate::getAuthenticatedUser();

        $postLike = new PostLike(
            userId: $user->getId(),
            postId: $validatedData['post_id'],
        );

        $postLikeDao = DAOFactory::getPostLikeDAO();
        $success = $postLikeDao->create($postLike);

        if (!$success) throw new Exception('Failed to create a post-like!');

        $postDao = DAOFactory::getPostDAO();
        $post = $postDao->getById($validatedData['post_id']);

        // 自分が投稿したポストじゃない場合はnotificationを作成する
        if ($post->getUserId() !== $user->getId()) {
            $notification = new Notification(
                userId: $post->getUserId(),
                sourceId: $user->getId(),
                notificationType: NotificationType::POST_LIKE->value,
                postId: $validatedData['post_id']
            );

            $notificationDao = DAOFactory::getNotificationDAO();
            $success = $notificationDao->create($notification);

            if (!$success) throw new Exception('Failed to create a notification!');
        }

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/delete-like-post' => Route::create('form/delete-like-post', function (): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $user = Authenticate::getAuthenticatedUser();

        $postLikeDao = DAOFactory::getPostLikeDAO();
        $success = $postLikeDao->delete($user->getId(), $validatedData['post_id']);

        if (!$success) throw new Exception('Failed to delete a post-like!');

        $postDao = DAOFactory::getPostDAO();
        $post = $postDao->getById($validatedData['post_id']);

        $notificationDao = DAOFactory::getNotificationDAO();
        $success = $notificationDao->delete($post->getUserId(), NotificationType::POST_LIKE->value, $user->getId(), $validatedData['post_id'], null, null);

        if (!$success) throw new Exception('Failed to delete a notification!');

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/like-comment' => Route::create('form/like-comment', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $user = Authenticate::getAuthenticatedUser();

        $commentLike = new CommentLike(
            userId: $user->getId(),
            commentId: $validatedData['post_id'],
        );

        $commentLikeDao = DAOFactory::getCommentLikeDAO();
        $success = $commentLikeDao->create($commentLike);

        if (!$success) throw new Exception('Failed to create a comment-like!');

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/delete-like-comment' => Route::create('form/delete-like-comment', function (): HTTPRenderer {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $user = Authenticate::getAuthenticatedUser();

        $commentLikeDao = DAOFactory::getCommentLikeDAO();
        $success = $commentLikeDao->delete($user->getId(), $validatedData['post_id']);

        if (!$success) throw new Exception('Failed to delete a comment-like!');

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/follow' => Route::create('form/follow', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'follower_user_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $user = Authenticate::getAuthenticatedUser();

        $follow = new Follow(
            followingUserId: $user->getId(),
            followerUserId: $validatedData['follower_user_id']
        );

        $followDao = DAOFactory::getFollowDAO();
        $success = $followDao->create($follow);

        if (!$success) throw new Exception('Failed to create a follow!');

        $notification = new Notification(
            userId: $validatedData['follower_user_id'],
            sourceId: $user->getId(),
            notificationType: NotificationType::FOLLOW->value,
        );

        $notificationDao = DAOFactory::getNotificationDAO();
        $success = $notificationDao->create($notification);

        if (!$success) throw new Exception('Failed to create a notification!');

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/unFollow' => Route::create('form/unFollow', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'follower_user_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
        $user = Authenticate::getAuthenticatedUser();

        $followDao = DAOFactory::getFollowDAO();
        $success = $followDao->delete($user->getId(), $validatedData['follower_user_id']);

        if (!$success) throw new Exception('Failed to delete a follow!');

        $notificationDao = DAOFactory::getNotificationDAO();
        $success = $notificationDao->delete($validatedData['follower_user_id'], NotificationType::FOLLOW->value, $user->getId(), null, null, null);

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'direct' => Route::create('direct', function (): HTTPRenderer {

        // TODO: Authenticateでも良いかも
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query, $output);
        $url = $output['url'];

        $user = Authenticate::getAuthenticatedUser();
        $dmThreadDao = DAOFactory::getDmThreadDAO();
        $dmThread = $dmThreadDao->getByUserIdAndUrl($user->getId(), $url);

        // TODO: try-catch
        if ($dmThread === null) throw new Exception('Invalid URL!');

        $receiverUserId = $dmThread->getUserId1() === $user->getId() ? $dmThread->getUserId2() : $dmThread->getUserId1();
        $userDao = DAOFactory::getUserDAO();
        $receiverUser = $userDao->getById($receiverUserId);

        $dmMessageDao = DAOFactory::getDmMessageDAO();
        $messages = $dmMessageDao->getOneHundredByDmThreadId($dmThread->getId());

        return new HTMLRenderer('page/direct', ['user' => $user, 'receiverUser' => $receiverUser, 'dmThread' => $dmThread, 'messages' => $messages]);
    })->setMiddleware(['auth']),
    'notifications' => Route::create('notifications', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();

        $notificationDao = DAOFactory::getNotificationDAO();
        $notifications = $notificationDao->getNotificationList($user->getId());

        return new HTMLRenderer('page/notifications', ['notifications' => $notifications, 'user' => $user]);
    })->setMiddleware(['auth']),
    'update-isRead' => Route::create('update-isRead', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'notification_id' => GeneralValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST);


        $notificationDao = DAOFactory::getNotificationDAO();
        $success = $notificationDao->updateReadStatus($validatedData['notification_id']);

        if (!$success) throw new Exception('Failed to update a notification!');

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'messages' => Route::create('messages', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();

        $messageDao = DAOFactory::getDmMessageDAO();
        $messageList = $messageDao->getMessageList($user->getId());

        return new HTMLRenderer('page/messages', ['messageList' => $messageList, 'user' => $user]);
    })->setMiddleware(['auth']),
    'search/user' => Route::create('search/user', function (): HTTPRenderer {

        if (isset($_GET['keyword']) && strlen($_GET['keyword']) !== 0) {
            // TODO: 厳格なバリデーション
            $required_fields = [
                'keyword' => GeneralValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $userDao = DAOFactory::getUserDAO();
            $users = $userDao->getUserListForSearch($validatedData['keyword']);

            return new HTMLRenderer('page/search_user', ['users' => $users]);
        }

        // デフォルトではフォロワーが多いユーザーを表示する
        $userDao = DAOFactory::getUserDAO();
        $users = $userDao->getTopFollowedUsers();

        return new HTMLRenderer('page/search_user', ['users' => $users]);
    })->setMiddleware(['auth']),
    'scheduled_posts' => Route::create('scheduled_post', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();

        $postDao = DAOFactory::getPostDAO();
        $scheduledPosts = $postDao->getScheduledPosts($user->getId());

        return new HTMLRenderer('page/scheduled_posts', ['scheduledPosts' => $scheduledPosts]);
    })->setMiddleware(['auth'])
];
