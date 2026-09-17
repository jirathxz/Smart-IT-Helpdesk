<?php
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

$currentStatus = $filters['status'] ?? '';
?>

<div class="space-y-6">
    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight"><?= htmlspecialchars($title) ?></h1>
            <p class="text-sm text-slate-400 mt-1">ทั้งหมด <?= count($tickets) ?> รายการ</p>
        </div>
        <a href="/tickets/create" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-lg shadow-blue-600/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            เปิดตั๋วแจ้งซ่อมใหม่
        </a>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
        <!-- Status Pills -->
        <div class="flex flex-wrap gap-1.5 border-b border-slate-800/80 pb-3 text-xs">
            <a href="/tickets<?= !empty($_GET['view']) ? '?view=' . htmlspecialchars($_GET['view']) : '' ?>" 
               class="px-3 py-1.5 rounded-lg font-medium transition-all <?= empty($currentStatus) ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                ทั้งหมด
            </a>
            <?php foreach (TicketStatus::cases() as $st): ?>
                <a href="?status=<?= $st->value ?><?= !empty($_GET['view']) ? '&view=' . htmlspecialchars($_GET['view']) : '' ?>" 
                   class="px-3 py-1.5 rounded-lg font-medium transition-all <?= ($currentStatus === $st->value) ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' ?>">
                    <?= $st->label() ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="/tickets" class="grid sm:grid-cols-4 gap-2.5">
            <?php if (!empty($_GET['view'])): ?>
                <input type="hidden" name="view" value="<?= htmlspecialchars($_GET['view']) ?>">
            <?php endif; ?>
            <?php if (!empty($currentStatus)): ?>
                <input type="hidden" name="status" value="<?= htmlspecialchars($currentStatus) ?>">
            <?php endif; ?>

            <div class="sm:col-span-2">
                <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="ค้นหาตามหัวข้อ, รายละเอียด หรือชื่อผู้แจ้ง..." 
                       class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
            </div>

            <div>
                <select name="category_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="">-- ทุกหมวดหมู่ --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (!empty($filters['category_id']) && (int)$filters['category_id'] === $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-2">
                <select name="priority" class="w-full px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-blue-500">
                    <option value="">-- ทุกความเร่งด่วน --</option>
                    <?php foreach (TicketPriority::cases() as $pr): ?>
                        <option value="<?= $pr->value ?>" <?= (!empty($filters['priority']) && $filters['priority'] === $pr->value) ? 'selected' : '' ?>>
                            <?= $pr->label() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">กรอง</button>
            </div>
        </form>
    </div>

    <!-- Tickets Grid / List -->
    <?php if (empty($tickets)): ?>
        <div class="text-center py-12 bg-slate-900/40 rounded-2xl border border-slate-800">
            <div class="w-12 h-12 rounded-full bg-slate-800/80 flex items-center justify-center mx-auto text-slate-500 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            </div>
            <h3 class="text-base font-bold text-white">ไม่พบรายการตั๋วงานซ่อม</h3>
            <p class="text-xs text-slate-400 mt-1">ไม่มีรายการที่ตรงกับเงื่อนไขการค้นหาในขณะนี้</p>
        </div>
    <?php else: ?>
        <div class="grid gap-3">
            <?php foreach ($tickets as $ticket): 
                $sEnum = TicketStatus::tryFrom($ticket['status']);
                $pEnum = TicketPriority::tryFrom($ticket['priority']);
            ?>
                <a href="/tickets/<?= $ticket['id'] ?>" class="block p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-slate-800/80 hover:border-slate-700 hover:bg-slate-900 transition-all shadow-sm group">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-mono text-xs text-slate-500 font-semibold">#<?= $ticket['id'] ?></span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border <?= $sEnum ? $sEnum->badgeClasses() : '' ?>">
                                    <?= $sEnum ? $sEnum->label() : $ticket['status'] ?>
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border <?= $pEnum ? $pEnum->badgeClasses() : '' ?>">
                                    <?= $pEnum ? $pEnum->label() : $ticket['priority'] ?>
                                </span>
                                <span class="text-xs text-slate-400 font-medium">&bull; <?= htmlspecialchars($ticket['category_name']) ?></span>
                            </div>
                            <h2 class="text-base font-bold text-white group-hover:text-blue-400 transition-colors truncate">
                                <?= htmlspecialchars($ticket['title']) ?>
                            </h2>
                            <p class="text-xs text-slate-400 line-clamp-1">
                                <?= htmlspecialchars($ticket['description']) ?>
                            </p>
                        </div>

                        <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center border-t sm:border-t-0 border-slate-800/60 pt-2 sm:pt-0 text-xs text-slate-400 gap-1 flex-shrink-0">
                            <div>
                                ผู้แจ้ง: <span class="text-slate-200 font-medium"><?= htmlspecialchars($ticket['user_name']) ?></span>
                            </div>
                            <div>
                                ช่างผู้รับผิดชอบ: 
                                <span class="font-medium <?= $ticket['technician_name'] ? 'text-amber-400' : 'text-slate-500 italic' ?>">
                                    <?= htmlspecialchars($ticket['technician_name'] ?: 'ยังไม่ระบุ') ?>
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono">
                                <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
