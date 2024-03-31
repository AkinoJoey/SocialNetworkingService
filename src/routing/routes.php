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
use src\models\DmThread;
use src\models\Follow;
use src\models\Notification;
use src\models\PasswordResetToken;
use src\types\NotificationType;
use src\types\PostStatusType;
use src\types\UserValueType;
use src\types\PostValueType;

return [
    '' => Route::create('', function (): HTTPRenderer {
        try {
            $user = Authenticate::getAuthenticatedUser();

            // ゲストの場合
            if ($user === null) {
                return new HTMLRenderer('page/guest');
            }

            // Eメール認証が済んでいない場合はログインページに遷移する
            if (!$user->getEmailVerified()) {
                FlashData::setFlashData('error', "Eメールの認証が完了していません");
                return new RedirectRenderer('login');
            }

            $followDao =  DAOFactory::getFollowDAO();
            $followingUserIdList = $followDao->getFollowingUserIdList($user->getId());

            if (count($followingUserIdList) === 0) {
                return new HTMLRenderer('page/top', ['user' => $user, 'tabActive' => 'trend']);
            } else {

                return new HTMLRenderer('page/top', ['user' => $user, 'tabActive' => 'following']);
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('');
        }
    })->setMiddleware([]),
    'timeline/following' => Route::create('timeline/following', function (): HTTPRenderer {
        try {
            $user = Authenticate::getAuthenticatedUser();
            $followDao =  DAOFactory::getFollowDAO();
            $followingUserIdList = $followDao->getFollowingUserIdList($user->getId());

            $required_fields = [
                'offset' => GeneralValueType::INT
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $postDao = DAOFactory::getPostDAO();
            $postsByFollowedUsers = $postDao->getPostsByFollowedUsers($followingUserIdList, $user->getId(), $validatedData['offset'], 20);

            $htmlString = "";

            foreach ($postsByFollowedUsers as $post) {
                ob_start();
                $user;
                include(__DIR__ . '/../views/components/post_card.php');
                $postCardHtml = ob_get_clean();
                $htmlString .= $postCardHtml;
            }
            return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'timeline/trend' => Route::create('timeline/trend', function (): HTTPRenderer {
        try {
            $required_fields = [
                'offset' => GeneralValueType::INT
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $user = Authenticate::getAuthenticatedUser();
            $postDao = DAOFactory::getPostDAO();
            $trendPosts = $postDao->getTrendPosts($user->getId(), $validatedData['offset'], 20);

            $htmlString = "";
            foreach ($trendPosts as $post) {
                ob_start();
                include(__DIR__ . '/../views/components/post_card.php');
                $postCardHtml = ob_get_clean();
                $htmlString .= $postCardHtml;
            }
            return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'timeline/guest' => Route::create("timeline/guest", function (): HTTPRenderer {
        try {
            $required_fields = [
                'offset' => GeneralValueType::INT
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $postDao = DAOFactory::getPostDAO();
            $posts = $postDao->getTrendPostsForGuest($validatedData['offset'], 20);

            $htmlString = "";
            foreach ($posts as $post) {
                ob_start();
                include(__DIR__ . '/../views/components/post_card_for_guest.php');
                $postCardHtml = ob_get_clean();
                $htmlString .= $postCardHtml;
            }

            return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['guest']),
    'guest' => Route::create('guest', function (): HTTPRenderer {
        return new HTMLRenderer('page/guest');
    })->setMiddleware(['guest']),
    'signup' => Route::create('signup', function (): HTTPRenderer {
        return new HTMLRenderer('page/signup');
    })->setMiddleware(['guest']),
    'form/signup' => Route::create('form/signup', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'account_name' => UserValueType::ACCOUNT_NAME,
                'email' => UserValueType::EMAIL,
                'password' => UserValueType::PASSWORD,
                'confirm_password' => UserValueType::PASSWORD,
            ];

            $userDao = DAOFactory::getUserDAO();

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if ($validatedData['confirm_password'] !== $validatedData['password']) {
                return new JSONRenderer(['status' => 'error', 'message' => 'パスワードが一致していません']);
            }

            // Eメールは一意でなければならないので、Eメールがすでに使用されていないか確認します
            if ($userDao->getByEmail($validatedData['email'])) {
                return new JSONRenderer(['status' => 'error', 'message' => '既に登録済みのEメールです']);
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
                throw new Exception('ユーザーの作成に失敗しました');
            }
            // ユーザーログイン
            Authenticate::loginAsUser($user);

            $lasts = 1 * 60 * 30;
            $param = [
                'id' => $user->getId(),
                'user' => hash('sha256', $user->getEmail()),
                'expiration' => time() + $lasts
            ];

            $signedUrl = Route::create('verify/email', function () {
            })->getSignedURL($param);
            Authenticate::sendVerificationEmail($user, $signedUrl);

            FlashData::setFlashData('success', 'アカウントを作成しました。Eメールを確認してください');
            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\LengthException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['guest']),
    'verify/email' => Route::create('verify/email', function (): HTTPRenderer {
        try {
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
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', '無効なデータが入力されました');
            return new RedirectRenderer('signup');
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('signup');
        }
    })->setMiddleware(['signature']),
    'profile/edit' => Route::create('profile/edit', function (): HTTPRenderer {
        try {
            $user = Authenticate::getAuthenticatedUser();

            $profileDao = DAOFactory::getProfileDAO();
            $profile = $profileDao->getByUserId($user->getId());

            return new HTMLRenderer('page/profile_form', ['user' => $user, 'profile' => $profile]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'form/profile/edit' => Route::create('form/profile/edit', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'id' => GeneralValueType::INT,
                'username' => UserValueType::USERNAME
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            $userDao = DAOFactory::getUserDAO();
            $user = Authenticate::getAuthenticatedUser();

            // ユーザー名を変更している場合は重複していないかどうかチェック
            if ($user->getUsername() !== $validatedData['username']) {
                $result = $userDao->checkUsernameExists($validatedData['username']);
                if ($result) {
                    throw new \InvalidArgumentException('既に存在するユーザー名には変更できません');
                } else {
                    $user->setUsername($validatedData['username']);
                }
            }

            $profileDao = DAOFactory::getProfileDAO();
            $profile = $profileDao->getById($validatedData['id']);

            if (isset($_POST['age'])) {
                $age = $_POST['age'] === '' ? null : ValidationHelper::age($_POST['age']);
                $profile->setAge($age);
            }

            if (isset($_POST['location'])) {
                $location = ValidationHelper::location($_POST['location']);
                $profile->setLocation($location);
            }

            if (isset($_POST['description'])) {
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

            $success = $profileDao->update($profile);
            if (!$success) throw new Exception('データベースの更新に失敗しました');

            $updatedSuccess = $userDao->update($user);

            if (!$updatedSuccess) throw new Exception('ユーザーの更新に失敗しました');

            FlashData::setFlashData('success', 'プロフィールを更新しました');
            return new JSONRenderer(['status' => 'success', 'newUsername' => $validatedData['username']]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\LengthException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'login' => Route::create('login', function (): HTTPRenderer {
        return new HTMLRenderer('page/login');
    })->setMiddleware(['register']),
    'form/login' => Route::create('form/login', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'email' => UserValueType::EMAIL,
                'password' => UserValueType::PASSWORD,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            Authenticate::authenticate($validatedData['email'], $validatedData['password']);

            return new JSONRenderer(['status' => 'success']);
        } catch (AuthenticationFailureException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' =>  $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'エラーが発生しました']);
        }
    })->setMiddleware(['register']),
    'logout' => Route::create('logout', function (): HTTPRenderer {
        Authenticate::logoutUser();
        FlashData::setFlashData('success', 'ログアウトしました');
        return new RedirectRenderer('login');
    })->setMiddleware(['auth']),
    'profile' => Route::create('profile', function (): HTTPRenderer {
        try {
            $required_fields = [
                'username' => UserValueType::USERNAME
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $userDao = DAOFactory::getUserDAO();
            $user = $userDao->getByUsername($validatedData['username']);
            $authenticatedUser = Authenticate::getAuthenticatedUser();

            // userが存在しない場合は自分のプロフィールページにリダイレクト
            if (!isset($user)) {
                FlashData::setFlashData('error', '存在しないユーザー名です');
                return new RedirectRenderer(sprintf('profile?username=%s', $authenticatedUser->getUsername()));
            }

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
                    if (!$success) throw new Exception('スレッドの作成に失敗しました');
                }

                $isFollow = $followDao->isFollow($authenticatedUser->getId(), $user->getId());

                return new HTMLRenderer('page/profile', ['user' => $user, 'profile' => $profile, 'authenticatedUser' => $authenticatedUser, 'followingCount' => $followingCount, 'followerCount' => $followerCount, 'isFollow' => $isFollow, 'dmUrl' => $dmThread->getUrl()]);
            }
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', $e->getMessage());
            return new HTMLRenderer('');
        } catch (\LengthException $e) {
            error_log($e->getMessage());
            FlashData::setFlashData('error', $e->getMessage());
            return new HTMLRenderer('');
        } catch (Exception $e) {
            error_log($e->getMessage());
            FlashData::setFlashData('error', 'エラーが発生しました');
            return new HTMLRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'profile/posts' => Route::create('profile/posts', function (): HTTPRenderer {
        try {
            $required_fields = [
                'username' => UserValueType::USERNAME,
                'offset' => GeneralValueType::INT
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);
            $user = Authenticate::getAuthenticatedUser();

            $postDao = DAOFactory::getPostDAO();
            $posts = $postDao->getPostsByUsername($validatedData['username'], $user->getId(), $validatedData['offset']);

            $htmlString = "";

            foreach ($posts as $post) {
                ob_start();
                $user;
                include(__DIR__ . '/../views/components/post_card.php');
                $postCardHtml = ob_get_clean();
                $htmlString .= $postCardHtml;
            }

            return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/new' => Route::create('form/new', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

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

            if (isset($_POST['scheduled_at'])) {
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

            if (!$success) throw new Exception('投稿の作成に失敗しました');

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        } catch (\LengthException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'posts' => Route::create('posts', function (): HTTPRenderer {
        try {
            $required_fields = [
                'url' => GeneralValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $user = Authenticate::getAuthenticatedUser();
            $postDao = DAOFactory::getPostDAO();
            $post = $postDao->getByUrl($validatedData['url'], $user->getId());

            if ($post === null) {
                return new HTMLRenderer('page/404');
            }

            return new HTMLRenderer('page/posts', ['post' => $post, 'user' => $user, 'path' => 'posts']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new RedirectRenderer('');
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'posts/comments' => Route::create('posts/comments', function (): HTTPRenderer {
        try {
            $required_fields = [
                'type_reply_to' => PostValueType::TYPE_REPLY_TO,
                'post_id' => GeneralValueType::INT,
                'offset' => GeneralValueType::INT
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $user = Authenticate::getAuthenticatedUser();

            $commentDao = DAOFactory::getCommentDAO();

            if ($validatedData['type_reply_to'] === 'post') {
                $comments = $commentDao->getCommentsToPost($validatedData['post_id'], $user->getId(), $validatedData['offset'], 20);
            } else if ($validatedData['type_reply_to'] === 'comment') {
                $comments = $commentDao->getChildComments($validatedData['post_id'], $user->getId(), $validatedData['offset'], 20);
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
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'delete/post' => Route::create('delete/post', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception(
                '無効なリクエストメソッド'
            );

            $required_fields = [
                'post_id' => GeneralValueType::INT,
            ];

            $user = Authenticate::getAuthenticatedUser();
            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
            $postDao = DAOFactory::getPostDAO();
            $success = $postDao->delete($validatedData['post_id'], $user->getId());

            if (!$success) throw new Exception('コメントの削除に失敗しました');

            FlashData::setFlashData('success', "投稿を削除しました");
            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/reply' => Route::create('form/reply', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエスト');

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

                if (!$success) throw new Exception('コメントの作成に失敗しました');

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

                    if (!$success) throw new Exception('通知の作成に失敗しました');
                }
            } else {
                // コメントへの返信の場合
                $comment->setParentCommentId($validatedData['post_id']);
                $success = $commentDao->create($comment);
                if (!$success) throw new Exception('コメントの作成に失敗しました');

            }

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        } catch (\LengthException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(["status" => "error", "message" => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'comments' => Route::create('comments', function (): HTTPRenderer {
        try {
            $required_fields = [
                'url' => GeneralValueType::STRING
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);
            $user = Authenticate::getAuthenticatedUser();

            $commentDao = DAOFactory::getCommentDAO();
            $parentComment = $commentDao->getByUrl($validatedData['url'], $user->getId());

            if ($parentComment === null) {
                http_response_code(404);
                return new HTMLRenderer('page/404');
            }

            return new HTMLRenderer('page/posts', ['post' => $parentComment, 'user' => $user, 'path' => 'comments']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new RedirectRenderer('');
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'delete/comment' => Route::create('delete/comment', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'post_id' => GeneralValueType::INT,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
            $commentDao = DAOFactory::getCommentDAO();

            $user = Authenticate::getAuthenticatedUser();
            $success = $commentDao->delete($validatedData['post_id'], $user->getId());
            if (!$success) throw new Exception('コメントの削除に失敗しました');

            FlashData::setFlashData('success', "コメントを削除しました");
            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/like-post' => Route::create('form/like-post', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

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

            if (!$success) throw new Exception('いいねの作成に失敗しました');

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

                if (!$success) throw new Exception('通知の作成に失敗しました');
            }

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/delete-like-post' => Route::create('form/delete-like-post', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'post_id' => GeneralValueType::INT,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
            $user = Authenticate::getAuthenticatedUser();

            $postLikeDao = DAOFactory::getPostLikeDAO();
            $success = $postLikeDao->delete($user->getId(), $validatedData['post_id']);

            if (!$success) throw new Exception('いいねの削除に失敗しました');

            $postDao = DAOFactory::getPostDAO();
            $post = $postDao->getById($validatedData['post_id']);

            $notificationDao = DAOFactory::getNotificationDAO();
            $success = $notificationDao->delete($post->getUserId(), NotificationType::POST_LIKE->value, $user->getId(), $validatedData['post_id'], null, null);

            if (!$success) throw new Exception('通知の削除に失敗しました');

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/like-comment' => Route::create('form/like-comment', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

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

            if (!$success) throw new Exception('コメントのいいね作成に失敗しました');

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/delete-like-comment' => Route::create('form/delete-like-comment', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'post_id' => GeneralValueType::INT,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
            $user = Authenticate::getAuthenticatedUser();

            $commentLikeDao = DAOFactory::getCommentLikeDAO();
            $success = $commentLikeDao->delete($user->getId(), $validatedData['post_id']);

            if (!$success) throw new Exception('コメントのいいね削除に失敗しました');

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/follow' => Route::create('form/follow', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

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

            if (!$success) throw new Exception('フォローの作成に失敗しました');

            $notification = new Notification(
                userId: $validatedData['follower_user_id'],
                sourceId: $user->getId(),
                notificationType: NotificationType::FOLLOW->value,
            );

            $notificationDao = DAOFactory::getNotificationDAO();
            $success = $notificationDao->create($notification);

            if (!$success) throw new Exception('通知の作成に失敗しました');

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'form/unFollow' => Route::create('form/unFollow', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'follower_user_id' => GeneralValueType::INT,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);
            $user = Authenticate::getAuthenticatedUser();

            $followDao = DAOFactory::getFollowDAO();
            $success = $followDao->delete($user->getId(), $validatedData['follower_user_id']);

            if (!$success) throw new Exception('フォローの削除に失敗しました');

            $notificationDao = DAOFactory::getNotificationDAO();
            $success = $notificationDao->delete($validatedData['follower_user_id'], NotificationType::FOLLOW->value, $user->getId(), null, null, null);

            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'direct' => Route::create('direct', function (): HTTPRenderer {
        try {
            $required_fields = [
                'url' => GeneralValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $user = Authenticate::getAuthenticatedUser();
            $dmThreadDao = DAOFactory::getDmThreadDAO();
            $dmThread = $dmThreadDao->getByUserIdAndUrl($user->getId(), $validatedData['url']);

            if ($dmThread === null) throw new Exception('無効なURL');

            $receiverUserId = $dmThread->getUserId1() === $user->getId() ? $dmThread->getUserId2() : $dmThread->getUserId1();
            $userDao = DAOFactory::getUserDAO();
            $receiverUser = $userDao->getById($receiverUserId);

            $dmMessageDao = DAOFactory::getDmMessageDAO();
            $messages = $dmMessageDao->getOneHundredByDmThreadId($dmThread->getId());

            return new HTMLRenderer('page/direct', ['user' => $user, 'receiverUser' => $receiverUser, 'dmThread' => $dmThread, 'messages' => $messages]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', $e->getMessage());
            return new RedirectRenderer('');
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'notifications' => Route::create('notifications', function (): HTTPRenderer {
        try {
            $user = Authenticate::getAuthenticatedUser();

            $notificationDao = DAOFactory::getNotificationDAO();
            $notifications = $notificationDao->getNotificationList($user->getId());

            return new HTMLRenderer('page/notifications', ['notifications' => $notifications, 'user' => $user]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'update-isRead' => Route::create('update-isRead', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'notification_id' => GeneralValueType::INT,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            $notificationDao = DAOFactory::getNotificationDAO();
            $success = $notificationDao->updateReadStatus($validatedData['notification_id']);

            if (!$success) throw new Exception('通知の更新に失敗しました');

            return new JSONRenderer(['status' => 'success']);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'messages' => Route::create('messages', function (): HTTPRenderer {
        try {
            $user = Authenticate::getAuthenticatedUser();

            $messageDao = DAOFactory::getDmMessageDAO();
            $messageList = $messageDao->getMessageList($user->getId());

            return new HTMLRenderer('page/messages', ['messageList' => $messageList, 'user' => $user]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'search/user' => Route::create('search/user', function (): HTTPRenderer {
        return new HTMLRenderer('page/search_user');
    })->setMiddleware(['auth', 'verify']),
    'search/user-list' => Route::create('search-user-list', function (): HTTPRenderer {
        try {
            $userDao = DAOFactory::getUserDAO();

            if (isset($_GET['keyword']) && strlen($_GET['keyword']) !== 0) {
                $required_fields = [
                    'keyword' => GeneralValueType::STRING,
                ];

                $validatedData = ValidationHelper::validateFields($required_fields, $_GET);
                $users = $userDao->getUserListForSearch($validatedData['keyword']);
            } else {
                // デフォルトではフォロワーが多いユーザーを表示する
                $userDao = DAOFactory::getUserDAO();
                $users = $userDao->getTopFollowedUsers();
            }

            $htmlString = "";

            foreach ($users as $user) {
                ob_start();
                $user;
                include(__DIR__ . '/../views/components/item_in_search_user.php');
                $postCardHtml = ob_get_clean();
                $htmlString .= $postCardHtml;
            }

            return new JSONRenderer(['status' => 'success', 'htmlString' => $htmlString]);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
    'scheduled_posts' => Route::create('scheduled_post', function (): HTTPRenderer {
        try {
            $user = Authenticate::getAuthenticatedUser();

            $postDao = DAOFactory::getPostDAO();
            $scheduledPosts = $postDao->getScheduledPosts($user->getId());

            return new HTMLRenderer('page/scheduled_posts', ['scheduledPosts' => $scheduledPosts, 'user' => $user]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('');
        }
    })->setMiddleware(['auth', 'verify']),
    'forgot_password' => Route::create('forgot_password', function (): HTTPRenderer {
        return new HTMLRenderer('page/forgot_password');
    })->setMiddleware(['guest']),
    'form/forgot_password' => Route::create('form/forgot_password', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'email' => UserValueType::EMAIL,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            $userDao = DAOFactory::getUserDAO();
            $user = $userDao->getByEmail($validatedData['email']);

            // データベースにEメールが存在しない場合
            if ($user === null) {
                return new JSONRenderer(['status' => 'error', 'message' =>  '登録されていないEメールです']);
            }

            // Eメールが認証されていない場合
            if (!$user->getEmailVerified()) {
                return new JSONRenderer(['status' => 'error', 'message' =>  '登録されていないEメールです']);
            }

            // 期限を30分に設定
            $lasts = 1 * 60 * 30;
            $param = [
                'expiration' => time() + $lasts
            ];

            // 存在する場合はパスワードリセット用のEメールを送信する
            $route =  Route::create('verify/forgot_password', function () {
            });
            $signedUrl = $route->getSignedURL($param);
            Authenticate::sendForgotPasswordEmail($user, $signedUrl);

            // テーブルにトークンとユーザーを保存する
            $signature = $route->getSignature($signedUrl);
            $passwordResetToken = new PasswordResetToken(
                userId: $user->getId(),
                token: pack('H*', $signature) //バイナリーに変換
            );

            $passwordResetTokenDao = DAOFactory::getPasswordResetTokenDAO();
            $success = $passwordResetTokenDao->create($passwordResetToken);

            if (!$success) {
                throw new Exception("パスワードリセットトークンの作成に失敗しました");
            }

            FlashData::setFlashData('success', 'Eメールを送信しました。パスワードリセットのためにメールを確認してください');
            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['guest']),
    'verify/forgot_password' => Route::create('verify/forgot_password', function (): HTTPRenderer {
        try {
            $required_fields = [
                'signature' => GeneralValueType::STRING
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET);

            $passwordResetTokenDao = DAOFactory::getPasswordResetTokenDAO();
            $passwordResetToken = $passwordResetTokenDao->getByToken(pack('H*', $validatedData['signature']));

            if($passwordResetToken === null){
                FlashData::setFlashData('error', '無効なURLです。');
                return new RedirectRenderer('login');
            }

            return new HTMLRenderer('page/verify_forgot_password', ['userId' => $passwordResetToken->getUserId()]);
        } catch (\Exception $e) {
            error_log($e->getMessage());

            FlashData::setFlashData('error', 'エラーが発生しました');
            return new RedirectRenderer('login');
        }
    })->setMiddleware(['signature']),
    'form/verify/forgot_password' => Route::create('form/verify/forgot_password', function (): HTTPRenderer {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'user_id' => GeneralValueType::INT,
                'password' => UserValueType::PASSWORD,
                'confirm_password' => UserValueType::PASSWORD,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            if ($validatedData['confirm_password'] !== $validatedData['password']) {
                FlashData::setFlashData('error', 'パスワードが一致しません');
                return new JSONRenderer(['status' => 'error', 'message' => 'パスワードが一致しません']);
            }

            $userDao = DAOFactory::getUserDAO();
            $user = $userDao->getById($validatedData['user_id']);
            $userUpdateSuccess = $userDao->update($user, $validatedData['password']);

            if (!$userUpdateSuccess) {
                throw new Exception('パスワードの更新に失敗しました');
            }

            $passwordResetTokenDao = DAOFactory::getPasswordResetTokenDAO();
            $resetTokenDeleted = $passwordResetTokenDao->deleteByUserId(intval($validatedData['user_id']));


            if (!$resetTokenDeleted) {
                throw new Exception("パスワードリセットトークの削除に失敗しました");
            }

            FlashData::setFlashData('success', 'パスワードをリセットしました。新しいパスワードでログインしてください');
            return new JSONRenderer(['status' => 'success']);
        } catch (\InvalidArgumentException $e) {
            error_log($e->getMessage());

            return new JSONRenderer(['status' => 'error', 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['guest']),
    'user/delete'=> Route::create('user/delete', function () : HTTPRenderer {
        try{
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('無効なリクエストメソッド');

            $required_fields = [
                'user_id' => GeneralValueType::INT
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_POST);

            $user = Authenticate::getAuthenticatedUser();

            if ($user->getId() === $validatedData['user_id']) {
                $userDao = DAOFactory::getUserDAO();
                $success = $userDao->delete($user->getId());
                if (!$success) {
                    throw new \Exception('アカウントの削除に失敗しました');
                }

                // ログアウトさせる
                Authenticate::logoutUser();
            } else {
                throw new \Exception('無効なリクエスト');
            }

            FlashData::setFlashData('success', 'アカウントを削除しました');
            return new JSONRenderer(['status' => 'success']);
        }catch(\InvalidArgumentException $e){
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message'=>$e->getMessage()]);
        }catch(\Exception $e){
            error_log($e->getMessage());
            return new JSONRenderer(['status' => 'error', 'message' => 'エラーが発生しました']);
        }
    })->setMiddleware(['auth', 'verify']),
];
