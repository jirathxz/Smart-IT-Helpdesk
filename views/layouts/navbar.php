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
        <a href="/" class="flex items-center gap-2.5 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 rounded-lg py-1 px-1.5 -ml-1.5 transition-colors">
            <div class="flex items-baseline tracking-tight font-sans select-none">
                <span class="text-lg sm:text-xl font-bold text-slate-900 group-hover:text-slate-950 transition-colors">Smart</span>
                <span class="text-lg sm:text-xl font-black text-blue-600 tracking-tighter ml-0.5">IT</span>
                <span class="text-sm sm:text-base font-medium text-slate-500 ml-1.5 tracking-normal">Helpdesk</span>
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 ml-1 mb-0.5 inline-block"></span>
            </div>
        </a>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">
        <?php if ($currentUser): 
            $initial = mb_substr($currentUser['name'], 0, 1, 'UTF-8');
            $roleShort = match ($currentUser['role']) {
                'admin'      => 'Admin',
                'technician' => 'Tech',
                default      => 'User',
            };
        ?>
            <!-- New Ticket Quick Action -->
            <a href="/tickets/create" class="inline-flex items-center gap-1.5 min-h-[34px] px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium shadow-2xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 transition-all">
                <i class="fa-solid fa-plus text-[11px]" aria-hidden="true"></i>
                <span class="hidden sm:inline">แจ้งซ่อมใหม่</span>
            </a>

            <!-- User Profile Dropdown Menu (Anti-Slop Modern Enterprise Identity) -->
            <div class="relative">
                <button id="userMenuBtn" 
                        type="button" 
                        aria-expanded="false" 
                        aria-haspopup="true"
                        aria-label="เมนูผู้ใช้งาน" 
                        class="flex items-center gap-2.5 py-1 px-1.5 sm:px-2 rounded-lg hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-semibold select-none shadow-2xs">
                        <?= htmlspecialchars($initial) ?>
                    </div>
                    <div class="text-left hidden md:block">
                        <div class="text-xs font-semibold text-slate-800 leading-tight">
                            <?= htmlspecialchars($currentUser['name']) ?>
                        </div>
                        <div class="text-[10px] text-slate-400 font-mono tracking-tight flex items-center gap-1 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span><?= $roleShort ?></span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="truncate max-w-[120px]"><?= htmlspecialchars($currentUser['email']) ?></span>
                        </div>
                    </div>
                    <i id="userMenuChevron" class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-150" aria-hidden="true"></i>
                </button>

                <!-- Dropdown Popup Card -->
                <div id="userDropdown" 
                     class="hidden absolute right-0 top-full mt-1.5 w-64 bg-white border border-slate-200 rounded-xl shadow-lg p-1.5 z-50 divide-y divide-slate-100 animate-in fade-in zoom-in-95 duration-100">
                    <!-- Dropdown Header -->
                    <div class="px-3 py-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-900 truncate">
                                <?= htmlspecialchars($currentUser['name']) ?>
                            </span>
                            <span class="text-[10px] font-mono font-medium px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                <?= $roleShort ?>
                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 truncate mt-0.5">
                            <?= htmlspecialchars($currentUser['email']) ?>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="py-1 space-y-0.5 text-xs font-medium text-slate-700">
                        <a href="/profile" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors">
                            <i class="fa-regular fa-user text-slate-400 w-4 text-center"></i>
                            <span>โปรไฟล์ส่วนตัว (My Profile)</span>
                        </a>
                        <a href="/profile#security" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors">
                            <i class="fa-solid fa-lock text-slate-400 w-4 text-center"></i>
                            <span>ความปลอดภัยและรหัสผ่าน</span>
                        </a>
                        <?php if ($currentUser['role'] === 'admin'): ?>
                            <a href="/admin/dashboard" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-50 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-chart-pie text-slate-400 w-4 text-center"></i>
                                <span>Executive Dashboard</span>
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- Sign Out Button -->
                    <div class="pt-1">
                        <a href="/logout" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket text-rose-500 w-4 text-center"></i>
                            <span>ออกจากระบบ (Sign out)</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <a href="/login" class="text-xs font-medium text-slate-700 hover:text-blue-600 px-3.5 py-1.5 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 transition-all">เข้าสู่ระบบ</a>
        <?php endif; ?>
    </div>
</header>

<!-- Vanilla JS for User Profile Dropdown Interaction -->
<script>
(function() {
    const btn = document.getElementById('userMenuBtn');
    const menu = document.getElementById('userDropdown');
    const chevron = document.getElementById('userMenuChevron');
    if (!btn || !menu) return;

    function toggleMenu() {
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            btn.setAttribute('aria-expanded', 'true');
            if (chevron) chevron.classList.add('rotate-180');
        } else {
            closeMenu();
        }
    }

    function closeMenu() {
        if (!menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
            if (chevron) chevron.classList.remove('rotate-180');
        }
    }

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMenu();
    });

    document.addEventListener('click', (e) => {
        if (!btn.contains(e.target) && !menu.contains(e.target)) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeMenu();
    });
})();
</script>
