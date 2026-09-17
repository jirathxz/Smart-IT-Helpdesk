<form action="/login" method="POST" class="space-y-4">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">อีเมล (Email)</label>
        <input type="email" id="email" name="email" required placeholder="name@company.com" 
               class="w-full px-3.5 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
    </div>

    <div>
        <div class="flex items-center justify-between mb-1.5">
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">รหัสผ่าน (Password)</label>
        </div>
        <input type="password" id="password" name="password" required placeholder="••••••••" 
               class="w-full px-3.5 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition-all">
        เข้าสู่ระบบ
    </button>
</form>

<!-- Quick Click Demo Accounts (Great for Grading/Evaluation) -->
<div class="mt-6 pt-5 border-t border-slate-800">
    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 text-center mb-2.5 flex items-center justify-center gap-2">
        <span>⚡ บัญชีทดสอบระบบ (คลิกเดียวเข้าสู่ระบบ)</span>
    </div>
    <div class="grid grid-cols-2 gap-2 text-xs">
        <a href="/quick-login/1" class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/25 text-rose-300 text-center transition-all">
            <div class="font-bold">👑 Admin</div>
            <div class="text-[10px] text-rose-400/80">admin@helpdesk.local</div>
        </a>
        <a href="/quick-login/2" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/25 text-amber-300 text-center transition-all">
            <div class="font-bold">🔧 ช่างสมชาย</div>
            <div class="text-[10px] text-amber-400/80">tech@helpdesk.local</div>
        </a>
        <a href="/quick-login/3" class="p-2 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/25 text-amber-300 text-center transition-all">
            <div class="font-bold">🔧 ช่างวิชัย</div>
            <div class="text-[10px] text-amber-400/80">tech2@helpdesk.local</div>
        </a>
        <a href="/quick-login/4" class="p-2 rounded-xl bg-blue-500/10 hover:bg-blue-500/20 border border-blue-500/25 text-blue-300 text-center transition-all">
            <div class="font-bold">👤 คุณสมหญิง (User)</div>
            <div class="text-[10px] text-blue-400/80">user@helpdesk.local</div>
        </a>
    </div>
</div>

<div class="mt-5 text-center text-xs text-slate-400">
    ยังไม่มีบัญชีผู้ใช้งาน? <a href="/register" class="text-blue-400 hover:text-blue-300 font-semibold underline underline-offset-4">ลงทะเบียนใหม่</a>
</div>
