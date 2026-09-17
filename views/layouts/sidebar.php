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
</aside>

