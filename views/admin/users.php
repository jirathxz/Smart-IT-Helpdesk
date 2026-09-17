<?php
use App\Enums\UserRole;
?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-users-gear text-blue-600 text-xl" aria-hidden="true"></i>
                <span>จัดการผู้ใช้งานในระบบ (Users)</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">รายชื่อผู้ใช้งานทั้งหมด พร้อมกำหนดบทบาทสิทธิ์ (Admin, Technician, User)</p>
        </div>
        <button onclick="document.getElementById('createUserModal').classList.remove('hidden')" class="min-h-[40px] px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs shadow-blue-500/25 transition-all flex items-center gap-2 self-start sm:self-auto focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
            <i class="fa-solid fa-user-plus text-xs" aria-hidden="true"></i>
            <span>เพิ่มผู้ใช้งานใหม่</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <caption class="sr-only">ตารางรายชื่อผู้ใช้งานในระบบ</caption>
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                    <tr>
                        <th scope="col" class="p-3.5 font-semibold">ID</th>
                        <th scope="col" class="p-3.5 font-semibold">ชื่อ - นามสกุล</th>
                        <th scope="col" class="p-3.5 font-semibold">อีเมล</th>
                        <th scope="col" class="p-3.5 font-semibold">บทบาท (Role)</th>
                        <th scope="col" class="p-3.5 font-semibold">LINE User ID</th>
                        <th scope="col" class="p-3.5 font-semibold">สร้างเมื่อ</th>
                        <th scope="col" class="p-3.5 font-semibold text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    <?php foreach ($users as $user): 
                        $roleEnum = UserRole::tryFrom($user['role']);
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-3.5 font-mono text-slate-400 tabular-nums">#<?= $user['id'] ?></td>
                            <td class="p-3.5 font-semibold text-slate-900"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="p-3.5 text-slate-600"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full border text-[11px] font-semibold <?= $roleEnum ? $roleEnum->badgeColor() : 'bg-slate-100 text-slate-700 border-slate-200' ?>">
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <i class="fa-solid fa-shield-halved text-[9px]" aria-hidden="true"></i>
                                    <?php elseif ($user['role'] === 'technician'): ?>
                                        <i class="fa-solid fa-wrench text-[9px]" aria-hidden="true"></i>
                                    <?php else: ?>
                                        <i class="fa-solid fa-user text-[9px]" aria-hidden="true"></i>
                                    <?php endif; ?>
                                    <?= $roleEnum ? $roleEnum->label() : $user['role'] ?>
                                </span>
                            </td>
                            <td class="p-3.5 font-mono text-slate-500"><?= htmlspecialchars($user['line_user_id'] ?: '-') ?></td>
                            <td class="p-3.5 text-slate-400 font-mono tabular-nums"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                            <td class="p-3.5 text-right">
                                <?php if ($user['id'] !== \App\Core\Auth::id()): ?>
                                    <form action="/admin/users/<?= $user['id'] ?>/delete" method="POST" onsubmit="return confirm('ยืนยันการลบผู้ใช้งาน <?= addslashes($user['name']) ?> ออกจากระบบ?');" class="inline">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button type="submit" aria-label="ลบผู้ใช้ <?= htmlspecialchars($user['name']) ?>" class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-2.5 py-1 rounded-lg font-semibold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600">
                                            <i class="fa-solid fa-trash-can mr-1" aria-hidden="true"></i>ลบ
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-slate-400 text-[11px] italic bg-slate-100 px-2.5 py-1 rounded-md">บัญชีปัจจุบัน</span>
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
<div id="createUserModal" role="dialog" aria-modal="true" aria-labelledby="createUserModalTitle" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h2 id="createUserModalTitle" class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-blue-600" aria-hidden="true"></i>
                <span>เพิ่มผู้ใช้งานใหม่</span>
            </h2>
            <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded" aria-label="ปิดหน้าต่าง">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <form action="/admin/users" method="POST" class="space-y-3.5">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label for="new_user_name" class="block text-xs font-semibold text-slate-700 mb-1">ชื่อ - นามสกุล</label>
                <input type="text" id="new_user_name" name="name" required autocomplete="name" placeholder="สมชาย ใจดี…" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20">
            </div>
            <div>
                <label for="new_user_email" class="block text-xs font-semibold text-slate-700 mb-1">อีเมล</label>
                <input type="email" id="new_user_email" name="email" required autocomplete="email" spellcheck="false" inputmode="email" placeholder="name@company.com…" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20">
            </div>
            <div>
                <label for="new_user_password" class="block text-xs font-semibold text-slate-700 mb-1">รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)</label>
                <input type="password" id="new_user_password" name="password" required minlength="6" autocomplete="new-password" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20">
            </div>
            <div>
                <label for="new_user_role" class="block text-xs font-semibold text-slate-700 mb-1">บทบาทสิทธิ์ (Role)</label>
                <select id="new_user_role" name="role" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600">
                    <option value="user">ผู้ใช้งานทั่วไป (User)</option>
                    <option value="technician">ช่างเทคนิค (Technician)</option>
                    <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                </select>
            </div>
            <div>
                <label for="new_user_line" class="block text-xs font-semibold text-slate-700 mb-1">LINE User ID (ถ้ามี)</label>
                <input type="text" id="new_user_line" name="line_user_id" autocomplete="off" placeholder="U1234567890abcdef…" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createUserModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white shadow-xs shadow-blue-500/25">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>
