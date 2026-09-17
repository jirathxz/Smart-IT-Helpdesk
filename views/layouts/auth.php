<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'เข้าสู่ระบบ - Smart IT Helpdesk') ?></title>
    
    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6.5.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Kanit"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Kanit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-center items-center p-4 selection:bg-blue-600 selection:text-white relative overflow-hidden">
    <!-- Subtle Ambient Background Elements -->
    <div class="absolute -top-32 -left-32 w-80 h-80 bg-blue-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>
    <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-indigo-100 rounded-full blur-3xl pointer-events-none opacity-60"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-7">
            <div class="inline-flex w-12 h-12 rounded-2xl bg-blue-600 items-center justify-center shadow-lg shadow-blue-500/25 mb-3 text-white">
                <i class="fa-solid fa-headset text-xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Smart IT Helpdesk</h1>
            <p class="text-xs text-slate-500 mt-1">ระบบแจ้งซ่อมและติดตามงานไอที (OOP Architecture Edition)</p>
        </div>

        <!-- Flash Alerts -->
        <?php include __DIR__ . '/alerts.php'; ?>

        <!-- Content Card -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-xl shadow-slate-200/60">
            <?= $content ?? '' ?>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-slate-400 mt-6 font-light">
            สาขาวิชาเทคโนโลยีสารสนเทศ &bull; Mini Project: Custom OOP PHP
        </div>
    </div>
</body>
</html>
