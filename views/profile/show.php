<?php
$roleLabel = match ($user['role']) {
    'admin'      => 'ผู้ดูแลระบบ (Administrator)',
    'technician' => 'ช่างเทคนิค (Technician)',
    default      => 'ผู้ใช้งานทั่วไป (User)',
};
$roleBadge = match ($user['role']) {
    'admin'      => 'bg-slate-900 text-white',
    'technician' => 'bg-amber-500 text-white',
    default      => 'bg-blue-600 text-white',
};
$initial = mb_substr($user['name'], 0, 1, 'UTF-8');
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header: Profile Identity Summary -->
    <div class="bg-white border border-slate-200 rounded-xl p-5 sm:p-6 shadow-2xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-slate-900 text-white flex items-center justify-center text-xl font-bold select-none shadow-sm">
                    <?= htmlspecialchars($initial) ?>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-semibold text-slate-900"><?= htmlspecialchars($user['name']) ?></h1>
                        <span class="text-[10px] font-mono uppercase tracking-wider px-2 py-0.5 rounded-full font-medium <?= $roleBadge ?>">
                            <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-2">
                        <span><?= htmlspecialchars($user['email']) ?></span>
                        <span class="text-slate-300">&bull;</span>
                        <span><?= $roleLabel ?></span>
                        <span class="text-slate-300">&bull;</span>
                        <span>เริ่มใช้งานเมื่อ <?= date('d M Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="#security" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                    <i class="fa-solid fa-lock text-slate-400 text-[11px]"></i>
                    <span>เปลี่ยนรหัสผ่าน</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Role-based Statistics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php if ($user['role'] === 'technician'): ?>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">งานทั้งหมดที่รับ</span>
                <div class="text-2xl font-semibold text-slate-900 font-mono mt-1"><?= $stats['total_assigned'] ?></div>
                <div class="text-[10px] text-slate-400 mt-0.5">ตลอดอายุการทำงาน</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-amber-600 uppercase tracking-wider">งานที่กำลังทำ</span>
                <div class="text-2xl font-semibold text-amber-600 font-mono mt-1"><?= $stats['active_assigned'] ?></div>
                <div class="text-[10px] text-slate-400 mt-0.5">งานค้างในมือปัจจุบัน</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">ปิดงานสำเร็จ</span>
                <div class="text-2xl font-semibold text-emerald-600 font-mono mt-1"><?= $stats['resolved_jobs'] ?></div>
                <div class="text-[10px] text-slate-400 mt-0.5">ซ่อมเสร็จสิ้น</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-yellow-600 uppercase tracking-wider">คะแนน CSAT</span>
                <div class="text-2xl font-semibold text-slate-900 font-mono mt-1">
                    <?= $stats['avg_rating'] > 0 ? number_format($stats['avg_rating'], 1) : '—' ?>
                    <span class="text-amber-500 text-xs">★</span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">ความพึงพอใจเฉลี่ย</div>
            </div>
        <?php else: ?>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">ตั๋วที่เคยแจ้ง</span>
                <div class="text-2xl font-semibold text-slate-900 font-mono mt-1"><?= $stats['total_created'] ?></div>
                <div class="text-[10px] text-slate-400 mt-0.5">รายการทั้งหมด</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-sky-600 uppercase tracking-wider">กำลังดำเนินการ</span>
                <div class="text-2xl font-semibold text-sky-600 font-mono mt-1"><?= $stats['active_created'] ?></div>
                <div class="text-[10px] text-slate-400 mt-0.5">รอดำเนินการ/ซ่อม</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">ซ่อมเสร็จสิ้น</span>
                <div class="text-2xl font-semibold text-emerald-600 font-mono mt-1">
                    <?= max(0, $stats['total_created'] - $stats['active_created']) ?>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">แก้ไขเรียบร้อยแล้ว</div>
            </div>
            <div class="bg-white border border-slate-200 rounded-lg p-3.5 shadow-2xs">
                <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">สถานะระบบ</span>
                <div class="text-xs font-semibold text-emerald-600 mt-2 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>ใช้งานได้ปกติ</span>
                </div>
                <div class="text-[10px] text-slate-400 mt-0.5">Active account</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Section 1: Edit Profile & LINE Notification Settings -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">ข้อมูลส่วนตัวและการแจ้งเตือน (Personal Information)</h2>
            <span class="text-[11px] text-slate-500">จัดการข้อมูลชื่อบัญชี และรหัส LINE สำหรับรับการแจ้งเตือนงานแจ้งซ่อม</span>
        </div>

        <form action="/profile" method="POST" class="p-5 sm:p-6 space-y-4">
            <?= \App\Core\Csrf::field() ?>

            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-700 mb-1">
                        ชื่อ-นามสกุล <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="<?= htmlspecialchars($user['name']) ?>" 
                           required 
                           class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 font-medium shadow-2xs">
                </div>

                <!-- Email Field (Read-only) -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1">
                        อีเมลล็อกอิน (ไม่สามารถเปลี่ยนได้)
                    </label>
                    <input type="email" 
                           id="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" 
                           disabled 
                           class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-500 font-mono cursor-not-allowed">
                </div>
            </div>

            <!-- LINE User ID Field -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="line_user_id" class="text-xs font-medium text-slate-700">
                        LINE User ID (สำหรับการแจ้งเตือนส่วนบุคคล)
                    </label>
                    <span class="text-[10px] text-slate-400">ตัวอย่าง: U1234567890abcdef1234567890abcdef</span>
                </div>
                <div class="relative">
                    <input type="text" 
                           id="line_user_id" 
                           name="line_user_id" 
                           value="<?= htmlspecialchars($user['line_user_id'] ?? '') ?>" 
                           placeholder="ระบุ LINE User ID เพื่อรับการแจ้งเตือนตั๋วงาน" 
                           class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 font-mono shadow-2xs">
                </div>
                <p class="text-[11px] text-slate-400 mt-1">
                    * เมื่อกรอก LINE User ID ระบบจะส่งข้อความแจ้งเตือนสถานะตั๋วและการมอบหมายงานตรงเข้าแอปพลิเคชัน LINE ของคุณทันที
                </p>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-2xs transition-colors">
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>

    <!-- Section 2: Security & Password Settings -->
    <div id="security" class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 border-b border-slate-200">
            <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">ความปลอดภัยและรหัสผ่าน (Security & Password)</h2>
            <span class="text-[11px] text-slate-500">เปลี่ยนรหัสผ่านเพื่อรักษาความปลอดภัยของบัญชีผู้ใช้</span>
        </div>

        <form action="/profile/password" method="POST" class="p-5 sm:p-6 space-y-4">
            <?= \App\Core\Csrf::field() ?>

            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-xs font-medium text-slate-700 mb-1">
                    รหัสผ่านปัจจุบัน <span class="text-rose-500">*</span>
                </label>
                <input type="password" 
                       id="current_password" 
                       name="current_password" 
                       required 
                       placeholder="••••••••" 
                       class="w-full sm:w-80 bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 shadow-2xs">
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-xs font-medium text-slate-700 mb-1">
                        รหัสผ่านใหม่ (อย่างน้อย 6 ตัวอักษร) <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           id="new_password" 
                           name="new_password" 
                           required 
                           minlength="6" 
                           placeholder="••••••••" 
                           class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 shadow-2xs">
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="confirm_password" class="block text-xs font-medium text-slate-700 mb-1">
                        ยืนยันรหัสผ่านใหม่ <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required 
                           minlength="6" 
                           placeholder="••••••••" 
                           class="w-full bg-white border border-slate-200 rounded-lg px-3 py-2 text-xs text-slate-900 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 shadow-2xs">
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-medium rounded-lg shadow-2xs transition-colors">
                    อัปเดตรหัสผ่าน
                </button>
            </div>
        </form>
    </div>

    <!-- Section 3: Recent Ticket History for this User -->
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-2xs">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h2 class="text-xs font-semibold text-slate-900 uppercase tracking-wider">
                    <?= $user['role'] === 'technician' ? 'งานที่ได้รับมอบหมายล่าสุด' : 'ประวัติตั๋วงานของคุณล่าสุด' ?>
                </h2>
                <span class="text-[11px] text-slate-500">5 รายการล่าสุด</span>
            </div>
            <a href="/tickets" class="text-xs font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <span>ดูทั้งหมด</span>
                <i class="fa-solid fa-arrow-right text-[10px]" aria-hidden="true"></i>
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            <?php if (empty($stats['recent_tickets'])): ?>
                <div class="p-6 text-center text-xs text-slate-400">ยังไม่มีประวัติตั๋วงานในระบบ</div>
            <?php else: ?>
                <?php foreach ($stats['recent_tickets'] as $ticket): ?>
                    <div class="p-3.5 sm:px-5 hover:bg-slate-50/60 transition-colors flex items-center justify-between gap-3 text-xs">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-slate-400">#<?= $ticket['id'] ?></span>
                                <a href="/tickets/<?= $ticket['id'] ?>" class="font-medium text-slate-900 hover:text-blue-600 truncate">
                                    <?= htmlspecialchars($ticket['title']) ?>
                                </a>
                                <?php if ($ticket['priority'] === 'urgent'): ?>
                                    <span class="text-[9px] bg-rose-50 text-rose-700 border border-rose-200 px-1.5 rounded font-semibold">Urgent</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                <?= htmlspecialchars($ticket['category_name']) ?> &bull; <?= date('d M Y, H:i', strtotime($ticket['created_at'])) ?>
                            </div>
                        </div>

                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full border bg-slate-50 text-slate-600 border-slate-200">
                            <?= htmlspecialchars($ticket['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
