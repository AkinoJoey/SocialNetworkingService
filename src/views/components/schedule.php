<div id="schedule_modal" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="fixed left-0 right-0 top-0 z-50 hidden h-screen w-full items-center justify-center">
    <div class="relative w-full max-h-96 sm:max-h-[570px] overflow-auto rounded-xl bg-white sm:w-2/3 lg:w-2/5">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center p-4 md:p-5 border-b rounded-t dark:border-gray-600 align-middle">
                <button data-modal-hide="schedule_modal" class="text-blue-400 rounded-full px-3 py-1 hover:bg-blue-100">
                    <i class="fas fa-times text-xl"></i>
                </button>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    予約投稿
                </h3>
            </div>
            <!-- Modal body -->
            <div class="py-12 space-y-4 flex justify-center">
                <input class="rounded-lg flatpickr" type="text"  placeholder="日付を入力" readonly>
            </div>
            <!-- Modal footer -->
            <div class="flex justify-end items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="schedule_modal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">予約する</button>
                <button data-modal-hide="schedule_modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">キャンセル</button>
            </div>
        </div>
    </div>
</div>