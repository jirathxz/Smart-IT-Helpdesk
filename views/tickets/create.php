<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-circle-plus text-blue-600 text-xl"></i>
                <span>เปิดตั๋วแจ้งซ่อม / ปัญหาใหม่</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">กรอกรายละเอียดอุปกรณ์และปัญหาเพื่อให้เจ้าหน้าที่เข้าดำเนินการ</p>
        </div>
        <a href="/tickets" class="text-xs font-medium text-slate-600 hover:text-slate-900 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 shadow-sm">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>ยกเลิก</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="p-6 sm:p-8 rounded-2xl bg-white border border-slate-200/90 shadow-sm">
        <form action="/tickets" method="POST" enctype="multipart/form-data" class="space-y-5">
            <?= \App\Core\Csrf::field() ?>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-slate-400 text-xs"></i>
                    <span>หัวข้อปัญหา / อุปกรณ์ที่ชำรุด</span>
                    <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="title" name="title" required placeholder="เช่น จอคอมพิวเตอร์แผนกบัญชีเปิดไม่ติด, Wi-Fi ชั้น 2 หลุดบ่อย" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
            </div>

            <!-- Two-col category and priority -->
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-tags text-slate-400 text-xs"></i>
                        <span>หมวดหมู่งานซ่อม</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 transition-all">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-gauge-high text-slate-400 text-xs"></i>
                        <span>ระดับความเร่งด่วน</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <select id="priority" name="priority" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 transition-all">
                        <option value="low">ต่ำ (Low) - ไม่กระทบงาน</option>
                        <option value="medium" selected>ปานกลาง (Medium) - กระทบบางส่วน</option>
                        <option value="high">สูง (High) - ไม่สามารถทำงานได้</option>
                        <option value="urgent">เร่งด่วนที่สุด (Urgent) - ระบบหลักล่ม</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-align-left text-slate-400 text-xs"></i>
                    <span>รายละเอียดอาการ / สถานที่ตั้งอุปกรณ์</span>
                    <span class="text-rose-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required placeholder="อธิบายอาการอย่างละเอียด ระบุชั้น ห้อง หรือหมายเลขอุปกรณ์..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all"></textarea>
            </div>

            <!-- Image Upload with Live Preview -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-camera text-slate-400 text-xs"></i>
                    <span>แนบรูปภาพอาการปัญหา (ไม่บังคับ - สูงสุด 5MB)</span>
                </label>
                <div class="border-2 border-dashed border-slate-300 hover:border-blue-400 bg-slate-50/50 hover:bg-blue-50/20 rounded-2xl p-6 text-center cursor-pointer relative transition-all">
                    <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div id="uploadPrompt" class="space-y-1.5">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-blue-500 mx-auto"></i>
                        <p class="text-xs text-slate-700 font-medium">คลิกเพื่อเลือกไฟล์รูปภาพ หรือลากไฟล์มาวางที่นี่</p>
                        <p class="text-[11px] text-slate-400">รองรับไฟล์ JPG, PNG, WEBP ขนาดไม่เกิน 5MB</p>
                    </div>
                    <img id="imagePreview" class="hidden mx-auto max-h-52 rounded-xl mt-3 object-contain shadow-sm border border-slate-200" alt="พรีวิวรูปภาพ">
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>ยืนยันการเปิดตั๋วแจ้งซ่อม</span>
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
