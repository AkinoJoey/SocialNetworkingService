<?php
$success = src\response\FlashData::getFlashData('success');
$error = src\response\FlashData::getFlashData('error');
?>

<?php if ($success) : ?>
    <div class="flex items-center justify-center mt-4 mb-3 relative w-full h-full top-0">
        <div role="alert" class="alert alert-success w-10/12 sm:w-1/2 lg:w-2/5 animate-fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    </div>
<?php endif; ?>

<?php if ($error) : ?>
    <div class="flex items-center justify-center mt-4 mb-3 relative w-full h-full top-0">
        <div role="alert" class="alert alert-error w-10/12 sm:w-1/2 lg:w-2/5 animate-wobble-hor-bottom">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    </div>
<?php endif; ?>