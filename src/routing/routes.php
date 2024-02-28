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
use src\response\render\JSONRenderer;
use src\models\Comment;
use src\models\PostLike;
use src\models\CommentLike;
use src\models\DmMessage;
use src\models\DmThread;
use src\models\Follow;
use src\models\Notification;
use src\types\NotificationType;

return [
    '' => Route::create('', function (): HTTPRenderer {
        // TODO: ゲスト用とユーザー用で振り分ける
        $user = Authenticate::getAuthenticatedUser();

        // ゲストの場合
        if ($user === null) {
            return new HTMLRenderer('page/guest');
        }

        // TODO: フォロワーのツイートを見られるようにする
        $followDao =  DAOFactory::getFollowDAO();
        $followingUserIdList = $followDao->getFollowingUserIdList($user->getId());

        $postDao = DAOFactory::getPostDAO();
        $postsByFollowedUsers = $postDao->getPostsByFollowedUsers($followingUserIdList, $user->getId(), 0);

        return new HTMLRenderer('page/top', ['posts' => $postsByFollowedUsers, 'user' => $user]);
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
                username: uniqid("")
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
    'profile/edit' => Route::create('profile/edit', function (): HTTPRenderer {
        $user = Authenticate::getAuthenticatedUser();

        $profileDao = DAOFactory::getProfileDAO();
        $profile = $profileDao->getByUserId($user->getId());

        return new HTMLRenderer('page/profile_form', ['user' => $user, 'profile' => $profile]);
    })->setMiddleware(['auth']),
    'form/profile/edit' => Route::create('form/profile/edit', function (): HTTPRenderer {
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
        if (isset($validatedData['id'])) $success = $profileDao->update($profile);
        else $success = $profileDao->create($profile);

        if (!$success) throw new Exception('Database update failed!');

        $userDao = DAOFactory::getUserDAO();

        $user->setUsername($validatedData['username']);

        $updatedSuccess = $userDao->update($user);

        if (!$updatedSuccess) throw new Exception('Failed to update user!');

        return new RedirectRenderer(sprintf('profile?username=%s', $user->getUsername()));
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
            'username' => ValueType::STRING
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET, true);

        $userDao = DAOFactory::getUserDAO();
        $user = $userDao->getByUsername($validatedData['username']);
        $authenticatedUser = Authenticate::getAuthenticatedUser();

        $profileDao = DAOFactory::getProfileDAO();
        $profile = $profileDao->getByUserId($user->getId());

        $postDao = DAOFactory::getPostDAO();
        $posts = $postDao->getPostsByFollowedUsers([], $user->getId(), 0);

        $followDao = DAOFactory::getFollowDAO();
        $followingList = $followDao->getFollowingUserIdList($user->getId());
        $followingCount = $followingList !== null ? count($followingList) : 0;
        $followerList = $followDao->getFollowerUserIdList($user->getId());
        $followerCount = $followerList !== null ? count($followerList) : 0;

        // 自分のプロフィールを見る場合
        if ($user->getId() === $authenticatedUser->getId()) {
            return new HTMLRenderer('page/profile', ['user' => $user, 'profile' => $profile, 'posts' => $posts, 'authenticatedUser' => $authenticatedUser, 'followingCount' => $followingCount, 'followerCount' => $followerCount]);
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

            return new HTMLRenderer('page/profile', ['user' => $user, 'profile' => $profile, 'posts' => $posts, 'authenticatedUser' => $authenticatedUser, 'followingCount' => $followingCount, 'followerCount' => $followerCount, 'isFollow' => $isFollow, 'dmUrl' => $dmThread->getUrl()]);
        }
    })->setMiddleware(['auth']),
    'form/new' => Route::create('form/new', function (): HTTPRenderer {
        // TODO: try-catch文を書く

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        error_log(print_r($_POST, true));
        error_log(print_r($_FILES, true));

        // メディアがある場合はcontentはnullable, メディアがない場合はnot null
        
        // $required_fields = [
        //     'content' => ValueType::STRING,
        // ];

        // // TODO: 厳格なバリデーションを作成
        // $validatedRequiredData = ValidationHelper::validateFields($required_fields, $_POST, true);

        // $nullableFields = [
        //     'media_path' => ValueType::STRING,
        //     'scheduled_at' => ValueType::DATE
        // ];

        // $validatedNullableData = ValidationHelper::validateFields($nullableFields, $_POST, false);
        // $validatedData = array_merge($validatedRequiredData, $validatedNullableData);

        // // TODO: 画像アップロード時の振る舞いを追加

        // $user = Authenticate::getAuthenticatedUser();

        // $numberOfCharacters = 18;
        // $randomString = bin2hex(random_bytes($numberOfCharacters / 2));

        // $post = new Post(
        //     content: $validatedData['content'],
        //     url: $randomString,
        //     userId: $user->getId(),
        //     mediaPath: $validatedData['media_path'],
        //     scheduledAt: $validatedData['scheduled_at']
        // );

        // $postDao = DAOFactory::getPostDAO();
        // $success = $postDao->create($post);

        // if (!$success) throw new Exception('Failed to create a post!');

        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'posts' => Route::create('posts', function (): HTTPRenderer {
        // TODO: 厳格なバリデーション
        $required_fields = [
            'url' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET, true);

        $currentUser = Authenticate::getAuthenticatedUser();
        $postDao = DAOFactory::getPostDAO();
        $post = $postDao->getByUrl($validatedData['url'], $currentUser->getId());

        $commentDao = DAOFactory::getCommentDAO();
        $comments = $commentDao->getCommentsToPost($post->getId(), $currentUser->getId(), 0);

        $createFormAction = "/form/comment";

        return new HTMLRenderer('page/posts', ['post' => $post,  'comments' => $comments, 'createFormAction' => $createFormAction, 'user' => $currentUser]);
    })->setMiddleware(['auth']),
    'delete/post' => Route::create('delete/post', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception(
            'Invalid request method!'
        );

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => ValueType::INT,
        ];

        $user = Authenticate::getAuthenticatedUser();
        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
        $postDao = DAOFactory::getPostDAO();
        $success = $postDao->delete($validatedData['post_id'], $user->getId());

        if (!$success) throw new Exception('Failed to delete a comment!');

        FlashData::setFlashData('success', "投稿を削除しました");
        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/comment' => Route::create('form/comment', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'content' => ValueType::STRING,
            'post_id' => ValueType::INT
        ];

        // TODO: 厳格なバリデーションを作成
        $validatedRequiredData = ValidationHelper::validateFields($required_fields, $_POST, true);

        $nullableFields = [
            'media_path' => ValueType::STRING
        ];

        $validatedNullableData = ValidationHelper::validateFields($nullableFields, $_POST, false);
        $validatedData = array_merge($validatedRequiredData, $validatedNullableData);

        $postDao = DAOFactory::getPostDAO();

        $currentPost = $postDao->getById($validatedData['post_id']);

        $user = Authenticate::getAuthenticatedUser();

        $numberOfCharacters = 18;
        $randomString = bin2hex(random_bytes($numberOfCharacters / 2));

        $comment = new Comment(
            content: $validatedData['content'],
            url: $randomString,
            userId: $user->getId(),
            postId: $validatedData['post_id'],
            mediaPath: $validatedData['media_path'],
        );

        $commentDao = DAOFactory::getCommentDAO();
        $success = $commentDao->create($comment);

        if (!$success) throw new Exception('Failed to create a comment!');

        // 自分の投稿に対して自分がコメントしていない場合は通知する
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

        return new RedirectRenderer(sprintf('posts?url=%s', $currentPost->getUrl()));
    })->setMiddleware(['auth']),
    'comments' => Route::create('comments', function (): HTTPRenderer {
        // TODO: 厳格なバリデーション
        $required_fields = [
            'url' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_GET, true);
        $currentUser = Authenticate::getAuthenticatedUser();

        $commentDao = DAOFactory::getCommentDAO();
        $parentComment = $commentDao->getByUrl($validatedData['url'], $currentUser->getId());

        $childComments = $commentDao->getChildComments($parentComment->getId(), $currentUser->getId(),  0);

        $createFormAction = "/form/comment-to-comment";

        $commentLikeDao = DAOFactory::getCommentLikeDAO();
        $numberOfPostLike = $commentLikeDao->getNumberOfLikes($parentComment->getId());

        return new HTMLRenderer('page/posts', ['post' => $parentComment, 'numberOfPostLike' => $numberOfPostLike, 'comments' => $childComments, 'createFormAction' => $createFormAction, 'user' => $currentUser]);
    })->setMiddleware(['auth']),
    'form/comment-to-comment' => Route::create('form/comment-to-comment', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'content' => ValueType::STRING,
            'post_id' => ValueType::INT
        ];
        // TODO: 厳格なバリデーションを作成
        $validatedRequiredData = ValidationHelper::validateFields($required_fields, $_POST, true);

        $nullableFields = [
            'media_path' => ValueType::STRING
        ];

        $validatedNullableData = ValidationHelper::validateFields($nullableFields, $_POST, false);
        $validatedData = array_merge($validatedRequiredData, $validatedNullableData);

        $commentDao = DAOFactory::getCommentDAO();

        $currentComment = $commentDao->getById($validatedData['post_id']);

        $user = Authenticate::getAuthenticatedUser();

        $numberOfCharacters = 18;
        $randomString = bin2hex(random_bytes($numberOfCharacters / 2));

        $comment = new Comment(
            content: $validatedData['content'],
            url: $randomString,
            userId: $user->getId(),
            parentCommentId: $validatedData['post_id'],
            mediaPath: $validatedData['media_path'],
        );

        $success = $commentDao->create($comment);

        if (!$success) throw new Exception('Failed to create a comment!');

        return new RedirectRenderer(sprintf('comments?url=%s', $currentComment->getUrl()));
    })->setMiddleware(['auth']),
    'delete/comment' => Route::create('delete/comment', function (): HTTPRenderer {
        // TODO: 投稿者だけが削除できるようにする
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        error_log(print_r($_POST, true));

        // TODO: 厳格なバリデーション
        $required_fields = [
            'comment_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
        $commentDao = DAOFactory::getCommentDAO();

        $user = Authenticate::getAuthenticatedUser();
        $success = $commentDao->delete($validatedData['comment_id'], $user->getId());
        if (!$success) throw new Exception('Failed to delete a comment!');

        FlashData::setFlashData('success', "コメントを削除しました");
        return new JSONRenderer(['status' => 'success']);
    })->setMiddleware(['auth']),
    'form/like-post' => Route::create('form/like-post', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'post_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
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
            'post_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
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
            'post_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
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
            'post_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
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
            'follower_user_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
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
            'follower_user_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);
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
            'notification_id' => ValueType::INT,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);


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
                'keyword' => ValueType::STRING,
            ];

            $validatedData = ValidationHelper::validateFields($required_fields, $_GET, true);

            $userDao = DAOFactory::getUserDAO();
            $users = $userDao->getUserListForSearch($validatedData['keyword']);

            return new HTMLRenderer('page/search_user', ['users' => $users]);
        }

        // デフォルトではフォロワーが多いユーザーを表示する
        $userDao = DAOFactory::getUserDAO();
        $users = $userDao->getTopFollowedUsers();

        return new HTMLRenderer('page/search_user', ['users' => $users]);
    })->setMiddleware(['auth']),
    'form/search/user' => Route::create('form/search/user', function (): HTTPRenderer {
        // TODO: try-catch文を書く
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Invalid request method!');

        // TODO: 厳格なバリデーション
        $required_fields = [
            'keywords' => ValueType::STRING,
        ];

        $validatedData = ValidationHelper::validateFields($required_fields, $_POST, true);


        $userDao = DAOFactory::getUserDAO();
        $users = $userDao->getUserListForSearch($validatedData['keywords']);

        return new JSONRenderer(['status' => 'success', 'users' => $users]);
    })->setMiddleware(['auth'])
];
