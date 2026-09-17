<form action="/register" method="POST" class="space-y-4">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-user text-slate-400 text-xs"></i>
            ชื่อ - นามสกุล
        </label>
        <input type="text" id="name" name="name" required placeholder="สมชาย ใจดี" 
               class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
    </div>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
            อีเมล (Email)
        </label>
        <input type="email" id="email" name="email" required placeholder="name@company.com" 
               class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-lock text-slate-400 text-xs"></i>
            รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)
        </label>
        <input type="password" id="password" name="password" required minlength="6" placeholder="••••••••" 
               class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
    </div>

    <div>
        <label for="line_user_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
            <i class="fa-brands fa-line text-emerald-500 text-sm"></i>
            LINE User ID (สำหรับรับแจ้งเตือน - ไม่บังคับ)
        </label>
        <input type="text" id="line_user_id" name="line_user_id" placeholder="U1234567890abcdef..." 
               class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
        <i class="fa-solid fa-user-plus text-xs"></i>
        <span>สมัครสมาชิก</span>
    </button>
</form>

<div class="mt-5 text-center text-xs text-slate-500">
    มีบัญชีผู้ใช้งานแล้ว? <a href="/login" class="text-blue-600 hover:text-blue-700 font-semibold underline underline-offset-4">เข้าสู่ระบบ</a>
</div>
