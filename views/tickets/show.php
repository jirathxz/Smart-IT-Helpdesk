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
    'open'        => ['label' => 'เปิดตั๋วใหม่', 'desc' => 'รอจ่ายงาน'],
    'assigned'    => ['label' => 'มอบหมายแล้ว', 'desc' => 'ช่างรับมอบหมาย'],
    'in_progress' => ['label' => 'กำลังซ่อม', 'desc' => 'ช่างเข้าดำเนินงาน'],
    'resolved'    => ['label' => 'ซ่อมเสร็จสิ้น', 'desc' => 'รอตรวจรับ'],
    'closed'      => ['label' => 'ปิดงานสมบูรณ์', 'desc' => 'ให้คะแนนแล้ว'],
];

$statusOrder = ['open' => 1, 'assigned' => 2, 'in_progress' => 3, 'resolved' => 4, 'closed' => 5];
$currentOrder = $statusOrder[$ticket['status']] ?? 1;
?>

<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="/tickets" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="font-mono text-sm text-slate-500 font-bold">#<?= $ticket['id'] ?></span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border <?= $statusEnum ? $statusEnum->badgeClasses() : '' ?>">
                        <?= $statusEnum ? $statusEnum->label() : $ticket['status'] ?>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border <?= $priorityEnum ? $priorityEnum->badgeClasses() : '' ?>">
                        <?= $priorityEnum ? $priorityEnum->label() : $ticket['priority'] ?>
                    </span>
                    <span class="text-xs text-slate-400">&bull; หมวดหมู่: <?= htmlspecialchars($ticket['category_name']) ?></span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-white mt-1">
                    <?= htmlspecialchars($ticket['title']) ?>
                </h1>
            </div>
        </div>

        <!-- Role Action Buttons Container -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Admin: Assign Tech Button -->
            <?php if ($isAdmin && $ticket['status'] === 'open'): ?>
                <button onclick="document.getElementById('assignModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold shadow-lg shadow-purple-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    มอบหมายช่างเทคนิค
                </button>
            <?php endif; ?>

            <!-- Technician: Start Repair Button -->
            <?php if (($isAssignedTech || $isAdmin) && $ticket['status'] === 'assigned'): ?>
                <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="inline">
                    <?= \App\Core\Csrf::field() ?>
                    <input type="hidden" name="status" value="in_progress">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold shadow-lg shadow-amber-600/30 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        เริ่มดำเนินการซ่อม (Start Job)
                    </button>
                </form>
            <?php endif; ?>

            <!-- Technician: Resolve with photo -->
            <?php if (($isAssignedTech || $isAdmin) && $ticket['status'] === 'in_progress'): ?>
                <button onclick="document.getElementById('resolveModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    บันทึกซ่อมเสร็จสิ้น (Resolve)
                </button>
            <?php endif; ?>

            <!-- User / Owner: Confirm & Rate or Reject -->
            <?php if (($isOwner || $isAdmin) && $ticket['status'] === 'resolved'): ?>
                <button onclick="document.getElementById('rateModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    ตรวจรับงานและให้คะแนน (Close)
                </button>
                <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/20 text-xs font-semibold transition-all">
                    ส่งกลับแก้ไข (Reject)
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- State Machine Stepper Timeline -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 shadow-sm overflow-x-auto">
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
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all shadow-md
                        <?= $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-500/20 shadow-blue-500/30' : ($isCompleted ? 'bg-emerald-500 text-slate-950 font-extrabold' : 'bg-slate-800 text-slate-500') ?>">
                        <?php if ($isCompleted && !$isCurrent): ?>
                            ✓
                        <?php else: ?>
                            <?= $order ?>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs font-bold mt-2 <?= $isCurrent ? 'text-blue-400' : ($isCompleted ? 'text-emerald-400' : 'text-slate-400') ?>">
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
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                    รายละเอียดปัญหาที่แจ้ง
                </h3>
                <div class="text-sm text-slate-200 whitespace-pre-line leading-relaxed bg-slate-950/40 p-4 rounded-xl border border-slate-800/60 font-sans">
                    <?= htmlspecialchars($ticket['description']) ?>
                </div>

                <!-- Star Rating Display if Closed -->
                <?php if ($rating): ?>
                    <div class="p-4 rounded-xl bg-yellow-500/10 border border-yellow-500/25 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="text-xs text-yellow-400 font-bold uppercase tracking-wider">ผลการประเมินจากผู้แจ้งซ่อม</div>
                            <div class="flex items-center gap-1 mt-1 text-yellow-400 text-lg">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <span><?= ($s <= $rating['score']) ? '★' : '☆' ?></span>
                                <?php endfor; ?>
                                <span class="text-xs font-bold ml-1 text-slate-300">(<?= $rating['score'] ?>/5 คะแนน)</span>
                            </div>
                            <?php if (!empty($rating['feedback'])): ?>
                                <p class="text-xs text-slate-300 mt-1 italic">"<?= htmlspecialchars($rating['feedback']) ?>"</p>
                            <?php endif; ?>
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono">
                            ประเมินเมื่อ: <?= date('d/m/Y H:i', strtotime($rating['created_at'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Comments & Communication Section -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-5 shadow-sm">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    กล่องข้อความและการปฏิบัติงาน (<?= count($comments) ?>)
                </h3>

                <!-- Comments Feed -->
                <div class="space-y-4">
                    <?php if (empty($comments)): ?>
                        <p class="text-xs text-slate-500 italic text-center py-4">ยังไม่มีข้อความหรือบันทึกในตั๋วนี้</p>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-white"><?= htmlspecialchars($c['user_name']) ?></span>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 font-semibold uppercase">
                                            <?= $c['user_role'] ?>
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-mono"><?= date('d/m/y H:i', strtotime($c['created_at'])) ?></span>
                                </div>
                                <div class="text-xs text-slate-200 whitespace-pre-line leading-relaxed">
                                    <?= htmlspecialchars($c['body']) ?>
                                </div>
                                <?php if (!empty($c['image_path'])): ?>
                                    <div class="mt-2">
                                        <a href="/<?= htmlspecialchars($c['image_path']) ?>" target="_blank" class="inline-block group relative rounded-lg overflow-hidden border border-slate-800">
                                            <img src="/<?= htmlspecialchars($c['image_path']) ?>" class="max-h-48 rounded-lg object-contain" alt="รูปภาพแนบ">
                                            <div class="text-[10px] text-blue-400 mt-1 hover:underline">🔍 ดูภาพขนาดเต็ม</div>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Add Comment Form -->
                <?php if ($ticket['status'] !== 'closed'): ?>
                    <form action="/tickets/<?= $ticket['id'] ?>/comment" method="POST" enctype="multipart/form-data" class="space-y-3 pt-3 border-t border-slate-800">
                        <?= \App\Core\Csrf::field() ?>
                        <textarea name="body" rows="3" placeholder="พิมพ์ข้อความตอบกลับ อัปเดตความคืบหน้า หรือสอบถามข้อมูล..." 
                                  class="w-full px-3 py-2.5 bg-slate-950 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"></textarea>
                        
                        <div class="flex items-center justify-between gap-3">
                            <label class="cursor-pointer inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-white transition-colors">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <span>แนบรูปภาพ</span>
                                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="alert('เลือกไฟล์: ' + this.files[0].name)">
                            </label>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-600/30 transition-all">
                                ส่งข้อความ
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Meta Sidebar & Audit Trail -->
        <div class="space-y-6">
            <!-- Meta Details Card -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-sm text-xs">
                <h3 class="font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-800">ข้อมูลตั๋วงานซ่อม</h3>
                
                <div class="space-y-3 text-slate-300">
                    <div>
                        <div class="text-slate-400 text-[11px]">ผู้แจ้งซ่อม:</div>
                        <div class="font-semibold text-white mt-0.5"><?= htmlspecialchars($ticket['user_name']) ?></div>
                        <div class="text-slate-400 text-[11px]"><?= htmlspecialchars($ticket['user_email']) ?></div>
                    </div>

                    <div>
                        <div class="text-slate-400 text-[11px]">ช่างผู้รับผิดชอบ:</div>
                        <div class="font-semibold mt-0.5 <?= $ticket['technician_name'] ? 'text-amber-400' : 'text-slate-400 italic' ?>">
                            <?= htmlspecialchars($ticket['technician_name'] ?: 'ยังไม่ได้รับมอบหมาย') ?>
                        </div>
                    </div>

                    <div>
                        <div class="text-slate-400 text-[11px]">วันที่เปิดตั๋ว:</div>
                        <div class="font-mono text-white mt-0.5"><?= date('d/m/Y H:i:s', strtotime($ticket['created_at'])) ?></div>
                    </div>

                    <?php if (!empty($ticket['resolved_at'])): ?>
                        <div>
                            <div class="text-slate-400 text-[11px]">วันที่ซ่อมเสร็จ:</div>
                            <div class="font-mono text-emerald-400 mt-0.5"><?= date('d/m/Y H:i:s', strtotime($ticket['resolved_at'])) ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($ticket['closed_at'])): ?>
                        <div>
                            <div class="text-slate-400 text-[11px]">วันที่ปิดงาน:</div>
                            <div class="font-mono text-slate-400 mt-0.5"><?= date('d/m/Y H:i:s', strtotime($ticket['closed_at'])) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Transition History (Audit Trail) -->
            <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-sm text-xs">
                <h3 class="font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-800">
                    ประวัติการเปลี่ยนสถานะ
                </h3>
                <div class="space-y-3">
                    <?php foreach ($statusLogs as $log): ?>
                        <div class="border-l-2 border-blue-500/40 pl-3 py-0.5 space-y-0.5">
                            <div class="font-semibold text-white flex items-center gap-1.5">
                                <span class="font-mono text-slate-400"><?= $log['from_status'] ?></span>
                                <span class="text-slate-500">&rarr;</span>
                                <span class="font-mono text-blue-400 font-bold"><?= $log['to_status'] ?></span>
                            </div>
                            <div class="text-[11px] text-slate-400"><?= htmlspecialchars($log['note'] ?? '-') ?></div>
                            <div class="text-[10px] text-slate-400 font-mono">โดย: <?= htmlspecialchars($log['changed_by_name']) ?> &bull; <?= date('d/m H:i', strtotime($log['created_at'])) ?></div>
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
<div id="assignModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">มอบหมายช่างเทคนิค</h3>
            <button onclick="document.getElementById('assignModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="/admin/tickets/<?= $ticket['id'] ?>/assign" method="POST" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs text-slate-400 mb-1">เลือกช่างเทคนิค</label>
                <select name="technician_id" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                    <option value="">-- เลือกช่างเทคนิค --</option>
                    <?php foreach ($technicians as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ((int)$ticket['technician_id'] === $t['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('assignModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-500 text-xs font-semibold text-white">บันทึกมอบหมาย</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- 2. Technician Resolve Modal (Requires photo & note) -->
<div id="resolveModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">บันทึกผลการซ่อมเสร็จสิ้น (Resolve)</h3>
            <button onclick="document.getElementById('resolveModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" enctype="multipart/form-data" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="resolved">

            <div>
                <label class="block text-xs text-slate-400 mb-1">สรุปแนวทางการแก้ไขปัญหา <span class="text-rose-500">*</span></label>
                <textarea name="note" rows="3" required placeholder="อธิบายขั้นตอนการซ่อม การเปลี่ยนอะไหล่ หรือการตั้งค่า..." 
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white"></textarea>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">แนบรูปถ่ายหลักฐานการซ่อมเสร็จ <span class="text-rose-500">* (บังคับตามกฎ State Machine)</span></label>
                <input type="file" name="image" required accept="image/jpeg,image/png,image/webp" 
                       class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('resolveModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-xs font-semibold text-white">ยืนยันซ่อมเสร็จสิ้น</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. User Confirm & Rate 1-5 Stars Modal -->
<div id="rateModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">ตรวจรับงานและประเมินความพึงพอใจ</h3>
            <button onclick="document.getElementById('rateModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="closed">

            <!-- Star Picker -->
            <div>
                <label class="block text-xs text-slate-400 mb-2">ให้คะแนนความพึงพอใจการให้บริการ <span class="text-rose-500">*</span></label>
                <div class="flex items-center justify-center gap-3 text-3xl cursor-pointer select-none py-2" id="starContainer">
                    <span data-val="1" class="star text-yellow-400">★</span>
                    <span data-val="2" class="star text-yellow-400">★</span>
                    <span data-val="3" class="star text-yellow-400">★</span>
                    <span data-val="4" class="star text-yellow-400">★</span>
                    <span data-val="5" class="star text-yellow-400">★</span>
                </div>
                <input type="hidden" name="score" id="scoreInput" value="5">
                <div class="text-center text-xs font-bold text-yellow-400 mt-1" id="scoreLabel">5 จาก 5 คะแนน (ยอดเยี่ยม)</div>
            </div>

            <div>
                <label class="block text-xs text-slate-400 mb-1">ข้อเสนอแนะเพิ่มเติม</label>
                <textarea name="feedback" rows="2" placeholder="ความประทับใจ หรือข้อเสนอแนะเพิ่มเติมเพื่อการปรับปรุง..." 
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('rateModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-xs font-semibold text-white">บันทึกและปิดงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. User Reject Modal (Reverts to In Progress) -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-rose-400">ส่งกลับแก้ไข / ปฏิเสธผลงาน</h3>
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="status" value="in_progress">

            <div>
                <label class="block text-xs text-slate-400 mb-1">ระบุเหตุผลที่งานยังไม่เรียบร้อย <span class="text-rose-500">*</span></label>
                <textarea name="note" rows="3" required placeholder="เช่น อุปกรณ์ยังเปิดไม่ติดเหมือนเดิม หรือมีข้อความแจ้งเตือนใหม่อื่นๆ..." 
                          class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-xs font-semibold text-white">ส่งกลับแก้ไข</button>
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
                s.classList.add('text-yellow-400');
                s.classList.remove('text-slate-600');
                s.textContent = '★';
            } else {
                s.classList.remove('text-yellow-400');
                s.classList.add('text-slate-600');
                s.textContent = '☆';
            }
        });
    });
});
</script>
