<?php
use App\Core\Auth;

$role = Auth::role();
$currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
?>
<!-- Mobile Backdrop Overlay -->
<div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/30 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

<!-- Sidebar Component -->
<aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-56 border-r border-slate-200 bg-white flex-shrink-0 flex flex-col justify-between p-3 transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-in-out">
    <div class="space-y-4">
        <!-- Main Navigation Links -->
        <div>
            <div class="px-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Workspace</div>
            <nav class="space-y-0.5" aria-label="เมนูการใช้งานหลัก">
                <?php if ($role === 'admin'): ?>
                    <a href="/admin/dashboard" 
                       <?= ($currentUri === '/admin/dashboard') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($currentUri === '/admin/dashboard') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-chart-pie w-4 text-center text-slate-500 text-xs" aria-hidden="true"></i>
                        <span>Dashboard</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets" 
                   <?= ($currentUri === '/tickets' && empty($_GET['view'])) ? 'aria-current="page"' : '' ?>
                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($currentUri === '/tickets' && empty($_GET['view'])) ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <i class="fa-solid fa-ticket w-4 text-center text-slate-500 text-xs" aria-hidden="true"></i>
                    <span><?= ($role === 'user') ? 'My tickets' : (($role === 'technician') ? 'Assigned' : 'Tickets') ?></span>
                </a>

                <?php if ($role === 'technician'): ?>
                    <a href="/tickets?view=all" 
                       <?= (!empty($_GET['view']) && $_GET['view'] === 'all') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors <?= (!empty($_GET['view']) && $_GET['view'] === 'all') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-layer-group w-4 text-center text-slate-500 text-xs" aria-hidden="true"></i>
                        <span>All tickets</span>
                    </a>
                <?php endif; ?>

                <a href="/tickets/create" 
                   <?= ($currentUri === '/tickets/create') ? 'aria-current="page"' : '' ?>
                   class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($currentUri === '/tickets/create') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                    <i class="fa-solid fa-plus w-4 text-center text-slate-500 text-xs" aria-hidden="true"></i>
                    <span>New ticket</span>
                </a>
            </nav>
        </div>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Section -->
            <div>
                <div class="px-2 text-[10px] font-semibold uppercase tracking-wider text-slate-400 mb-1">Management</div>
                <nav class="space-y-0.5" aria-label="เมนูผู้ดูแลระบบ">
                    <a href="/admin/users" 
                       <?= ($currentUri === '/admin/users') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($currentUri === '/admin/users') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-users w-4 text-center text-slate-500 text-xs" aria-hidden="true"></i>
                        <span>Users</span>
                    </a>
                    <a href="/admin/categories" 
                       <?= ($currentUri === '/admin/categories') ? 'aria-current="page"' : '' ?>
                       class="flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors <?= ($currentUri === '/admin/categories') ? 'bg-slate-100 text-slate-900 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                        <i class="fa-solid fa-tags w-4 text-center text-slate-500 text-xs" aria-hidden="true"></i>
                        <span>Categories</span>
                    </a>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</aside>
