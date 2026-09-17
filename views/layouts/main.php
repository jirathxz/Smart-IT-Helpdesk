<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Smart IT Helpdesk & Notification System') ?></title>
    
    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
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
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Kanit', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col antialiased selection:bg-blue-600 selection:text-white">
    <!-- Navbar Header -->
    <?php include __DIR__ . '/navbar.php'; ?>

    <div class="flex flex-1 relative overflow-hidden">
        <!-- Sidebar Navigation -->
        <?php include __DIR__ . '/sidebar.php'; ?>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50/70">
            <div class="max-w-7xl mx-auto">
                <!-- Flash Alerts -->
                <?php include __DIR__ . '/alerts.php'; ?>

                <!-- View Content -->
                <?= $content ?? '' ?>
            </div>
        </main>
    </div>

    <!-- Mobile Drawer Overlay & Scripts -->
    <script>
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            if (!sidebar) return;
            const isHidden = sidebar.classList.contains('-translate-x-full');
            if (isHidden) {
                sidebar.classList.remove('-translate-x-full');
                if (sidebarBackdrop) sidebarBackdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                if (sidebarBackdrop) sidebarBackdrop.classList.add('hidden');
            }
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebar);
        }
    </script>
</body>
</html>
