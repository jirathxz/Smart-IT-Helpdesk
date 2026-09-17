<form action="/login" method="POST" class="space-y-4">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center gap-1.5">
            <i class="fa-solid fa-envelope text-slate-400 text-xs" aria-hidden="true"></i>
            <span>อีเมลบัญชีผู้ใช้</span>
        </label>
        <input type="email" id="email" name="email" required autocomplete="email" spellcheck="false" inputmode="email" placeholder="name@company.com…" 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                <i class="fa-solid fa-lock text-slate-400 text-xs" aria-hidden="true"></i>
                <span>รหัสผ่าน</span>
            </label>
        </div>
        <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••" 
               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus-visible:ring-2 focus-visible:ring-blue-600/20 transition-all">
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-xs shadow-blue-500/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 transition-all flex items-center justify-center gap-2">
        <i class="fa-solid fa-right-to-bracket text-xs" aria-hidden="true"></i>
        <span>เข้าสู่ระบบ</span>
    </button>
</form>

<!-- Quick Click Demo Accounts (Sandbox Accounts) -->
<div class="mt-6 pt-5 border-t border-slate-200">
    <div class="text-[11px] font-semibold text-slate-500 text-center mb-2.5 flex items-center justify-center gap-1.5">
        <i class="fa-solid fa-user-gear text-slate-400" aria-hidden="true"></i>
        <span>บัญชีทดสอบระบบ (คลิกเข้าใช้งานด่วน)</span>
    </div>
    <div class="grid grid-cols-2 gap-2 text-xs">
        <a href="/quick-login/1" class="p-2.5 rounded-xl bg-slate-50 hover:bg-purple-50/70 border border-slate-200 hover:border-purple-200 text-slate-800 text-center transition-all flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-purple-600">
            <div class="font-bold flex items-center gap-1 text-purple-700">
                <i class="fa-solid fa-shield-halved text-xs" aria-hidden="true"></i>
                <span>Admin</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate">admin@helpdesk.local</div>
        </a>
        <a href="/quick-login/2" class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50/70 border border-slate-200 hover:border-amber-200 text-slate-800 text-center transition-all flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-amber-600">
            <div class="font-bold flex items-center gap-1 text-amber-700">
                <i class="fa-solid fa-wrench text-xs" aria-hidden="true"></i>
                <span>ช่างสมชาย</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate">tech@helpdesk.local</div>
        </a>
        <a href="/quick-login/3" class="p-2.5 rounded-xl bg-slate-50 hover:bg-amber-50/70 border border-slate-200 hover:border-amber-200 text-slate-800 text-center transition-all flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-amber-600">
            <div class="font-bold flex items-center gap-1 text-amber-700">
                <i class="fa-solid fa-screwdriver text-xs" aria-hidden="true"></i>
                <span>ช่างวิชัย</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate">tech2@helpdesk.local</div>
        </a>
        <a href="/quick-login/4" class="p-2.5 rounded-xl bg-slate-50 hover:bg-blue-50/70 border border-slate-200 hover:border-blue-200 text-slate-800 text-center transition-all flex flex-col items-center justify-center gap-0.5 focus-visible:ring-2 focus-visible:ring-blue-600">
            <div class="font-bold flex items-center gap-1 text-blue-700">
                <i class="fa-solid fa-user text-xs" aria-hidden="true"></i>
                <span>คุณสมหญิง (User)</span>
            </div>
            <div class="text-[10px] text-slate-500 truncate">user@helpdesk.local</div>
        </a>
    </div>
</div>

<div class="mt-5 text-center text-xs text-slate-500">
    ยังไม่มีบัญชีผู้ใช้งาน? <a href="/register" class="text-blue-600 hover:text-blue-700 font-semibold underline underline-offset-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 rounded">ลงทะเบียนใหม่</a>
</div>
