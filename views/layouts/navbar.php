<?php
use App\Core\Auth;
use App\Enums\UserRole;

$currentUser = Auth::user();
$roleEnum = $currentUser ? UserRole::tryFrom($currentUser['role']) : null;
?>
<header class="h-16 border-b border-slate-800/80 bg-slate-900/60 backdrop-blur-md sticky top-0 z-40 flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/25">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <div>
                <span class="font-bold text-base tracking-tight text-white flex items-center gap-2">
                    Smart IT Helpdesk
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 font-semibold uppercase tracking-wider">OOP Edition</span>
                </span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <?php if ($currentUser): ?>
            <!-- Current User Info -->
            <div class="hidden sm:flex items-center gap-3 pr-3 border-r border-slate-800">
                <div class="text-right">
                    <div class="text-sm font-semibold text-white leading-tight"><?= htmlspecialchars($currentUser['name']) ?></div>
                    <div class="text-xs text-slate-400"><?= htmlspecialchars($currentUser['email']) ?></div>
                </div>
                <span class="px-2.5 py-1 text-xs font-semibold rounded-full border <?= $roleEnum ? $roleEnum->badgeColor() : 'bg-slate-800 text-slate-300' ?>">
                    <?= $roleEnum ? $roleEnum->label() : htmlspecialchars($currentUser['role']) ?>
                </span>
            </div>

            <!-- New Ticket Quick Button -->
            <a href="/tickets/create" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>แจ้งซ่อม</span>
            </a>

            <!-- Logout Button -->
            <a href="/logout" title="ออกจากระบบ" class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </a>
        <?php else: ?>
            <a href="/login" class="text-xs text-slate-300 hover:text-white px-3 py-1.5 rounded-lg bg-slate-800">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </div>
</header>
