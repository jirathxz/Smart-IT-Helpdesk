<?php
use App\Enums\UserRole;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">จัดการผู้ใช้งานในระบบ (Users)</h1>
            <p class="text-sm text-slate-400 mt-1">รายชื่อผู้ใช้งานทั้งหมด พร้อมกำหนดบทบาทสิทธิ์ (Admin, Technician, User)</p>
        </div>
        <button onclick="document.getElementById('createUserModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            เพิ่มผู้ใช้งานใหม่
        </button>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400">
                    <tr>
                        <th class="p-3.5 font-semibold">ID</th>
                        <th class="p-3.5 font-semibold">ชื่อ - นามสกุล</th>
                        <th class="p-3.5 font-semibold">อีเมล</th>
                        <th class="p-3.5 font-semibold">บทบาท (Role)</th>
                        <th class="p-3.5 font-semibold">LINE User ID</th>
                        <th class="p-3.5 font-semibold">สร้างเมื่อ</th>
                        <th class="p-3.5 font-semibold text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    <?php foreach ($users as $user): 
                        $roleEnum = UserRole::tryFrom($user['role']);
                    ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="p-3.5 font-mono text-slate-400"><?= $user['id'] ?></td>
                            <td class="p-3.5 font-semibold text-white"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="p-3.5"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-0.5 rounded-full border text-[11px] font-semibold <?= $roleEnum ? $roleEnum->badgeColor() : '' ?>">
                                    <?= $roleEnum ? $roleEnum->label() : $user['role'] ?>
                                </span>
                            </td>
                            <td class="p-3.5 font-mono text-slate-400"><?= htmlspecialchars($user['line_user_id'] ?: '-') ?></td>
                            <td class="p-3.5 text-slate-400"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td class="p-3.5 text-right">
                                <?php if ($user['id'] !== \App\Core\Auth::id()): ?>
                                    <form action="/admin/users/<?= $user['id'] ?>/delete" method="POST" onsubmit="return confirm('คุณแน่ใจว่าต้องการลบผู้ใช้งานนี้?');" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">ลบ</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-slate-500 italic">ฉันเอง</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div id="createUserModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">เพิ่มผู้ใช้งานใหม่</h3>
            <button onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="/admin/users" method="POST" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs text-slate-400 mb-1">ชื่อ - นามสกุล</label>
                <input type="text" name="name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">อีเมล</label>
                <input type="email" name="email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">รหัสผ่าน</label>
                <input type="password" name="password" required minlength="6" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">บทบาท (Role)</label>
                <select name="role" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
                    <option value="user">ผู้ใช้งานทั่วไป (User)</option>
                    <option value="technician">ช่างเทคนิค (Technician)</option>
                    <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">LINE User ID (ถ้ามี)</label>
                <input type="text" name="line_user_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">บันทึก</button>
            </div>
        </form>
    </div>
</div>
