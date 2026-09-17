<!DOCTYPE html>
<html lang="th" class="h-full">
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
                        sans: ['"Kanit"', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Kanit', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-100/70 text-slate-800 min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 selection:bg-blue-600 selection:text-white">
    <div class="w-full max-w-md my-auto">
        <!-- Brand Wordmark Header -->
        <div class="text-center mb-6">
            <div class="inline-flex items-baseline tracking-tight font-sans select-none mb-1">
                <span class="text-3xl sm:text-4xl font-bold text-slate-900">Smart</span>
                <span class="text-3xl sm:text-4xl font-black text-blue-600 tracking-tighter ml-0.5">IT</span>
                <span class="text-xl sm:text-2xl font-medium text-slate-500 ml-2">Helpdesk</span>
                <span class="w-2 h-2 rounded-full bg-blue-600 ml-1.5 mb-1 inline-block"></span>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-normal">ระบบบริหารจัดการและติดตามงานบริการเทคโนโลยีสารสนเทศ</p>
        </div>

        <!-- Flash Alerts -->
        <?php include __DIR__ . '/alerts.php'; ?>

        <!-- Content Card -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-md shadow-slate-200/50">
            <?= $content ?? '' ?>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-slate-400 mt-6">
            &copy; <?= date('Y') ?> Smart IT Helpdesk &bull; Enterprise IT Service Management
        </div>
    </div>
</body>
</html>
