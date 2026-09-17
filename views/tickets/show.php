<?php
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;

$statusEnum = TicketStatus::tryFrom($ticket['status']) ?? TicketStatus::OPEN;
$priorityEnum = TicketPriority::tryFrom($ticket['priority']) ?? TicketPriority::MEDIUM;
$ticketCode = '#TK-' . str_pad((string)$ticket['id'], 4, '0', STR_PAD_LEFT);
$currentStep = $statusEnum->stepIndex();
?>

<div class="space-y-6">
    <!-- Breadcrumb & Back Link -->
    <div class="flex items-center justify-between">
        <a href="/tickets" class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>กลับไปหน้ารายการงานแจ้งซ่อม</span>
        </a>
        <span class="text-xs text-slate-400 font-mono">ID: <?= $ticket['id'] ?></span>
    </div>

    <!-- Ticket Main Title Card -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-2xs p-5 sm:p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-1.5 flex-wrap">
                    <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-200">
                        <?= $ticketCode ?>
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-0.5 rounded-md border"
                          style="background-color: <?= $priorityEnum->dotColor() ?>15; border-color: <?= $priorityEnum->dotColor() ?>40; color: <?= $priorityEnum->dotColor() ?>;">
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: <?= $priorityEnum->dotColor() ?>;"></span>
                        <span><?= $priorityEnum->shortLabel() ?></span>
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-md border <?= $statusEnum->badgeClass() ?>">
                        <?= $statusEnum->shortLabel() ?>
                    </span>
                </div>
                <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">
                    <?= htmlspecialchars($ticket['title']) ?>
                </h1>
            </div>

            <!-- Action Buttons for Roles -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Technician: Accept Job -->
                <?php if ($role === 'technician' && $ticket['status'] === 'assigned'): ?>
                    <button type="button" onclick="openModal('acceptModal')" 
                            class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-play text-xs"></i>
                        <span>รับงานซ่อม</span>
                    </button>
                <?php endif; ?>

                <!-- Technician: Resolve Job -->
                <?php if ($role === 'technician' && $ticket['status'] === 'in_progress'): ?>
                    <button type="button" onclick="openModal('resolveModal')" 
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>ปิดงานซ่อมและส่งมอบ</span>
                    </button>
                <?php endif; ?>

                <!-- User: Approve & Rate OR Rework -->
                <?php if ($role === 'user' && $ticket['status'] === 'resolved'): ?>
                    <button type="button" onclick="openModal('reworkModal')" 
                            class="px-3.5 py-2 rounded-xl border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-xs sm:text-sm font-medium transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>ส่งกลับแก้ไข</span>
                    </button>
                    <button type="button" onclick="openModal('rateModal')" 
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-star text-xs"></i>
                        <span>ตรวจรับงาน &amp; ให้คะแนน</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stepper Timeline (5 Stages) -->
        <div class="mt-6 pt-6 border-t border-slate-100">
            <div class="grid grid-cols-5 gap-2 text-center">
                <?php
                $steps = [
                    1 => ['title' => '1. แจ้งเรื่อง', 'desc' => 'บันทึกเข้าระบบ'],
                    2 => ['title' => '2. มอบหมายช่าง', 'desc' => 'จัดสรรผู้รับผิดชอบ'],
                    3 => ['title' => '3. ดำเนินการซ่อม', 'desc' => 'ช่างเข้าปฏิบัติงาน'],
                    4 => ['title' => '4. ซ่อมเสร็จสิ้น', 'desc' => 'ส่งมอบงาน'],
                    5 => ['title' => '5. ตรวจรับ & ปิดงาน', 'desc' => 'ประเมินความพึงพอใจ'],
                ];
                foreach ($steps as $stepNum => $stepInfo):
                    $isDone = $currentStep >= $stepNum;
                    $isCurrent = $currentStep === $stepNum;
                ?>
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold font-mono transition-colors <?= $isDone ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-slate-100 text-slate-400' ?>">
                            <?= $isDone && $stepNum < $currentStep ? '✓' : $stepNum ?>
                        </div>
                        <div class="text-[11px] sm:text-xs font-semibold mt-2 <?= $isDone ? 'text-slate-900' : 'text-slate-400' ?>">
                            <?= $stepInfo['title'] ?>
                        </div>
                        <div class="text-[10px] text-slate-400 hidden sm:block">
                            <?= $stepInfo['desc'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 2-Column Responsive Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Details, Rating, Comments (2fr) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Problem Description Card -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-2xs p-5 sm:p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-blue-600"></i>
                        <span>รายละเอียดอาการขัดข้อง</span>
                    </h2>
                </div>

                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    <?= htmlspecialchars($ticket['description']) ?>
                </p>

                <!-- Specs Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-4 border-t border-slate-100 text-xs">
                    <div>
                        <span class="text-slate-400 block mb-0.5">หมวดหมู่อุปกรณ์:</span>
                        <strong class="text-slate-800 font-semibold"><?= htmlspecialchars($ticket['category_name']) ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">ผู้แจ้งซ่อม:</span>
                        <strong class="text-slate-800 font-semibold"><?= htmlspecialchars($ticket['user_name']) ?></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">ช่างผู้รับผิดชอบ:</span>
                        <strong class="<?= !empty($ticket['technician_name']) ? 'text-blue-700' : 'text-amber-600' ?> font-semibold">
                            <?= !empty($ticket['technician_name']) ? htmlspecialchars($ticket['technician_name']) : 'ยังไม่ได้มอบหมาย' ?>
                        </strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block mb-0.5">วันที่แจ้ง:</span>
                        <span class="text-slate-700 font-mono"><?= date('d/m/Y H:i น.', strtotime($ticket['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <!-- Rating Result Card (if closed) -->
            <?php if (!empty($rating)): ?>
                <div class="rounded-2xl bg-amber-50/60 border border-amber-200 p-5 shadow-2xs">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span>ผลการประเมินความพึงพอใจการบริการ</span>
                        </span>
                        <div class="text-amber-500 text-base font-bold">
                            <?= str_repeat('★', (int)$rating['score']) . str_repeat('☆', 5 - (int)$rating['score']) ?>
                            <span class="text-xs text-amber-800 font-mono ml-1">(<?= $rating['score'] ?>/5)</span>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-amber-900">
                        <?= htmlspecialchars($rating['feedback'] ?: 'ไม่มีข้อเสนอแนะเพิ่มเติม') ?>
                    </p>
                    <div class="text-[11px] text-amber-700 mt-2 font-mono">
                        ประเมินเมื่อ: <?= date('d/m/Y H:i น.', strtotime($rating['created_at'])) ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Comments & Discussion Section -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-2xs p-5 sm:p-6" id="comments">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-comments text-blue-600"></i>
                        <span>ประวัติการสนทนาและความคืบหน้า (<?= count($comments) ?>)</span>
                    </h2>
                </div>

                <!-- Comment Items -->
                <div class="space-y-3.5 mb-6">
                    <?php if (empty($comments)): ?>
                        <div class="py-8 text-center text-xs text-slate-400">ยังไม่มีข้อความอัปเดตความคืบหน้า</div>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-2">
                                        <strong class="text-slate-800 font-semibold"><?= htmlspecialchars($c['user_name']) ?></strong>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium <?= $c['user_role'] === 'technician' ? 'bg-blue-100 text-blue-800' : ($c['user_role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-slate-200 text-slate-700') ?>">
                                            <?= $c['user_role'] === 'technician' ? 'ช่าง' : ($c['user_role'] === 'admin' ? 'แอดมิน' : 'ผู้แจ้ง') ?>
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-mono"><?= date('d/m/Y H:i น.', strtotime($c['created_at'])) ?></span>
                                </div>
                                <p class="text-xs sm:text-sm text-slate-700 whitespace-pre-line leading-relaxed">
                                    <?= htmlspecialchars($c['body']) ?>
                                </p>
                                <?php if (!empty($c['image_path'])): ?>
                                    <div class="pt-2">
                                        <a href="<?= htmlspecialchars($c['image_path']) ?>" target="_blank" class="inline-block group">
                                            <img src="<?= htmlspecialchars($c['image_path']) ?>" alt="Attached file" 
                                                 class="max-h-48 rounded-lg border border-slate-200 group-hover:opacity-90 transition-opacity">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Add Comment Form -->
                <form action="/tickets/<?= $ticket['id'] ?>/comment" method="POST" enctype="multipart/form-data" class="pt-4 border-t border-slate-100 space-y-3">
                    <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div>
                        <label for="comment_body" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                            ส่งข้อความแจ้งความคืบหน้าหรือสอบถามข้อมูลเพิ่มเติม
                        </label>
                        <textarea id="comment_body" name="body" rows="3" required
                                  placeholder="พิมพ์ข้อความที่ต้องการสื่อสารที่นี่..."
                                  class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"></textarea>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <label for="comment_image" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-medium text-slate-700 cursor-pointer transition-colors">
                                <i class="fa-solid fa-camera text-slate-500"></i>
                                <span>แนบรูปภาพ</span>
                            </label>
                            <input type="file" id="comment_image" name="comment_image" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="document.getElementById('commentImgBadge').textContent = this.files[0]?.name || '';">
                            <span id="commentImgBadge" class="text-[11px] text-slate-500 ml-2 font-mono truncate max-w-xs"></span>
                        </div>

                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium shadow-xs transition-colors flex items-center gap-1.5">
                            <i class="fa-solid fa-paper-plane text-xs"></i>
                            <span>ส่งข้อความ</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Audit Trail / Status History (1fr) -->
        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-2xs p-5 sm:p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-blue-600"></i>
                        <span>ประวัติสถานะการดำเนินงาน</span>
                    </h2>
                </div>

                <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    <?php foreach ($statusLogs as $log): 
                        $toEnum = TicketStatus::tryFrom($log['to_status']);
                    ?>
                        <div class="relative">
                            <!-- Bullet Dot -->
                            <div class="absolute -left-[27px] top-1 w-3 h-3 rounded-full border-2 border-white ring-1 ring-slate-300 <?= $log['to_status'] === 'closed' ? 'bg-emerald-500' : ($log['to_status'] === 'resolved' ? 'bg-purple-500' : 'bg-blue-500') ?>"></div>
                            
                            <div class="text-xs font-bold text-slate-800">
                                <?= $toEnum ? $toEnum->shortLabel() : htmlspecialchars($log['to_status']) ?>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                โดย: <strong class="text-slate-700"><?= htmlspecialchars($log['changer_name']) ?></strong> (<?= htmlspecialchars($log['changer_role']) ?>)
                            </div>
                            <?php if (!empty($log['note'])): ?>
                                <div class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-lg border border-slate-100 mt-1.5 leading-relaxed">
                                    <?= htmlspecialchars($log['note']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-[10px] text-slate-400 font-mono mt-1">
                                <?= date('d/m/Y H:i:s น.', strtotime($log['created_at'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================================================================= -->
<!-- ACTION MODALS (Tailwind Modals) -->
<!-- ================================================================= -->

<!-- Modal 1: Accept Job (Assigned -> InProgress) -->
<div id="acceptModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-play text-blue-600 text-xs"></i>
                <span>ยืนยันการรับงานซ่อม</span>
            </h3>
            <button type="button" onclick="closeModal('acceptModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="p-4 sm:p-5 space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="to_status" value="in_progress">

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                ท่านต้องการยืนยันรับงาน <strong><?= $ticketCode ?></strong> และเริ่มเข้าดำเนินการตรวจสอบใช่หรือไม่?
            </p>

            <div>
                <label for="accept_note" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">
                    บันทึกข้อความเริ่มต้น (ไม่บังคับ)
                </label>
                <input type="text" id="accept_note" name="note" placeholder="เช่น กำลังเตรียมอุปกรณ์เข้าไปตรวจสอบหน้างาน..."
                       class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('acceptModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">ยืนยันเริ่มงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Resolve Job (InProgress -> Resolved) -->
<div id="resolveModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-check-double text-emerald-600 text-xs"></i>
                <span>ปิดงานซ่อมและส่งมอบให้ผู้แจ้งตรวจรับ</span>
            </h3>
            <button type="button" onclick="closeModal('resolveModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" enctype="multipart/form-data" class="p-4 sm:p-5 space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="to_status" value="resolved">

            <div>
                <label for="resolve_note" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">
                    สรุปรายละเอียดการแก้ไขปัญหา <span class="text-red-500">*</span>
                </label>
                <textarea id="resolve_note" name="note" rows="3" required
                          placeholder="ระบุอะไหล่ที่เปลี่ยน หรือวิธีการแก้ไขปัญหาให้เรียบร้อย..."
                          class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">
                    รูปภาพหลักฐานผลการซ่อมเสร็จ
                </label>
                <div onclick="document.getElementById('resolve_img_input').click()" class="border-2 border-dashed border-slate-200 hover:border-emerald-500 rounded-xl p-4 text-center cursor-pointer bg-slate-50/50">
                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-1 block"></i>
                    <p class="text-xs font-semibold text-slate-700">คลิกเพื่อเลือกภาพถ่ายผลงานการซ่อม</p>
                    <span id="resolve_img_badge" class="text-[11px] text-slate-400 font-mono">JPG, PNG หรือ WebP</span>
                    <input type="file" id="resolve_img_input" name="image" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="document.getElementById('resolve_img_badge').textContent = this.files[0]?.name || '';">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('resolveModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">บันทึกและส่งมอบงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 3: Rate & Close Job (Resolved -> Closed) -->
<div id="rateModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                <span>ตรวจรับงานและประเมินความพึงพอใจ</span>
            </h3>
            <button type="button" onclick="closeModal('rateModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="p-4 sm:p-5 space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="to_status" value="closed">
            <input type="hidden" id="rating_score" name="score" value="5">

            <div class="text-center py-2">
                <p class="text-xs text-slate-500 mb-2">โปรดให้คะแนนความพึงพอใจต่อการปฏิบัติงานของช่าง</p>
                <div class="flex items-center justify-center gap-1.5 text-2xl text-slate-300 cursor-pointer select-none" id="starRatingBox">
                    <span class="star text-amber-400 hover:scale-110 transition-transform" data-val="1">★</span>
                    <span class="star text-amber-400 hover:scale-110 transition-transform" data-val="2">★</span>
                    <span class="star text-amber-400 hover:scale-110 transition-transform" data-val="3">★</span>
                    <span class="star text-amber-400 hover:scale-110 transition-transform" data-val="4">★</span>
                    <span class="star text-amber-400 hover:scale-110 transition-transform" data-val="5">★</span>
                </div>
                <span class="text-[11px] text-slate-400 mt-1 block">(คลิกเลือก 1 - 5 ดาว)</span>
            </div>

            <div>
                <label for="rate_feedback" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">
                    ข้อคิดเห็นหรือคำชื่นชมเพิ่มเติม (ไม่บังคับ)
                </label>
                <textarea id="rate_feedback" name="feedback" rows="3"
                          placeholder="ช่างบริการดี รวดเร็ว เรียบร้อย..."
                          class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-600"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('rateModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">ยืนยันตรวจรับและปิดงาน</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 4: Rework Job (Resolved -> InProgress) -->
<div id="reworkModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-rotate-left text-red-600 text-xs"></i>
                <span>ส่งงานกลับให้ช่างแก้ไขเพิ่มเติม</span>
            </h3>
            <button type="button" onclick="closeModal('reworkModal')" class="text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/tickets/<?= $ticket['id'] ?>/status" method="POST" class="p-4 sm:p-5 space-y-4">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="to_status" value="in_progress">

            <p class="text-xs text-red-700 bg-red-50 p-2.5 rounded-xl border border-red-100">
                หากอาการขัดข้องยังไม่หาย หรือผลงานยังไม่สมบูรณ์ ท่านสามารถส่งงานกลับให้ช่างดำเนินการแก้ไขต่อได้
            </p>

            <div>
                <label for="rework_note" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1">
                    ระบุเหตุผลในการส่งกลับแก้ไข <span class="text-red-500">*</span>
                </label>
                <textarea id="rework_note" name="note" rows="3" required
                          placeholder="เช่น ทดสอบเปิดเครื่องแล้วยังมีเสียงเตือนดังอยู่..."
                          class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-red-600"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('reworkModal')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-50">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-semibold">ยืนยันส่งกลับแก้ไข</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('hidden');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('hidden');
}

// Star Rating click listener
document.addEventListener('DOMContentLoaded', () => {
    const stars = document.querySelectorAll('#starRatingBox .star');
    const scoreInput = document.getElementById('rating_score');

    stars.forEach(star => {
        star.addEventListener('click', () => {
            const val = parseInt(star.getAttribute('data-val'), 10);
            if (scoreInput) scoreInput.value = val;

            stars.forEach((s, idx) => {
                if (idx < val) {
                    s.classList.add('text-amber-400');
                    s.classList.remove('text-slate-300');
                } else {
                    s.classList.remove('text-amber-400');
                    s.classList.add('text-slate-300');
                }
            });
        });
    });
});
</script>
