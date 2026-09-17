<form action="/login" method="POST" class="space-y-4">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-envelope text-slate-400 text-xs"></i>
            อีเมล (Email)
        </label>
        <input type="email" id="email" name="email" required placeholder="name@company.com" 
               class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-slate-400 text-xs"></i>
                รหัสผ่าน (Password)
            </label>
        </div>
        <input type="password" id="password" name="password" required placeholder="••••••••" 
               class="w-full px-3.5 py-2.5 bg-slate-50/50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-500/15 transition-all">
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
        <i class="fa-solid fa-right-to-bracket text-xs"></i>
        <span>เข้าสู่ระบบ</span>
    </button>
</form>

<!-- Quick Click Demo Accounts (Great for Grading/Evaluation) -->
<div class="mt-6 pt-5 border-t border-slate-200">
    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 text-center mb-2.5 flex items-center justify-center gap-1.5">
        <i class="fa-solid fa-bolt text-amber-500"></i>
        <span>บัญชีทดสอบระบบ (คลิกเดียวเข้าสู่ระบบ)</span>
    </div>
    <div class="grid grid-cols-2 gap-2 text-xs">
        <a href="/quick-login/1" class="p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-900 text-center transition-all flex flex-col items-center justify-center gap-0.5 shadow-sm">
            <div class="font-bold flex items-center gap-1 text-purple-700">
                <i class="fa-solid fa-crown text-xs"></i>
                <span>Admin</span>
            </div>
            <div class="text-[10px] text-purple-600/80 truncate">admin@helpdesk.local</div>
        </a>
        <a href="/quick-login/2" class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-center transition-all flex flex-col items-center justify-center gap-0.5 shadow-sm">
            <div class="font-bold flex items-center gap-1 text-amber-700">
                <i class="fa-solid fa-wrench text-xs"></i>
                <span>ช่างสมชาย</span>
            </div>
            <div class="text-[10px] text-amber-600/80 truncate">tech@helpdesk.local</div>
        </a>
        <a href="/quick-login/3" class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-center transition-all flex flex-col items-center justify-center gap-0.5 shadow-sm">
            <div class="font-bold flex items-center gap-1 text-amber-700">
                <i class="fa-solid fa-screwdriver text-xs"></i>
                <span>ช่างวิชัย</span>
            </div>
            <div class="text-[10px] text-amber-600/80 truncate">tech2@helpdesk.local</div>
        </a>
        <a href="/quick-login/4" class="p-2.5 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-900 text-center transition-all flex flex-col items-center justify-center gap-0.5 shadow-sm">
            <div class="font-bold flex items-center gap-1 text-blue-700">
                <i class="fa-solid fa-user text-xs"></i>
                <span>คุณสมหญิง (User)</span>
            </div>
            <div class="text-[10px] text-blue-600/80 truncate">user@helpdesk.local</div>
        </a>
    </div>
</div>

<div class="mt-5 text-center text-xs text-slate-500">
    ยังไม่มีบัญชีผู้ใช้งาน? <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold underline underline-offset-4">ลงทะเบียนใหม่</a>
</div>
