<?php
use App\Core\Auth;

$role = Auth::role();
$userName = Auth::user()['name'] ?? 'ผู้ใช้งาน';
?>

<div class="max-w-4xl mx-auto py-6 sm:py-10 space-y-6">
    <!-- Main Status Card -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 sm:p-10 text-center relative overflow-hidden">
        <!-- Accent Top Bar -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-600 via-sky-500 to-indigo-600"></div>

        <!-- Animated Icon Badge -->
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 mx-auto flex items-center justify-center text-2xl sm:text-3xl shadow-xs mb-5">
            <i class="fa-solid fa-code-merge animate-pulse" aria-hidden="true"></i>
        </div>

        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold tracking-wide uppercase shadow-xs mb-3">
            <i class="fa-solid fa-clock-rotate-left text-amber-600 text-[11px]" aria-hidden="true"></i>
            <span>รอการผสานโค้ด (Awaiting Developer 1 Merge)</span>
        </span>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
            โมดูลวงจรชีวิตตั๋วงานซ่อม (Ticket Lifecycle & Operations)
        </h1>
        <p class="text-sm sm:text-base text-slate-500 mt-2 max-w-2xl mx-auto leading-relaxed">
            หน้านี้อยู่ในขอบเขตความรับผิดชอบของ <strong>Developer 1</strong> ตามข้อตกลงแบ่งงาน 50/50 ของโครงการ ระบบแกนกลาง (Developer 2) ได้เตรียมเส้นทางเชื่อมต่อและโครงสร้างไว้พร้อมสำหรับการ Merge โค้ด
        </p>

        <!-- Division of Work Checklist -->
        <div class="mt-8 pt-8 border-t border-slate-100 text-left">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-blue-600" aria-hidden="true"></i>
                <span>สถานะโมดูลของ Developer 1 ที่กำลังรอการ Merge</span>
            </h2>

            <div class="grid sm:grid-cols-2 gap-3.5 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                    <i class="fa-solid fa-circle-notch text-amber-500 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <strong class="text-slate-800 font-semibold block">Ticket Controller & Views</strong>
                        <span class="text-slate-500">`TicketController`, `views/tickets/create.php`, `index.php`, `show.php`</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                    <i class="fa-solid fa-circle-notch text-amber-500 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <strong class="text-slate-800 font-semibold block">Ticket State Machine</strong>
                        <span class="text-slate-500">`TicketStatusService` (State Pattern การเปลี่ยนสถานะตั๋วซ่อม)</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                    <i class="fa-solid fa-circle-notch text-amber-500 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <strong class="text-slate-800 font-semibold block">Data Repositories</strong>
                        <span class="text-slate-500">`TicketRepository`, `CommentRepository`, `StatusLogRepository`, `RatingRepository`</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start gap-3">
                    <i class="fa-solid fa-circle-notch text-amber-500 mt-0.5" aria-hidden="true"></i>
                    <div>
                        <strong class="text-slate-800 font-semibold block">File Uploader & Enums</strong>
                        <span class="text-slate-500">`FileUploader`, `TicketStatus`, `TicketPriority`, `ticket.js`</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ready Modules from Developer 2 -->
        <div class="mt-8 pt-8 border-t border-slate-100 text-left">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600" aria-hidden="true"></i>
                <span>โมดูลของ Developer 2 ที่พร้อมใช้งานแล้วในระบบ</span>
            </h2>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
                    <i class="fa-solid fa-check text-[10px] mr-1" aria-hidden="true"></i> Database PDO Singleton & Transaction
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
                    <i class="fa-solid fa-check text-[10px] mr-1" aria-hidden="true"></i> Router & Middleware (Auth, Role, CSRF)
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
                    <i class="fa-solid fa-check text-[10px] mr-1" aria-hidden="true"></i> Executive Dashboard & KPI Analytics
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
                    <i class="fa-solid fa-check text-[10px] mr-1" aria-hidden="true"></i> User & Category Management Repositories
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
                    <i class="fa-solid fa-check text-[10px] mr-1" aria-hidden="true"></i> LINE Messaging Service & Event Observer
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 font-medium">
                    <i class="fa-solid fa-check text-[10px] mr-1" aria-hidden="true"></i> Master Layout & Auth System
                </span>
            </div>
        </div>

        <!-- Action Links -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <?php if ($role === 'admin'): ?>
                <a href="/admin/dashboard" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs hover:shadow-sm transition-all flex items-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                    <i class="fa-solid fa-chart-pie" aria-hidden="true"></i>
                    <span>กลับไปยัง Executive Dashboard</span>
                </a>
                <a href="/admin/users" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-users-gear" aria-hidden="true"></i>
                    <span>จัดการผู้ใช้</span>
                </a>
                <a href="/admin/categories" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-tags" aria-hidden="true"></i>
                    <span>หมวดหมู่ซ่อม</span>
                </a>
            <?php else: ?>
                <a href="/quick-login/1" class="px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold shadow-xs transition-all flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                    <span>สลับไปยังบัญชี Admin เพื่อดูระบบส่วนกลาง</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
