<?php
use App\Core\Auth;

$role = Auth::role();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<aside id="sidebar" class="w-64 border-r border-slate-800/80 bg-slate-900/40 flex-shrink-0 hidden lg:flex flex-col justify-between p-4 transition-all duration-300">
    <div class="space-y-6">
        <!-- Main Navigation Links -->
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">เมนูหลัก</div>
            <nav class="space-y-1">
                <?php if ($role === 'admin'): ?>
                    <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= ($currentUri === '/admin/dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Executive Dashboard</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= ($currentUri === '/tickets' && empty($_GET['view'])) ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span><?= ($role === 'user') ? 'รายการแจ้งซ่อมของฉัน' : (($role === 'technician') ? 'งานที่ได้รับมอบหมาย' : 'จัดการตั๋วงานซ่อม') ?></span>
                </a>

                <?php if ($role === 'technician'): ?>
                    <a href="/tickets?view=all" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= (!empty($_GET['view']) && $_GET['view'] === 'all') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>ตั๋วงานซ่อมทั้งหมด</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets/create" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= ($currentUri === '/tickets/create') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>เปิดตั๋วแจ้งซ่อมใหม่</span>
                </a>
            </nav>
        </div>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Section -->
            <div>
                <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">การจัดการระบบ (Admin)</div>
                <nav class="space-y-1">
                    <a href="/admin/users" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= ($currentUri === '/admin/users') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span>จัดการผู้ใช้งาน</span>
                    </a>
                    <a href="/admin/categories" class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-colors <?= ($currentUri === '/admin/categories') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' ?>">
                        <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>หมวดหมู่งานซ่อม</span>
                    </a>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Role Switcher Widget (For Presentations & Grading) -->
    <div class="mt-6 pt-4 border-t border-slate-800">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2 px-1 flex items-center justify-between">
            <span>สลับบทบาททดสอบ</span>
            <span class="text-[9px] bg-amber-500/20 text-amber-300 px-1.5 py-0.5 rounded border border-amber-500/30">DEMO</span>
        </div>
        <div class="grid grid-cols-2 gap-1.5 text-xs">
            <a href="/quick-login/1" class="px-2.5 py-1.5 rounded-lg text-center bg-rose-500/10 text-rose-300 border border-rose-500/20 hover:bg-rose-500/20 transition-all font-medium">Admin</a>
            <a href="/quick-login/2" class="px-2.5 py-1.5 rounded-lg text-center bg-amber-500/10 text-amber-300 border border-amber-500/20 hover:bg-amber-500/20 transition-all font-medium">ช่างสมชาย</a>
            <a href="/quick-login/3" class="px-2.5 py-1.5 rounded-lg text-center bg-amber-500/10 text-amber-300 border border-amber-500/20 hover:bg-amber-500/20 transition-all font-medium">ช่างวิชัย</a>
            <a href="/quick-login/4" class="px-2.5 py-1.5 rounded-lg text-center bg-blue-500/10 text-blue-300 border border-blue-500/20 hover:bg-blue-500/20 transition-all font-medium">User สมหญิง</a>
        </div>
    </div>
</aside>
