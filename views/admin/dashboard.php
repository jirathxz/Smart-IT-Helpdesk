<?php
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

$sc = $stats['status_counts'];
?>

<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-white tracking-tight">Executive Dashboard</h1>
            <p class="text-sm text-slate-400 mt-1">ภาพรวมสถิติการดำเนินงานและตัวชี้วัดสำคัญของระบบ IT Helpdesk</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                ระบบปฏิบัติการ Online
            </span>
            <a href="/tickets" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-all">
                ดูตั๋วทั้งหมด &rarr;
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 sm:gap-4">
        <!-- Total -->
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800/80 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">ตั๋วทั้งหมด</div>
            <div class="text-2xl font-extrabold text-white mt-1"><?= $stats['total_tickets'] ?></div>
            <div class="text-[10px] text-slate-500 mt-1">ทุกหมวดหมู่</div>
        </div>

        <!-- Open -->
        <div class="p-4 rounded-2xl bg-sky-500/10 border border-sky-500/20 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-sky-400">เปิดใหม่ (Open)</div>
            <div class="text-2xl font-extrabold text-sky-300 mt-1"><?= $sc['open'] ?></div>
            <div class="text-[10px] text-sky-400/70 mt-1">รอมอบหมายงาน</div>
        </div>

        <!-- Assigned -->
        <div class="p-4 rounded-2xl bg-purple-500/10 border border-purple-500/20 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-purple-400">จ่ายงานแล้ว</div>
            <div class="text-2xl font-extrabold text-purple-300 mt-1"><?= $sc['assigned'] ?></div>
            <div class="text-[10px] text-purple-400/70 mt-1">รอช่างเริ่มงาน</div>
        </div>

        <!-- In Progress -->
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-400">กำลังซ่อม</div>
            <div class="text-2xl font-extrabold text-amber-300 mt-1"><?= $sc['in_progress'] ?></div>
            <div class="text-[10px] text-amber-400/70 mt-1">ช่างกำลังตรวจเช็ค</div>
        </div>

        <!-- Resolved -->
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-400">ซ่อมเสร็จ</div>
            <div class="text-2xl font-extrabold text-emerald-300 mt-1"><?= $sc['resolved'] ?></div>
            <div class="text-[10px] text-emerald-400/70 mt-1">รอผู้ใช้ตรวจรับ</div>
        </div>

        <!-- Avg Hours -->
        <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 shadow-sm">
            <div class="text-[11px] font-bold uppercase tracking-wider text-indigo-400">เวลาเฉลี่ย (ชม.)</div>
            <div class="text-2xl font-extrabold text-indigo-300 mt-1"><?= $stats['avg_resolution_hours'] ?></div>
            <div class="text-[10px] text-indigo-400/70 mt-1">ต่อการแก้ปัญหา</div>
        </div>

        <!-- Avg Rating -->
        <div class="p-4 rounded-2xl bg-yellow-500/10 border border-yellow-500/20 shadow-sm col-span-2 sm:col-span-1">
            <div class="text-[11px] font-bold uppercase tracking-wider text-yellow-400">ความพึงพอใจ</div>
            <div class="text-2xl font-extrabold text-yellow-300 mt-1 flex items-center gap-1">
                <?= $stats['avg_rating'] ?: '5.0' ?> <span class="text-sm">★</span>
            </div>
            <div class="text-[10px] text-yellow-400/70 mt-1">จากผู้ใช้งาน</div>
        </div>
    </div>

    <!-- Urgent / Unassigned Tickets Widget -->
    <?php if (!empty($unassignedTickets)): ?>
        <div class="rounded-2xl border border-sky-500/30 bg-sky-500/5 p-5 shadow-lg shadow-sky-500/5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-sky-400"></span>
                    <h2 class="text-base font-bold text-white">ตั๋วเปิดใหม่ที่รอมอบหมายช่าง (<?= count($unassignedTickets) ?> รายการ)</h2>
                </div>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
                <?php foreach ($unassignedTickets as $ticket): 
                    $pEnum = TicketPriority::tryFrom($ticket['priority']);
                ?>
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 flex flex-col justify-between space-y-3">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-mono text-slate-400">#<?= $ticket['id'] ?></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border <?= $pEnum ? $pEnum->badgeClasses() : '' ?>">
                                    <?= $pEnum ? $pEnum->label() : $ticket['priority'] ?>
                                </span>
                            </div>
                            <h3 class="font-bold text-sm text-white line-clamp-1"><?= htmlspecialchars($ticket['title']) ?></h3>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-2"><?= htmlspecialchars($ticket['description']) ?></p>
                            <div class="text-[11px] text-slate-500 mt-2">ผู้แจ้ง: <?= htmlspecialchars($ticket['user_name']) ?> &bull; หมวดหมู่: <?= htmlspecialchars($ticket['category_name']) ?></div>
                        </div>
                        
                        <!-- Quick Assign Form -->
                        <form action="/admin/tickets/<?= $ticket['id'] ?>/assign" method="POST" class="pt-2 border-t border-slate-800/80 flex items-center gap-2">
                            <?= \App\Core\Csrf::field() ?>
                            <select name="technician_id" required class="flex-1 bg-slate-950 border border-slate-800 text-xs rounded-lg px-2.5 py-1.5 text-white focus:outline-none focus:border-blue-500">
                                <option value="">-- เลือกช่างเทคนิค --</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-sm transition-all whitespace-nowrap">
                                จ่ายงาน
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Two-Column Analytics Layout -->
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Top Technicians Leaderboard -->
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    อันดับผลงานช่างเทคนิค (Technician Leaderboard)
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-800">
                            <th class="pb-2.5 font-semibold">ช่างเทคนิค</th>
                            <th class="pb-2.5 font-semibold text-center">งานทั้งหมด</th>
                            <th class="pb-2.5 font-semibold text-center">สำเร็จแล้ว</th>
                            <th class="pb-2.5 font-semibold text-center">คะแนนเฉลี่ย</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        <?php if (empty($stats['top_technicians'])): ?>
                            <tr><td colspan="4" class="py-4 text-center text-slate-500">ยังไม่มีข้อมูลผลงานช่าง</td></tr>
                        <?php else: ?>
                            <?php foreach ($stats['top_technicians'] as $idx => $t): ?>
                                <tr>
                                    <td class="py-3 flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-full flex items-center justify-center font-bold text-[10px] <?= $idx === 0 ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-slate-400' ?>">
                                            <?= $idx + 1 ?>
                                        </span>
                                        <span class="font-semibold text-white"><?= htmlspecialchars($t['name']) ?></span>
                                    </td>
                                    <td class="py-3 text-center"><?= $t['total_jobs'] ?></td>
                                    <td class="py-3 text-center font-bold text-emerald-400"><?= $t['completed_jobs'] ?></td>
                                    <td class="py-3 text-center text-amber-400 font-bold"><?= $t['avg_score'] ? $t['avg_score'] . ' ★' : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6">
            <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                การกระจายงานตามหมวดหมู่ (Category Distribution)
            </h3>
            <div class="space-y-3">
                <?php 
                $maxCat = max(1, ...array_map(fn($c) => (int)$c['ticket_count'], $stats['category_breakdown'] ?: [['ticket_count' => 1]]));
                foreach ($stats['category_breakdown'] as $cat): 
                    $percent = round(($cat['ticket_count'] / $maxCat) * 100);
                ?>
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="text-slate-200"><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="text-slate-400"><?= $cat['ticket_count'] ?> ตั๋ว</span>
                        </div>
                        <div class="w-full h-2 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-500" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Status Audit Trail (Recent Activity) -->
    <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-6">
        <h3 class="text-base font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            ประวัติการเปลี่ยนสถานะล่าสุด (Audit Trail)
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 border-b border-slate-800">
                        <th class="pb-2.5 font-semibold">วันเวลา</th>
                        <th class="pb-2.5 font-semibold">รหัสตั๋ว</th>
                        <th class="pb-2.5 font-semibold">การเปลี่ยนสถานะ</th>
                        <th class="pb-2.5 font-semibold">ผู้ดำเนินการ</th>
                        <th class="pb-2.5 font-semibold">บันทึกช่วยจำ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <?php foreach ($stats['recent_logs'] as $log): ?>
                        <tr>
                            <td class="py-2.5 font-mono text-slate-400"><?= date('d/m/y H:i', strtotime($log['created_at'])) ?></td>
                            <td class="py-2.5 font-semibold text-white">
                                <a href="/tickets/<?= $log['ticket_id'] ?>" class="hover:text-blue-400">#<?= $log['ticket_id'] ?></a>
                            </td>
                            <td class="py-2.5">
                                <span class="font-mono text-slate-400"><?= $log['from_status'] ?></span>
                                <span class="text-slate-500 mx-1">&rarr;</span>
                                <span class="font-mono text-blue-400 font-bold"><?= $log['to_status'] ?></span>
                            </td>
                            <td class="py-2.5">
                                <?= htmlspecialchars($log['changed_by_name']) ?>
                                <span class="text-[10px] text-slate-500">(<?= $log['changed_by_role'] ?>)</span>
                            </td>
                            <td class="py-2.5 text-slate-400"><?= htmlspecialchars($log['note'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
