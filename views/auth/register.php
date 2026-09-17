<form action="/register" method="POST" class="space-y-4">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-user text-slate-400 text-xs" aria-hidden="true"></i>
            <span>ชื่อ - นามสกุล</span>
        </label>
        <input type="text" id="name" name="name" required autocomplete="name" placeholder="สมชาย ใจดี…" 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
    </div>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-envelope text-slate-400 text-xs" aria-hidden="true"></i>
            <span>อีเมล (Email)</span>
        </label>
        <input type="email" id="email" name="email" required autocomplete="email" spellcheck="false" inputmode="email" placeholder="name@company.com…" 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-lock text-slate-400 text-xs" aria-hidden="true"></i>
            <span>รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)</span>
        </label>
        <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" placeholder="••••••••" 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
    </div>

    <div>
        <label for="line_user_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="fa-brands fa-line text-emerald-500 text-sm" aria-hidden="true"></i>
            <span>LINE User ID (สำหรับรับแจ้งเตือนสถานะงาน - ไม่บังคับ)</span>
        </label>
        <input type="text" id="line_user_id" name="line_user_id" autocomplete="off" placeholder="U1234567890abcdef…" 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs shadow-blue-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 transition-all flex items-center justify-center gap-2">
        <i class="fa-solid fa-user-plus text-xs" aria-hidden="true"></i>
        <span>สมัครสมาชิก</span>
    </button>
</form>

<div class="mt-5 text-center text-xs text-slate-500">
    มีบัญชีผู้ใช้งานแล้ว? <a href="/login" class="text-blue-600 hover:text-blue-700 font-semibold underline underline-offset-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">เข้าสู่ระบบ</a>
</div>
