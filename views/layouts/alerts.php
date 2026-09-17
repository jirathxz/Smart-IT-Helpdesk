<?php
$successMsg = $_SESSION['flash_success'] ?? null;
$errorMsg = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<?php if ($successMsg): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm animate-fade-in">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base flex-shrink-0"></i>
            <span class="font-medium text-sm"><?= htmlspecialchars($successMsg) ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-base leading-none p-1" aria-label="ปิดการแจ้งเตือน">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
<?php endif; ?>

<?php if ($errorMsg): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-sm animate-fade-in">
        <div class="flex items-center gap-3">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-base flex-shrink-0"></i>
            <span class="font-medium text-sm"><?= htmlspecialchars($errorMsg) ?></span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-base leading-none p-1" aria-label="ปิดการแจ้งเตือน">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
<?php endif; ?>
