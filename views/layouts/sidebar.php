<?php
use App\Core\Auth;

$role = Auth::role();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!-- Mobile Backdrop Overlay -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/30 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

<!-- Sidebar Component -->
<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 border-r border-slate-200 bg-white flex-shrink-0 flex flex-col justify-between p-4 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out shadow-lg lg:shadow-none">
    <div class="space-y-6">
        <!-- Main Navigation Links -->
        <div>
            <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">เมนูหลัก</div>
            <nav class="space-y-1" aria-label="เมนูการใช้งานหลัก">
                <?php if ($role === 'admin'): ?>
                    <a href="/admin/dashboard" 
                       <?= ($currentUri === '/admin/dashboard') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= ($currentUri === '/admin/dashboard') ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-sm" aria-hidden="true"></i>
                        <span>Executive Dashboard</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets" 
                   <?= ($currentUri === '/tickets' && empty($_GET['view'])) ? 'aria-current="page"' : '' ?>
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= ($currentUri === '/tickets' && empty($_GET['view'])) ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                    <i class="fa-solid fa-ticket w-5 text-center text-sm" aria-hidden="true"></i>
                    <span><?= ($role === 'user') ? 'รายการแจ้งซ่อมของฉัน' : (($role === 'technician') ? 'งานที่ได้รับมอบหมาย' : 'จัดการตั๋วงานซ่อม') ?></span>
                </a>

                <?php if ($role === 'technician'): ?>
                    <a href="/tickets?view=all" 
                       <?= (!empty($_GET['view']) && $_GET['view'] === 'all') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= (!empty($_GET['view']) && $_GET['view'] === 'all') ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-layer-group w-5 text-center text-sm" aria-hidden="true"></i>
                        <span>ตั๋วงานซ่อมทั้งหมด</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets/create" 
                   <?= ($currentUri === '/tickets/create') ? 'aria-current="page"' : '' ?>
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= ($currentUri === '/tickets/create') ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                    <i class="fa-solid fa-circle-plus w-5 text-center text-sm" aria-hidden="true"></i>
                    <span>เปิดตั๋วแจ้งซ่อมใหม่</span>
                </a>
            </nav>
        </div>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Section -->
            <div>
                <div class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">การจัดการระบบ</div>
                <nav class="space-y-1" aria-label="เมนูผู้ดูแลระบบ">
                    <a href="/admin/users" 
                       <?= ($currentUri === '/admin/users') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= ($currentUri === '/admin/users') ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-users-gear w-5 text-center text-sm" aria-hidden="true"></i>
                        <span>จัดการผู้ใช้งาน</span>
                    </a>
                    <a href="/admin/categories" 
                       <?= ($currentUri === '/admin/categories') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= ($currentUri === '/admin/categories') ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/25' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-tags w-5 text-center text-sm" aria-hidden="true"></i>
                        <span>หมวดหมู่งานซ่อม</span>
                    </a>
                </nav>
            </div>
        <?php endif; ?>
    </div>

    <!-- Quick Role Switcher Widget (Styled as Clean Dev Sandbox / Persona Switcher) -->
    <div class="mt-6 pt-4 border-t border-slate-200">
        <div class="text-[11px] font-semibold text-slate-500 mb-2.5 px-1 flex items-center justify-between">
            <span class="flex items-center gap-1.5">
                <i class="fa-solid fa-flask text-slate-400 text-xs" aria-hidden="true"></i>
                <span>สลับบทบาทผู้ใช้งาน</span>
            </span>
            <span class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200 font-medium">SANDBOX</span>
        </div>
        <div class="grid grid-cols-2 gap-1.5 text-xs">
            <a href="/quick-login/1" class="px-2.5 py-2 rounded-xl text-center bg-slate-50 hover:bg-purple-50 hover:text-purple-700 hover:border-purple-200 border border-slate-200 text-slate-700 transition-all font-medium flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-purple-600">
                <i class="fa-solid fa-shield-halved text-[11px] text-purple-600" aria-hidden="true"></i>
                <span>Admin</span>
            </a>
            <a href="/quick-login/2" class="px-2.5 py-2 rounded-xl text-center bg-slate-50 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 border border-slate-200 text-slate-700 transition-all font-medium flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-amber-600">
                <i class="fa-solid fa-wrench text-[11px] text-amber-600" aria-hidden="true"></i>
                <span>ช่างสมชาย</span>
            </a>
            <a href="/quick-login/3" class="px-2.5 py-2 rounded-xl text-center bg-slate-50 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 border border-slate-200 text-slate-700 transition-all font-medium flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-amber-600">
                <i class="fa-solid fa-screwdriver text-[11px] text-amber-600" aria-hidden="true"></i>
                <span>ช่างวิชัย</span>
            </a>
            <a href="/quick-login/4" class="px-2.5 py-2 rounded-xl text-center bg-slate-50 hover:bg-blue-50 hover:text-blue-700 hover:border-blue-200 border border-slate-200 text-slate-700 transition-all font-medium flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-blue-600">
                <i class="fa-solid fa-user text-[11px] text-blue-600" aria-hidden="true"></i>
                <span>User สมหญิง</span>
            </a>
        </div>
    </div>
</aside>
