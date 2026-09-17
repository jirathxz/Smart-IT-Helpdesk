<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">หมวดหมู่งานซ่อม (Categories)</h1>
            <p class="text-sm text-slate-400 mt-1">จัดการประเภทอุปกรณ์และปัญหา เพื่อใช้คัดแยกงานซ่อม</p>
        </div>
        <button onclick="document.getElementById('createCategoryModal').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            เพิ่มหมวดหมู่ใหม่
        </button>
    </div>

    <!-- Categories Table -->
    <div class="rounded-2xl bg-slate-900/80 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400">
                    <tr>
                        <th class="p-3.5 font-semibold">ID</th>
                        <th class="p-3.5 font-semibold">ชื่อหมวดหมู่</th>
                        <th class="p-3.5 font-semibold">คำอธิบาย</th>
                        <th class="p-3.5 font-semibold text-center">จำนวนตั๋วงานซ่อม</th>
                        <th class="p-3.5 font-semibold text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="p-3.5 font-mono text-slate-400"><?= $cat['id'] ?></td>
                            <td class="p-3.5 font-semibold text-white"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="p-3.5 text-slate-400"><?= htmlspecialchars($cat['description'] ?: '-') ?></td>
                            <td class="p-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20 font-bold font-mono">
                                    <?= $cat['ticket_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <form action="/admin/categories/<?= $cat['id'] ?>/delete" method="POST" onsubmit="return confirm('คุณแน่ใจว่าต้องการลบหมวดหมู่นี้?');" class="inline">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" class="text-rose-400 hover:text-rose-300 font-semibold text-xs">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div id="createCategoryModal" class="hidden fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-base font-bold text-white">เพิ่มหมวดหมู่ใหม่</h3>
            <button onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>
        <form action="/admin/categories" method="POST" class="space-y-3">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label class="block text-xs text-slate-400 mb-1">ชื่อหมวดหมู่</label>
                <input type="text" name="name" required placeholder="เช่น Network & Wi-Fi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white">
            </div>
            <div>
                <label class="block text-xs text-slate-400 mb-1">คำอธิบายหมวดหมู่</label>
                <textarea name="description" rows="3" placeholder="ระบุประเภทอุปกรณ์ที่ครอบคลุม..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3">
                <button type="button" onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="px-3 py-1.5 rounded-lg bg-slate-800 text-xs text-slate-300">ยกเลิก</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white">บันทึก</button>
            </div>
        </form>
    </div>
</div>
