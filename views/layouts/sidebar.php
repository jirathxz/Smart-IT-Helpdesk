<?php
use App\Core\Auth;

$role = Auth::role();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!-- Mobile Backdrop Overlay -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity"></div>

<!-- Sidebar Component -->
<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 border-r border-slate-200 bg-white flex-shrink-0 flex flex-col justify-between p-4 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    <div class="space-y-6">
        <!-- Main Navigation Links -->
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">เมนูหลัก</div>
            <nav class="space-y-1">
                <?php if ($role === 'admin'): ?>
                    <a href="/admin/dashboard" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= ($currentUri === '/admin/dashboard') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100/70' ?>">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-sm"></i>
                        <span>Executive Dashboard</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= ($currentUri === '/tickets' && empty($_GET['view'])) ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100/70' ?>">
                    <i class="fa-solid fa-ticket w-5 text-center text-sm"></i>
                    <span><?= ($role === 'user') ? 'รายการแจ้งซ่อมของฉัน' : (($role === 'technician') ? 'งานที่ได้รับมอบหมาย' : 'จัดการตั๋วงานซ่อม') ?></span>
                </a>

                <?php if ($role === 'technician'): ?>
                    <a href="/tickets?view=all" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= (!empty($_GET['view']) && $_GET['view'] === 'all') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100/70' ?>">
                        <i class="fa-solid fa-layer-group w-5 text-center text-sm"></i>
                        <span>ตั๋วงานซ่อมทั้งหมด</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets/create" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= ($currentUri === '/tickets/create') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100/70' ?>">
                    <i class="fa-solid fa-circle-plus w-5 text-center text-sm"></i>
                    <span>เปิดตั๋วแจ้งซ่อมใหม่</span>
                </a>
            </nav>
        </div>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Section -->
            <div>
                <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">การจัดการระบบ (Admin)</div>
                <nav class="space-y-1">
                    <a href="/admin/users" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= ($currentUri === '/admin/users') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100/70' ?>">
                        <i class="fa-solid fa-users-gear w-5 text-center text-sm"></i>
                        <span>จัดการผู้ใช้งาน</span>
                    </a>
                    <a href="/admin/categories" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all <?= ($currentUri === '/admin/categories') ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100/70' ?>">
                        <i class="fa-solid fa-tags w-5 text-center text-sm"></i>
                        <span>หมวดหมู่งานซ่อม</span>
                    </a>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Role Switcher Widget (For Presentations & Grading) -->
    <div class="mt-6 pt-4 border-t border-slate-200">
        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2.5 px-1 flex items-center justify-between">
            <span>สลับบทบาททดสอบ</span>
            <span class="text-[9px] bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded border border-amber-200 font-bold">DEMO</span>
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
            <a href="/quick-login/1" class="px-2.5 py-2 rounded-xl text-center bg-purple-50 text-purple-700 border border-purple-200 hover:bg-purple-100 transition-all font-medium flex flex-col items-center justify-center gap-0.5">
                <i class="fa-solid fa-crown text-[11px] text-purple-600"></i>
                <span>Admin</span>
            </a>
            <a href="/quick-login/2" class="px-2.5 py-2 rounded-xl text-center bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all font-medium flex flex-col items-center justify-center gap-0.5">
                <i class="fa-solid fa-wrench text-[11px] text-amber-600"></i>
                <span>ช่างสมชาย</span>
            </a>
            <a href="/quick-login/3" class="px-2.5 py-2 rounded-xl text-center bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition-all font-medium flex flex-col items-center justify-center gap-0.5">
                <i class="fa-solid fa-screwdriver text-[11px] text-amber-600"></i>
                <span>ช่างวิชัย</span>
            </a>
            <a href="/quick-login/4" class="px-2.5 py-2 rounded-xl text-center bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition-all font-medium flex flex-col items-center justify-center gap-0.5">
                <i class="fa-solid fa-user text-[11px] text-blue-600"></i>
                <span>User สมหญิง</span>
            </a>
        </div>
    </div>
</aside>
