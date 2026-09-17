<?php
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;

$statusEnum = TicketStatus::tryFrom($ticket['status']);
$priorityEnum = TicketPriority::tryFrom($ticket['priority']);
$role = $currentUser['role'];
$userId = (int) $currentUser['id'];
$isOwner = ((int) $ticket['user_id'] === $userId);
$isAssignedTech = ((int) ($ticket['technician_id'] ?? 0) === $userId);
$isAdmin = ($role === 'admin');

// Steps for visual state machine stepper
$steps = [
    'open'        => ['label' => 'เปิดตั๋วใหม่', 'desc' => 'รอจ่ายงาน', 'icon' => 'fa-envelope-open'],
    'assigned'    => ['label' => 'มอบหมายแล้ว', 'desc' => 'ช่างรับงาน', 'icon' => 'fa-user-check'],
    'in_progress' => ['label' => 'กำลังซ่อม', 'desc' => 'ช่างดำเนินงาน', 'icon' => 'fa-gears'],
    'resolved'    => ['label' => 'ซ่อมเสร็จสิ้น', 'desc' => 'รอตรวจรับ', 'icon' => 'fa-circle-check'],
    'closed'      => ['label' => 'ปิดงานสมบูรณ์', 'desc' => 'ให้คะแนนแล้ว', 'icon' => 'fa-check-double'],
];

$statusOrder = ['open' => 1, 'assigned' => 2, 'in_progress' => 3, 'resolved' => 4, 'closed' => 5];
$currentOrder = $statusOrder[$ticket['status']] ?? 1;
?>

