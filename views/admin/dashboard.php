<?php
$kpi = $stats['kpi'];
$sc = $stats['status_counts'];
$recentTickets = $stats['recent_tickets'] ?? [];
$workload = $stats['technician_workload'] ?? [];
$categories = $stats['category_breakdown'] ?? [];
$period = $currentPeriod ?? 'all';
$recentLogs = $stats['recent_logs'] ?? [];

$statusDots = [
    'open'        => ['label' => 'Open', 'dot' => 'bg-sky-500', 'badge' => 'text-sky-700 bg-sky-50 border-sky-200'],
    'assigned'    => ['label' => 'Assigned', 'dot' => 'bg-purple-500', 'badge' => 'text-purple-700 bg-purple-50 border-purple-200'],
    'in_progress' => ['label' => 'In Progress', 'dot' => 'bg-amber-500', 'badge' => 'text-amber-700 bg-amber-50 border-amber-200'],
    'resolved'    => ['label' => 'Resolved', 'dot' => 'bg-emerald-500', 'badge' => 'text-emerald-700 bg-emerald-50 border-emerald-200'],
    'closed'      => ['label' => 'Closed', 'dot' => 'bg-slate-400', 'badge' => 'text-slate-600 bg-slate-100 border-slate-200'],
];
?>

<div class="space-y-5 max-w-6xl mx-auto">
    <!-- Header: Operational Overview & Period Selector -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200">
        <div>
            <h1 class="text-lg sm:text-xl font-semibold text-slate-900 tracking-tight">Dashboard</h1>
            <div class="flex items-center gap-2.5 text-xs text-slate-500 mt-0.5">
                <span>Overview of helpdesk activity</span>
                <span class="text-slate-300">&bull;</span>
                <span class="inline-flex items-center gap-1.5 text-emerald-600 font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>All systems operational</span>
                </span>
            </div>
        </div>

        <!-- Time Period Selector -->
        <div class="flex items-center gap-2">
            <div class="relative inline-block">
                <select id="periodSelector" aria-label="Select time period" class="bg-white border border-slate-200 text-xs text-slate-700 rounded-lg px-2.5 py-1.5 pr-7 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 font-medium cursor-pointer shadow-2xs">
                    <option value="all" <?= $period === 'all' ? 'selected' : '' ?>>All time</option>
                    <option value="today" <?= $period === 'today' ? 'selected' : '' ?>>Today</option>
                    <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Last 7 days</option>
                    <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Last 30 days</option>
                    <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>This year</option>
                </select>
                <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-[9px] text-slate-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- 4 Clean Monochromatic KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- 1. Open Tickets -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors">
            <div class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Open tickets</div>
            <div id="kpi-open" class="text-2xl sm:text-3xl font-semibold text-slate-900 mt-1 tabular-nums">
                <?= number_format($kpi['open_tickets']) ?>
            </div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="text-sky-600 font-medium"><?= $sc['open'] ?> unassigned</span>
                <span class="text-slate-300">&bull;</span>
                <span><?= $sc['assigned'] ?> assigned</span>
            </div>
        </div>

        <!-- 2. In Progress -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors">
            <div class="text-[11px] font-medium uppercase tracking-wider text-slate-500">In progress</div>
            <div id="kpi-in-progress" class="text-2xl sm:text-3xl font-semibold text-slate-900 mt-1 tabular-nums">
                <?= number_format($kpi['in_progress']) ?>
            </div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="text-amber-600 font-medium">Under active work</span>
            </div>
        </div>

        <!-- 3. Resolved -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors">
            <div class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Resolved</div>
            <div id="kpi-resolved" class="text-2xl sm:text-3xl font-semibold text-slate-900 mt-1 tabular-nums">
                <?= number_format($kpi['resolved']) ?>
            </div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="text-emerald-600 font-medium"><?= $kpi['resolution_rate'] ?>% resolution rate</span>
            </div>
        </div>

        <!-- 4. Avg. Resolution -->
        <div class="bg-white border border-slate-200 rounded-lg p-3.5 hover:border-slate-300 transition-colors">
            <div class="text-[11px] font-medium uppercase tracking-wider text-slate-500">Avg. resolution</div>
            <div id="kpi-avg" class="text-2xl sm:text-3xl font-semibold text-slate-900 mt-1 tabular-nums">
                <?= $kpi['avg_resolution'] ?>
            </div>
            <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                <span class="text-slate-500">Mean time to repair</span>
            </div>
        </div>
    </div>

    <!-- Main Operational Workspace Grid (2 Columns: Main Feed vs Compact Status/Categories) -->
    <div class="grid lg:grid-cols-3 gap-5 items-start">
        <!-- Left 2 Cols: Recent Tickets & Technician Workload -->
        <div class="lg:col-span-2 space-y-5">
            <!-- Section 1: Recent Tickets Feed (Main Content) -->
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Recent tickets</h2>
                        <span class="text-[11px] text-slate-500">Latest active issues requiring operational attention</span>
                    </div>
                    <a href="/tickets" class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                        <span>View all</span>
                        <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    <?php if (empty($recentTickets)): ?>
                        <div class="p-6 text-center text-xs text-slate-400">No active tickets found</div>
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
                                        <span>Requester: <strong class="font-medium text-slate-700"><?= htmlspecialchars($ticket['user_name']) ?></strong></span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span><?= date('d M, H:i', strtotime($ticket['created_at'])) ?></span>
                                    </div>
                                </div>

                                <!-- Actions & Status -->
                                <div class="flex items-center gap-3 shrink-0">
                                    <?php if ($ticket['status'] === 'open'): ?>
                                        <!-- Inline Quick Assign -->
                                        <form action="/admin/tickets/<?= $ticket['id'] ?>/assign" method="POST" class="flex items-center gap-1.5">
                                            <?= \App\Core\Csrf::field() ?>
                                            <label for="tech_select_<?= $ticket['id'] ?>" class="sr-only">Assign technician</label>
                                            <select id="tech_select_<?= $ticket['id'] ?>" name="technician_id" required class="bg-white border border-slate-200 text-[11px] rounded-md px-2 py-1 text-slate-700 focus:outline-none focus:border-blue-600">
                                                <option value="">Assign tech...</option>
                                                <?php foreach ($technicians as $tech): ?>
                                                    <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="px-2 py-1 bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-medium rounded-md transition-colors">
                                                Assign
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <div class="text-right text-[11px]">
                                            <span class="text-slate-400">Tech:</span>
                                            <span class="text-slate-700 font-medium"><?= htmlspecialchars($ticket['tech_name'] ?? 'Assigned') ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Status Pill with Dot -->
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
            <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Technician workload</h2>
                    <span class="text-[11px] text-slate-500">Current assignment distribution and resolution performance</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 border-b border-slate-100 bg-slate-50/50">
                                <th scope="col" class="py-2 px-4 font-medium">Technician</th>
                                <th scope="col" class="py-2 px-4 font-medium w-40">Workload</th>
                                <th scope="col" class="py-2 px-4 font-medium text-center">Open</th>
                                <th scope="col" class="py-2 px-4 font-medium text-center">Resolved</th>
                                <th scope="col" class="py-2 px-4 font-medium text-right">Avg. time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-600">
                            <?php if (empty($workload)): ?>
                                <tr><td colspan="5" class="p-4 text-center text-slate-400">No technicians assigned yet</td></tr>
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

        <!-- Right 1 Col: Status Breakdown, Categories & Audit -->
        <div class="space-y-5">
            <!-- 1. Ticket Status Breakdown (Compact List) -->
            <div class="bg-white border border-slate-200 rounded-lg p-3.5">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Ticket status</h2>
                    <span class="text-[11px] text-slate-400"><?= number_format($kpi['total_tickets']) ?> total</span>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                            <span>Open</span>
                        </span>
                        <span id="stat-open" class="font-mono font-medium text-slate-800 tabular-nums"><?= $sc['open'] ?></span>
                    </div>
                    <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <span>Assigned</span>
                        </span>
                        <span id="stat-assigned" class="font-mono font-medium text-slate-800 tabular-nums"><?= $sc['assigned'] ?></span>
                    </div>
                    <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>In Progress</span>
                        </span>
                        <span id="stat-in_progress" class="font-mono font-medium text-slate-800 tabular-nums"><?= $sc['in_progress'] ?></span>
                    </div>
                    <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Resolved</span>
                        </span>
                        <span id="stat-resolved" class="font-mono font-medium text-slate-800 tabular-nums"><?= $sc['resolved'] ?></span>
                    </div>
                    <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            <span>Closed</span>
                        </span>
                        <span id="stat-closed" class="font-mono font-medium text-slate-800 tabular-nums"><?= $sc['closed'] ?></span>
                    </div>
                </div>
            </div>

            <!-- 2. Tickets by Category (Compact List without fake long bars) -->
            <div class="bg-white border border-slate-200 rounded-lg p-3.5">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Categories</h2>
                    <span class="text-[11px] text-slate-400"><?= count($categories) ?> categories</span>
                </div>
                <div class="space-y-1.5 text-xs text-slate-600">
                    <?php foreach ($categories as $cat): ?>
                        <div class="flex items-center justify-between py-1 px-1 rounded hover:bg-slate-50">
                            <span class="truncate text-slate-700"><?= htmlspecialchars($cat['name']) ?></span>
                            <span class="font-mono font-medium text-slate-800 tabular-nums ml-2"><?= $cat['ticket_count'] ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3. Recent Activity / Audit Log -->
            <div class="bg-white border border-slate-200 rounded-lg p-3.5">
                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100">
                    <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">Recent activity</h2>
                    <span class="text-[11px] text-slate-400">Audit trail</span>
                </div>
                <div class="space-y-2.5 text-xs">
                    <?php if (empty($recentLogs)): ?>
                        <div class="text-slate-400 text-center py-2">No activity recorded</div>
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
                                    by <?= htmlspecialchars($log['changed_by_name']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vanilla JS Period Selector & Interactive Updates -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const periodSelector = document.getElementById('periodSelector');
    if (!periodSelector) return;

    const kpiElements = {
        open: document.getElementById('kpi-open'),
        inProgress: document.getElementById('kpi-in-progress'),
        resolved: document.getElementById('kpi-resolved'),
        avg: document.getElementById('kpi-avg'),
    };

    function animateCount(el, start, end, duration = 300) {
        if (!el) return;
        const startTime = performance.now();
        function tick(currentTime) {
            const progress = Math.min((currentTime - startTime) / duration, 1);
            const current = Math.round(start + (end - start) * progress);
            el.textContent = current.toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        }
        requestAnimationFrame(tick);
    }

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

                animateCount(kpiElements.open, parseInt(kpiElements.open?.textContent || '0'), k.open_tickets);
                animateCount(kpiElements.inProgress, parseInt(kpiElements.inProgress?.textContent || '0'), k.in_progress);
                animateCount(kpiElements.resolved, parseInt(kpiElements.resolved?.textContent || '0'), k.resolved);
                if (kpiElements.avg) kpiElements.avg.textContent = k.avg_resolution;

                // Update status counts
                const statOpen = document.getElementById('stat-open');
                const statAssigned = document.getElementById('stat-assigned');
                const statInProgress = document.getElementById('stat-in_progress');
                const statResolved = document.getElementById('stat-resolved');
                const statClosed = document.getElementById('stat-closed');
                if (statOpen) statOpen.textContent = sc.open || 0;
                if (statAssigned) statAssigned.textContent = sc.assigned || 0;
                if (statInProgress) statInProgress.textContent = sc.in_progress || 0;
                if (statResolved) statResolved.textContent = sc.resolved || 0;
                if (statClosed) statClosed.textContent = sc.closed || 0;
            }
        } catch (err) {
            console.error('Failed to update period data:', err);
        }
    });
});
</script>
