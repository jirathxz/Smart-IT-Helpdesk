<?php
use App\Core\Auth;
use App\Enums\UserRole;

$currentUser = Auth::user();
$roleEnum = $currentUser ? UserRole::tryFrom($currentUser['role']) : null;
?>
<header class="h-16 border-b border-slate-200 bg-white/95 backdrop-blur-md sticky top-0 z-40 flex items-center justify-between px-4 sm:px-6 shadow-xs">
    <div class="flex items-center gap-3 sm:gap-4">
        <button id="sidebarToggle" class="lg:hidden min-w-[44px] min-h-[44px] inline-flex items-center justify-center rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-1 transition-colors" aria-label="เปิดเมนูนำทาง">
            <i class="fa-solid fa-bars text-lg" aria-hidden="true"></i>
        </button>
        <a href="/" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 rounded-xl">
            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm shadow-blue-500/25 group-hover:bg-blue-700 transition-colors">
                <i class="fa-solid fa-headset text-base" aria-hidden="true"></i>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-bold text-base tracking-tight text-slate-900">
                    Smart IT Helpdesk
                </span>
                <span class="hidden md:inline-flex text-[11px] px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-medium">Service Portal</span>
            </div>
        </a>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">
        <?php if ($currentUser): ?>
            <!-- Current User Profile Info -->
            <div class="hidden sm:flex items-center gap-3 pr-3 border-r border-slate-200">
                <div class="text-right">
                    <div class="text-xs sm:text-sm font-semibold text-slate-900 leading-tight"><?= htmlspecialchars($currentUser['name']) ?></div>
                    <div class="text-[11px] text-slate-500"><?= htmlspecialchars($currentUser['email']) ?></div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border <?= $roleEnum ? $roleEnum->badgeColor() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <i class="fa-solid fa-shield-halved text-[10px]" aria-hidden="true"></i>
                    <?php elseif ($currentUser['role'] === 'technician'): ?>
                        <i class="fa-solid fa-wrench text-[10px]" aria-hidden="true"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-user text-[10px]" aria-hidden="true"></i>
                    <?php endif; ?>
                    <?= $roleEnum ? $roleEnum->label() : htmlspecialchars($currentUser['role']) ?>
                </span>
            </div>

            <!-- New Ticket Quick Button -->
            <a href="/tickets/create" class="inline-flex items-center gap-1.5 min-h-[38px] px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs shadow-blue-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 transition-all">
                <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
                <span class="hidden xs:inline">แจ้งซ่อมใหม่</span>
            </a>

            <!-- Logout Button -->
            <a href="/logout" title="ออกจากระบบ" aria-label="ออกจากระบบ" class="min-w-[38px] min-h-[38px] inline-flex items-center justify-center rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600 focus-visible:ring-offset-2 transition-colors">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm" aria-hidden="true"></i>
            </a>
        <?php else: ?>
            <a href="/login" class="text-xs font-medium text-slate-700 hover:text-blue-600 px-3.5 py-2 rounded-xl border border-slate-200 hover:border-blue-300 hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 transition-all">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </div>
</header>
