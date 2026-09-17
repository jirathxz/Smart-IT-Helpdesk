<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2.5">
                <i class="fa-solid fa-circle-plus text-blue-600 text-xl" aria-hidden="true"></i>
                <span>เปิดตั๋วแจ้งซ่อม / ปัญหาใหม่</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">กรอกรายละเอียดอุปกรณ์และปัญหาเพื่อให้เจ้าหน้าที่เข้าดำเนินการตามลำดับความเร่งด่วน</p>
        </div>
        <a href="/tickets" class="text-xs font-medium text-slate-600 hover:text-slate-900 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-1.5 shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600">
            <i class="fa-solid fa-arrow-left text-[10px]" aria-hidden="true"></i>
            <span>ยกเลิก</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="p-6 sm:p-8 rounded-2xl bg-white border border-slate-200 shadow-xs">
        <form action="/tickets" method="POST" enctype="multipart/form-data" class="space-y-5" onsubmit="document.getElementById('submitBtn').disabled = true; document.getElementById('submitBtnText').textContent = 'กำลังบันทึกข้อมูล…';">
            <?= \App\Core\Csrf::field() ?>

            <!-- Title -->
            <div>
                <label for="ticket_title" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-pen-to-square text-slate-400 text-xs" aria-hidden="true"></i>
                    <span>หัวข้อปัญหา / อุปกรณ์ที่ชำรุด</span>
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <input type="text" id="ticket_title" name="title" required placeholder="เช่น จอคอมพิวเตอร์แผนกบัญชีเปิดไม่ติด, Wi-Fi ชั้น 2 หลุดบ่อย…" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
            </div>

            <!-- Two-col category and priority -->
            <div class="grid sm:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label for="ticket_category" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-tags text-slate-400 text-xs" aria-hidden="true"></i>
                        <span>หมวดหมู่งานซ่อม</span>
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <select id="ticket_category" name="category_id" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 transition-all">
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="ticket_priority" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-gauge-high text-slate-400 text-xs" aria-hidden="true"></i>
                        <span>ระดับความเร่งด่วน</span>
                        <span class="text-rose-500" aria-hidden="true">*</span>
                    </label>
                    <select id="ticket_priority" name="priority" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 transition-all">
                        <option value="low">ต่ำ (Low) - ไม่กระทบงานหลัก</option>
                        <option value="medium" selected>ปานกลาง (Medium) - กระทบบางฟังก์ชัน</option>
                        <option value="high">สูง (High) - ไม่สามารถปฏิบัติงานได้</option>
                        <option value="urgent">เร่งด่วนที่สุด (Urgent) - ระบบหลักหยุดชะงัก</option>
                    </select>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="ticket_description" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-align-left text-slate-400 text-xs" aria-hidden="true"></i>
                    <span>รายละเอียดอาการ / สถานที่ตั้งอุปกรณ์</span>
                    <span class="text-rose-500" aria-hidden="true">*</span>
                </label>
                <textarea id="ticket_description" name="description" rows="4" required placeholder="อธิบายอาการอย่างละเอียด ระบุชั้น ห้อง หรือหมายเลขครุภัณฑ์อุปกรณ์…" 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all"></textarea>
            </div>

            <!-- Image Upload with Live Preview -->
            <div>
                <label for="imageInput" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
                    <i class="fa-solid fa-camera text-slate-400 text-xs" aria-hidden="true"></i>
                    <span>แนบรูปภาพอาการปัญหา (ไม่บังคับ - สูงสุด 5&nbsp;MB)</span>
                </label>
                <div class="border-2 border-dashed border-slate-300 hover:border-blue-400 bg-slate-50/50 hover:bg-blue-50/20 rounded-2xl p-6 text-center cursor-pointer relative transition-all focus-within:ring-2 focus-within:ring-blue-600">
                    <input type="file" id="imageInput" name="image" accept="image/jpeg,image/png,image/webp" 
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div id="uploadPrompt" class="space-y-1.5 pointer-events-none">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-blue-500 mx-auto" aria-hidden="true"></i>
                        <p class="text-xs text-slate-700 font-medium">คลิกเพื่อเลือกไฟล์รูปภาพ หรือลากไฟล์มาวางที่นี่</p>
                        <p class="text-[11px] text-slate-400">รองรับไฟล์ JPG, PNG, WEBP ขนาดไม่เกิน 5&nbsp;MB</p>
                    </div>
                    <img id="imagePreview" class="hidden mx-auto max-h-52 rounded-xl mt-3 object-contain shadow-xs border border-slate-200" alt="พรีวิวรูปภาพอาการที่เลือก">
                </div>
            </div>

            <div class="pt-3">
                <button type="submit" id="submitBtn" class="w-full min-h-[44px] py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs shadow-blue-500/25 transition-all flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    <i class="fa-solid fa-paper-plane text-xs" aria-hidden="true"></i>
                    <span id="submitBtnText">ยืนยันการเปิดตั๋วแจ้งซ่อม</span>
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
