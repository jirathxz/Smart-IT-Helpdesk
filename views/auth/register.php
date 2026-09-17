<form action="/register" method="POST" class="space-y-4">
    <?= \App\Core\Csrf::field() ?>

    <div>
        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">ชื่อ - นามสกุล</label>
        <input type="text" id="name" name="name" required placeholder="สมชาย ใจดี" 
               class="w-full px-3.5 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
    </div>

    <div>
        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">อีเมล (Email)</label>
        <input type="email" id="email" name="email" required placeholder="name@company.com" 
               class="w-full px-3.5 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">รหัสผ่าน (อย่างน้อย 6 ตัวอักษร)</label>
        <input type="password" id="password" name="password" required minlength="6" placeholder="••••••••" 
               class="w-full px-3.5 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
    </div>

    <div>
        <label for="line_user_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5">LINE User ID (สำหรับรับแจ้งเตือน - ไม่บังคับ)</label>
        <input type="text" id="line_user_id" name="line_user_id" placeholder="U1234567890abcdef..." 
               class="w-full px-3.5 py-2.5 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
    </div>

    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition-all">
        สมัครสมาชิก
    </button>
</form>

<div class="mt-5 text-center text-xs text-slate-400">
    มีบัญชีผู้ใช้งานแล้ว? <a href="/login" class="text-blue-400 hover:text-blue-300 font-semibold underline underline-offset-4">เข้าสู่ระบบ</a>
</div>
