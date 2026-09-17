<?php
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

$sc = $stats['status_counts'];
?>

<div class="space-y-6 sm:space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-chart-pie text-blue-600 text-2xl" aria-hidden="true"></i>
                <span>Executive Dashboard</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">ภาพรวมสถิติการดำเนินงาน ตัวชี้วัดประสิทธิภาพ (KPI) และภาระงานระบบ</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                ระบบปฏิบัติการ Online
            </span>
            <a href="/tickets" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50/50 text-slate-700 hover:text-blue-600 text-xs font-semibold transition-all shadow-xs flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                <span>ตั๋วงานทั้งหมด</span>
                <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 sm:gap-4">
        <!-- Total -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">ตั๋วทั้งหมด</span>
                <i class="fa-solid fa-ticket text-slate-400 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-slate-900 mt-1 tabular-nums"><?= number_format($stats['total_tickets']) ?></div>
            <div class="text-[10px] text-slate-400 mt-1">ทุกหมวดหมู่งาน</div>
        </div>

        <!-- Open -->
        <div class="p-4 rounded-2xl bg-sky-50/60 border border-sky-200 shadow-xs hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-sky-800 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">เปิดใหม่</span>
                <i class="fa-solid fa-envelope-open text-sky-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-sky-900 mt-1 tabular-nums"><?= number_format($sc['open']) ?></div>
            <div class="text-[10px] text-sky-700/80 mt-1">รอมอบหมายงาน</div>
        </div>

        <!-- Assigned -->
        <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200 shadow-xs hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-purple-800 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">จ่ายงานแล้ว</span>
                <i class="fa-solid fa-user-check text-purple-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-purple-900 mt-1 tabular-nums"><?= number_format($sc['assigned']) ?></div>
            <div class="text-[10px] text-purple-700/80 mt-1">รอช่างเริ่มงาน</div>
        </div>

        <!-- In Progress -->
        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200 shadow-xs hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-amber-800 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">กำลังซ่อม</span>
                <i class="fa-solid fa-gears text-amber-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-amber-900 mt-1 tabular-nums"><?= number_format($sc['in_progress']) ?></div>
            <div class="text-[10px] text-amber-700/80 mt-1">อยู่ระหว่างตรวจเช็ค</div>
        </div>

        <!-- Resolved -->
        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 shadow-xs hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-emerald-800 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">ซ่อมเสร็จ</span>
                <i class="fa-solid fa-circle-check text-emerald-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-emerald-900 mt-1 tabular-nums"><?= number_format($sc['resolved']) ?></div>
            <div class="text-[10px] text-emerald-700/80 mt-1">รอตรวจรับงาน</div>
        </div>

        <!-- Avg Hours -->
        <div class="p-4 rounded-2xl bg-indigo-50/60 border border-indigo-200 shadow-xs hover:shadow-sm transition-shadow">
            <div class="flex items-center justify-between text-indigo-800 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">เวลาเฉลี่ย (ชม.)</span>
                <i class="fa-solid fa-stopwatch text-indigo-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-indigo-900 mt-1 tabular-nums"><?= $stats['avg_resolution_hours'] ?></div>
            <div class="text-[10px] text-indigo-700/80 mt-1">ต่อการแก้ปัญหา</div>
        </div>

        <!-- Avg Rating -->
        <div class="p-4 rounded-2xl bg-yellow-50/60 border border-yellow-200 shadow-xs hover:shadow-sm transition-shadow col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-yellow-800 mb-1">
                <span class="text-[11px] font-bold uppercase tracking-wider">ความพึงพอใจ</span>
                <i class="fa-solid fa-star text-yellow-500 text-xs" aria-hidden="true"></i>
            </div>
            <div class="text-2xl font-bold text-yellow-900 mt-1 flex items-center gap-1 tabular-nums">
                <?= $stats['avg_rating'] ?: '5.0' ?> <span class="text-amber-500 text-sm" aria-hidden="true">★</span>
            </div>
            <div class="text-[10px] text-yellow-700/80 mt-1">คะแนนประเมิน</div>
        </div>
    </div>

    <!-- Urgent / Unassigned Tickets Widget -->
    <?php if (!empty($unassignedTickets)): ?>
        <div class="rounded-2xl border border-sky-200 bg-sky-50/40 p-5 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-sky-600 text-sm" aria-hidden="true"></i>
                        <span>ตั๋วเปิดใหม่ที่รอมอบหมายช่าง (<?= count($unassignedTickets) ?> รายการ)</span>
                    </h2>
                </div>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3.5">
                <?php foreach ($unassignedTickets as $ticket): 
                    $pEnum = TicketPriority::tryFrom($ticket['priority']);
                ?>
                    <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between space-y-3 hover:border-slate-300 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-mono text-slate-400 font-semibold">#<?= $ticket['id'] ?></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border <?= $pEnum ? $pEnum->badgeClasses() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                    <?= $pEnum ? $pEnum->label() : $ticket['priority'] ?>
                                </span>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900 line-clamp-1"><?= htmlspecialchars($ticket['title']) ?></h3>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= htmlspecialchars($ticket['description']) ?></p>
                            <div class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                                <span>ผู้แจ้ง: <strong class="text-slate-700 font-medium"><?= htmlspecialchars($ticket['user_name']) ?></strong></span>
                                <span>&bull;</span>
                                <span><?= htmlspecialchars($ticket['category_name']) ?></span>
                            </div>
                        </div>
                        
                        <!-- Quick Assign Form -->
                        <form action="/admin/tickets/<?= $ticket['id'] ?>/assign" method="POST" class="pt-2.5 border-t border-slate-100 flex items-center gap-2">
                            <?= \App\Core\Csrf::field() ?>
                            <label for="tech_select_<?= $ticket['id'] ?>" class="sr-only">เลือกช่างเทคนิคสำหรับตั๋ว #<?= $ticket['id'] ?></label>
                            <select id="tech_select_<?= $ticket['id'] ?>" name="technician_id" required class="flex-1 bg-slate-50 border border-slate-200 text-xs rounded-xl px-2.5 py-1.5 text-slate-800 focus:outline-none focus:border-blue-600 focus:bg-white">
                                <option value="">-- เลือกช่างเทคนิค --</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs transition-all whitespace-nowrap flex items-center gap-1 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                                <i class="fa-solid fa-user-plus text-[10px]" aria-hidden="true"></i>
                                <span>จ่ายงาน</span>
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two-Column Analytics Layout -->
    <div class="grid lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Top Technicians Leaderboard -->
        <div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-amber-500" aria-hidden="true"></i>
                    <span>อันดับผลงานช่างเทคนิค (Technician Leaderboard)</span>
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <caption class="sr-only">ตารางแสดงอันดับและผลงานช่างเทคนิค</caption>
                    <thead>
                        <tr class="text-slate-500 border-b border-slate-200">
                            <th scope="col" class="pb-2.5 font-semibold">ช่างเทคนิค</th>
                            <th scope="col" class="pb-2.5 font-semibold text-center">งานทั้งหมด</th>
                            <th scope="col" class="pb-2.5 font-semibold text-center">สำเร็จแล้ว</th>
                            <th scope="col" class="pb-2.5 font-semibold text-center">คะแนนเฉลี่ย</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        <?php if (empty($stats['top_technicians'])): ?>
                            <tr><td colspan="4" class="py-4 text-center text-slate-400">ยังไม่มีข้อมูลผลงานช่าง</td></tr>
                        <?php else: ?>
                            <?php foreach ($stats['top_technicians'] as $idx => $t): ?>
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 flex items-center gap-2.5">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] <?= $idx === 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' ?>">
                                            <?= $idx + 1 ?>
                                        </span>
                                        <span class="font-semibold text-slate-900"><?= htmlspecialchars($t['name']) ?></span>
                                    </td>
                                    <td class="py-3 text-center font-mono font-medium text-slate-700 tabular-nums"><?= $t['total_jobs'] ?></td>
                                    <td class="py-3 text-center font-mono font-bold text-emerald-600 tabular-nums"><?= $t['completed_jobs'] ?></td>
                                    <td class="py-3 text-center text-amber-600 font-bold font-mono tabular-nums"><?= $t['avg_score'] ? $t['avg_score'] . ' ★' : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs">
            <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-blue-600" aria-hidden="true"></i>
                <span>การกระจายงานตามหมวดหมู่ (Category Distribution)</span>
            </h3>
            <div class="space-y-3.5">
                <?php 
                $maxCat = max(1, ...array_map(fn($c) => (int)$c['ticket_count'], $stats['category_breakdown'] ?: [['ticket_count' => 1]]));
                foreach ($stats['category_breakdown'] as $cat): 
                    $percent = round(($cat['ticket_count'] / $maxCat) * 100);
                ?>
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-800"><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="text-slate-500 font-mono tabular-nums"><?= $cat['ticket_count'] ?> ตั๋ว</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Status Audit Trail (Recent Activity) -->
    <div class="rounded-2xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs">
        <h3 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-indigo-600" aria-hidden="true"></i>
            <span>ประวัติการเปลี่ยนสถานะล่าสุด (Audit Trail)</span>
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <caption class="sr-only">ตารางบันทึกการเปลี่ยนสถานะล่าสุดของตั๋วงานในระบบ</caption>
                <thead>
                    <tr class="text-slate-500 border-b border-slate-200">
                        <th scope="col" class="pb-2.5 font-semibold">วันเวลา</th>
                        <th scope="col" class="pb-2.5 font-semibold">รหัสตั๋ว</th>
                        <th scope="col" class="pb-2.5 font-semibold">การเปลี่ยนสถานะ</th>
                        <th scope="col" class="pb-2.5 font-semibold">ผู้ดำเนินการ</th>
                        <th scope="col" class="pb-2.5 font-semibold">บันทึกช่วยจำ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    <?php foreach ($stats['recent_logs'] as $log): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-2.5 font-mono text-slate-400 tabular-nums"><?= date('d/m/y H:i', strtotime($log['created_at'])) ?></td>
                            <td class="py-2.5 font-semibold text-slate-900">
                                <a href="/tickets/<?= $log['ticket_id'] ?>" class="text-blue-600 hover:text-blue-700 font-mono tabular-nums">#<?= $log['ticket_id'] ?></a>
                            </td>
                            <td class="py-2.5">
                                <span class="font-mono text-slate-500"><?= $log['from_status'] ?></span>
                                <span class="text-slate-400 mx-1" aria-hidden="true">&rarr;</span>
                                <span class="font-mono text-blue-600 font-bold"><?= $log['to_status'] ?></span>
                            </td>
                            <td class="py-2.5">
                                <span class="text-slate-800 font-medium"><?= htmlspecialchars($log['changed_by_name']) ?></span>
                                <span class="text-[10px] text-slate-400">(<?= $log['changed_by_role'] ?>)</span>
                            </td>
                            <td class="py-2.5 text-slate-500"><?= htmlspecialchars($log['note'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
