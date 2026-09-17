<?php
use App\Core\Auth;
use App\Enums\UserRole;

$currentUser = Auth::user();
$roleEnum = $currentUser ? UserRole::tryFrom($currentUser['role']) : null;
?>
<header class="h-16 border-b border-slate-200 bg-white/90 backdrop-blur-md sticky top-0 z-40 flex items-center justify-between px-4 sm:px-6 shadow-sm">
    <div class="flex items-center gap-3 sm:gap-4">
        <button id="sidebarToggle" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none" aria-label="เปิดเมนู">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/25">
                <i class="fa-solid fa-headset text-base"></i>
            </div>
            <div>
                <span class="font-bold text-base tracking-tight text-slate-900 flex items-center gap-2">
                    Smart IT Helpdesk
                    <span class="hidden sm:inline-flex text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-semibold uppercase tracking-wider">OOP Edition</span>
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">
        <?php if ($currentUser): ?>
            <!-- Current User Info -->
            <div class="hidden sm:flex items-center gap-3 pr-3 border-r border-slate-200">
                <div class="text-right">
                    <div class="text-xs sm:text-sm font-semibold text-slate-900 leading-tight"><?= htmlspecialchars($currentUser['name']) ?></div>
                    <div class="text-[11px] text-slate-500"><?= htmlspecialchars($currentUser['email']) ?></div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full border <?= $roleEnum ? $roleEnum->badgeColor() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                    <?php elseif ($currentUser['role'] === 'technician'): ?>
                        <i class="fa-solid fa-wrench text-[10px]"></i>
                    <?php else: ?>
                        <i class="fa-solid fa-user text-[10px]"></i>
                    <?php endif; ?>
                    <?= $roleEnum ? $roleEnum->label() : htmlspecialchars($currentUser['role']) ?>
                </span>
            </div>

            <!-- New Ticket Quick Button -->
            <a href="/tickets/create" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm shadow-blue-600/25 transition-all">
                <i class="fa-solid fa-plus text-xs"></i>
                <span class="hidden xs:inline">แจ้งซ่อม</span>
            </a>

            <!-- Logout Button -->
            <a href="/logout" title="ออกจากระบบ" class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
            </a>
        <?php else: ?>
            <a href="/login" class="text-xs font-medium text-slate-700 hover:text-blue-600 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-blue-200 hover:bg-blue-50 transition-all">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </div>
</header>
