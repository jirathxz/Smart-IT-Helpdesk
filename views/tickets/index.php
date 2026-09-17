<?php
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

$currentStatus = $filters['status'] ?? '';
?>

<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-ticket text-blue-600 text-xl" aria-hidden="true"></i>
                <span><?= htmlspecialchars($title) ?></span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">ทั้งหมด <strong class="text-slate-800 font-semibold tabular-nums"><?= count($tickets) ?></strong> รายการ</p>
        </div>
        <a href="/tickets/create" class="inline-flex items-center gap-2 min-h-[40px] px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs shadow-blue-500/25 transition-all self-start sm:self-auto focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
            <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
            <span>เปิดตั๋วแจ้งซ่อมใหม่</span>
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-3.5">
        <!-- Status Pills -->
        <nav class="flex flex-wrap gap-1.5 border-b border-slate-100 pb-3 text-xs" aria-label="กรองตามสถานะงาน">
            <a href="/tickets<?= !empty($_GET['view']) ? '?view=' . htmlspecialchars($_GET['view']) : '' ?>" 
               class="px-3 py-1.5 rounded-xl font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= empty($currentStatus) ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100' ?>">
                ทั้งหมด
            </a>
            <?php foreach (TicketStatus::cases() as $st): ?>
                <a href="?status=<?= $st->value ?><?= !empty($_GET['view']) ? '&view=' . htmlspecialchars($_GET['view']) : '' ?>" 
                   class="px-3 py-1.5 rounded-xl font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 <?= ($currentStatus === $st->value) ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/20 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100' ?>">
                    <?= $st->label() ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Filter Form -->
        <form method="GET" action="/tickets" class="grid sm:grid-cols-4 gap-2.5">
            <?php if (!empty($_GET['view'])): ?>
                <input type="hidden" name="view" value="<?= htmlspecialchars($_GET['view']) ?>">
            <?php endif; ?>
            <?php if (!empty($currentStatus)): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
            <?php endif; ?>

            <div class="sm:col-span-2 relative">
                <label for="ticket_search" class="sr-only">ค้นหาตั๋วงานซ่อม</label>
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none" aria-hidden="true"></i>
                <input type="text" id="ticket_search" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="ค้นหาตามหัวข้อ, รายละเอียด หรือชื่อผู้แจ้ง…" 
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20">
            </div>

            <div>
                <label for="ticket_category_filter" class="sr-only">หมวดหมู่งานซ่อม</label>
                <select id="ticket_category_filter" name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600">
                    <option value="">-- ทุกหมวดหมู่ --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (!empty($filters['category_id']) && (int)$filters['category_id'] === $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-2">
                <label for="ticket_priority_filter" class="sr-only">ระดับความเร่งด่วน</label>
                <select id="ticket_priority_filter" name="priority" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600">
                    <option value="">-- ทุกความเร่งด่วน --</option>
                    <?php foreach (TicketPriority::cases() as $pr): ?>
                        <option value="<?= $pr->value ?>" <?= (!empty($filters['priority']) && $filters['priority'] === $pr->value) ? 'selected' : '' ?>>
                            <?= $pr->label() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold shadow-xs transition-all flex items-center gap-1.5 flex-shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-700">
                    <i class="fa-solid fa-filter text-xs" aria-hidden="true"></i>
                    <span>กรอง</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Tickets Grid / List -->
    <?php if (empty($tickets)): ?>
        <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-slate-200">
            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto text-slate-400 mb-3">
                <i class="fa-solid fa-box-open text-xl" aria-hidden="true"></i>
            </div>
            <h2 class="text-base font-bold text-slate-800">ไม่พบรายการตั๋วงานซ่อม</h2>
            <p class="text-xs text-slate-500 mt-1">ไม่มีรายการตั๋วงานที่ตรงกับเงื่อนไขการค้นหาในขณะนี้</p>
        </div>
    <?php else: ?>
        <div class="grid gap-3 sm:gap-3.5">
            <?php foreach ($tickets as $ticket): 
                $sEnum = TicketStatus::tryFrom($ticket['status']);
                $pEnum = TicketPriority::tryFrom($ticket['priority']);
            ?>
                <a href="/tickets/<?= $ticket['id'] ?>" class="block p-4 sm:p-5 rounded-2xl bg-white border border-slate-200 hover:border-blue-300 hover:shadow-sm transition-all shadow-xs group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs text-slate-400 font-semibold tabular-nums">#<?= $ticket['id'] ?></span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold border <?= $sEnum ? $sEnum->badgeClasses() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                    <?= $sEnum ? $sEnum->label() : $ticket['status'] ?>
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border <?= $pEnum ? $pEnum->badgeClasses() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                    <?= $pEnum ? $pEnum->label() : $ticket['priority'] ?>
                                </span>
                                <span class="text-xs text-slate-400 font-medium">&bull; <?= htmlspecialchars($ticket['category_name']) ?></span>
                            </div>
                            <h2 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors truncate">
                                <?= htmlspecialchars($ticket['title']) ?>
                            </h2>
                            <p class="text-xs text-slate-500 line-clamp-1">
                                <?= htmlspecialchars($ticket['description']) ?>
                            </p>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 border-slate-100 pt-2.5 sm:pt-0 text-xs text-slate-500 gap-1 flex-shrink-0">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-user text-[10px] text-slate-400" aria-hidden="true"></i>
                                <span>ผู้แจ้ง: <strong class="text-slate-700 font-medium"><?= htmlspecialchars($ticket['user_name']) ?></strong></span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid fa-wrench text-[10px] text-amber-500" aria-hidden="true"></i>
                                <span>ช่าง: 
                                    <span class="font-medium <?= $ticket['technician_name'] ? 'text-amber-800' : 'text-slate-400 italic' ?>">
                                        <?= htmlspecialchars($ticket['technician_name'] ?: 'ยังไม่ระบุ') ?>
                                    </span>
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono flex items-center gap-1 tabular-nums">
                                <i class="fa-regular fa-clock text-[10px]" aria-hidden="true"></i>
                                <span><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
