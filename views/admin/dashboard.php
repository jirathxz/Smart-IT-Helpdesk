<?php
$kpi = $stats['kpi'];
$sc = $stats['status_counts'];
$chartData = $stats['chart_data'];
$period = $currentPeriod ?? 'all';

$priorityBadges = [
    'urgent' => ['label' => 'เร่งด่วนที่สุด', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
    'high'   => ['label' => 'สูง', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
    'medium' => ['label' => 'ปานกลาง', 'class' => 'bg-sky-50 text-sky-700 border-sky-200'],
    'low'    => ['label' => 'ต่ำ', 'class' => 'bg-slate-100 text-slate-600 border-slate-200'],
];

$periods = [
    'all'   => 'ทั้งหมด (All)',
    'today' => 'วันนี้ (Today)',
    'week'  => '7 วันล่าสุด',
    'month' => 'เดือนนี้',
    'year'  => 'ปีนี้',
];
?>

<div class="space-y-6 sm:space-y-8">
    <!-- Page Header & Period Filter Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-chart-pie text-blue-600 text-2xl" aria-hidden="true"></i>
                <span>Executive Dashboard</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">ภาพรวมสถิติการดำเนินงาน ตัวชี้วัดประสิทธิภาพ (KPI) และภาระงานระบบ</p>
        </div>

        <!-- Period Filter Selector (Basecoat UI Pill Group with Vanilla JS) -->
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 hidden sm:inline-flex items-center gap-1.5 mr-1">
                <i class="fa-solid fa-calendar-days text-slate-400" aria-hidden="true"></i>
                <span>ช่วงเวลา:</span>
            </span>
            <div id="periodButtonGroup" class="inline-flex p-1 bg-white border border-slate-200 rounded-2xl shadow-xs" role="tablist" aria-label="ตัวกรองช่วงเวลาสถิติ">
                <?php foreach ($periods as $pKey => $pLabel): ?>
                    <button type="button"
                            data-period="<?= $pKey ?>"
                            role="tab"
                            aria-selected="<?= $period === $pKey ? 'true' : 'false' ?>"
                            class="period-btn px-3 py-1.5 text-xs font-semibold rounded-xl transition-all <?= $period === $pKey ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' ?>">
                        <?= $pLabel ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Live Status Indicator -->
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Online</span>
            </span>
        </div>
    </div>

    <!-- Core 6 KPI Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3.5 sm:gap-4">
        <!-- 1. Total Tickets -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-blue-300 hover:shadow-sm transition-all flex flex-col justify-between group">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-blue-600 transition-colors">Total Tickets</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm border border-blue-100 shadow-2xs">
                    <i class="fa-solid fa-ticket" aria-hidden="true"></i>
                </span>
            </div>
            <div>
                <div id="kpi-total-tickets" class="text-2xl sm:text-3xl font-extrabold text-slate-900 tabular-nums">
                    <?= number_format($kpi['total_tickets']) ?>
                </div>
                <div class="text-[11px] text-slate-500 mt-1 flex items-center justify-between">
                    <span>Ticket ทั้งหมด</span>
                    <span id="period-badge" class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 font-medium"><?= htmlspecialchars($stats['period_label']) ?></span>
                </div>
            </div>
        </div>

        <!-- 2. Open / Pending -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-sky-300 hover:shadow-sm transition-all flex flex-col justify-between group">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-sky-700 group-hover:text-sky-800 transition-colors">Open / Pending</span>
                <span class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-sm border border-sky-100 shadow-2xs">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                </span>
            </div>
            <div>
                <div id="kpi-open-pending" class="text-2xl sm:text-3xl font-extrabold text-sky-900 tabular-nums">
                    <?= number_format($kpi['open_pending']) ?>
                </div>
                <div class="text-[11px] text-sky-700/80 mt-1">
                    งานที่ยังไม่ได้แก้
                </div>
            </div>
        </div>

        <!-- 3. In Progress -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-amber-300 hover:shadow-sm transition-all flex flex-col justify-between group">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700 group-hover:text-amber-800 transition-colors">In Progress</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm border border-amber-100 shadow-2xs">
                    <i class="fa-solid fa-gears" aria-hidden="true"></i>
                </span>
            </div>
            <div>
                <div id="kpi-in-progress" class="text-2xl sm:text-3xl font-extrabold text-amber-900 tabular-nums">
                    <?= number_format($kpi['in_progress']) ?>
                </div>
                <div class="text-[11px] text-amber-700/80 mt-1">
                    งานที่กำลังดำเนินการ
                </div>
            </div>
        </div>

        <!-- 4. Urgent -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-rose-300 hover:shadow-sm transition-all flex flex-col justify-between group">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700 group-hover:text-rose-800 transition-colors">Urgent</span>
                <span class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm border border-rose-100 shadow-2xs">
                    <i class="fa-solid fa-bolt animate-pulse" aria-hidden="true"></i>
                </span>
            </div>
            <div>
                <div id="kpi-urgent" class="text-2xl sm:text-3xl font-extrabold text-rose-900 tabular-nums">
                    <?= number_format($kpi['urgent']) ?>
                </div>
                <div class="text-[11px] text-rose-700/80 mt-1">
                    Ticket ระดับ Urgent
                </div>
            </div>
        </div>

        <!-- 5. Resolved -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-emerald-300 hover:shadow-sm transition-all flex flex-col justify-between group">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700 group-hover:text-emerald-800 transition-colors">Resolved</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm border border-emerald-100 shadow-2xs">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                </span>
            </div>
            <div>
                <div id="kpi-resolved" class="text-2xl sm:text-3xl font-extrabold text-emerald-900 tabular-nums">
                    <?= number_format($kpi['resolved']) ?>
                </div>
                <div class="text-[11px] text-emerald-700/80 mt-1">
                    งานที่แก้ไขแล้ว
                </div>
            </div>
        </div>

        <!-- 6. CSAT / Rating -->
        <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs hover:border-yellow-300 hover:shadow-sm transition-all flex flex-col justify-between group">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-yellow-800 group-hover:text-yellow-900 transition-colors">CSAT / Rating</span>
                <span class="w-8 h-8 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-sm border border-yellow-100 shadow-2xs">
                    <i class="fa-solid fa-star text-amber-500" aria-hidden="true"></i>
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-1 text-yellow-950">
                    <span id="kpi-csat-rating" class="text-2xl sm:text-3xl font-extrabold tabular-nums">
                        <?= number_format($kpi['csat_rating'], 1) ?>
                    </span>
                    <span class="text-amber-500 text-sm" aria-hidden="true">★</span>
                    <span class="text-[11px] text-slate-400 font-medium">/ 5.0</span>
                </div>
                <div class="text-[11px] text-yellow-700/90 mt-1">
                    ความพึงพอใจเฉลี่ย
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Interactive Visualizations -->
    <div class="grid lg:grid-cols-3 gap-6 sm:gap-8">
        <!-- 1. Volume & Resolution Trend (2 Cols) -->
        <div class="lg:col-span-2 rounded-3xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-600" aria-hidden="true"></i>
                        <span>แนวโน้มปริมาณงานแจ้งซ่อมและงานที่เสร็จสิ้น (7 วันล่าสุด)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">เปรียบเทียบจำนวนตั๋วที่เปิดใหม่ vs งานที่ช่างซ่อมเสร็จสิ้น</p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-blue-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> ตั๋วแจ้งใหม่
                    </span>
                    <span class="flex items-center gap-1.5 text-emerald-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> ซ่อมเสร็จสิ้น
                    </span>
                </div>
            </div>
            <div class="relative w-full h-64 sm:h-72">
                <canvas id="trendChart" aria-label="กราฟแสดงแนวโน้มปริมาณงานแจ้งซ่อมและงานที่เสร็จสิ้น" role="img"></canvas>
            </div>
        </div>

        <!-- 2. Status Breakdown Donut Chart (1 Col) -->
        <div class="rounded-3xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div class="mb-3">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-indigo-600" aria-hidden="true"></i>
                    <span>สัดส่วนสถานะงานซ่อม</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">กระจายตามสถานะวงจรชีวิตตั๋วในระบบ</p>
            </div>
            <div class="relative w-full h-52 sm:h-56 flex items-center justify-center my-auto">
                <canvas id="statusChart" aria-label="แผนภูมิวงกลมแสดงสัดส่วนสถานะงานซ่อม" role="img"></canvas>
            </div>
            <div id="statusLegend" class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-slate-100 text-slate-600">
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-400"></span> เปิดใหม่: <strong id="leg-open" class="font-mono ml-auto"><?= $sc['open'] ?></strong></div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-purple-500"></span> จ่ายงาน: <strong id="leg-assigned" class="font-mono ml-auto"><?= $sc['assigned'] ?></strong></div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> กำลังซ่อม: <strong id="leg-in_progress" class="font-mono ml-auto"><?= $sc['in_progress'] ?></strong></div>
                <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> ซ่อมเสร็จ: <strong id="leg-resolved" class="font-mono ml-auto"><?= $sc['resolved'] ?></strong></div>
            </div>
        </div>
    </div>

    <!-- Category Distribution & Technician Leaderboard -->
    <div class="grid lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Category Distribution Chart -->
        <div class="rounded-3xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div class="mb-4">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-blue-600" aria-hidden="true"></i>
                    <span>การกระจายงานตามหมวดหมู่ (Category Distribution)</span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">ปริมาณงานซ่อมจำแนกตามประเภทอุปกรณ์และระบบงาน</p>
            </div>
            <div class="relative w-full h-56 sm:h-64">
                <canvas id="categoryChart" aria-label="แผนภูมิแท่งแสดงการกระจายงานตามหมวดหมู่" role="img"></canvas>
            </div>
        </div>

        <!-- Top Technicians Leaderboard -->
        <div class="rounded-3xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-trophy text-amber-500" aria-hidden="true"></i>
                        <span>อันดับผลงานช่างเทคนิค (Leaderboard)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">ช่างเทคนิคที่มีสถิติการปิดงานซ่อมและคะแนนประเมินสูงสุด</p>
                </div>
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
    </div>

    <!-- Urgent / Unassigned Tickets Widget -->
    <?php if (!empty($unassignedTickets)): ?>
        <div class="rounded-3xl border border-sky-200 bg-sky-50/30 p-5 sm:p-6 shadow-xs">
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
                    $pInfo = $priorityBadges[$ticket['priority']] ?? ['label' => $ticket['priority'], 'class' => 'bg-slate-100 text-slate-700 border-slate-200'];
                ?>
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between space-y-3 hover:border-slate-300 transition-all">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-xs font-mono text-slate-400 font-semibold">#<?= $ticket['id'] ?></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border <?= $pInfo['class'] ?>">
                                    <?= $pInfo['label'] ?>
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

    <!-- Status Audit Trail (Recent Activity) -->
    <div class="rounded-3xl bg-white border border-slate-200 p-5 sm:p-6 shadow-xs">
        <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-indigo-600" aria-hidden="true"></i>
            <span>ประวัติการเปลี่ยนสถานะล่าสุด (Audit Trail)</span>
        </h2>
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
                    <?php if (empty($stats['recent_logs'])): ?>
                        <tr><td colspan="5" class="py-4 text-center text-slate-400">ยังไม่มีประวัติการเปลี่ยนสถานะ</td></tr>
                    <?php else: ?>
                        <?php foreach ($stats['recent_logs'] as $log): ?>
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-2.5 font-mono text-slate-400 tabular-nums"><?= date('d/m/y H:i', strtotime($log['created_at'])) ?></td>
                                <td class="py-2.5 font-mono font-semibold text-blue-600">#<?= $log['ticket_id'] ?></td>
                                <td class="py-2.5">
                                    <span class="inline-flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600"><?= $log['from_status'] ?></span>
                                        <i class="fa-solid fa-arrow-right text-[9px] text-slate-400" aria-hidden="true"></i>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200"><?= $log['to_status'] ?></span>
                                    </span>
                                </td>
                                <td class="py-2.5 text-slate-700 font-medium"><?= htmlspecialchars($log['changed_by_name']) ?></td>
                                <td class="py-2.5 text-slate-500"><?= htmlspecialchars($log['note'] ?: '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Vanilla JS & Chart.js Integration Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Initial Chart Data from PHP
    const initialChartData = <?= json_encode($chartData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

    // Set global Chart.js typography to Kanit
    if (window.Chart) {
        Chart.defaults.font.family = "'Kanit', sans-serif";
        Chart.defaults.color = '#64748b';
    }

    // --- 1. Line Chart: Volume & Resolution Trend ---
    const trendCtx = document.getElementById('trendChart')?.getContext('2d');
    let trendChart = null;
    if (trendCtx) {
        trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: initialChartData.trend.labels,
                datasets: [
                    {
                        label: 'ตั๋วแจ้งใหม่',
                        data: initialChartData.trend.created,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#2563eb',
                    },
                    {
                        label: 'ซ่อมเสร็จสิ้น',
                        data: initialChartData.trend.resolved,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // --- 2. Donut Chart: Status Breakdown ---
    const statusCtx = document.getElementById('statusChart')?.getContext('2d');
    let statusChart = null;
    if (statusCtx) {
        statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: initialChartData.status.labels,
                datasets: [{
                    data: initialChartData.status.data,
                    backgroundColor: initialChartData.status.colors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 10,
                    }
                }
            }
        });
    }

    // --- 3. Horizontal Bar Chart: Category Breakdown ---
    const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
    let categoryChart = null;
    if (categoryCtx) {
        categoryChart = new Chart(categoryCtx, {
            type: 'bar',
            data: {
                labels: initialChartData.categories.labels,
                datasets: [{
                    label: 'จำนวนตั๋ว',
                    data: initialChartData.categories.data,
                    backgroundColor: '#3b82f6',
                    borderRadius: 8,
                    maxBarThickness: 24,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 10,
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

    // --- 4. Vanilla JS Period Filter & Dynamic Counter Animation ---
    const periodButtons = document.querySelectorAll('.period-btn');
    const kpiElements = {
        totalTickets: document.getElementById('kpi-total-tickets'),
        openPending: document.getElementById('kpi-open-pending'),
        inProgress: document.getElementById('kpi-in-progress'),
        urgent: document.getElementById('kpi-urgent'),
        resolved: document.getElementById('kpi-resolved'),
        csatRating: document.getElementById('kpi-csat-rating'),
        periodBadge: document.getElementById('period-badge'),
    };

    function animateValue(obj, start, end, duration = 400, isFloat = false) {
        if (!obj) return;
        const startTime = performance.now();
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const current = start + (end - start) * progress;
            obj.textContent = isFloat ? current.toFixed(1) : Math.round(current).toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }
        requestAnimationFrame(update);
    }

    periodButtons.forEach(btn => {
        btn.addEventListener('click', async () => {
            const period = btn.getAttribute('data-period');

            // Update active styling
            periodButtons.forEach(b => {
                b.setAttribute('aria-selected', 'false');
                b.className = 'period-btn px-3 py-1.5 text-xs font-semibold rounded-xl transition-all text-slate-600 hover:text-slate-900 hover:bg-slate-50';
            });
            btn.setAttribute('aria-selected', 'true');
            btn.className = 'period-btn px-3 py-1.5 text-xs font-semibold rounded-xl transition-all bg-blue-600 text-white shadow-xs shadow-blue-500/20';

            // Push state to browser URL without full reload
            const url = new URL(window.location);
            url.searchParams.set('period', period);
            window.history.pushState({}, '', url);

            // Fetch AJAX data
            try {
                const response = await fetch(`/admin/dashboard?period=${period}&ajax=1`);
                const result = await response.json();
                if (result.success && result.stats) {
                    const k = result.stats.kpi;
                    const sc = result.stats.status_counts;

                    // Animate numbers
                    animateValue(kpiElements.totalTickets, parseFloat(kpiElements.totalTickets.textContent.replace(/,/g, '')), k.total_tickets);
                    animateValue(kpiElements.openPending, parseFloat(kpiElements.openPending.textContent.replace(/,/g, '')), k.open_pending);
                    animateValue(kpiElements.inProgress, parseFloat(kpiElements.inProgress.textContent.replace(/,/g, '')), k.in_progress);
                    animateValue(kpiElements.urgent, parseFloat(kpiElements.urgent.textContent.replace(/,/g, '')), k.urgent);
                    animateValue(kpiElements.resolved, parseFloat(kpiElements.resolved.textContent.replace(/,/g, '')), k.resolved);
                    animateValue(kpiElements.csatRating, parseFloat(kpiElements.csatRating.textContent), k.csat_rating, 400, true);

                    if (kpiElements.periodBadge) {
                        kpiElements.periodBadge.textContent = result.period_label;
                    }

                    // Update legend numbers
                    const legOpen = document.getElementById('leg-open');
                    const legAssigned = document.getElementById('leg-assigned');
                    const legInProgress = document.getElementById('leg-in_progress');
                    const legResolved = document.getElementById('leg-resolved');
                    if (legOpen) legOpen.textContent = sc.open || 0;
                    if (legAssigned) legAssigned.textContent = sc.assigned || 0;
                    if (legInProgress) legInProgress.textContent = sc.in_progress || 0;
                    if (legResolved) legResolved.textContent = sc.resolved || 0;

                    // Update Chart.js instances
                    if (statusChart && result.stats.chart_data?.status) {
                        statusChart.data.datasets[0].data = result.stats.chart_data.status.data;
                        statusChart.update();
                    }
                    if (categoryChart && result.stats.chart_data?.categories) {
                        categoryChart.data.labels = result.stats.chart_data.categories.labels;
                        categoryChart.data.datasets[0].data = result.stats.chart_data.categories.data;
                        categoryChart.update();
                    }
                }
            } catch (err) {
                console.error('Failed to load period statistics:', err);
            }
        });
    });
});
</script>