<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/tickets" class="p-2.5 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-all shadow-sm" aria-label="กลับสู่รายการตั๋ว">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-sm text-slate-400 font-bold">#<?= $ticket['id'] ?></span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $statusEnum ? $statusEnum->badgeClasses() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                        <?= $statusEnum ? $statusEnum->label() : $ticket['status'] ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $priorityEnum ? $priorityEnum->badgeClasses() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                        <?= $priorityEnum ? $priorityEnum->label() : $ticket['priority'] ?>
                    </span>
                    <span class="text-xs text-slate-400 font-medium">&bull; หมวดหมู่: <?= htmlspecialchars($ticket['category_name']) ?></span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">
                    <?= htmlspecialchars($ticket['title']) ?>
                </h1>
            </div>
        </div>

        <!-- Role Action Buttons Container -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Admin: Assign Tech Button -->
            <?php if ($isAdmin && $ticket['status'] === 'open'): ?>
                <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold shadow-sm shadow-purple-500/25 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>มอบหมายช่างเทคนิค</span>
                </button>
            <?php endif; ?>

            <!-- Technician: Start Repair Button -->
            <?php if (($isAssignedTech || $isAdmin) && $ticket['status'] === 'assigned'): ?>
                <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold shadow-sm shadow-amber-500/25 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-play text-xs"></i>
                        <span>เริ่มดำเนินการซ่อม (Start Job)</span>
                    </button>
                </form>
            <?php endif; ?>

            <!-- Technician: Resolve with photo -->
            <?php if (($isAssignedTech || $isAdmin) && $ticket['status'] === 'in_progress'): ?>
                <button onclick="document.getElementById('resolveModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm shadow-emerald-500/25 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>บันทึกซ่อมเสร็จสิ้น (Resolve)</span>
                </button>
            <?php endif; ?>

            <!-- User / Owner: Confirm & Rate or Reject -->
            <?php if (($isOwner || $isAdmin) && $ticket['status'] === 'resolved'): ?>
                <button onclick="document.getElementById('rateModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm shadow-emerald-500/25 transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-star text-yellow-300 text-xs"></i>
                    <span>ตรวจรับงานและให้คะแนน (Close)</span>
                </button>
                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                    <span>ส่งกลับแก้ไข (Reject)</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- State Machine Stepper Timeline -->
    <div class="p-5 sm:p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm overflow-x-auto">
        <div class="flex items-center justify-between min-w-[650px] relative">
            <?php 
            $i = 0;
            foreach ($steps as $stKey => $step): 
                $i++;
                $order = $statusOrder[$stKey];
                $isCompleted = ($order < $currentOrder) || ($ticket['status'] === 'closed');
                $isCurrent = ($ticket['status'] === $stKey);
            ?>
                <div class="flex flex-col items-center relative z-10 text-center flex-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-sm
                        <?= $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-blue-500/25' : ($isCompleted ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400') ?>">
                        <?php if ($isCompleted && !$isCurrent): ?>
                            <i class="fa-solid fa-check text-xs"></i>
                        <?php else: ?>
                            <i class="fa-solid <?= $step['icon'] ?> text-xs"></i>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs font-bold mt-2 <?= $isCurrent ? 'text-blue-600' : ($isCompleted ? 'text-emerald-700' : 'text-slate-400') ?>">
                        <?= $step['label'] ?>
                    </span>
                    <span class="text-[10px] text-slate-400 mt-0.5"><?= $step['desc'] ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Ticket Description & Rating Box (if Closed) -->
    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Description -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 space-y-4 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-file-lines text-blue-600 text-sm"></i>
                    <span>รายละเอียดปัญหาที่แจ้ง</span>
                </h3>
                <div class="text-sm text-slate-700 whitespace-pre-line leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200/80">
                    <?= htmlspecialchars($ticket['description']) ?>
                </div>

                <!-- Star Rating Display if Closed -->
                <?php if ($rating): ?>
                    <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
                        <div>
                            <div class="text-xs text-amber-800 font-bold uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-star text-amber-500"></i>
                                <span>ผลการประเมินจากผู้แจ้งซ่อม</span>
                            </div>
                            <div class="flex items-center gap-1 mt-1 text-amber-500 text-base">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="fa-<?= ($s <= $rating['score']) ? 'solid' : 'regular' ?> fa-star"></i>
                                <?php endfor; ?>
                                <span class="text-xs font-bold ml-1.5 text-slate-700">(<?= $rating['score'] ?>/5 คะแนน)</span>
                            </div>
                            <?php if (!empty($rating['feedback'])): ?>
                                <p class="text-xs text-slate-600 mt-1 italic">"<?= htmlspecialchars($rating['feedback']) ?>"</p>
                            <?php endif; ?>
                        </div>
                        <div class="text-[11px] text-slate-400 font-mono flex items-center gap-1">
                            <i class="fa-regular fa-calendar-check text-[10px]"></i>
                            <span><?= date('d/m/Y H:i', strtotime($rating['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comments & Communication Section -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 space-y-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-600 flex items-center gap-2">
                    <i class="fa-solid fa-comments text-blue-600 text-sm"></i>
                    <span>กล่องข้อความและการปฏิบัติงาน (<?= count($comments) ?>)</span>
                </h3>

                <!-- Comments Feed -->
                <div class="space-y-3.5">
                    <?php if (empty($comments)): ?>
                        <p class="text-xs text-slate-400 italic text-center py-4">ยังไม่มีข้อความหรือบันทึกในตั๋วนี้</p>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900"><?= htmlspecialchars($c['user_name']) ?></span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 font-semibold uppercase">
                                            <?= $c['user_role'] ?>
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-mono"><?= date('d/m/y H:i', strtotime($c['created_at'])) ?></span>
                                </div>
                                <div class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                                    <?= htmlspecialchars($c['body']) ?>
                                </div>
                                <?php if (!empty($c['image_path'])): ?>
                                    <div class="mt-2.5">
                                        <a href="/<?= htmlspecialchars($c['image_path']) ?>" target="_blank" class="inline-block group relative rounded-xl overflow-hidden border border-slate-200 bg-white p-1 shadow-sm hover:shadow transition-shadow">
                                            <img src="/<?= htmlspecialchars($c['image_path']) ?>" class="max-h-48 rounded-lg object-contain" alt="รูปภาพแนบ">
                                            <div class="text-[10px] text-blue-600 mt-1 flex items-center gap-1 font-medium">
                                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                <span>ดูภาพขนาดเต็ม</span>
                                            </div>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Add Comment Form -->
                <?php if ($ticket['status'] !== 'closed'): ?>
                    <form action="/tickets/<?= $ticket['id'] ?>/comment" method="POST" enctype="multipart/form-data" class="space-y-3 pt-3 border-t border-slate-100">
                        <?= \App\Core\Csrf::field() ?>
                        <textarea name="body" rows="3" placeholder="พิมพ์ข้อความตอบกลับ อัปเดตความคืบหน้า หรือสอบถามข้อมูล..." 
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-500 transition-all"></textarea>
                        
                        <div class="flex items-center justify-between gap-3">
                            <label class="cursor-pointer inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-paperclip text-sm"></i>
                                <span>แนบรูปภาพ</span>
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="alert('เลือกไฟล์: ' + this.files[0].name)">
                            </label>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm shadow-blue-500/25 transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-paper-plane text-xs"></i>
                                <span>ส่งข้อความ</span>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta Sidebar & Audit Trail -->
        <div class="space-y-6">
            <!-- Meta Details Card -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 space-y-4 shadow-sm text-xs">
                <h3 class="font-bold uppercase tracking-wider text-slate-600 pb-2.5 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-blue-600"></i>
                    <span>ข้อมูลตั๋วงานซ่อม</span>
                </h3>
                
                <div class="space-y-3 text-slate-600">
                    <div>
                        <div class="text-slate-400 text-[11px]">ผู้แจ้งซ่อม:</div>
                        <div class="font-semibold text-slate-900 mt-0.5"><?= htmlspecialchars($ticket['user_name']) ?></div>
                        <div class="text-slate-500 text-[11px]"><?= htmlspecialchars($ticket['user_email']) ?></div>
                    </div>

                    <div>
                        <div class="text-slate-400 text-[11px]">ช่างผู้รับผิดชอบ:</div>
                        <div class="font-semibold mt-0.5 <?= $ticket['technician_name'] ? 'text-amber-700' : 'text-slate-400 italic' ?>">
                            <?= htmlspecialchars($ticket['technician_name'] ?: 'ยังไม่ได้รับมอบหมาย') ?>
                        </div>
                    </div>

                    <div>
                        <div class="text-slate-400 text-[11px]">วันที่เปิดตั๋ว:</div>
                        <div class="font-mono text-slate-800 mt-0.5"><?= date('d/m/Y H:i:s', strtotime($ticket['created_at'])) ?></div>
                    </div>

                    <?php if (!empty($ticket['resolved_at'])): ?>
                        <div>
                            <div class="text-slate-400 text-[11px]">วันที่ซ่อมเสร็จ:</div>
                            <div class="font-mono text-emerald-600 font-semibold mt-0.5"><?= date('d/m/Y H:i:s', strtotime($ticket['resolved_at'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($ticket['closed_at'])): ?>
                        <div>
                            <div class="text-slate-400 text-[11px]">วันที่ปิดงาน:</div>
                            <div class="font-mono text-slate-600 mt-0.5"><?= date('d/m/Y H:i:s', strtotime($ticket['closed_at'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Transition History (Audit Trail) -->
            <div class="p-6 rounded-2xl bg-white border border-slate-200/90 space-y-3 shadow-sm text-xs">
                <h3 class="font-bold uppercase tracking-wider text-slate-600 pb-2.5 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                    <span>ประวัติการเปลี่ยนสถานะ</span>
                </h3>
                <div class="space-y-3">
                    <?php foreach ($statusLogs as $log): ?>
                        <div class="border-l-2 border-blue-500 pl-3 py-0.5 space-y-0.5">
                            <div class="font-semibold text-slate-800 flex items-center gap-1.5">
                                <span class="font-mono text-slate-500"><?= $log['from_status'] ?></span>
                                <span class="text-slate-400">&rarr;</span>
                                <span class="font-mono text-blue-600 font-bold"><?= $log['to_status'] ?></span>
                            </div>
                            <div class="text-[11px] text-slate-500"><?= htmlspecialchars($log['note'] ?? '-') ?></div>
                            <div class="text-[10px] text-slate-400 font-mono flex items-center gap-1">
                                <span>โดย: <?= htmlspecialchars($log['changed_by_name']) ?></span>
                                <span>&bull;</span>
                                <span><?= date('d/m H:i', strtotime($log['created_at'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODALS ==================== -->

<!-- 1. Admin Assign Tech Modal -->
<?php if ($isAdmin): ?>
<div id="assignModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-purple-600"></i>
                <span>มอบหมายช่างเทคนิค</span>
            </h3>
            <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-base" aria-label="ปิด">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/admin/tickets/<?= $ticket['id'] ?>/assign" method="POST" class="space-y-3.5">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">เลือกช่างเทคนิค</label>
                <select name="technician_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600">
                    <option value="">-- เลือกช่างเทคนิค --</option>
                    <?php foreach ($technicians as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ((int)$ticket['technician_id'] === $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-xs font-semibold text-white shadow-sm shadow-purple-500/25">บันทึกมอบหมาย</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 2. Technician Resolve Modal (Requires photo & note) -->
<div id="resolveModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>บันทึกผลการซ่อมเสร็จสิ้น (Resolve)</span>
            </h3>
            <button onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-base" aria-label="ปิด">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" enctype="multipart/form-data" class="space-y-3.5">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="resolved">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">สรุปแนวทางการแก้ไขปัญหา <span class="text-rose-500">*</span></label>
                <textarea name="note" rows="3" required placeholder="อธิบายขั้นตอนการซ่อม การเปลี่ยนอะไหล่ หรือการตั้งค่า..." 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">แนบรูปถ่ายหลักฐานการซ่อมเสร็จ <span class="text-rose-500">* (บังคับตามกฎ State Machine)</span></label>
                <input type="file" name="image" required accept="image/jpeg,image/png,image/webp" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-sm shadow-emerald-500/25">ยืนยันซ่อมเสร็จสิ้น</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. User Confirm & Rate 1-5 Stars Modal -->
<div id="rateModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-500"></i>
                <span>ตรวจรับงานและประเมินความพึงพอใจ</span>
            </h3>
            <button onclick="document.getElementById('rateModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-base" aria-label="ปิด">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="closed">

            <!-- Star Picker -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-center">
                <label class="block text-xs font-semibold text-slate-600 mb-2">ให้คะแนนความพึงพอใจการให้บริการ <span class="text-rose-500">*</span></label>
                <div class="flex items-center justify-center gap-3 text-3xl cursor-pointer select-none py-1" id="starContainer">
                    <span data-val="1" class="star text-amber-400 hover:scale-110 transition-transform">★</span>
                    <span data-val="2" class="star text-amber-400 hover:scale-110 transition-transform">★</span>
                    <span data-val="3" class="star text-amber-400 hover:scale-110 transition-transform">★</span>
                    <span data-val="4" class="star text-amber-400 hover:scale-110 transition-transform">★</span>
                    <span data-val="5" class="star text-amber-400 hover:scale-110 transition-transform">★</span>
                </div>
                <input type="hidden" name="score" id="scoreInput" value="5">
                <div class="text-center text-xs font-bold text-amber-600 mt-1" id="scoreLabel">5 จาก 5 คะแนน (ยอดเยี่ยม)</div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">ข้อเสนอแนะเพิ่มเติม</label>
                <textarea name="feedback" rows="2" placeholder="ความประทับใจ หรือข้อเสนอแนะเพิ่มเติมเพื่อการปรับปรุง..." 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('rateModal').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-xs font-semibold text-white shadow-sm shadow-emerald-500/25">บันทึกและปิดงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. User Reject Modal (Reverts to In Progress) -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="text-base font-bold text-rose-600 flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>ส่งกลับแก้ไข / ปฏิเสธผลงาน</span>
            </h3>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-base" aria-label="ปิด">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="space-y-3.5">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="in_progress">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">ระบุเหตุผลที่งานยังไม่เรียบร้อย <span class="text-rose-500">*</span></label>
                <textarea name="note" rows="3" required placeholder="เช่น อุปกรณ์ยังเปิดไม่ติดเหมือนเดิม หรือมีข้อความแจ้งเตือนใหม่อื่นๆ..." 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-xs font-semibold text-white shadow-sm shadow-rose-500/25">ส่งกลับแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<script>
// Interactive Star Picker
const stars = document.querySelectorAll('#starContainer .star');
const scoreInput = document.getElementById('scoreInput');
const scoreLabel = document.getElementById('scoreLabel');
const labelMap = {
    1: '1 จาก 5 คะแนน (ต้องปรับปรุงมาก)',
    2: '2 จาก 5 คะแนน (พอใช้)',
    3: '3 จาก 5 คะแนน (ปานกลาง)',
    4: '4 จาก 5 คะแนน (ดี)',
    5: '5 จาก 5 คะแนน (ยอดเยี่ยม)'
};

stars.forEach(star => {
    star.addEventListener('click', function() {
        const val = parseInt(this.getAttribute('data-val'));
        scoreInput.value = val;
        scoreLabel.textContent = labelMap[val];

        stars.forEach(s => {
            const sVal = parseInt(s.getAttribute('data-val'));
            if (sVal <= val) {
                s.classList.add('text-amber-400');
                s.classList.remove('text-slate-300');
                s.textContent = '★';
            } else {
                s.classList.remove('text-amber-400');
                s.classList.add('text-slate-300');
                s.textContent = '☆';
            }
        });
    });
});
</script>
