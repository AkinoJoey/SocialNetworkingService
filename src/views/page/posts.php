<?php

use Carbon\Carbon;

?>
<div class="container mx-auto mb-14 flex flex-col items-center justify-center p-4">
    <div class="mt-4 flex w-full flex-col space-y-6 overflow-hidden rounded-lg p-4 shadow-md dark:bg-gray-900 dark:text-gray-100 sm:w-4/5 md:w-3/5 lg:w-1/2">
        <!-- main post -->
        <div class="flex space-x-4 justify-between items-center">
            <div class="flex space-x-4">
                <a href="/profile?username=<?= htmlspecialchars($post->getUsername()) ?>" class="z-30 min-w-12">
                    <img alt="" src="<?= $post->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($post->getProfileImagePath(), 0, 2) . '/' .  $post->getProfileImagePath() . $post->getProfileImageExtension() ?>" class="h-12 w-12 rounded-full object-cover shadow dark:bg-gray-500 hover:opacity-50" />
                </a>
                <div class="flex flex-col space-y-1">
                    <div class="flex">
                        <a href="/profile?username=<?= htmlspecialchars($post->getUsername()) ?>" class="text-sm font-semibold z-30 hover:underline"><?= htmlspecialchars($post->getAccountName()) ?></a>
                        <span class="text-xs text-gray-500 leading-5 ml-1"><?= '@' . htmlspecialchars($post->getUsername()) ?></span>
                    </div>
                    <?php if ($path === 'comments') : ?>
                        <span class="text-xs dark:text-gray-400"><?= Carbon::parse($post->getTimeStamp()->getCreatedAt())->diffForHumans() ?></span>
                    <?php else : ?>
                        <!-- 予約投稿じゃない場合 -->
                        <?php if ($post->getScheduledAt() === null) : ?>
                            <span class="text-xs dark:text-gray-400"><?= Carbon::parse($post->getTimeStamp()->getCreatedAt())->diffForHumans() ?></span>
                            <!-- 予約投稿の場合 -->
                        <?php else : ?>
                            <span class="text-xs dark:text-gray-400"><?= Carbon::parse($post->getScheduledAt())->diffForHumans() ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($post->getUserId() === $user->getId()) : ?>
                <?php
                $classNameArr = explode("\\", get_class($post));
                $dataPostType = $classNameArr[count($classNameArr) - 1];
                $dataPostId = $post->getId();
                include(__DIR__ . '/../components/post_dropdown.php');
                ?>
            <?php endif; ?>
        </div>
        <div>
            <p class="mb-4 text-sm dark:text-gray-400">
                <?= $post->getContent() !== null ? nl2br(htmlspecialchars($post->getContent())) : null ?>
            </p>
            <!-- media -->
            <?php if ($post->getExtension() === '.mov' || $post->getExtension() === '.mp4') : ?>
                <video autoplay muted class="w-full object-cover dark:bg-gray-500" controls with="720">
                    <source src="/uploads/<?= substr($post->getMediaPath(), 0, 2) . '/' . $post->getMediaPath() . $post->getExtension() ?>" type="video/mp4">
                </video>
            <?php elseif ($post->getExtension() === '.gif' || $post->getExtension() === '.jpg' || $post->getExtension() === '.jpeg' || $post->getExtension() === '.png') : ?>
                <a class="z-20 relative" href="/uploads/<?= substr($post->getMediaPath(), 0, 2) . '/' . $post->getMediaPath() . $post->getExtension() ?>">
                    <?php
                    if ($post->getExtension() === '.gif') {
                        $src = substr($post->getMediaPath(), 0, 2) . '/' . $post->getMediaPath() . $post->getExtension();
                    } else {
                        $src = substr($post->getMediaPath(), 0, 2) . '/' . $post->getMediaPath() . '_thumb' . $post->getExtension();
                    }
                    ?>
                    <img src="/uploads/<?= $src ?>" alt="uploaded image" class="w-full object-cover dark:bg-gray-500" />
                </a>
            <?php endif; ?>
        </div>
        <div class="flex flex-wrap justify-between">
            <div class="flex space-x-2 text-sm dark:text-gray-400">
                <a href="#comments" class="flex items-center space-x-1.5 p-1 z-30 hover:bg-gray-300 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="Number of comments" class="h-4 w-4 fill-current dark:text-violet-400">
                        <path d="M448.205,392.507c30.519-27.2,47.8-63.455,47.8-101.078,0-39.984-18.718-77.378-52.707-105.3C410.218,158.963,366.432,144,320,144s-90.218,14.963-123.293,42.131C162.718,214.051,144,251.445,144,291.429s18.718,77.378,52.707,105.3c33.075,27.168,76.861,42.13,123.293,42.13,6.187,0,12.412-.273,18.585-.816l10.546,9.141A199.849,199.849,0,0,0,480,496h16V461.943l-4.686-4.685A199.17,199.17,0,0,1,448.205,392.507ZM370.089,423l-21.161-18.341-7.056.865A180.275,180.275,0,0,1,320,406.857c-79.4,0-144-51.781-144-115.428S240.6,176,320,176s144,51.781,144,115.429c0,31.71-15.82,61.314-44.546,83.358l-9.215,7.071,4.252,12.035a231.287,231.287,0,0,0,37.882,67.817A167.839,167.839,0,0,1,370.089,423Z"></path>
                        <path d="M60.185,317.476a220.491,220.491,0,0,0,34.808-63.023l4.22-11.975-9.207-7.066C62.918,214.626,48,186.728,48,156.857,48,96.833,109.009,48,184,48c55.168,0,102.767,26.43,124.077,64.3,3.957-.192,7.931-.3,11.923-.3q12.027,0,23.834,1.167c-8.235-21.335-22.537-40.811-42.2-56.961C270.072,30.279,228.3,16,184,16S97.928,30.279,66.364,56.206C33.886,82.885,16,118.63,16,156.857c0,35.8,16.352,70.295,45.25,96.243a188.4,188.4,0,0,1-40.563,60.729L16,318.515V352H32a190.643,190.643,0,0,0,85.231-20.125,157.3,157.3,0,0,1-5.071-33.645A158.729,158.729,0,0,1,60.185,317.476Z"></path>
                    </svg>
                    <span><?= $post->getNumberOfComments() ?></span>
                </a>
                <button type="button" name="post_like_btn" class="like-btn z-30 flex items-center space-x-1.5 p-1 hover:bg-blue-100 rounded-full" data-post-id="<?= $post->getId() ?>" data-isLike="<?= $post->getIsLike() ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-label="Number of likes" class="good-icon h-4 w-4 dark:text-violet-400 <?php if ($post->getIsLike() === 1) echo 'fill-blue-600' ?> ">
                        <path d="M126.638,202.672H51.986a24.692,24.692,0,0,0-24.242,19.434,487.088,487.088,0,0,0-1.466,206.535l1.5,7.189a24.94,24.94,0,0,0,24.318,19.78h74.547a24.866,24.866,0,0,0,24.837-24.838V227.509A24.865,24.865,0,0,0,126.638,202.672ZM119.475,423.61H57.916l-.309-1.487a455.085,455.085,0,0,1,.158-187.451h61.71Z"></path>
                        <path d="M494.459,277.284l-22.09-58.906a24.315,24.315,0,0,0-22.662-15.706H332V173.137l9.573-21.2A88.117,88.117,0,0,0,296.772,35.025a24.3,24.3,0,0,0-31.767,12.1L184.693,222.937V248h23.731L290.7,67.882a56.141,56.141,0,0,1,21.711,70.885l-10.991,24.341L300,169.692v48.98l16,16H444.3L464,287.2v9.272L396.012,415.962H271.07l-86.377-50.67v37.1L256.7,444.633a24.222,24.222,0,0,0,12.25,3.329h131.6a24.246,24.246,0,0,0,21.035-12.234L492.835,310.5A24.26,24.26,0,0,0,496,298.531V285.783A24.144,24.144,0,0,0,494.459,277.284Z"></path>
                    </svg>
                    <span class="number-of-likes"><?= $post->getNumberOfLikes() ?></span>
                </button>
            </div>

            <!-- reply -->
            <div class="w-full border-t border-gray-200">
                <form enctype="multipart/form-data" id="reply-form">
                    <input type="hidden" name="csrf_token" value="<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>" />
                    <input type="hidden" name="post_id" value="<?= $post->getId()  ?>">
                    <div class="flex p-2">
                        <div class="min-w-8">
                            <img class="h-8 w-8 rounded-full object-cover" src="<?= $user->getProfileImagePath() === null ? '/images/user_default_portrait.png' : '/uploads/' . substr($user->getProfileImagePath(), 0, 2) . '/' .  $user->getProfileImagePath() . $user->getProfileImageExtension() ?>" />
                        </div>

                        <div class="ml-3 flex w-full flex-col">
                            <textarea id="reply-content" name="content" placeholder="返信する" class="h-32 w-full resize-none rounded-xl border-gray-200 text-xl border-none focus:ring-0"></textarea>
                            <input type="hidden" name="MAX_FILE_SIZE" value="41943040" />
                            <input class="hidden" id="reply-file-input" type="file" name="media" accept="image/png, image/gif, image/jpeg, image/jpg, image/webp, video/mp4, video/quicktime">
                            <div class="w-full flex justify-center rounded-full relative" id="reply-previewContainer">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-4 text-blue-400">
                        <div class="flex text-2xl">
                            <div id="reply-file-input-icon" class="flex cursor-pointer items-center justify-center rounded-full p-3 hover:bg-blue-100">
                                <i class="fas fa-image"></i>
                            </div>
                        </div>

                        <div class="mb-1">
                            <button id="reply-submit" type="submit" class="inline cursor-pointer rounded-full bg-blue-500 px-4 py-3 font-bold text-white hover:opacity-75 btn btn-disabled">
                                返信
                            </button>
                            <button id="reply-loading-btn" type="button" class="hidden cursor-pointer rounded-full bg-blue-500 px-4 py-3 font-bold text-white hover:opacity-75 btn btn-disabled">
                                <svg aria-hidden="true" role="status" class="inline w-4 h-4 me-3 text-white animate-spin" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="#E5E7EB" />
                                    <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentColor" />
                                </svg>
                                送信中...
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- comments -->
            <div id="comments_container" class="w-full">
                <?php include(__DIR__ . "/../components/comment.php") ?>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/../components/alert_modal.php') ?>

</div>

<script>
    let csrfToken = "<?= src\helpers\CrossSiteForgeryProtection::getToken() ?>"
    let postId = "<?= $post->getId() ?>"
</script>

<script src="/posts.bundle.js"></script>