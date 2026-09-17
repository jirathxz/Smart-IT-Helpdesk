<?php
use App\Enums\UserRole;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-users-gear text-blue-600 text-xl"></i>
                <span>จัดการผู้ใช้งานในระบบ (Users)</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">รายชื่อผู้ใช้งานทั้งหมด พร้อมกำหนดบทบาทสิทธิ์ (Admin, Technician, User)</p>
        </div>
        <button onclick="document.getElementById('createUserModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm shadow-blue-500/25 transition-all flex items-center gap-2 self-start sm:self-auto">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>เพิ่มผู้ใช้งานใหม่</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl bg-white border border-slate-200/90 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-slate-500">
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
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    <?php foreach ($users as $user): 
                        $roleEnum = UserRole::tryFrom($user['role']);
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-3.5 font-mono text-slate-400">#<?= $user['id'] ?></td>
                            <td class="p-3.5 font-semibold text-slate-900"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="p-3.5 text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border text-[11px] font-semibold <?= $roleEnum ? $roleEnum->badgeColor() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <i class="fa-solid fa-shield-halved text-[9px]"></i>
                                    <?php elseif ($user['role'] === 'technician'): ?>
                                        <i class="fa-solid fa-wrench text-[9px]"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-user text-[9px]"></i>
                                    <?php endif; ?>
                                    <?= $roleEnum ? $roleEnum->label() : $user['role'] ?>
                                </span>
                            </td>
                            <td class="p-3.5 font-mono text-slate-500"><?= htmlspecialchars($user['line_user_id'] ?: '-') ?></td>
                            <td class="p-3.5 text-slate-400 font-mono"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td class="p-3.5 text-right">
                                <?php if ($user['id'] !== \App\Core\Auth::id()): ?>
                                    <form action="/admin/users/<?= $user['id'] ?>/delete" method="POST" onsubmit="return confirm('คุณแน่ใจว่าต้องการลบผู้ใช้งานนี้?');" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-2.5 py-1 rounded-lg font-semibold text-xs transition-colors">
                                            <i class="fa-solid fa-trash-can mr-1"></i>ลบ
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-slate-400 text-[11px] italic bg-slate-100 px-2 py-0.5 rounded-md">ฉันเอง</span>
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
<div id="createUserModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-600"></i>
                <span>เพิ่มผู้ใช้งานใหม่</span>
            </h3>
            <button onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-base" aria-label="ปิด">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/admin/users" method="POST" class="space-y-3.5">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">ชื่อ - นามสกุล</label>
                <input type="text" name="name" required placeholder="สมชาย ใจดี" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">อีเมล</label>
                <input type="email" name="email" required placeholder="name@company.com" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)</label>
                <input type="password" name="password" required minlength="6" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">บทบาท (Role)</label>
                <select name="role" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600">
                    <option value="user">ผู้ใช้งานทั่วไป (User)</option>
                    <option value="technician">ช่างเทคนิค (Technician)</option>
                    <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">LINE User ID (ถ้ามี)</label>
                <input type="text" name="line_user_id" placeholder="U1234567890abcdef..." class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white shadow-sm shadow-blue-500/25">บันทึก</button>
            </div>
        </form>
    </div>
</div>
