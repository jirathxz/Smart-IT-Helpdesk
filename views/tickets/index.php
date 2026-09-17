<?php
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;

$startItem = $totalFiltered > 0 ? ($currentPage - 1) * $perPage + 1 : 0;
$endItem = min($currentPage * $perPage, $totalFiltered);
?>

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-ticket text-blue-600 text-lg"></i>
                <span>งานแจ้งซ่อม</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1"><?= htmlspecialchars($pageSubTitle) ?></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/tickets/create" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium shadow-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>แจ้งซ่อมใหม่</span>
            </a>
        </div>
    </div>

    <!-- 1. สรุปตัวเลข 4 ค่าหลัก (Clean White Enterprise Metrics) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="rounded-xl bg-white border border-slate-200/90 p-4 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                <span>งานทั้งหมด</span>
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-slate-900 font-mono"><?= number_format($totalCount) ?></div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200/90 p-4 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                <span>รอดำเนินการ</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-amber-600 font-mono"><?= number_format($pendingCount) ?></div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200/90 p-4 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                <span>กำลังซ่อม</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-blue-600 font-mono"><?= number_format($inProgressCount) ?></div>
        </div>

        <div class="rounded-xl bg-white border border-slate-200/90 p-4 shadow-2xs hover:border-slate-300 transition-colors">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">
                <span>รอตรวจรับ</span>
                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
            </div>
            <div class="text-2xl sm:text-3xl font-bold text-purple-600 font-mono"><?= number_format($resolvedCount) ?></div>
        </div>
    </div>

    <!-- 2. ตารางงานแจ้งซ่อม (Hero Component) -->
    <div class="rounded-xl bg-white border border-slate-200 shadow-2xs overflow-hidden">
        <!-- Search & Filter Controls Toolbar -->
        <form method="GET" action="/tickets" class="p-3.5 sm:p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <?php if (!empty($view)): ?>
                <input type="hidden" name="view" value="<?= htmlspecialchars($view) ?>">
            <?php endif; ?>

            <!-- Keyword Search -->
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" 
                       placeholder="ค้นหารหัสงาน, หัวข้อปัญหา, รายละเอียด หรือชื่อผู้แจ้ง..." 
                       class="w-full pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <!-- Status Filter -->
                <select name="status" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">-- ทุกสถานะ --</option>
                    <option value="open" <?= $statusFilter === 'open' ? 'selected' : '' ?>>รอดำเนินการ</option>
                    <option value="assigned" <?= $statusFilter === 'assigned' ? 'selected' : '' ?>>มอบหมายแล้ว</option>
                    <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>กำลังซ่อม</option>
                    <option value="resolved" <?= $statusFilter === 'resolved' ? 'selected' : '' ?>>รอตรวจรับ</option>
                    <option value="closed" <?= $statusFilter === 'closed' ? 'selected' : '' ?>>ปิดงานแล้ว</option>
                </select>

                <!-- Priority Filter -->
                <select name="priority" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <option value="">-- ทุกระดับความเร่งด่วน --</option>
                    <option value="urgent" <?= $priorityFilter === 'urgent' ? 'selected' : '' ?>>🔴 เร่งด่วน</option>
                    <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>🟠 สูง</option>
                    <option value="medium" <?= $priorityFilter === 'medium' ? 'selected' : '' ?>>🔵 ปกติ</option>
                    <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>⚪ ต่ำ</option>
                </select>

                <button type="submit" class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium transition-colors">
                    <i class="fa-solid fa-filter text-xs mr-1"></i> กรอง
                </button>

                <?php if (!empty($keyword) || !empty($statusFilter) || !empty($priorityFilter)): ?>
                    <a href="/tickets<?= !empty($view) ? '?view=' . urlencode($view) : '' ?>" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 text-slate-600 text-xs sm:text-sm font-medium transition-colors">
                        ล้างตัวกรอง
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <!-- Table View -->
        <?php if (empty($tickets)): ?>
            <!-- Empty State -->
            <div class="py-12 sm:py-16 text-center px-4">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-200 text-slate-400 mx-auto flex items-center justify-center text-2xl mb-3">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">ไม่พบรายการแจ้งซ่อม</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-sm mx-auto">ลองเปลี่ยนคำค้นหาหรือตัวกรอง หรือสร้างใบแจ้งซ่อมใหม่เข้าระบบ</p>
                <?php if (!empty($keyword) || !empty($statusFilter) || !empty($priorityFilter)): ?>
                    <a href="/tickets<?= !empty($view) ? '?view=' . urlencode($view) : '' ?>" class="inline-flex items-center gap-1.5 mt-4 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-medium text-slate-700">
                        <i class="fa-solid fa-rotate-left text-xs"></i> ล้างตัวกรองทั้งหมด
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" id="ticketsTable">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-3.5 w-10 text-center">
                                <input type="checkbox" id="selectAllCheckbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="py-3 px-3 w-24">รหัสงาน</th>
                            <th class="py-3 px-3">รายการ / อาการเสีย</th>
                            <th class="py-3 px-3">ผู้แจ้ง</th>
                            <th class="py-3 px-3">ผู้รับผิดชอบ</th>
                            <th class="py-3 px-3 w-24">ความสำคัญ</th>
                            <th class="py-3 px-3 w-28">สถานะ</th>
                            <th class="py-3 px-3 w-28">วันที่แจ้ง</th>
                            <th class="py-3 px-3 w-16 text-right"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        <?php foreach ($tickets as $t): 
                            $statusEnum = TicketStatus::tryFrom($t['status']) ?? TicketStatus::OPEN;
                            $priorityEnum = TicketPriority::tryFrom($t['priority']) ?? TicketPriority::MEDIUM;
                            $ticketCode = '#TK-' . str_pad((string)$t['id'], 4, '0', STR_PAD_LEFT);
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors cursor-pointer group" data-href="/tickets/<?= $t['id'] ?>">
                                <td class="py-3 px-3.5 text-center" onclick="event.stopPropagation();">
                                    <input type="checkbox" class="ticket-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500" value="<?= $t['id'] ?>" data-code="<?= $ticketCode ?>">
                                </td>
                                <td class="py-3 px-3 font-mono font-semibold text-slate-900 group-hover:text-blue-600">
                                    <?= $ticketCode ?>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-medium text-slate-900 line-clamp-1 group-hover:text-blue-600 transition-colors">
                                        <?= htmlspecialchars($t['title']) ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                        <span class="text-slate-500 font-medium"><?= htmlspecialchars($t['category_name']) ?></span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap text-slate-600">
                                    <?= htmlspecialchars($t['user_name']) ?>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <?php if (!empty($t['technician_name'])): ?>
                                        <span class="text-blue-700 font-medium"><?= htmlspecialchars($t['technician_name']) ?></span>
                                    <?php else: ?>
                                        <span class="text-amber-600 font-medium bg-amber-50 px-2 py-0.5 rounded border border-amber-200/60 text-[11px]">รอจ่ายงาน</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 font-medium text-[11px]">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: <?= $priorityEnum->dotColor() ?>;"></span>
                                        <span class="text-slate-700"><?= $priorityEnum->shortLabel() ?></span>
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <?php
                                    $badgeStyles = match ($t['status']) {
                                        'open'        => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'assigned'    => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'in_progress' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'resolved'    => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'closed'      => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        default       => 'bg-slate-50 text-slate-700 border-slate-200',
                                    };
                                    ?>
                                    <span class="inline-block px-2 py-0.5 rounded border text-[11px] font-medium <?= $badgeStyles ?>">
                                        <?= $statusEnum->shortLabel() ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap text-[11px] text-slate-500 font-mono">
                                    <?= date('d/m/y H:i', strtotime($t['created_at'])) ?>
                                </td>
                                <td class="py-3 px-3 text-right whitespace-nowrap" onclick="event.stopPropagation();">
                                    <a href="/tickets/<?= $t['id'] ?>" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-medium text-slate-600 hover:text-blue-700 hover:bg-blue-50 border border-slate-200 hover:border-blue-200 transition-colors">
                                        <span>ดู</span>
                                        <i class="fa-solid fa-chevron-right text-[9px]"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div class="p-3.5 sm:p-4 border-t border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
                <div>
                    แสดง <strong><?= $startItem ?></strong> - <strong><?= $endItem ?></strong> จากทั้งหมด <strong><?= number_format($totalFiltered) ?></strong> รายการ
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="flex items-center gap-1">
                        <?php
                        $buildPageUrl = function($p) use ($keyword, $statusFilter, $priorityFilter, $view) {
                            $params = ['page' => $p];
                            if ($keyword) $params['keyword'] = $keyword;
                            if ($statusFilter) $params['status'] = $statusFilter;
                            if ($priorityFilter) $params['priority'] = $priorityFilter;
                            if ($view) $params['view'] = $view;
                            return '/tickets?' . http_build_query($params);
                        };
                        ?>

                        <!-- Prev Button -->
                        <?php if ($currentPage > 1): ?>
                            <a href="<?= $buildPageUrl($currentPage - 1) ?>" class="px-2.5 py-1 rounded border border-slate-200 bg-white hover:bg-slate-100 text-slate-700">
                                ‹ ก่อนหน้า
                            </a>
                        <?php endif; ?>

                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <?php if ($p === $currentPage): ?>
                                <span class="px-2.5 py-1 rounded bg-blue-600 text-white font-semibold"><?= $p ?></span>
                            <?php elseif ($p <= 3 || $p >= $totalPages - 1 || abs($p - $currentPage) <= 1): ?>
                                <a href="<?= $buildPageUrl($p) ?>" class="px-2.5 py-1 rounded border border-slate-200 bg-white hover:bg-slate-100 text-slate-700"><?= $p ?></a>
                            <?php elseif ($p === 4 && $totalPages > 6): ?>
                                <span class="px-1 text-slate-400">...</span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <!-- Next Button -->
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?= $buildPageUrl($currentPage + 1) ?>" class="px-2.5 py-1 rounded border border-slate-200 bg-white hover:bg-slate-100 text-slate-700">
                                ถัดไป ›
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Action Floating Bar (Admin Multi-Select Action) -->
<div id="bulkActionBar" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-xl flex items-center gap-4 text-xs sm:text-sm transition-all duration-200 opacity-0 pointer-events-none translate-y-4">
    <div class="flex items-center gap-2">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        <span>เลือกไว้ <strong id="bulkSelectedCount" class="font-mono text-emerald-400 font-bold">0</strong> รายการ</span>
    </div>
    <div class="h-4 w-px bg-slate-700"></div>
    <button type="button" onclick="window.print()" class="hover:text-blue-400 transition-colors flex items-center gap-1.5 font-medium">
        <i class="fa-solid fa-print"></i> พิมพ์
    </button>
    <button type="button" id="exportCsvBtn" class="hover:text-emerald-400 transition-colors flex items-center gap-1.5 font-medium">
        <i class="fa-solid fa-file-csv"></i> ส่งออก CSV
    </button>
    <button type="button" id="clearSelectionBtn" class="text-slate-400 hover:text-white transition-colors ml-2">
        <i class="fa-solid fa-xmark"></i>
    </button>
