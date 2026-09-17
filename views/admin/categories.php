<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-tags text-blue-600 text-xl" aria-hidden="true"></i>
                <span>หมวดหมู่งานซ่อม (Categories)</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">จัดการประเภทอุปกรณ์และระบบ เพื่อจัดกลุ่มและคัดแยกงานซ่อมอย่างมีประสิทธิภาพ</p>
        </div>
        <button onclick="document.getElementById('createCategoryModal').classList.remove('hidden')" class="min-h-[40px] px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-xs shadow-blue-500/25 transition-all flex items-center gap-2 self-start sm:self-auto focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
            <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
            <span>เพิ่มหมวดหมู่ใหม่</span>
        </button>
    </div>

    <!-- Categories Table -->
    <div class="rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <caption class="sr-only">ตารางแสดงหมวดหมู่งานซ่อมทั้งหมด</caption>
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500">
                    <tr>
                        <th scope="col" class="p-3.5 font-semibold">ID</th>
                        <th scope="col" class="p-3.5 font-semibold">ชื่อหมวดหมู่</th>
                        <th scope="col" class="p-3.5 font-semibold">คำอธิบาย</th>
                        <th scope="col" class="p-3.5 font-semibold text-center">จำนวนตั๋วงานซ่อม</th>
                        <th scope="col" class="p-3.5 font-semibold text-right">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">
                    <?php foreach ($categories as $cat): ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="p-3.5 font-mono text-slate-400 tabular-nums">#<?= $cat['id'] ?></td>
                            <td class="p-3.5 font-semibold text-slate-900"><?= htmlspecialchars($cat['name']) ?></td>
                            <td class="p-3.5 text-slate-500"><?= htmlspecialchars($cat['description'] ?: '-') ?></td>
                            <td class="p-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold font-mono text-[11px] tabular-nums">
                                    <?= number_format($cat['ticket_count'] ?? 0) ?>
                                </span>
                            </td>
                            <td class="p-3.5 text-right">
                                <form action="/admin/categories/<?= $cat['id'] ?>/delete" method="POST" onsubmit="return confirm('ยืนยันการลบหมวดหมู่ <?= addslashes($cat['name']) ?>?');" class="inline">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button type="submit" aria-label="ลบหมวดหมู่ <?= htmlspecialchars($cat['name']) ?>" class="text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-2.5 py-1 rounded-lg font-semibold text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-600">
                                        <i class="fa-solid fa-trash-can mr-1" aria-hidden="true"></i>ลบ
                                    </button>
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
<div id="createCategoryModal" role="dialog" aria-modal="true" aria-labelledby="createCatModalTitle" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
            <h2 id="createCatModalTitle" class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-folder-plus text-blue-600" aria-hidden="true"></i>
                <span>เพิ่มหมวดหมู่ใหม่</span>
            </h2>
            <button type="button" onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded" aria-label="ปิดหน้าต่าง">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
        <form action="/admin/categories" method="POST" class="space-y-3.5">
            <?= \App\Core\Csrf::field() ?>
            <div>
                <label for="new_cat_name" class="block text-xs font-semibold text-slate-700 mb-1">ชื่อหมวดหมู่</label>
                <input type="text" id="new_cat_name" name="name" required placeholder="เช่น Network & Wi-Fi…" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20">
            </div>
            <div>
                <label for="new_cat_desc" class="block text-xs font-semibold text-slate-700 mb-1">คำอธิบายหมวดหมู่</label>
                <textarea id="new_cat_desc" name="description" rows="3" placeholder="ระบุประเภทปัญหาหรืออุปกรณ์ที่ครอบคลุม…" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2 text-xs text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createCategoryModal').classList.add('hidden')" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-600 hover:bg-slate-100">ยกเลิก</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white shadow-xs shadow-blue-500/25">บันทึกข้อมูล</button>
            </div>
        </form>
    </div>
</div>
