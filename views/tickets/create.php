<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">เปิดตั๋วแจ้งซ่อม / ปัญหาใหม่</h1>
            <p class="text-sm text-slate-400 mt-1">กรอกรายละเอียดอุปกรณ์และปัญหาเพื่อให้เจ้าหน้าที่เข้าดำเนินการ</p>
        </div>
        <a href="/tickets" class="text-xs text-slate-400 hover:text-white px-3 py-1.5 rounded-lg bg-slate-800 transition-all">
            &larr; ยกเลิก
        </a>
    </div>

    <!-- Form Card -->
    <div class="p-6 sm:p-8 rounded-2xl bg-slate-900/80 border border-slate-800 shadow-xl">
        <form action="/tickets" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    หัวข้อปัญหา / อุปกรณ์ที่ชำรุด <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required placeholder="เช่น จอคอมพิวเตอร์แผนกบัญชีเปิดไม่ติด, Wi-Fi ชั้น 2 หลุดบ่อย" 
                       class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <!-- Two-col category and priority -->
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        หมวดหมู่งานซ่อม <span class="text-rose-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required 
                            class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        ระดับความเร่งด่วน <span class="text-rose-500">*</span>
                    </label>
                    <select id="priority" name="priority" required 
                            class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500">
                        <option value="low">ต่ำ (Low) - ไม่กระทบงาน</option>
                        <option value="medium" selected>ปานกลาง (Medium) - กระทบบางส่วน</option>
                        <option value="high">สูง (High) - ไม่สามารถทำงานได้</option>
                        <option value="urgent">เร่งด่วนที่สุด (Urgent) - ระบบหลักล่ม</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    รายละเอียดอาการ / สถานที่ตั้งอุปกรณ์ <span class="text-rose-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required placeholder="อธิบายอาการอย่างละเอียด ระบุชั้น ห้อง หรือหมายเลขอุปกรณ์..." 
                          class="w-full px-3.5 py-2.5 bg-slate-950/70 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"></textarea>
            </div>

            <!-- Image Upload with Live Preview -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    แนบรูปภาพอาการปัญหา (ไม่บังคับ - สูงสุด 5MB)
                </label>
                <div class="border-2 border-dashed border-slate-800 hover:border-slate-700 rounded-xl p-4 text-center cursor-pointer relative bg-slate-950/40">
                    <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div id="uploadPrompt" class="space-y-1">
                        <svg class="w-8 h-8 text-slate-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-xs text-slate-400 font-medium">คลิกเพื่อเลือกไฟล์รูปภาพ หรือลากไฟล์มาวางที่นี่</p>
                        <p class="text-[10px] text-slate-500">รองรับไฟล์ JPG, PNG, WEBP ขนาดไม่เกิน 5MB</p>
                    </div>
                    <img id="imagePreview" class="hidden mx-auto max-h-48 rounded-lg mt-2 object-contain shadow-md" alt="พรีวิวรูปภาพ">
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    ยืนยันการเปิดตั๋วแจ้งซ่อม
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const prompt = document.getElementById('uploadPrompt');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            prompt.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