</div>

<!-- Dynamic JavaScript for Table Interaction -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Clickable table rows
    document.querySelectorAll('tr[data-href]').forEach(row => {
        row.addEventListener('click', (e) => {
            if (e.target.closest('input, a, button')) return;
            window.location.href = row.getAttribute('data-href');
        });
    });

    // 2. Multi-select Checkboxes & Bulk Action Bar
    const selectAll = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.ticket-checkbox');
    const bulkBar = document.getElementById('bulkActionBar');
    const countSpan = document.getElementById('bulkSelectedCount');
    const clearBtn = document.getElementById('clearSelectionBtn');
    const exportBtn = document.getElementById('exportCsvBtn');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.ticket-checkbox:checked');
        const count = checked.length;
        if (countSpan) countSpan.textContent = count;

        if (count > 0) {
            bulkBar.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
            bulkBar.classList.add('opacity-100', 'pointer-events-auto', 'translate-y-0');
        } else {
            bulkBar.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
            bulkBar.classList.remove('opacity-100', 'pointer-events-auto', 'translate-y-0');
            if (selectAll) selectAll.checked = false;
        }

        // Highlight selected rows
        rowCheckboxes.forEach(cb => {
            const tr = cb.closest('tr');
            if (tr) {
                if (cb.checked) {
                    tr.classList.add('bg-blue-50/60');
                } else {
                    tr.classList.remove('bg-blue-50/60');
                }
            }
        });
    }

    if (selectAll) {
        selectAll.addEventListener('change', (e) => {
            rowCheckboxes.forEach(cb => cb.checked = e.target.checked);
            updateBulkBar();
        });
    }

    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            if (selectAll) {
                selectAll.checked = Array.from(rowCheckboxes).every(c => c.checked);
            }
            updateBulkBar();
        });
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            rowCheckboxes.forEach(cb => cb.checked = false);
            if (selectAll) selectAll.checked = false;
            updateBulkBar();
        });
    }

    // CSV Export of Selected
    if (exportBtn) {
        exportBtn.addEventListener('click', () => {
            const checked = document.querySelectorAll('.ticket-checkbox:checked');
            if (checked.length === 0) return;

            let csvContent = "\uFEFFรหัสงาน,รายการ,ผู้แจ้ง,สถานะ\n";
            checked.forEach(cb => {
                const tr = cb.closest('tr');
                if (tr) {
                    const code = cb.getAttribute('data-code') || '';
                    const title = tr.querySelector('td:nth-child(3) .font-medium')?.textContent.trim() || '';
                    const user = tr.querySelector('td:nth-child(4)')?.textContent.trim() || '';
                    const status = tr.querySelector('td:nth-child(7)')?.textContent.trim() || '';
                    csvContent += `"${code}","${title.replace(/"/g, '""')}","${user}","${status}"\n`;
                }
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `tickets_export_${new Date().toISOString().slice(0, 10)}.csv`;
            a.click();
            URL.revokeObjectURL(url);
        });
    }
});
</script>
