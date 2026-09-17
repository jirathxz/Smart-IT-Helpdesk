<?php
$kpi = $stats['kpi'];
$sc = $stats['status_counts'];
$recentTickets = $stats['recent_tickets'] ?? [];
$workload = $stats['technician_workload'] ?? [];
$categories = $stats['category_breakdown'] ?? [];
$chartData = $stats['chart_data'] ?? [];
$recentLogs = $stats['recent_logs'] ?? [];
$period = $currentPeriod ?? 'all';

$statusDots = [
    'open'        => ['label' => 'Open', 'dot' => 'bg-sky-500', 'badge' => 'text-sky-700 bg-sky-50 border-sky-200'],
    'assigned'    => ['label' => 'Assigned', 'dot' => 'bg-purple-500', 'badge' => 'text-purple-700 bg-purple-50 border-purple-200'],
    'in_progress' => ['label' => 'In Progress', 'dot' => 'bg-amber-500', 'badge' => 'text-amber-700 bg-amber-50 border-amber-200'],
    'resolved'    => ['label' => 'Resolved', 'dot' => 'bg-emerald-500', 'badge' => 'text-emerald-700 bg-emerald-50 border-emerald-200'],
    'closed'      => ['label' => 'Closed', 'dot' => 'bg-slate-400', 'badge' => 'text-slate-600 bg-slate-100 border-slate-200'],
];
?>

