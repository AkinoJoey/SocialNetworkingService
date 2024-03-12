<div class="z-40 fixed top-0 -translate-x-1/2 left-1/2 w-full">
    <div class="grid max-w-xs grid-cols-2 gap-1 p-1 mx-auto my-2 bg-gray-100 rounded-lg dark:bg-gray-600" role="group">
        <button data-tab="trend" type="button" class="tab px-5 py-1.5 text-xs font-medium  rounded-lg <?php if($tabActive === 'trend') echo   'tab-active' ?>">
            トレンド
        </button>
        <button data-tab="following" type="button" class="tab px-5 py-1.5 text-xs font-medium  rounded-lg <?php if($tabActive === 'following') echo 'tab-active' ?>">
            フォロー中
        </button>
    </div>
</div>