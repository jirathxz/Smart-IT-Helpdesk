<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div>
        <a href="/tickets" class="inline-flex items-center gap-2 text-xs font-medium text-slate-500 hover:text-blue-600 transition-colors">
            <i class="fa-solid fa-arrow-left text-[11px]"></i>
            <span>กลับไปหน้ารายการงานแจ้งซ่อม</span>
        </a>
    </div>

    <!-- Main Card -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-2xs overflow-hidden">
        <div class="p-4 sm:p-6 border-b border-slate-200 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">แบบฟอร์มแจ้งซ่อมอุปกรณ์ไอที</h1>
                    <p class="text-xs text-slate-500 mt-0.5">กรอกข้อมูลรายละเอียดอาการเสียเพื่อให้เจ้าหน้าที่ช่างเข้าดำเนินการแก้ไข</p>
                </div>
            </div>
        </div>

        <form action="/tickets" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-5">
            <?= \App\Core\Csrf::field() ?>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <!-- หัวข้องานซ่อม -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    หัวข้องานซ่อม / อาการที่พบ <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" required autofocus
                       placeholder="เช่น คอมพิวเตอร์เปิดไม่ติด มีไฟกระพริบ, จอฟ้า, เครื่องพิมพ์ไม่ดึงกระดาษ..."
                       class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
            </div>

            <!-- หมวดหมู่อุปกรณ์ & ระดับความเร่งด่วน -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        หมวดหมู่อุปกรณ์ <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" required
                            class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                        <option value="">-- เลือกหมวดหมู่อุปกรณ์ --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        ระดับความสำคัญ / ความเร่งด่วน <span class="text-red-500">*</span>
                    </label>
                    <select id="priority" name="priority" required
                            class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent">
                        <option value="low">⚪ ต่ำ - ไม่ส่งผลกระทบต่องานหลัก</option>
                        <option value="medium" selected>🔵 ปกติ - ใช้งานได้บางส่วน รอดำเนินการ</option>
                        <option value="high">🟠 สูง - ไม่สามารถปฏิบัติงานได้ชั่วคราว</option>
                        <option value="urgent">🔴 เร่งด่วน - กระทบทั้งระบบ / ผู้บริหาร / ห้องประชุม</option>
                    </select>
                </div>
            </div>

            <!-- รายละเอียดอาการ -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    รายละเอียดของปัญหาและสถานที่เกิดเหตุ <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4" required
                          placeholder="ระบุสถานที่เกิดเหตุ เช่น อาคาร 2 ห้อง 305 และพฤติกรรมก่อนเกิดปัญหา เพื่อให้ช่างจัดเตรียมอะไหล่ได้ตรงจุด..."
                          class="w-full px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"></textarea>
            </div>

            <!-- กล่องอัปโหลดรูปภาพพร้อม Live Preview -->
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    รูปภาพประกอบอาการขัดข้อง (ไม่บังคับ, สูงสุด 5MB)
                </label>
                <div id="dropzone" class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-2xl p-6 text-center cursor-pointer bg-slate-50/50 hover:bg-blue-50/20 transition-all">
                    <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 mb-2 block"></i>
                    <p class="text-xs sm:text-sm font-semibold text-slate-700">คลิกเพื่อเลือกไฟล์รูปภาพ หรือลากและวางที่นี่</p>
                    <p class="text-[11px] text-slate-400 mt-1">รองรับไฟล์ภาพ JPG, PNG หรือ WebP ขนาดไม่เกิน 5MB</p>
                    <input type="file" id="ticket_image" name="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                </div>

                <div id="previewContainer" class="hidden mt-3 p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img id="previewImg" src="#" alt="Preview" class="w-14 h-14 object-cover rounded-lg border border-slate-200">
                        <div>
                            <div id="previewFilename" class="text-xs font-semibold text-slate-800 line-clamp-1"></div>
                            <div id="previewFilesize" class="text-[11px] text-slate-400 font-mono"></div>
                        </div>
                    </div>
                    <button type="button" id="removeImgBtn" class="text-slate-400 hover:text-red-600 p-2 text-xs transition-colors">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="/tickets" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-xs sm:text-sm font-medium text-slate-600 transition-colors">
                    ยกเลิก
                </a>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium shadow-xs transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>ส่งใบแจ้งซ่อมเข้าระบบ</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('ticket_image');
    const previewContainer = document.getElementById('previewContainer');
    const previewImg = document.getElementById('previewImg');
    const previewFilename = document.getElementById('previewFilename');
    const previewFilesize = document.getElementById('previewFilesize');
    const removeBtn = document.getElementById('removeImgBtn');

    if (!dropzone || !fileInput) return;

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-blue-500', 'bg-blue-50/30');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50/30');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-blue-500', 'bg-blue-50/30');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            handleFiles(fileInput.files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            handleFiles(fileInput.files[0]);
        }
    });

    function handleFiles(file) {
        if (!file.type.match('image.*')) {
            alert('กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPG, PNG, WebP)');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            previewFilename.textContent = file.name;
            previewFilesize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', () => {
            fileInput.value = '';
            previewContainer.classList.add('hidden');
            previewImg.src = '#';
        });
    }
});
</script>