<div class="space-y-5 max-w-7xl mx-auto">
    <!-- Header: Operational Overview & Period Selector -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-lg sm:text-xl font-semibold text-slate-900 tracking-tight">ภาพรวมระบบ (Dashboard)</h1>
            <div class="flex items-center gap-2.5 text-xs text-slate-500 mt-0.5">
                <span>สถานะงานแจ้งซ่อมและการดำเนินงาน</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>All systems operational</span>
                </span>
            </div>
        </div>

        <!-- Period Selector Toolbar -->
        <div class="flex items-center gap-2">
            <label for="periodSelector" class="text-xs text-slate-500 font-medium">ช่วงเวลา:</label>
            <div class="relative inline-block">
                <select id="periodSelector" aria-label="เลือกช่วงเวลาสถิติ" class="bg-white border border-slate-200 text-xs text-slate-700 rounded-lg px-2.5 py-1.5 pr-7 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 font-medium cursor-pointer shadow-2xs">
                    <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>ทั้งหมด (All time)</option>
                    <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>วันนี้ (Today)</option>
                    <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>7 วันล่าสุด (Last 7 days)</option>
                    <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>เดือนนี้ (Last 30 days)</option>
                    <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>ปีนี้ (This year)</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none" aria-hidden="true"></i>
            </div>
        </div>
    </div>

    <!-- 6 Core KPI Cards (Basecoat UI Clean Monochromatic White Surface) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <!-- 1. Total Tickets -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-medium uppercase tracking-wider">Total Tickets</span>
                <i class="fa-solid fa-ticket text-slate-400 text-xs" aria-hidden="true"></i>
            </div>
            <div class="mt-1">
                <div id="kpi-total" class="text-2xl sm:text-3xl font-semibold text-slate-900 tabular-nums">
                    <?= number_format($kpi['total_tickets']) ?>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="Ticket ทั้งหมดในช่วงเวลาที่เลือก">
                    Ticket ทั้งหมดในช่วงเวลาที่เลือก
                </div>
            </div>
        </div>

        <!-- 2. Open / Pending -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-medium uppercase tracking-wider text-sky-700">Open / Pending</span>
                <i class="fa-solid fa-circle-exclamation text-sky-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="mt-1">
                <div id="kpi-open" class="text-2xl sm:text-3xl font-semibold text-slate-900 tabular-nums">
                    <?= number_format($kpi['open_pending']) ?>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="งานที่ยังไม่ได้แก้">
                    งานที่ยังไม่ได้แก้
                </div>
            </div>
        </div>

        <!-- 3. In Progress -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-medium uppercase tracking-wider text-amber-700">In Progress</span>
                <i class="fa-solid fa-screwdriver-wrench text-amber-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="mt-1">
                <div id="kpi-in-progress" class="text-2xl sm:text-3xl font-semibold text-slate-900 tabular-nums">
                    <?= number_format($kpi['in_progress']) ?>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="งานที่ช่างกำลังดำเนินการ">
                    งานที่ช่างกำลังดำเนินการ
                </div>
            </div>
        </div>

        <!-- 4. Urgent -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-medium uppercase tracking-wider text-rose-700">Urgent</span>
                <i class="fa-solid fa-bolt text-rose-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="mt-1">
                <div id="kpi-urgent" class="text-2xl sm:text-3xl font-semibold text-slate-900 tabular-nums">
                    <?= number_format($kpi['urgent']) ?>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="Ticket ระดับ Urgent">
                    Ticket ระดับ Urgent
                </div>
            </div>
        </div>

        <!-- 5. Resolved -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-medium uppercase tracking-wider text-emerald-700">Resolved</span>
                <i class="fa-solid fa-circle-check text-emerald-600 text-xs" aria-hidden="true"></i>
            </div>
            <div class="mt-1">
                <div id="kpi-resolved" class="text-2xl sm:text-3xl font-semibold text-slate-900 tabular-nums">
                    <?= number_format($kpi['resolved']) ?>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="งานที่แก้ไขแล้ว">
                    งานที่แก้ไขแล้ว
                </div>
            </div>
        </div>

        <!-- 6. CSAT / Rating -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors flex flex-col justify-between shadow-2xs">
            <div class="flex items-center justify-between text-slate-500">
                <span class="text-[11px] font-medium uppercase tracking-wider text-yellow-800">CSAT / Rating</span>
                <i class="fa-solid fa-star text-amber-500 text-xs" aria-hidden="true"></i>
            </div>
            <div class="mt-1">
                <div class="flex items-baseline gap-1 text-slate-900">
                    <span id="kpi-csat" class="text-2xl sm:text-3xl font-semibold tabular-nums">
                        <?= number_format($kpi['csat_rating'], 1) ?>
                    </span>
                    <span class="text-amber-500 text-xs" aria-hidden="true">★</span>
                </div>
                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="คะแนนความพึงพอใจเฉลี่ย">
                    คะแนนความพึงพอใจเฉลี่ย
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Visualizations (Volume Trend & Status Breakdown) -->
    <div class="grid lg:grid-cols-3 gap-5 items-start">
        <!-- 1. Volume Trend Line Chart (2 Cols) -->
        <div class="lg:col-span-2 bg-white border border-slate-200 rounded-lg p-4 shadow-2xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3">
                <div>
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Ticket volume & resolution trend</h2>
                    <span class="text-[11px] text-slate-500">เปรียบเทียบตั๋วที่เปิดใหม่ vs งานที่ช่างซ่อมเสร็จสิ้นรายวัน (7 วันล่าสุด)</span>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-slate-600 font-medium">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-1.5 bg-blue-600 rounded-xs"></span> ตั๋วเปิดใหม่
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2.5 h-1.5 bg-emerald-500 rounded-xs"></span> ซ่อมเสร็จสิ้น
                    </span>
                </div>
            </div>
            <div class="relative w-full h-52 sm:h-60">
                <canvas id="trendChart" aria-label="กราฟแสดงแนวโน้มปริมาณงานแจ้งซ่อมและงานที่เสร็จสิ้น" role="img"></canvas>
            </div>
        </div>

        <!-- 2. Status Breakdown Doughnut Chart (1 Col) -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-2xs flex flex-col justify-between">
            <div class="mb-2">
                <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Ticket status breakdown</h2>
                <span class="text-[11px] text-slate-500">สัดส่วนตามสถานะวงจรชีวิตตั๋ว</span>
            </div>
            <div class="relative w-full h-44 sm:h-48 flex items-center justify-center my-auto">
                <canvas id="statusChart" aria-label="แผนภูมิวงกลมแสดงสัดส่วนสถานะงานซ่อม" role="img"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-1.5 text-[11px] pt-2.5 border-t border-slate-100 text-slate-600">
                <div class="flex items-center justify-between"><span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>Open</span> <strong id="leg-open" class="font-mono"><?= $sc['open'] ?></strong></div>
                <div class="flex items-center justify-between"><span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>Assigned</span> <strong id="leg-assigned" class="font-mono"><?= $sc['assigned'] ?></strong></div>
                <div class="flex items-center justify-between"><span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>In Progress</span> <strong id="leg-in_progress" class="font-mono"><?= $sc['in_progress'] ?></strong></div>
                <div class="flex items-center justify-between"><span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Resolved</span> <strong id="leg-resolved" class="font-mono"><?= $sc['resolved'] ?></strong></div>
            </div>
        </div>
    </div>

    <!-- Main Operational Content: Recent Tickets & Technician Workload -->
    <div class="grid lg:grid-cols-3 gap-5 items-start">
        <!-- Left 2 Cols: Recent Tickets & Workload -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Section 1: Recent Tickets Feed -->
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-2xs">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Recent tickets</h2>
                        <span class="text-[11px] text-slate-500">ตั๋วงานล่าสุดที่ต้องได้รับการตรวจสอบหรือจ่ายงาน</span>
                    </div>
                    <a href="/tickets" class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        <span>View all</span>
                        <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    <?php if (empty($recentTickets)): ?>
                        <div class="p-6 text-center text-xs text-slate-400">ยังไม่มีรายการตั๋วงานในระบบ</div>
                    <?php else: ?>
                        <?php foreach ($recentTickets as $ticket): 
                            $st = $statusDots[$ticket['status']] ?? ['label' => $ticket['status'], 'dot' => 'bg-slate-400', 'badge' => 'text-slate-600 bg-slate-100'];
                        ?>
                            <div class="p-3.5 hover:bg-slate-50/60 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-xs text-slate-400 font-medium">#<?= $ticket['id'] ?></span>
                                        <a href="/tickets" class="text-xs font-medium text-slate-900 hover:text-blue-600 truncate transition-colors">
                                            <?= htmlspecialchars($ticket['title']) ?>
                                        </a>
                                        <?php if ($ticket['priority'] === 'urgent'): ?>
                                            <span class="text-[9px] font-semibold px-1.5 py-0.2 rounded bg-rose-50 text-rose-700 border border-rose-200">Urgent</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-2">
                                        <span><?= htmlspecialchars($ticket['category_name']) ?></span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>ผู้แจ้ง: <strong class="font-medium text-slate-700"><?= htmlspecialchars($ticket['user_name']) ?></strong></span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span><?= date('d M, H:i', strtotime($ticket['created_at'])) ?></span>
                                    </div>
                                </div>

                                <!-- Inline Actions & Status Badge -->
                                <div class="flex items-center gap-2.5 shrink-0">
                                    <?php if ($ticket['status'] === 'open'): ?>
                                        <form action="/admin/tickets/<?= $ticket['id'] ?>/assign" method="POST" class="flex items-center gap-1.5">
                                            <?= \App\Core\Csrf::field() ?>
                                            <label for="tech_select_<?= $ticket['id'] ?>" class="sr-only">เลือกช่าง</label>
                                            <select id="tech_select_<?= $ticket['id'] ?>" name="technician_id" required class="bg-white border border-slate-200 text-[11px] rounded-md px-2 py-1 text-slate-700 focus:outline-none focus:border-blue-600">
                                                <option value="">เลือกช่าง...</option>
                                                <?php foreach ($technicians as $tech): ?>
                                                    <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-medium rounded-md transition-colors">
                                                จ่ายงาน
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="text-right text-[11px]">
                                            <span class="text-slate-400">ช่าง:</span>
                                            <span class="text-slate-700 font-medium"><?= htmlspecialchars($ticket['tech_name'] ?? 'มอบหมายแล้ว') ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium border <?= $st['badge'] ?>">
                                        <span class="w-1.5 h-1.5 rounded-full <?= $st['dot'] ?>"></span>
                                        <span><?= $st['label'] ?></span>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section 2: Technician Workload (Operational Capacity) -->
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden shadow-2xs">
                <div class="px-4 py-3 border-b border-slate-200">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Technician workload</h2>
                    <span class="text-[11px] text-slate-500">ภาระงานปัจจุบันและสถิติการแก้ปัญหาของช่างเทคนิค</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100 bg-slate-50/50">
                                <th scope="col" class="py-2 px-4 font-medium">ช่างเทคนิค</th>
                                <th scope="col" class="py-2 px-4 font-medium w-40">ภาระงาน (Workload)</th>
                                <th scope="col" class="py-2 px-4 font-medium text-center">Open</th>
                                <th scope="col" class="py-2 px-4 font-medium text-center">Resolved</th>
                                <th scope="col" class="py-2 px-4 font-medium text-right">Avg. time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <?php if (empty($workload)): ?>
                                <tr><td colspan="5" class="p-4 text-center text-slate-400">ยังไม่มีข้อมูลช่างเทคนิค</td></tr>
                            <?php else: ?>
                                <?php foreach ($workload as $tech): 
                                    $openCount = (int) $tech['open_jobs'];
                                    $resolvedCount = (int) $tech['resolved_jobs'];
                                    $totalJobs = max(1, $openCount + $resolvedCount);
                                    $workloadPct = min(100, round(($openCount / $totalJobs) * 100));
                                    $avgTime = !empty($tech['avg_minutes']) ? $tech['avg_minutes'] . 'm' : '—';
                                ?>
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-2.5 px-4 font-medium text-slate-900">
                                            <?= htmlspecialchars($tech['name']) ?>
                                        </td>
                                        <td class="py-2.5 px-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-blue-600 rounded-full" style="width: <?= $workloadPct ?>%"></div>
                                                </div>
                                                <span class="text-[10px] text-slate-400 font-mono"><?= $openCount ?> open</span>
                                            </div>
                                        </td>
                                        <td class="py-2.5 px-4 text-center font-mono font-medium text-slate-800 tabular-nums">
                                            <?= $openCount ?>
                                        </td>
                                        <td class="py-2.5 px-4 text-center font-mono font-medium text-emerald-600 tabular-nums">
                                            <?= $resolvedCount ?>
                                        </td>
                                        <td class="py-2.5 px-4 text-right font-mono text-slate-600 tabular-nums">
                                            <?= $avgTime ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Categories & Recent Activity -->
        <div class="space-y-5">
            <!-- Tickets by Category (Compact List) -->
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Tickets by category</h2>
                    <span class="text-[11px] text-slate-400"><?= count($categories) ?> หมวดหมู่</span>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <?php foreach ($categories as $cat): ?>
                        <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50 transition-colors">
                            <span class="truncate text-slate-700"><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="font-mono font-medium text-slate-800 tabular-nums ml-2"><?= $cat['ticket_count'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Status Audit Trail -->
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Recent activity</h2>
                    <span class="text-[11px] text-slate-400">Audit trail</span>
                </div>
                <div class="space-y-2.5 text-xs">
                    <?php if (empty($recentLogs)): ?>
                        <div class="text-slate-400 text-center py-2">ยังไม่มีประวัติการเปลี่ยนสถานะ</div>
                    <?php else: ?>
                        <?php foreach (array_slice($recentLogs, 0, 4) as $log): ?>
                            <div class="text-[11px] space-y-0.5">
                                <div class="flex items-center justify-between text-slate-500">
                                    <span class="font-mono text-blue-600 font-medium">#<?= $log['ticket_id'] ?></span>
                                    <span class="font-mono text-[10px] text-slate-400"><?= date('H:i, d M', strtotime($log['created_at'])) ?></span>
                                </div>
                                <div class="text-slate-800 line-clamp-1">
                                    <?= htmlspecialchars($log['note'] ?: ($log['from_status'] . ' → ' . $log['to_status'])) ?>
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    โดย <?= htmlspecialchars($log['changed_by_name']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
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
                        label: 'ตั๋วเปิดใหม่',
                        data: initialChartData.trend.created,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.05)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3.5,
                        pointBackgroundColor: '#2563eb',
                    },
                    {
                        label: 'ซ่อมเสร็จสิ้น',
                        data: initialChartData.trend.resolved,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3.5,
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
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 8,
                        cornerRadius: 6,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0, font: { size: 10 } },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { font: { size: 10 } },
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
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4,
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
                        padding: 8,
                        cornerRadius: 6,
                    }
                }
            }
        });
    }

    // --- 3. Vanilla JS Period Selector & Counter Animations ---
    const periodSelector = document.getElementById('periodSelector');
    const kpiElements = {
        total: document.getElementById('kpi-total'),
        open: document.getElementById('kpi-open'),
        inProgress: document.getElementById('kpi-in-progress'),
        urgent: document.getElementById('kpi-urgent'),
        resolved: document.getElementById('kpi-resolved'),
        csat: document.getElementById('kpi-csat'),
    };

    function animateCount(el, start, end, duration = 300, isFloat = false) {
        if (!el) return;
        const startTime = performance.now();
        function tick(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const current = start + (end - start) * progress;
            el.textContent = isFloat ? current.toFixed(1) : Math.round(current).toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        }
        requestAnimationFrame(tick);
    }

    if (periodSelector) {
        periodSelector.addEventListener('change', async (e) => {
            const period = e.target.value;

            // Push URL state
            const url = new URL(window.location);
            url.searchParams.set('period', period);
            window.history.pushState({}, '', url);

            try {
                const res = await fetch(`/admin/dashboard?period=${period}&ajax=1`);
                const data = await res.json();
                if (data.success && data.stats) {
                    const k = data.stats.kpi;
                    const sc = data.stats.status_counts;

                    // Animate the 6 core KPI numbers
                    animateCount(kpiElements.total, parseInt(kpiElements.total?.textContent || '0'), k.total_tickets);
                    animateCount(kpiElements.open, parseInt(kpiElements.open?.textContent || '0'), k.open_pending);
                    animateCount(kpiElements.inProgress, parseInt(kpiElements.inProgress?.textContent || '0'), k.in_progress);
                    animateCount(kpiElements.urgent, parseInt(kpiElements.urgent?.textContent || '0'), k.urgent);
                    animateCount(kpiElements.resolved, parseInt(kpiElements.resolved?.textContent || '0'), k.resolved);
                    animateCount(kpiElements.csat, parseFloat(kpiElements.csat?.textContent || '0'), k.csat_rating, 300, true);

                    // Update legend numbers
                    const legOpen = document.getElementById('leg-open');
                    const legAssigned = document.getElementById('leg-assigned');
                    const legInProgress = document.getElementById('leg-in_progress');
                    const legResolved = document.getElementById('leg-resolved');
                    if (legOpen) legOpen.textContent = sc.open || 0;
                    if (legAssigned) legAssigned.textContent = sc.assigned || 0;
                    if (legInProgress) legInProgress.textContent = sc.in_progress || 0;
                    if (legResolved) legResolved.textContent = sc.resolved || 0;

                    // Update Chart.js datasets
                    if (statusChart && data.stats.chart_data?.status) {
                        statusChart.data.datasets[0].data = data.stats.chart_data.status.data;
                        statusChart.update();
                    }
                }
            } catch (err) {
                console.error('Failed to update period data:', err);
            }
        });
    }
});
</script>
